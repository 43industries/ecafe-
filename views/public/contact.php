<div class="container my-5 page-enter">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card-ecafe p-4">
                <h1 class="fw-bold mb-4">Contact <span class="text-primary">Us</span></h1>
                <form id="contactForm">
                    <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-ecafe">Send Message</button>
                </form>
            </div>
            <div class="row mt-4 g-3">
                <div class="col-md-4"><div class="glass-card p-3 text-center"><i class="fas fa-map-marker-alt text-primary"></i><p class="small mb-0 mt-2">School Campus, Main Building</p></div></div>
                <div class="col-md-4"><div class="glass-card p-3 text-center"><i class="fas fa-phone text-primary"></i><p class="small mb-0 mt-2">+254 700 000 000</p></div></div>
                <div class="col-md-4"><div class="glass-card p-3 text-center"><i class="fas fa-envelope text-primary"></i><p class="small mb-0 mt-2">cafe@school.edu</p></div></div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('contactForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const res = await ECafe.fetch('<?= url('/contact') ?>', { method: 'POST', body: new FormData(form) });
    ECafe.toast(res.message);
    form.reset();
});
</script>
