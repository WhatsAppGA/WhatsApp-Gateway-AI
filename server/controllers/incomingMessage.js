const {
  parseIncomingMessage,
  formatReceipt,
  prepareMediaMessage,
  delayMsg,
} = require("../lib/helper");
require("dotenv").config();

const axios = require("axios");
const {
  isExistsEqualCommand,
  isExistsContainCommand,
  getUrlWebhook,
  getDevice,
} = require("../database/model");
const { formatButtonMsg, Button } = require("../dto/button");
const { ulid } = require("ulid");
const { Section, formatListMsg } = require("../dto/list");
const { getPpUrl } = require("../whatsapp");

const IncomingMessage = async (msg, sock) => {
  try {
    let quoted = false;

    if (!msg.messages) return;

    msg = msg.messages[0];

    const senderName = msg?.pushName || "";

    if (msg.key.fromMe === true) return;
    if (msg.key.remoteJid === "status@broadcast") return;
    const participant =
      msg.key.participant && formatReceipt(msg.key.participant);
    const { command, media, from } = await parseIncomingMessage(msg, sock);

    let reply;
    let result;
    const numberWa = sock.user.id.split(":")[0];

    //getting profile picture
    let ppUrl = null;
    try {
      ppUrl = await sock.profilePictureUrl(
        msg.key.participant ?? msg.key.remoteJid
      );
    } catch (error) {
      console.log(error);
      ppUrl =
        "https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/1200px-WhatsApp.svg.png";
    }

    const checkEqual = await isExistsEqualCommand(command, numberWa);
    if (checkEqual.length > 0) {
      result = checkEqual;
    } else {
      result = await isExistsContainCommand(command, numberWa);
    }

    // end check autoreply in database

    const device = await getDevice(numberWa);
    if (device.length > 0) {
      if (device[0].wh_read == 1) {
        sock.readMessages([msg.key]);
      }
    }
    if (result.length === 0) {
      const url = await getUrlWebhook(numberWa);

      if (url == null) return;
      const r = await sendWebhook({
        device: numberWa,
        command: command,
        media,
        from,
        name: senderName,
        url,
        participant,
        ppUrl,
      });
      if (r === false) return;
      if (r === undefined) return;
      if (typeof r != "object") return;
      quoted = r?.quoted ? true : false;

      reply = JSON.stringify(r);
    } else {
      replyorno =
        result[0].reply_when == "All"
          ? true
          : result[0].reply_when == "Group" &&
            msg.key.remoteJid.includes("@g.us")
          ? true
          : result[0].reply_when == "Personal" &&
            !msg.key.remoteJid.includes("@g.us")
          ? true
          : false;

      if (replyorno === false) return;

      if (result[0].is_read != 0) {
        sock.readMessages([msg.key]);
      }
      if (result[0].is_typing == 1) {
        const delay = result[0].delay == 0 ? 2000 : result[0].delay * 1000;
        await delayMsg(delay, sock, msg.key.remoteJid);
      }
      quoted = result[0].is_quoted ? true : false;
      if (typeof result[0].reply === "object") {
        reply = JSON.stringify(result[0].reply);
      } else {
        reply = result[0].reply;
      }
    }
    reply = reply.replace(/{name}/g, senderName);
    // replace if exists {name} with sender name in reply
    reply = JSON.parse(reply);

    //typing
    if (device.length > 0) {
      if (device[0].wh_typing == 1) {
        const delay = 2 * 1000;
        // await sock.sendPresenceUpdate("composing", jid);
        await delayMsg(delay, sock, msg.key.remoteJid, true);
        // await sock.sendPresenceUpdate("paused", recipient);
      }
    }

    // send MEDIA MESSAGE

    if ("type" in reply) {
      let ownerJid = sock.user.id.replace(/:\d+/, "");
      //audio
      if (reply.type == "audio") {
        return await sock.sendMessage(msg.key.remoteJid, {
          audio: { url: reply.url },
          ptt: true,
          mimetype: "audio/mpeg",
        });
      }
      //button

      // for send media ( document/video or image)
      const generate = await prepareMediaMessage(sock, {
        caption: reply.caption ? reply.caption : "",
        fileName: reply.filename,
        media: reply.url,
        mediatype:
          reply.type !== "video" && reply.type !== "image"
            ? "document"
            : reply.type,
      });

      const message = { ...generate.message };

      return await sock.sendMessage(
        msg.key.remoteJid,
        {
          forward: {
            key: { remoteJid: ownerJid, fromMe: true },
            message: message,
          },
        },
        {
          quoted: quoted ? msg : null,
        }
      );
      //SEND TEXT MESSAGE
    } else if ("buttons" in reply) {
      const btns = reply.buttons.map((btn) => new Button(btn));
      const message = formatButtonMsg(
        btns,
        reply?.footer,
        reply.text ?? reply?.caption,
        sock,
        reply?.image?.url
      );
      const msgId = ulid(Date.now());
      return await sock.relayMessage(msg.key.remoteJid, message, {
        messageId: msgId,
      });
    } else if ("sections" in reply) {
      const sections = reply.sections.map((sect) => new Section(sect));
      const message = formatListMsg(
        sections,
        reply?.footer ?? "..",
        reply.text ?? reply.caption,
        sock,
        reply?.image?.url
      );
      const msgId = ulid(Date.now());
      return await sock.relayMessage(msg.key.remoteJid, message, {
        messageId: msgId,
      });
    } else {
      await sock
        .sendMessage(msg.key.remoteJid, reply, {
          quoted: quoted ? msg : null,
        })
        .catch((e) => {
          console.log(e);
        });
    }
    return true;
  } catch (e) {
    console.log("err", e);
  }
};

async function sendWebhook({
  device,
  command,
  media,
  from,
  name,
  url,
  participant,
  ppUrl,
}) {
  try {
    const data = {
      device,
      message: command,
      media,
      from,
      name,
      participant,
      ppUrl,
    };

    console.log("forward to " + url);
    const res = await axios
      .post(url, data, {
        headers: { "Content-Type": "application/json; charset=utf-8" },
        maxContentLength: 100 * 5024 * 1024, // 10 MB
        maxBodyLength: 100 * 1024 * 1024, // 10 MB
      })
      .catch((er) => {
        console.log(er);
        return false;
      });

    return res.data;
  } catch (error) {
    console.log("error send webhook", error);
    return false;
  }
}

module.exports = { IncomingMessage };
