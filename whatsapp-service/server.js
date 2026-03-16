const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
} = require("@whiskeysockets/baileys");
const express = require("express");
const qrcode = require("qrcode-terminal");
const pino = require("pino");

const app = express();
app.use(express.json());

let sock = null;
let isConnected = false;

async function connectWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState("auth_info");

    sock = makeWASocket({
        auth: state,
        logger: pino({ level: "silent" }),
        version: [2, 3000, 1015901307], // tambah ini
        browser: ["GIBOT-v2", "Chrome", "20.0.0"], // tambah ini
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

            console.log("❌ Koneksi terputus, kode:", code);

            if (shouldReconnect) {
                console.log("🔄 Mencoba reconnect...");
                connectWhatsApp();
            } else {
                console.log(
                    "🚪 Logged out, hapus folder auth_info dan restart.",
                );
            }
        }

        if (connection === "open") {
            isConnected = true;
            console.log("✅ WhatsApp berhasil terhubung!");
        }
    });
}

// Endpoint kirim pesan
app.post("/send", async (req, res) => {
    const { phone, message } = req.body;

    if (!isConnected || !sock) {
        return res
            .status(500)
            .json({ success: false, message: "WhatsApp belum terhubung" });
    }

    if (!phone || !message) {
        return res
            .status(400)
            .json({ success: false, message: "phone dan message wajib diisi" });
    }

    try {
        const jid = `${phone}@s.whatsapp.net`;
        await sock.sendMessage(jid, { text: message });
        return res.json({ success: true, message: "Pesan berhasil dikirim" });
    } catch (error) {
        console.error("Error kirim pesan:", error.message);
        return res.status(500).json({ success: false, message: error.message });
    }
});

// Endpoint kirim ke banyak nomor sekaligus
app.post("/send-bulk", async (req, res) => {
    const { phones, message } = req.body;

    if (!isConnected || !sock) {
        return res
            .status(500)
            .json({ success: false, message: "WhatsApp belum terhubung" });
    }

    if (!phones || !Array.isArray(phones) || !message) {
        return res.status(400).json({
            success: false,
            message: "phones (array) dan message wajib diisi",
        });
    }

    const results = [];

    for (const phone of phones) {
        try {
            const jid = `${phone}@s.whatsapp.net`;
            await sock.sendMessage(jid, { text: message });
            results.push({ phone, success: true });

            // Delay 1 detik antar pesan biar tidak kena spam filter WA
            await new Promise((resolve) => setTimeout(resolve, 1000));
        } catch (error) {
            results.push({ phone, success: false, error: error.message });
        }
    }

    return res.json({ success: true, results });
});

// Endpoint cek status
app.get("/status", (req, res) => {
    return res.json({ connected: isConnected });
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`WhatsApp service running on port ${PORT}`);
    connectWhatsApp();
});
