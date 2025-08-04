const { ulid } = require("ulid");
const { prepareMediaMessage } = require("../lib/helper");

class Button {
  constructor(props) {
    this.type = props.type || "reply"; // Default to 'reply' if type is not provided
    this.displayText = props.displayText || "";
    this.id = props.id;
    this.url = props.url;
    this.copyCode = props.copyCode;
    this.phoneNumber = props.phoneNumber;

    if (this.type === "reply" && !this.id) {
      this.id = ulid(); // Assuming `ulid()` is defined/imported elsewhere
    }

    this.mapType = new Map([
      ["reply", "quick_reply"],
      ["copy", "cta_copy"],
      ["url", "cta_url"],
      ["call", "cta_call"],
    ]);
  }

  get typeButton() {
    return this.mapType.get(this.type);
  }

  toJSONString() {
    const toString = (obj) => JSON.stringify(obj);

    const json = {
      call: () =>
        toString({
          display_text: this.displayText,
          phone_number: this.phoneNumber,
        }),
      reply: () => toString({ display_text: this.displayText, id: this.id }),
      copy: () =>
        toString({ display_text: this.displayText, copy_code: this.copyCode }),
      url: () =>
        toString({
          display_text: this.displayText,
          url: this.url,
          merchant_url: this.url,
        }),
    };

    return json[this.type]?.() || "";
  }
}

const formatButtonMsg = async (
  buttons,
  footer,
  msg,
  sock,
  image = null
) => {
  const generate = await (async () => {
    if (image) {
      return await prepareMediaMessage(sock, {
        mediatype: "image",
        media: image,
      });
    }
  })();

  return {
    viewOnceMessage: {
      message: {
        messageContextInfo: {
          deviceListMetadata: {},
          deviceListMetadataVersion: 2,
        },
        interactiveMessage: {
          body: {
            text: (() => {
              return msg;
            })(),
          },
          footer: {
            text: footer ?? "..",
          },
          header: (() => {
            if (generate?.message?.imageMessage) {
              return {
                hasMediaAttachment: !!generate.message.imageMessage,
                imageMessage: generate.message.imageMessage,
              };
            }
          })(),
          nativeFlowMessage: {
            buttons: buttons.map((value) => {
              return {
                name: value.typeButton,
                buttonParamsJson: value.toJSONString(),
              };
            }),
            messageParamsJson: JSON.stringify({
              from: "api",
              templateId: ulid(Date.now()),
            }),
          },
        },
      },
    },
  };
};
module.exports = { Button, formatButtonMsg };
