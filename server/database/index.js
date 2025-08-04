const mysql2 = require("mysql2");
require("dotenv").config();
// Create the connection pool. The pool-specific settings are the defaults
const db = mysql2.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USERNAME,
  database: process.env.DB_DATABASE,
  password: process.env.DB_PASSWORD,
  port: process.env.DB_PORT || 3306,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});

const setStatus = (device, status) => {
  try {
    db.query(`UPDATE devices SET status = '${status}' WHERE body = ${device} `);
    return true;
  } catch (error) {
    return false;
  }
};

function dbQuery(query, values) {
    return new Promise((resolve, reject) => {
        db.query(query, values, (err, res) => {
            if (err) {
                reject(err);
            } else {
                resolve(res);
            }
        });
    });
}

module.exports = { setStatus, dbQuery, db };

// EXPORT
