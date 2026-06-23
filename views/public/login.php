<div class="container my-5 page-enter">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-ecafe p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-utensils fa-3x text-primary mb-3"></i>
                    <h2 class="fw-bold">Welcome Back</h2>
                    <p class="text-muted">Sign in to your e-Café account</p>
                </div>
                <form method="POST" action="<?= url('/login') ?>">
                    <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
                    <div class="mb-3">
                        <label class="form-label">Student ID / Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" name="identifier" class="form-control" placeholder="e.g. STU001 or admin" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-ecafe w-100 btn-lg">Sign In</button>
                </form>
                <div class="mt-4 p-3 bg-light rounded small">
                    <strong>Demo accounts:</strong><br>
                    Student: STU001 / Password123!<br>
                    Staff: staff01 / Password123!<br>
                    Admin: admin / Password123!
                </div>
            </div>
        </div>
    </div>
</div>
