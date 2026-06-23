<div class="d-flex justify-content-between mb-4">
    <h5>Staff Members</h5>
    <button class="btn btn-primary-ecafe btn-sm" data-bs-toggle="modal" data-bs-target="#addStaff">Add Staff</button>
</div>
<div class="table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Username</th><th>Name</th><th>Email</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($staff as $s): ?>
        <tr>
            <td><?= e($s['username']) ?></td>
            <td><?= e($s['full_name']) ?></td>
            <td><?= e($s['email']) ?></td>
            <td><span class="badge <?= $s['is_active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $s['is_active'] ? 'Active' : 'Inactive' ?></span></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="addStaff" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="<?= url('/admin/staff') ?>">
            <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
            <div class="modal-header"><h5>Add Staff</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input name="username" class="form-control mb-2" placeholder="Username" required>
                <input name="full_name" class="form-control mb-2" placeholder="Full Name" required>
                <input name="email" type="email" class="form-control mb-2" placeholder="Email" required>
                <input name="phone" class="form-control mb-2" placeholder="Phone">
                <input name="password" type="password" class="form-control" value="Password123!">
            </div>
            <div class="modal-footer"><button class="btn btn-primary-ecafe">Create</button></div>
        </form>
    </div></div>
</div>
