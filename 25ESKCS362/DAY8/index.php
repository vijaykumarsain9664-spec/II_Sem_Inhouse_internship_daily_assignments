<?php require_once 'logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration Portal</title>
 
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
 
<style>
  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: #f4f6f9;
  }
 
  main {
    flex: 1;
  }
 
  .card-form {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.08);
  }
 
  .profile-placeholder {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #adb5bd;
    margin: 0 auto 1rem auto;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #dee2e6;
  }
 
  /* Bonus: gradient background on the confirmation card */
  .welcome-card {
    border: none;
    border-radius: 1rem;
    color: #fff;
    background: linear-gradient(135deg, #4e73df 0%, #6f42c1 50%, #20c997 100%);
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.2);
    overflow: hidden;
  }
 
  .welcome-card .detail-row {
    background: rgba(255,255,255,0.12);
    border-radius: 0.5rem;
    padding: 0.6rem 1rem;
    margin-bottom: 0.5rem;
  }
 
  footer {
    background-color: #212529;
  }
</style>
</head>
<body>
 
<!-- ===== NAVBAR ===== -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="fa-solid fa-graduation-cap me-2"></i>Student Registration Portal
    </a>
  </div>
</nav>
 
<main class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
 
        <?php if (!$submitted): ?>
 
          <!-- ===== REGISTRATION FORM ===== -->
          <div class="card card-form p-4 p-md-5">
            <h3 class="text-center mb-1"><i class="fa-solid fa-user-plus text-primary me-2"></i>Student Registration</h3>
            <p class="text-center text-muted mb-4">Fill in your details below to register</p>
 
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><strong>Please fix the following:</strong>
                <ul class="mb-0 mt-2">
                  <?php foreach ($errors as $err): ?>
                    <li><?php echo e($err); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
 
            <form action="index.php" method="POST" novalidate>
 
              <div class="mb-3">
                <label for="name" class="form-label"><i class="fa-solid fa-user me-1"></i>Full Name</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?php echo e($data['name']); ?>" placeholder="e.g. Ananya Sharma">
              </div>
 
              <div class="mb-3">
                <label for="email" class="form-label"><i class="fa-solid fa-envelope me-1"></i>Email Address</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?php echo e($data['email']); ?>" placeholder="e.g. ananya@example.com">
              </div>
 
              <div class="mb-3">
                <label for="cgpa" class="form-label"><i class="fa-solid fa-chart-line me-1"></i>CGPA</label>
                <input type="number" step="0.01" min="0" max="10" class="form-control" id="cgpa" name="cgpa"
                       value="<?php echo e($data['cgpa']); ?>" placeholder="e.g. 8.5">
              </div>
 
              <div class="mb-3">
                <label for="branch" class="form-label"><i class="fa-solid fa-code-branch me-1"></i>Branch</label>
                <select class="form-select" id="branch" name="branch">
                  <option value="" disabled <?php echo $data['branch'] === '' ? 'selected' : ''; ?>>Choose your branch</option>
                  <?php
                  $branches = ['Computer Science', 'Information Technology', 'Electronics & Communication',
                               'Mechanical Engineering', 'Civil Engineering', 'Electrical Engineering'];
                  foreach ($branches as $b):
                  ?>
                    <option <?php echo ($data['branch'] === $b) ? 'selected' : ''; ?>><?php echo e($b); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
 
              <div class="mb-4">
                <label for="college" class="form-label"><i class="fa-solid fa-school me-1"></i>College Name</label>
                <input type="text" class="form-control" id="college" name="college"
                       value="<?php echo e($data['college']); ?>" placeholder="e.g. MNIT Jaipur">
              </div>
 
              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="fa-solid fa-paper-plane me-2"></i>Register Now
                </button>
              </div>
 
            </form>
          </div>
 
        <?php else: ?>
 
          <!-- ===== WELCOME / CONFIRMATION CARD ===== -->
          <div class="card welcome-card p-4 p-md-5 mb-4">
 
            <div class="profile-placeholder">
              <i class="fa-solid fa-user"></i>
            </div>
 
            <h3 class="text-center mb-1">Welcome, <?php echo e($data['name']); ?>! <i class="fa-solid fa-champagne-glasses"></i></h3>
            <p class="text-center mb-4" style="opacity:.9;">You have successfully registered on <?php echo e($today); ?></p>
 
            <div class="detail-row d-flex justify-content-between align-items-center">
              <span><i class="fa-solid fa-envelope me-2"></i>Email</span>
              <strong><?php echo e($data['email']); ?></strong>
            </div>
            <div class="detail-row d-flex justify-content-between align-items-center">
              <span><i class="fa-solid fa-code-branch me-2"></i>Branch</span>
              <strong><?php echo e($data['branch']); ?></strong>
            </div>
            <div class="detail-row d-flex justify-content-between align-items-center">
              <span><i class="fa-solid fa-school me-2"></i>College</span>
              <strong><?php echo e($data['college']); ?></strong>
            </div>
            <div class="detail-row d-flex justify-content-between align-items-center">
              <span><i class="fa-solid fa-chart-line me-2"></i>CGPA</span>
              <strong><?php echo e(number_format($data['cgpa'], 2)); ?></strong>
            </div>
          </div>
 
          <!-- ===== GRADE ALERT ===== -->
          <div class="alert alert-<?php echo e($alertType); ?> d-flex align-items-center shadow-sm" role="alert">
            <i class="fa-solid <?php echo e($icon); ?> fa-2x me-3"></i>
            <div>
              <h5 class="mb-1">Grade: <?php echo e($grade); ?></h5>
              <span>Based on a CGPA of <?php echo e(number_format($data['cgpa'], 2)); ?>, your calculated grade is <strong><?php echo e($grade); ?></strong>.</span>
            </div>
          </div>
 
          <div class="d-grid mt-4">
            <a href="index.php" class="btn btn-outline-primary">
              <i class="fa-solid fa-rotate-left me-2"></i>Register Another Student
            </a>
          </div>
 
        <?php endif; ?>
 
      </div>
    </div>
  </div>
</main>
 
<!-- ===== FOOTER ===== -->
<footer class="text-light py-4 mt-auto">
  <div class="container text-center">
    <p class="mb-1">
      <i class="fa-solid fa-code me-1"></i>
      Student Registration Confirmation System
    </p>
    <small class="text-secondary">
      &copy; <?php echo date("Y"); ?> All rights reserved. Built with PHP &amp; Bootstrap 5.
    </small>
  </div>
</footer>
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
