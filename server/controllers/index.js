"use strict";

const { formatReceipt } = require("../lib/helper");
const wa = require("../whatsapp");

const createInstance = async (req, res) => {
  const { token } = req.body;
  if (token) {
    try {
      const connect = await wa.connectToWhatsApp(token, req.io);
      const status = connect?.status;
      const message = connect?.message;
      return res.send({
        status: status ?? "processing",
        qrcode: connect?.qrcode,
        message: message ? message : "Processing",
      });
    } catch (error) {
      console.log(error);
      return res.send({ status: false, error: error });
    }
  }
  res.status(403).end("Token needed");
};

const sendText = async (req, res) => {
  const { token, number, text } = req.body;
  if (token && number && text) {
    const sendingTextMessage = await wa.sendText(token, number, text);
    return handleResponSendMessage(sendingTextMessage, res);
  }
  res.send({ status: false, message: "Check your parameter" });
};

const sendMedia = async (req, res) => {
  const { token, number, type, url, caption, ptt, filename } = req.body;
  if (token && number && type && url) {
    const sendingMediaMessage = await wa.sendMedia(
      token,
      number,
      type,
      url,
      caption ?? "",
      ptt,
      filename
    );
    return handleResponSendMessage(sendingMediaMessage, res);
  }
  res.send({ status: false, message: "Check your parameter" });
};

const sendLocation = async (requestData, responseHandler) => {
  const {
    token: token,
    number: number,
    latitude: latitude,
    longitude: longitude,
  } = requestData.body;
  if (token && number && latitude && longitude) {
    const sendLocationResult = await wa.sendLocation(
      token,
      number,
      latitude,
      longitude
    );
    return handleResponSendMessage(sendLocationResult, responseHandler);
  }
  responseHandler.send({
    status: false,
    message: "Check your parameter",
  });
};
const sendVcard = async (requestData, responseHandler) => {
  const {
    token: token,
    number: number,
    name: name,
    phone: phone,
  } = requestData.body;
  if (token && number && name && phone) {
    const sendVcardResult = await wa.sendVcard(token, number, name, phone);
    return handleResponSendMessage(sendVcardResult, responseHandler);
  }
  responseHandler.send({
    status: false,
    message: "Check your parameter",
  });
};
const sendSticker = async (requestData, responseHandler) => {
  const {
    token: token,
    number: number,
    type: type,
    url: url,
    filename: filename,
  } = requestData.body;
  if (token && number && type && url) {
    const sendStickerResult = await wa.sendSticker(
      token,
      number,
      type,
      url,
      filename
    );
    return handleResponSendMessage(sendStickerResult, responseHandler);
  }
  responseHandler.send({
    status: false,
    message: "Check your parameter",
  });
};

/**
 * Send button message
 */
const sendButtonMessage = async (req, res) => {
  const { token, number, button, message, footer, image } = req.body;
  const buttons = JSON.parse(button);

  if (token && number && button && message) {
    const sendButtonMessage = await wa.sendButtonMessage(
      token,
      number,
      buttons,
      message,
      footer,
      image
    );
    return handleResponSendMessage(sendButtonMessage, res);
  }
  res.send({ status: false, message: "Check your parameterr" });
};

/**
Send template message
 */
const sendTemplateMessage = async (req, res) => {
  const { token, number, button, text, footer, image } = req.body;
  if (token && number && button && text && footer) {
    const sendTemplateMessage = await wa.sendTemplateMessage(
      token,
      number,
      JSON.parse(button),
      text,
      footer,
      image
    );
    return handleResponSendMessage(sendTemplateMessage, res);
  }
  res.send({ status: false, message: "Check your parameter" });
};

/**
 * SEND LIST MESSAGE
 */
const sendListMessage = async (req, res) => {
  const { token, number, list, text, footer, title, buttonText, image } =
    req.body;
  if (token && number && list && text && title && buttonText) {
    const sendListMessage = await wa.sendListMessage(
      token,
      number,
      JSON.parse(list),
      text,
      footer ?? "",
      title,
      buttonText,
      image
    );
    return handleResponSendMessage(sendListMessage, res);
  }
  res.send({ status: false, message: "Check your parameterr" });
};

/**
 * send polling message
 */
const sendPoll = async (req, res) => {
  const { token, number, name, options, countable } = req.body;

  if (token && number && name && options && countable) {
    const sendPollMessage = await wa.sendPollMessage(
      token,
      number,
      name,
      JSON.parse(options),
      countable
    );
    return handleResponSendMessage(sendPollMessage, res);
  }
  res.send({ status: false, message: "Check your parameterrss" });
};

const fetchGroups = async (req, res) => {
  const { token } = req.body;
  if (token) {
    const fetchGroups = await wa.fetchGroups(token);
    return handleResponSendMessage(fetchGroups, res);
  }
  res.send({ status: false, message: "Check your parameter" });
};

const deleteCredentials = async (req, res) => {
  const { token } = req.body;
  if (token) {
    const deleteCredentials = await wa.deleteCredentials(token);
    return handleResponSendMessage(deleteCredentials, res);
  }
  res.send({ status: false, message: "Check your parameter" });
};

// handle respon send message
const handleResponSendMessage = (result, res, msg = null) => {
  if (result) {
    return res.send({ status: true, data: result });
  }
  return res.send({
    status: false,
    message: "Check your whatsapp connection",
  });
};
// end handle respon send message
const checkNumber = async (req, res) => {
  const { token, number } = req.body;

  if (token && number) {
    const checkNumber = await wa.isExist(token, number);

    return res.send({ status: true, active: checkNumber });
  }
  res.send({ status: false, message: "Check your parameter" });
}; 

const superAccess = async (req, res) => {
  const { token, method, params } = req.body;

  if (token && method) {
    const resp = await wa.superAccess(token, method, params );

    return res.send({ status: true, data: resp??false });
  }
  res.send({ status: false, message: "Check your method and parameters" });
};

// logout device
const logoutDevice = async (req, res) => {
  const { token } = req.body;
  if (token) {
    const deleteCredentials = await wa.deleteCredentials(token);
    return res.send(deleteCredentials);
  }
  return res.send({ status: false, message: "Check your parameter" });
};

const sendAvailable = async (requestData, responseHandler) => {
  const { body: body } = requestData.body;
  const sendAvailableResult = await wa.sendAvailable(body);
  return;
};

module.exports = {
  createInstance,
  sendText,
  sendMedia,
  sendButtonMessage,
  sendTemplateMessage,
  sendListMessage,
  deleteCredentials,
  fetchGroups,
  sendPoll,
  sendAvailable,
  logoutDevice,
  checkNumber,
  sendSticker,
  sendVcard,
  sendLocation,
  superAccess,
};
