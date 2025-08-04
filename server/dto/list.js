const { ulid } = require("ulid");
const { prepareMediaMessage } = require("../lib/helper");
class Row {
  constructor(props) {
    Object.assign(this, props);
    if (!this.id) {
      this.id = ulid(Date.now());
    }
    if (!this.header) {
      this.header = "";
    }
  }
}

class ListSection {
  constructor(props) {
    Object.assign(this, props);
    this.rows = props.rows.map((row) => new Row(row));
  }
}

class Section {
  constructor(props) {
    Object.assign(this, props);
    this.list = props.list.map((item) => new ListSection(item));
  }

  toSectionsString() {
    return JSON.stringify({
      title: this.buttonText,
      sections: this.list,
    });
  }
}
const formatListMsg = async (sections, footer, message,sock, image) => {


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
            text: (function () {
              return message;
            })(),
          },
          footer: {
            text: footer ?? "..",
          },
          header: (function () {
            if (generate?.message?.imageMessage) {
              return {
                hasMediaAttachment: !!generate.message.imageMessage,
                imageMessage: generate.message.imageMessage,
              };
            }
          })(),
          nativeFlowMessage: {
            buttons: sections.map(function (value) {
              return {
                name: "single_select",
                buttonParamsJson: value.toSectionsString(),
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

module.exports = { formatListMsg, Section };
