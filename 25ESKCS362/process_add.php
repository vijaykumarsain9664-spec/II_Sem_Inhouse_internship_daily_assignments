<?php
require_once __DIR__ . '/config.php';

// Preserve old input + errors after a failed submission (passed back from process_add.php)
$old    = [];
$errors = [];
if (isset($_GET['errors'])) {
    $errors = json_decode(base64_decode($_GET['errors']), true) ?: [];
}
if (isset($_GET['old'])) {
    $old = json_decode(base64_decode($_GET['old']), true) ?: [];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="card panel-card form-card">
  <div class="card-body p-4">
    <h5 class="mb-4"><i class="bi bi-person-plus-fill me-2"></i>Add New Student</h5>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <strong><i class="bi bi-exclamation-triangle-fill me-1"></i>Please fix the following:</strong>
        <ul class="mb-0 mt-2">
          <?php foreach ($errors as $err): ?>
            <li><?= h($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="process_add.php" method="POST" enctype="multipart/form-data" novalidate id="studentForm">

      <div class="mb-3">
        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required minlength="2" maxlength="100"
               value="<?= h($old['name'] ?? '') ?>" placeholder="e.g. Aarav Sharma">
        <div class="invalid-feedback">Please enter the student's full name (min 2 characters).</div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" required
               value="<?= h($old['email'] ?? '') ?>" placeholder="e.g. aarav@example.com">
        <div class="invalid-feedback">Please enter a valid, unique email address.</div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">Branch / Course <span class="text-danger">*</span></label>
          <select name="branch" class="form-select" required>
            <option value="" disabled <?= empty($old['branch']) ? 'selected' : '' ?>>Select branch</option>
            <?php foreach (BRANCHES as $b): ?>
              <option value="<?= h($b) ?>" <?= ($old['branch'] ?? '') === $b ? 'selected' : '' ?>><?= h($b) ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Please select a branch.</div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">CGPA <span class="text-danger">*</span></label>
          <input type="number" step="0.01" min="0" max="10" name="cgpa" class="form-control" required
                 value="<?= h($old['cgpa'] ?? '') ?>" placeholder="0.00 - 10.00">
          <div class="invalid-feedback">CGPA must be a number between 0 and 10.</div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
          <option value="Active"   <?= ($old['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
          <option value="Inactive" <?= ($old['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="form-label fw-semibold">Profile Photo</label>
        <input type="file" name="photo" accept="image/png, image/jpeg, image/webp, image/gif" class="form-control">
        <div class="form-text">JPG, PNG, WEBP or GIF. Max 2 MB. Optional.</div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle-fill me-1"></i>Save Student
        </button>
        <a href="index.php" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<script>
// Client-side Bootstrap validation
(() => {
  const form = document.getElementById('studentForm');
  form.addEventListener('submit', (event) => {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  }, false);
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
