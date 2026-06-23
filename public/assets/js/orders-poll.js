/**
 * Poll order status for real-time updates
 */
const OrderPoll = {
    interval: null,

    start(orderId, onUpdate) {
        this.stop();
        const poll = async () => {
            try {
                const res = await fetch(`${ECAFE_BASE}/api/orders/status/${orderId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.success && onUpdate) onUpdate(data);
            } catch (e) { /* silent */ }
        };
        poll();
        this.interval = setInterval(poll, 8000);
    },

    stop() {
        if (this.interval) clearInterval(this.interval);
    }
};

document.querySelectorAll('[data-poll-order]').forEach(el => {
    const orderId = el.dataset.pollOrder;
    OrderPoll.start(orderId, (data) => {
        const badge = el.querySelector('.order-status-badge');
        if (badge) {
            badge.textContent = data.status;
            badge.className = `badge order-status-badge status-${data.status}`;
        }
        if (data.status === 'ready') {
            ECafe.toast('Your order is ready for pickup!');
            if (data.qr_code) {
                const qrImg = el.querySelector('.qr-code-img');
                if (qrImg) qrImg.src = `${ECAFE_BASE}/${data.qr_code}`;
            }
        }
    });
});

// Notification polling
if (document.getElementById('notification-badge')) {
    setInterval(async () => {
        try {
            const res = await fetch(`${ECAFE_BASE}/api/notifications`);
            const data = await res.json();
            const badge = document.getElementById('notification-badge');
            if (badge && data.unread > 0) {
                badge.textContent = data.unread;
                badge.style.display = 'inline';
            }
        } catch (e) { /* silent */ }
    }, 10000);
}
