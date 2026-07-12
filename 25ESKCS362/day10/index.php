<?php
require_once __DIR__ . '/config.php';

// ------------------------------------------------------------------
// Read filter / search inputs
// ------------------------------------------------------------------
$search     = trim($_GET['q'] ?? '');
$branch     = trim($_GET['branch'] ?? '');
$cgpaMin    = $_GET['cgpa_min'] ?? '';
$cgpaMax    = $_GET['cgpa_max'] ?? '';
// Default view = Active only, unless user explicitly asks for all / inactive
$statusView = $_GET['status'] ?? 'Active'; // 'Active' | 'Inactive' | 'All'

$where  = [];
$params = [];

if ($search !== '') {
    // Multi-field search: name, email, branch all at once
    $where[] = "(name LIKE :search OR email LIKE :search OR branch LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($branch !== '') {
    $where[] = "branch = :branch";
    $params[':branch'] = $branch;
}

if ($cgpaMin !== '' && is_numeric($cgpaMin)) {
    $where[] = "cgpa >= :cgpa_min";
    $params[':cgpa_min'] = $cgpaMin;
}
if ($cgpaMax !== '' && is_numeric($cgpaMax)) {
    $where[] = "cgpa <= :cgpa_max";
    $params[':cgpa_max'] = $cgpaMax;
}

if ($statusView === 'Active' || $statusView === 'Inactive') {
    $where[] = "status = :status";
    $params[':status'] = $statusView;
}
// if 'All' -> no status filter applied

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT * FROM students $whereSql ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

// ------------------------------------------------------------------
// Dashboard aggregate stats (always reflect the FULL dataset,
// not the filtered view, so the numbers stay meaningful)
// ------------------------------------------------------------------
$totalStudents = (int) $pdo->query("SELECT COUNT(*) AS c FROM students")->fetch()['c'];
$activeCount   = (int) $pdo->query("SELECT COUNT(*) AS c FROM students WHERE status = 'Active'")->fetch()['c'];
$avgCgpaRow    = $pdo->query("SELECT ROUND(AVG(cgpa), 2) AS avg_cgpa FROM students")->fetch();
$avgCgpa       = $avgCgpaRow['avg_cgpa'] ?? 0;

$perBranchStmt = $pdo->query("SELECT branch, COUNT(*) AS total FROM students GROUP BY branch ORDER BY total DESC");
$perBranch     = $perBranchStmt->fetchAll();
$topBranch     = $perBranch[0]['branch'] ?? 'N/A';
$topBranchCount = $perBranch[0]['total'] ?? 0;

$allBranches = BRANCHES;

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
  <?php
    $msgType = $_GET['msg'];
    $alerts = [
        'added'   => ['success', 'bi-check-circle-fill', 'Student added successfully!'],
        'updated' => ['success', 'bi-check-circle-fill', 'Student details updated successfully!'],
        'deleted' => ['success', 'bi-check-circle-fill', 'Student record deleted successfully.'],
    ];
    $alert = $alerts[$msgType] ?? null;
  ?>
  <?php if ($alert): ?>
    <div class="alert alert-<?= $alert[0] ?> alert-dismissible fade show shadow-sm" role="alert">
      <i class="bi <?= $alert[1] ?> me-2"></i><?= h($alert[2]) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
<?php endif; ?>

<!-- ===================== Stats Row ===================== -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card bg-total d-flex justify-content-between align-items-center">
      <div>
        <div class="stat-label">Total Students</div>
        <div class="stat-value"><?= $totalStudents ?></div>
      </div>
      <i class="bi bi-people-fill stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card bg-avg d-flex justify-content-between align-items-center">
      <div>
        <div class="stat-label">Average CGPA</div>
        <div class="stat-value"><?= h($avgCgpa) ?></div>
      </div>
      <i class="bi bi-graph-up-arrow stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card bg-active d-flex justify-content-between align-items-center">
      <div>
        <div class="stat-label">Active Students</div>
        <div class="stat-value"><?= $activeCount ?></div>
      </div>
      <i class="bi bi-person-check-fill stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card bg-branch d-flex justify-content-between align-items-center">
      <div>
        <div class="stat-label">Top Branch</div>
        <div class="stat-value" style="font-size:1.15rem;"><?= h($topBranch) ?></div>
        <div class="small opacity-75"><?= $topBranchCount ?> students</div>
      </div>
      <i class="bi bi-bar-chart-fill stat-icon"></i>
    </div>
  </div>
