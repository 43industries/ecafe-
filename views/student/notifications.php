<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Notifications</h5>
    <button class="btn btn-sm btn-outline-primary" onclick="markAllRead()">Mark all read</button>
</div>
<?php if (empty($notifications)): ?>
<p class="text-muted">No notifications.</p>
<?php else: ?>
<?php foreach ($notifications as $n): ?>
<div class="card-ecafe p-3 mb-2 <?= !$n['is_read'] ? 'border-primary' : '' ?>">
    <div class="d-flex justify-content-between">
        <strong><?= e($n['title']) ?></strong>
        <small class="text-muted"><?= date('M j, g:i A', strtotime($n['created_at'])) ?></small>
    </div>
    <p class="mb-0 small"><?= e($n['message']) ?></p>
</div>
<?php endforeach; ?>
<?php endif; ?>
<script>
async function markAllRead() {
    await ECafe.fetch(ECAFE_BASE + '/api/notifications/read', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: '' });
    location.reload();
}
</script>
