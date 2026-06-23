<div class="d-flex justify-content-between mb-4">
    <h5>Students</h5>
    <button class="btn btn-primary-ecafe btn-sm" data-bs-toggle="modal" data-bs-target="#addStudent">Add Student</button>
</div>
<div class="table-responsive">
    <table class="table table-hover">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Grade</th><th>Points</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($students as $s): ?>
        <tr>
            <td><?= e($s['student_id']) ?></td>
            <td><?= e($s['full_name']) ?></td>
            <td><?= e($s['email']) ?></td>
            <td><?= e($s['grade'] ?? '-') ?></td>
            <td><?= (int)$s['loyalty_points'] ?></td>
            <td>
                <form method="POST" action="<?= url('/admin/students/' . $s['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Deactivate?')">
                    <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
                    <button class="btn btn-sm btn-outline-danger">Deactivate</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="addStudent" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="<?= url('/admin/students') ?>">
            <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
            <div class="modal-header"><h5 class="modal-title">Add Student</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-2"><input name="student_id" class="form-control" placeholder="Student ID" required></div>
                <div class="mb-2"><input name="full_name" class="form-control" placeholder="Full Name" required></div>
                <div class="mb-2"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
                <div class="mb-2"><input name="phone" class="form-control" placeholder="Phone"></div>
                <div class="mb-2"><input name="grade" class="form-control" placeholder="Grade"></div>
                <div class="mb-2"><input name="password" type="password" class="form-control" placeholder="Password" value="Password123!"></div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary-ecafe">Create</button></div>
        </form>
    </div></div>
</div>
