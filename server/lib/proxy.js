const https = require("https");
//process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0"; // avoid connection errors
//const { SocksProxyAgent } = require("socks-proxy-agent");
const { HttpsProxyAgent } = require("https-proxy-agent");
// insert your proxy below
// example socks5://00.444.556.99:4145
const proxyUrl =
  "http://geonode_c1gnTEMEm5:4e0888c3-c7c6-4fc2-b66d-2f87fe713d3d@premium-residential.geonode.com:10000";

const agent = new HttpsProxyAgent(proxyUrl);
console.log("here");
// website to check your connection data and show in your console/terminal below
// https.get("https://ipinfo.io", { agent }, (res) => {
//   console.log(res.statusCode, res.headers);
//   res.pipe(process.stdout);
// });
module.exports = { agent };
