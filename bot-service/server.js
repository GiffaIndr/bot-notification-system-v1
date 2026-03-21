const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
} = require("@whiskeysockets/baileys");
const { Client, GatewayIntentBits } = require("discord.js");
const express = require("express");
const qrcode = require("qrcode-terminal");
const pino = require("pino");
require("dotenv").config();
const app = express();
app.use(express.json());

// WHATSAPP

let sock = null;
let isConnected = false;

async function connectWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState("auth_info");

    sock = makeWASocket({
        auth: state,
        logger: pino({ level: "silent" }),
        version: [2, 3000, 1015901307],
        browser: ["Bot", "Chrome", "20.0.0"],
    });

    sock.ev.on("creds.update", saveCreds);

    sock.ev.on("connection.update", ({ connection, lastDisconnect, qr }) => {
        if (qr) {
            console.clear();
            console.log("📱 Scan QR code ini dengan WhatsApp kamu:\n");
            qrcode.generate(qr, { small: true });
        }

        if (connection === "close") {
            isConnected = false;
            const code = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = code !== DisconnectReason.loggedOut;
            if (shouldReconnect) connectWhatsApp();
        }

        if (connection === "open") {
            isConnected = true;
            console.log("✅ WhatsApp berhasil terhubung!");
        }
    });
}

// DISCORD

const discordClient = new Client({
    intents: [GatewayIntentBits.Guilds, GatewayIntentBits.GuildMessages],
});

discordClient.once("clientReady", () => {
    console.log(`✅ Discord Bot terhubung sebagai ${discordClient.user.tag}`);
});

discordClient.login(process.env.DISCORD_TOKEN);

// ENDPOINTS

// Kirim WA ke 1 nomor
app.post("/send", async (req, res) => {
    const { phone, message } = req.body;

    if (!isConnected || !sock) {
        return res
            .status(500)
            .json({ success: false, message: "WhatsApp belum terhubung" });
    }

    try {
        const jid = `${phone}@s.whatsapp.net`;
        await sock.sendMessage(jid, { text: message });
        return res.json({ success: true, message: "Pesan berhasil dikirim" });
    } catch (error) {
        return res.status(500).json({ success: false, message: error.message });
    }
});

// Kirim WA ke banyak nomor
app.post("/send-bulk", async (req, res) => {
    const { phones, message } = req.body;

    if (!isConnected || !sock) {
        return res
            .status(500)
            .json({ success: false, message: "WhatsApp belum terhubung" });
    }

    const results = [];
    for (const phone of phones) {
        try {
            const jid = `${phone}@s.whatsapp.net`;
            await sock.sendMessage(jid, { text: message });
            results.push({ phone, success: true });
            await new Promise((resolve) => setTimeout(resolve, 1000));
        } catch (error) {
            results.push({ phone, success: false, error: error.message });
        }
    }

    return res.json({ success: true, results });
});

// Kirim Discord ke channel
app.post("/discord/send", async (req, res) => {
    const { channel_id, message } = req.body;

    try {
        const channel = await discordClient.channels.fetch(channel_id);

        if (!channel) {
            return res
                .status(404)
                .json({ success: false, message: "Channel tidak ditemukan" });
        }

        await channel.send(message);
        return res.json({
            success: true,
            message: "Pesan Discord berhasil dikirim",
        });
    } catch (error) {
        return res.status(500).json({ success: false, message: error.message });
    }
});
app.get("/discord/channel/:channel_id", async (req, res) => {
    try {
        const channel = await discordClient.channels.fetch(
            req.params.channel_id,
        );
        return res.json({ success: true, name: channel.name });
    } catch (error) {
        return res.status(500).json({ success: false, message: error.message });
    }
});
// WA: terima caption
app.post("/send-file", async (req, res) => {
    const { phones, filename, type, mime_type, data, caption } = req.body;

    if (!isConnected || !sock) {
        return res
            .status(500)
            .json({ success: false, message: "WhatsApp belum terhubung" });
    }

    const buffer = Buffer.from(data, "base64");
    const results = [];

    for (const phone of phones) {
        try {
            const jid = `${phone}@s.whatsapp.net`;

            if (type === "image") {
                await sock.sendMessage(jid, {
                    image: buffer,
                    caption: caption || filename,
                });
            } else {
                await sock.sendMessage(jid, {
                    document: buffer,
                    fileName: filename,
                    mimetype: mime_type,
                    caption: caption || "",
                });
            }

            results.push({ phone, success: true });
            await new Promise((resolve) => setTimeout(resolve, 1000));
        } catch (error) {
            results.push({ phone, success: false, error: error.message });
        }
    }

    return res.json({ success: true, results });
});

app.post("/discord/send-with-files", async (req, res) => {
    const { channel_id, message, files } = req.body;

    try {
        const channel = await discordClient.channels.fetch(channel_id);
        const attachments = files.map((f) => ({
            attachment: Buffer.from(f.data, "base64"),
            name: f.filename,
        }));

        await channel.send({
            content: message,
            files: attachments,
        });

        return res.json({ success: true });
    } catch (error) {
        return res.status(500).json({ success: false, message: error.message });
    }
});

// Kirim file Discord
app.post("/discord/send-file", async (req, res) => {
    const { channel_id, filename, mime_type, data } = req.body;

    try {
        const channel = await discordClient.channels.fetch(channel_id);
        const buffer = Buffer.from(data, "base64");

        await channel.send({
            files: [{ attachment: buffer, name: filename }],
        });

        return res.json({ success: true });
    } catch (error) {
        return res.status(500).json({ success: false, message: error.message });
    }
});
// Cek status
app.get("/status", (req, res) => {
    return res.json({
        whatsapp: isConnected,
        discord: discordClient.isReady(),
    });
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Service running on port ${PORT}`);
    connectWhatsApp();
});
