<div class="d-flex justify-content-between mb-4">
    <h5>Announcements</h5>
    <button class="btn btn-primary-ecafe btn-sm" data-bs-toggle="modal" data-bs-target="#addAnn">New Announcement</button>
</div>
<?php foreach ($announcements as $a): ?>
<div class="card-ecafe p-3 mb-2">
    <div class="d-flex justify-content-between"><strong><?= e($a['title']) ?></strong><span class="badge bg-secondary"><?= e($a['target_role']) ?></span></div>
    <p class="mb-0 small"><?= e($a['content']) ?></p>
</div>
<?php endforeach; ?>
<div class="modal fade" id="addAnn" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="<?= url('/admin/announcements') ?>">
            <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
            <div class="modal-header"><h5>New Announcement</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input name="title" class="form-control mb-2" placeholder="Title" required>
                <textarea name="content" class="form-control mb-2" placeholder="Content" required></textarea>
                <select name="target_role" class="form-select mb-2"><option value="all">All</option><option value="student">Students</option><option value="staff">Staff</option></select>
                <div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" checked> Active</div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary-ecafe">Create</button></div>
        </form>
    </div></div>
</div>
