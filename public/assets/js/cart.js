/**
 * Cart operations
 */
const Cart = {
    async add(menuItemId, quantity = 1) {
        const result = await ECafe.fetch(ECAFE_BASE + '/api/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `menu_item_id=${menuItemId}&quantity=${quantity}`,
        });
        if (result.success) {
            ECafe.toast('Added to cart!');
            const badge = document.getElementById('cart-badge');
            if (badge) badge.textContent = result.count;
        } else {
            ECafe.toast(result.message || 'Failed to add', 'error');
        }
    },

    async update(menuItemId, quantity) {
        return ECafe.fetch(ECAFE_BASE + '/api/cart/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `menu_item_id=${menuItemId}&quantity=${quantity}`,
        });
    },

    async remove(menuItemId) {
        await ECafe.fetch(ECAFE_BASE + '/api/cart/remove', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `menu_item_id=${menuItemId}`,
        });
        location.reload();
    }
};

document.querySelectorAll('[data-add-cart]').forEach(btn => {
    btn.addEventListener('click', () => Cart.add(btn.dataset.addCart, parseInt(btn.dataset.qty || 1)));
});

document.querySelectorAll('[data-cart-qty]').forEach(input => {
    input.addEventListener('change', async () => {
        await Cart.update(input.dataset.cartQty, input.value);
        location.reload();
    });
});

document.querySelectorAll('[data-cart-remove]').forEach(btn => {
    btn.addEventListener('click', () => Cart.remove(btn.dataset.cartRemove));
});
