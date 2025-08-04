const { dbQuery } = require("./index");
const cache = require("./../lib/cache");


const myCache = cache.myCache;

// autoreplies table
const isExistsEqualCommand = async (command, number) => {
  // find in autoreplies where has device.body = numberWa

  if (myCache.has(command + number)) {
    return myCache.get(command + number);
  }
  let checkDevice = await dbQuery(
    `SELECT * FROM devices WHERE body = '${number}' LIMIT 1`
  );
  if (checkDevice.length === 0) return [];
  let device_id = checkDevice[0].id;

  let data = await dbQuery(
    `SELECT * FROM autoreplies WHERE keyword = "${command}" AND type_keyword = 'Equal' AND device_id = ${device_id} AND status = 'Active' LIMIT 1`
  );

  if (data.length === 0) return [];
  myCache.set(command + number, data);
  return data;
};

const isExistsContainCommand = async (command, number) => {
  // find in autoreplies where has device.body = numberWa
  if (myCache.has("contain" + command + number)) {
    return myCache.get("contain" + command + number);
  }
  let checkDevice = await dbQuery(
    `SELECT * FROM devices WHERE body = '${number}' LIMIT 1`
  );
  if (checkDevice.length === 0) return [];
  let device_id = checkDevice[0].id;
  let data = await dbQuery(
    `SELECT * FROM autoreplies WHERE LOCATE(keyword, "${command}") > 0 AND type_keyword = 'Contain' AND device_id = ${device_id} AND status = 'Active' LIMIT 1`
  );
  if (data.length === 0) return [];

  myCache.set("contain" + command + number, data);
  return data;
};

const getUrlWebhook = async (number) => {
  if (myCache.has("webhook" + number)) {
    return myCache.get("webhook" + number);
  }
  let url = null;
  let data = await dbQuery(
    `SELECT webhook FROM devices WHERE body = '${number}' LIMIT 1`
  );
  if (data.length > 0) {
    url = data[0].webhook;
  }
  myCache.set("webhook" + number, url);
  return url;
};

const getDevice = async (deviceBody) => {
  if (myCache.has("deviceall" + deviceBody)) {
    return myCache.get("deviceall" + deviceBody);
  }
  let deviceall = null,
    deviceResult = await dbQuery(
      "SELECT * FROM devices WHERE body = '" + deviceBody + "' LIMIT 1"
    );
  return (
    deviceResult.length > 0 && (deviceall = deviceResult),
    myCache.set("deviceall" + deviceBody, deviceall),
    deviceall
  );
};
//  end autoreplies table

// find favorites

const isFavoriteContact = async (number,contact) => {
    if (myCache.has("favorite_" + number+"_"+contact)) {
        return myCache.get("favorite_" + number+"_"+contact);
    }
    let name = false;
    let data = await dbQuery(
        `SELECT contacts.name FROM contacts 
        inner join devices on devices.user_id=contacts.user_id  
        WHERE contacts.number='${contact}' and contacts.is_favorite = '1' and devices.body = '${number}' LIMIT 1`
    );
    if (data.length > 0) {
        name = data[0].name;
    }
    if(name != false){
          myCache.set("favorite_" + number+"_"+contact, name);
    }
    return name;
};

const getDeviceSettings = async (number) => {
    let data = await dbQuery(
        `SELECT * FROM devices WHERE body = '${number}' LIMIT 1`
    );
    return data.length > 0 ? data[0]:false;
};

const logNewMessage = async (sender_id, number, message, role) => {
    try {
        const query = `INSERT INTO ai_chats (id, sender_id, number, role, message, created_at) VALUES (NULL, ?, ?, ?, ?, NULL)`;
        const values = [sender_id, number, role, message];
        await dbQuery(query, values); // Pass the query and parameters separately
        return true;
    } catch (error) {
        console.error(error);
        return false;
    }
};

const getChatMessages = async (sender_id,number,limit=40)=>{
    try {
        let data = await dbQuery(
            `SELECT * FROM (
                        SELECT * FROM ai_chats 
                        WHERE number = '${number}' AND sender_id = '${sender_id}' 
                        AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) < 60 
                        ORDER BY id DESC LIMIT ${limit}
                    ) AS lastMessages ORDER BY id ASC`
        );
        return data.length > 0 ? data:false;    
    }
     catch (error) {
        console.error(error);
        return false
    }
}

module.exports = {
  isExistsEqualCommand,
  isExistsContainCommand,
  getUrlWebhook,
  getDevice,
  isFavoriteContact,
  getDeviceSettings,
  logNewMessage,
  getChatMessages,
};
