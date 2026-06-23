<?php if (empty($items)): ?>
<div class="text-center py-5">
    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
    <h4>Your cart is empty</h4>
    <a href="<?= url('/student/menu') ?>" class="btn btn-primary-ecafe">Browse Menu</a>
</div>
<?php else: ?>
<div class="row">
    <div class="col-lg-8">
        <?php foreach ($items as $item): ?>
        <div class="card-ecafe p-3 mb-3 d-flex flex-row align-items-center gap-3">
            <img src="<?= $item['image'] ? asset('img/'.$item['image']) : 'https://placehold.co/80x80' ?>" width="80" height="80" class="rounded" style="object-fit:cover">
            <div class="flex-grow-1">
                <h6 class="mb-1"><?= e($item['name']) ?></h6>
                <span class="text-primary"><?= formatMoney((float)$item['price']) ?></span>
            </div>
            <input type="number" class="form-control form-control-sm" style="width:70px" value="<?= $item['quantity'] ?>" min="1" data-cart-qty="<?= $item['menu_item_id'] ?>">
            <strong><?= formatMoney((float)$item['price'] * $item['quantity']) ?></strong>
            <button class="btn btn-sm btn-outline-danger" data-cart-remove="<?= $item['menu_item_id'] ?>"><i class="fas fa-trash"></i></button>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="col-lg-4">
        <div class="card-ecafe p-4">
            <h5>Order Summary</h5>
            <hr>
            <div class="d-flex justify-content-between mb-3"><span>Subtotal</span><strong><?= formatMoney($total) ?></strong></div>
            <a href="<?= url('/student/checkout') ?>" class="btn btn-primary-ecafe w-100">Proceed to Checkout</a>
        </div>
    </div>
</div>
<?php endif; ?>