</div>

<!-- Per-branch breakdown (collapsible detail) -->
<div class="card panel-card mb-4">
  <div class="card-body">
    <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class="bi bi-diagram-3 me-1"></i>Students per Branch</h6>
    <div class="d-flex flex-wrap gap-2">
      <?php foreach ($perBranch as $row): ?>
        <span class="badge branch-badge rounded-pill px-3 py-2">
          <?= h($row['branch']) ?> &middot; <?= $row['total'] ?>
        </span>
      <?php endforeach; ?>
      <?php if (!$perBranch): ?>
        <span class="text-muted small">No data yet.</span>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ===================== Filters / Search ===================== -->
<div class="card panel-card mb-4">
  <div class="card-body">
    <form method="GET" action="index.php" class="row g-3 align-items-end">

      <div class="col-md-4">
        <label class="form-label small fw-semibold"><i class="bi bi-search me-1"></i>Search (name, email, branch)</label>
        <input type="text" name="q" class="form-control" placeholder="e.g. Priya or Electronics"
               value="<?= h($search) ?>">
      </div>

      <div class="col-md-2">
        <label class="form-label small fw-semibold"><i class="bi bi-diagram-2 me-1"></i>Course / Branch</label>
        <select name="branch" class="form-select">
          <option value="">All Branches</option>
          <?php foreach ($allBranches as $b): ?>
            <option value="<?= h($b) ?>" <?= $branch === $b ? 'selected' : '' ?>><?= h($b) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label small fw-semibold"><i class="bi bi-toggle-on me-1"></i>Status</label>
        <select name="status" class="form-select">
          <option value="Active"   <?= $statusView === 'Active' ? 'selected' : '' ?>>Active only</option>
          <option value="Inactive" <?= $statusView === 'Inactive' ? 'selected' : '' ?>>Inactive only</option>
          <option value="All"      <?= $statusView === 'All' ? 'selected' : '' ?>>View All</option>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label small fw-semibold">Min CGPA</label>
        <input type="number" step="0.01" min="0" max="10" name="cgpa_min" class="form-control"
               value="<?= h($cgpaMin) ?>" placeholder="0.0">
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold">Max CGPA</label>
        <input type="number" step="0.01" min="0" max="10" name="cgpa_max" class="form-control"
               value="<?= h($cgpaMax) ?>" placeholder="10.0">
      </div>

      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-funnel-fill me-1"></i>Apply Filters
        </button>
        <a href="index.php" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
        </a>
      </div>
    </form>
  </div>
</div>

<!-- ===================== Student Table ===================== -->
<div class="card panel-card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <h5 class="mb-0"><i class="bi bi-table me-2"></i>Student Records</h5>
      <span class="badge bg-primary rounded-pill fs-6">
        <i class="bi bi-people me-1"></i><?= count($students) ?> record<?= count($students) === 1 ? '' : 's' ?> found
      </span>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Email</th>
            <th>Branch</th>
            <th>CGPA</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$students): ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                No students match your filters.
              </td>
            </tr>
          <?php endif; ?>

          <?php foreach ($students as $s): ?>
            <tr>
              <td>
                <?php if (!empty($s['photo']) && file_exists(__DIR__ . '/uploads/' . $s['photo'])): ?>
                  <img src="<?= UPLOAD_URL . h($s['photo']) ?>" alt="<?= h($s['name']) ?>" class="student-photo">
                <?php else: ?>
                  <span class="avatar-fallback"><?= h(strtoupper(substr($s['name'], 0, 1))) ?></span>
                <?php endif; ?>
              </td>
              <td class="fw-semibold"><?= h($s['name']) ?></td>
              <td><?= h($s['email']) ?></td>
              <td><span class="badge branch-badge"><?= h($s['branch']) ?></span></td>
              <td><?= h(number_format((float)$s['cgpa'], 2)) ?></td>
              <td>
                <?php if ($s['status'] === 'Active'): ?>
                  <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                <?php else: ?>
                  <span class="badge bg-secondary"><i class="bi bi-dash-circle me-1"></i>Inactive</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a href="edit_student.php?id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form action="delete_student.php" method="POST" class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete \'<?= h($s['name']) ?>\'? This cannot be undone.');">
                  <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                    <i class="bi bi-trash3"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
