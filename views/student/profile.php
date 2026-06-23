<div class="row">
    <div class="col-lg-6">
        <div class="card-ecafe p-4">
            <h5 class="mb-4">Edit Profile</h5>
            <form method="POST" action="<?= url('/student/profile') ?>">
                <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
                <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" value="<?= e($student['full_name']) ?>" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($student['email']) ?>" required></div>
                <div class="mb-3"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" value="<?= e($student['phone'] ?? '') ?>"></div>
                <div class="mb-3"><label class="form-label">Grade</label><input type="text" name="grade" class="form-control" value="<?= e($student['grade'] ?? '') ?>"></div>
                <div class="mb-3"><label class="form-label">New Password (leave blank to keep)</label><input type="password" name="password" class="form-control"></div>
                <button type="submit" class="btn btn-primary-ecafe">Save Changes</button>
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-ecafe p-4">
            <h5 class="mb-3"><i class="fas fa-heart text-danger"></i> Favorite Meals</h5>
            <?php if (empty($favorites)): ?>
            <p class="text-muted small">No favorites yet. Heart items on the menu!</p>
            <?php else: ?>
            <ul class="list-group list-group-flush">
            <?php foreach ($favorites as $fav): ?>
            <li class="list-group-item d-flex justify-content-between bg-transparent">
                <?= e($fav['name']) ?> <span class="text-primary"><?= formatMoney((float)$fav['price']) ?></span>
            </li>
            <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <div class="mt-3 p-3 bg-light rounded">
                <strong>Loyalty Points:</strong> <?= (int)$student['loyalty_points'] ?>
            </div>
        </div>
    </div>
</div>
