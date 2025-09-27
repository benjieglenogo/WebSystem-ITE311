<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-4">
    <div class="col-md-6 col-lg-5">
        <h1 class="text-center mb-4 text-primary fw-bold"></h1>

        <?php if (session()->getFlashdata('register_success')): ?>
            <div class="alert alert-success shadow-sm" role="alert">
                <?= esc(session()->getFlashdata('register_success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('login_error')): ?>
            <div class="alert alert-danger shadow-sm" role="alert">
                <?= esc(session()->getFlashdata('login_error')) ?>
            </div>
        <?php endif; ?>

        <!-- Card -->
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4">
                <form action="<?= site_url('login') ?>" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">username</label>
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
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2">
                        Sign In
                    </button>
                </form>
            </div>
        </div>

        <!-- Register Link -->
        <p class="text-center mt-3 small text-muted">
            Don’t have an account? 
            <a href="<?= base_url('register') ?>" class="fw-semibold text-decoration-none">Register</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
