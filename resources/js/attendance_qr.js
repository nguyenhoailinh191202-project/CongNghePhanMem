import QRCode from 'qrcode';

const qrContainer = document.getElementById('attendance-qr');

if (qrContainer) {
    const canvas = document.getElementById('attendance-qr-canvas');
    const status = document.getElementById('attendance-qr-status');
    const countdown = document.getElementById('attendance-qr-countdown');
    const tokenLabel = document.getElementById('attendance-qr-token');
    const tokenUrl = qrContainer.dataset.tokenUrl;
    let refreshTimer;
    let countdownTimer;

    const showError = () => {
        canvas.classList.add('hidden');
        status.classList.remove('hidden');
    };

    const loadQr = async () => {
        try {
            const response = await fetch(tokenUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Unable to issue QR token');
            }

            const payload = await response.json();
            await QRCode.toCanvas(canvas, payload.url, {
                width: Number(qrContainer.dataset.qrSize || 280),
                margin: 2,
                errorCorrectionLevel: 'M',
                color: { dark: '#0f172a', light: '#ffffff' },
            });

            canvas.classList.remove('hidden');
            status.classList.add('hidden');
            tokenLabel.textContent = payload.token;
            let remaining = payload.expires_in;
            countdown.textContent = remaining;

            window.clearInterval(countdownTimer);
            countdownTimer = window.setInterval(() => {
                remaining -= 1;
                countdown.textContent = Math.max(remaining, 0);
            }, 1000);

            window.clearTimeout(refreshTimer);
            refreshTimer = window.setTimeout(loadQr, payload.expires_in * 1000);
        } catch (error) {
            showError();
            window.clearTimeout(refreshTimer);
            refreshTimer = window.setTimeout(loadQr, 5000);
        }
    };

    loadQr();
}
