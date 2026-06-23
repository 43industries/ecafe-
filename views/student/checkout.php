<div class="row">
    <div class="col-lg-8">
        <div class="card-ecafe p-4">
            <h5 class="mb-4">Checkout</h5>
            <form method="POST" action="<?= url('/student/checkout') ?>" id="checkoutForm">
                <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">

                <div class="mb-3">
                    <label class="form-label">Pickup Time</label>
                    <input type="datetime-local" name="pickup_time" class="form-control" required
                        min="<?= date('Y-m-d\TH:i') ?>" value="<?= date('Y-m-d\TH:i', strtotime('+30 minutes')) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <label class="form-check"><input type="radio" name="payment_method" value="cash" class="form-check-input" checked> <i class="fas fa-money-bill"></i> Cash</label>
                        <label class="form-check"><input type="radio" name="payment_method" value="mobile_money" class="form-check-input" id="mpesaRadio"> <i class="fas fa-mobile-alt"></i> M-Pesa</label>
                        <label class="form-check"><input type="radio" name="payment_method" value="card" class="form-check-input"> <i class="fas fa-credit-card"></i> Card</label>
                    </div>
                </div>

                <div class="mb-3" id="mpesaPhoneGroup" style="display:none">
                    <label class="form-label">M-Pesa Phone Number</label>
                    <input type="tel" name="mpesa_phone" class="form-control" placeholder="07XX XXX XXX" value="<?= e($student['phone'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Coupon Code (optional)</label>
                    <input type="text" name="coupon_code" class="form-control" placeholder="e.g. WELCOME10">
                </div>

                <div class="mb-3">
                    <label class="form-label">Use Loyalty Points (<?= (int)$student['loyalty_points'] ?> available)</label>
                    <input type="number" name="loyalty_points" class="form-control" min="0" max="<?= (int)$student['loyalty_points'] ?>" value="0">
                    <small class="text-muted">100 points = KES 1.00 discount</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Special Instructions</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any allergies or requests..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary-ecafe btn-lg w-100">Place Order — <?= formatMoney($total) ?></button>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-ecafe p-4">
            <h6>Your Items</h6>
            <?php foreach ($items as $item): ?>
            <div class="d-flex justify-content-between small mb-2">
                <span><?= e($item['name']) ?> x<?= $item['quantity'] ?></span>
                <span><?= formatMoney((float)$item['price'] * $item['quantity']) ?></span>
            </div>
            <?php endforeach; ?>
            <hr>
            <div class="d-flex justify-content-between fw-bold"><span>Total</span><span><?= formatMoney($total) ?></span></div>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('[name=payment_method]').forEach(r => {
    r.addEventListener('change', () => {
        document.getElementById('mpesaPhoneGroup').style.display = r.value === 'mobile_money' && r.checked ? 'block' : 'none';
    });
});
</script>
