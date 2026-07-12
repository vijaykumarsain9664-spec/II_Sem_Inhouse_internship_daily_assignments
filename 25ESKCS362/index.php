<?php
require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: index.php');
    exit;
}

$errors = [];
if (isset($_GET['errors'])) {
    $errors = json_decode(base64_decode($_GET['errors']), true) ?: [];
}
// If there were validation errors, prefer the resubmitted (old) values over DB values
if (isset($_GET['old'])) {
    $old = json_decode(base64_decode($_GET['old']), true) ?: [];
    $student = array_merge($student, $old);
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="card panel-card form-card">
  <div class="card-body p-4">
    <h5 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Student — <?= h($student['name']) ?></h5>

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

    <div class="mb-4 d-flex align-items-center gap-3">
      <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/uploads/' . $student['photo'])): ?>
        <img src="<?= UPLOAD_URL . h($student['photo']) ?>" class="photo-preview" alt="Current photo">
      <?php else: ?>
        <span class="avatar-fallback" style="width:90px;height:90px;font-size:2rem;">
          <?= h(strtoupper(substr($student['name'], 0, 1))) ?>
        </span>
      <?php endif; ?>
      <div class="text-muted small">Current profile photo</div>
    </div>

    <form action="process_edit.php" method="POST" enctype="multipart/form-data" novalidate id="studentForm">
      <input type="hidden" name="id" value="<?= (int)$student['id'] ?>">

      <div class="mb-3">
        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required minlength="2" maxlength="100"
               value="<?= h($student['name']) ?>">
        <div class="invalid-feedback">Please enter the student's full name (min 2 characters).</div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" required value="<?= h($student['email']) ?>">
        <div class="invalid-feedback">Please enter a valid, unique email address.</div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">Branch / Course <span class="text-danger">*</span></label>
          <select name="branch" class="form-select" required>
            <?php foreach (BRANCHES as $b): ?>
              <option value="<?= h($b) ?>" <?= $student['branch'] === $b ? 'selected' : '' ?>><?= h($b) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">CGPA <span class="text-danger">*</span></label>
          <input type="number" step="0.01" min="0" max="10" name="cgpa" class="form-control" required
                 value="<?= h($student['cgpa']) ?>">
          <div class="invalid-feedback">CGPA must be a number between 0 and 10.</div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
          <option value="Active"   <?= $student['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
          <option value="Inactive" <?= $student['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="form-label fw-semibold">Replace Profile Photo</label>
        <input type="file" name="photo" accept="image/png, image/jpeg, image/webp, image/gif" class="form-control">
        <div class="form-text">Leave blank to keep the current photo. Max 2 MB.</div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle-fill me-1"></i>Update Student
        </button>
        <a href="index.php" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<script>
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
