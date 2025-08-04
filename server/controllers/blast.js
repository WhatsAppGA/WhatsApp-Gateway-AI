const { ulid } = require("ulid");
const { dbQuery } = require("../database");
const { Button, formatButtonMsg } = require("../dto/button");
const {
  formatReceipt,
  prepareMediaMessage,
  delayMsg,
} = require("../lib/helper");
const wa = require("../whatsapp");
const fs = require("fs");
let inProgress = [];

const updateStatus = async (campaignId, receiver, status) => {
  await dbQuery(
    `UPDATE blasts SET status = '${status}' WHERE receiver = '${receiver}' AND campaign_id = '${campaignId}'`
  );
};
const checkBlast = async (campaignId, receiver) => {
  const checkBlast = await dbQuery(
    `SELECT status FROM blasts WHERE receiver = '${receiver}' AND campaign_id = '${campaignId}'`
  );
  return checkBlast.length > 0 && checkBlast[0].status === "pending";
};
const sendBlastMessage = async (req, res) => {
  const data = JSON.parse(req.body.data);
  const dataBlast = data.data;
  const campaignId = data.campaign_id;

  const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

  if (inProgress[campaignId]) {
    console.log(
      `still any progress in campaign id ${campaignId}, request canceled. `
    );
    return res.send({ status: "in_progress" });
  }

  inProgress[campaignId] = true;
  console.log(`progress campaign ID : ${campaignId} started`);

  // Send the "in_progress" status immediately
  res.send({ status: "in_progress" });

  const send = async () => {
    for (let i in dataBlast) {
      const delay = data.delay;
      //   await sleep(delay * 1000);

      if (data.sender && dataBlast[i].receiver && dataBlast[i].message) {
        const isValid = await checkBlast(campaignId, dataBlast[i].receiver);
        if (isValid) {
          try {
            const check = await wa.isExist(
              data.sender,
              formatReceipt(dataBlast[i].receiver)
            );
            if (!check) {
              await updateStatus(campaignId, dataBlast[i].receiver, "failed");
              continue;
            }
          } catch (error) {
            console.error("Error in wa.isExist: ", error);
            await updateStatus(campaignId, dataBlast[i].receiver, "failed");
            continue;
          }

          // start send blast
          console.log(data);
          try {
            let sendingTextMessage;
            // MEDIA MESSAGE
            if (data.type === "media") {
              const fileDetail = JSON.parse(dataBlast[i].message);
              sendingTextMessage = await wa.sendMedia(
                data.sender,
                dataBlast[i].receiver,
                fileDetail.type,
                fileDetail.url,
                fileDetail.caption,
                0,
                fileDetail.filename,
                delay
              );
              // BUTTON MESSAGE
            } else if (data.type === "button") {
              const msg = JSON.parse(dataBlast[i].message);

              sendingTextMessage = await wa.sendButtonMessage(
                data.sender,
                dataBlast[i].receiver,
                msg.buttons,
                msg.text ?? msg.caption,
                msg.footer,
                msg?.image?.url
              );
            } else {
              //TEST MSG
              sendingTextMessage = await wa.sendMessage(
                data.sender,
                dataBlast[i].receiver,
                dataBlast[i].message,
                delay
              );
            }
            console.log("hereee");

            const status = sendingTextMessage ? "success" : "failed";
            await updateStatus(campaignId, dataBlast[i].receiver, status);
          } catch (error) {
            console.log("woyy");
            console.log(error);
            if (error.message.includes("503")) {
              console.log(
                "Server is busy, waiting for 5 seconds before retrying..."
              );
              await sleep(5000); // Wait for 5 seconds
              i--; // Decrement the counter to retry the current message
            } else {
              await updateStatus(campaignId, dataBlast[i].receiver, "failed");
            }
          }
        } else {
          console.log("no pending, not send!");
        }
      } else {
        console.log("wrong data, progress canceled!");
      }
    }

    delete inProgress[campaignId];
  };

  send().catch((error) => {
    console.error(`Error in send operation: ${error}`);
    delete inProgress[campaignId];
  });
};

module.exports = {
  sendBlastMessage,
};
