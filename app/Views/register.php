<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-4">
    <div class="col-md-7 col-lg-6">
        <h1 class="text-center mb-4 text-primary fw-bold">Create Account</h1>

        <?php if (session()->getFlashdata('register_error')): ?>
            <div class="alert alert-danger shadow-sm" role="alert">
                <?= esc(session()->getFlashdata('register_error')) ?>
            </div>
        <?php endif; ?>

        <!-- Card -->
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4">
                <form action="<?= site_url('register') ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Name</label>
                        <input type="text" 
                               class="form-control form-control-lg rounded-3" 
                               id="name" 
                               name="name" 
                               required 
                               value="<?= esc(old('name')) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" 
                               class="form-control form-control-lg rounded-3" 
                               id="email" 
                               name="email" 
                               required 
                               value="<?= esc(old('email')) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" 
                               class="form-control form-control-lg rounded-3" 
                               id="password" 
                               name="password" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" 
                               class="form-control form-control-lg rounded-3" 
                               id="password_confirm" 
                               name="password_confirm" 
                               required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2">
                        Create Account
                    </button>
                </form>
            </div>
        </div>

        <!-- Login Link -->
        <p class="text-center mt-3 small text-muted">
            Already have an account? 
            <a href="<?= base_url('login') ?>" class="fw-semibold text-decoration-none">Login</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
