
const http = require('http');
const express = require('express');
const app = express();
const server = http.createServer(app);
const qrcode = require('qrcode');
const { Server } = require('socket.io');
const io = new Server(server);
const fs = require('fs');
const { Client, Status, WAState } = require('whatsapp-web.js');
const { connect } = require('http2');


const StartWhatsapp = async ({ socket, id }) => {
    const client = new Client({ puppeteer: { headless: false, }, clientId: id });
    client.initialize().then(() => {
    }).catch(err => { console.log(err); })


    client.sendMessage()
    let qrRetry = 0;
    client.on('qr', (qr) => {
        console.log(qrRetry);
        qrcode.toDataURL(qr, (err, url) => {
            qrRetry++
            if (err) throw err;

            socket !== null ? socket.emit('QrGenerated', url) : console.log(qr);
            if (qrRetry > 1) {
                client.destroy();
            }
        })
    });

    client.on('authenticated', () => {
        socket.emit('Authenticated', client.data);
    });

    client.on('ready', () => {
        socket.emit('Authenticated', client.info);

    });

    client.on('disconnected', () => { console.log('discon'); })
    client.on('message', (msg) => {
        console.log(msg)
    })
    return;


}




io.on('connection', (socket) => {
    socket.on('StartConnection', id => {
        StartWhatsapp({
            socket: socket,
            id: id
        });
    })
})


server.listen(10000, () => {
    console.log('running at port 10000');
})



