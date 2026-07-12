<?php
require "db_connect.php";
 
// Fetch all students, most recent first
$result = $conn->query("SELECT * FROM students ORDER BY registered_at DESC");
$total = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Students</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Bonus: alternate row coloring by course, and a highlight for
       students registered in the last 24 hours */
    .row-new { background-color: #fff3cd !important; } /* soft yellow highlight */
</style>
</head>
<body class="bg-light">
<div class="container py-5">
 
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Registered Students</h3>
        <span class="badge bg-primary fs-6">Total: <?= $total ?></span>
    </div>
 
    <?php if ($total === 0): ?>
        <div class="alert alert-info">No students registered yet. <a href="index.php">Register the first one &rarr;</a></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Registered On</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()):
                    // Bonus: highlight rows registered in the last 24 hours
                    $isNew = (strtotime($row["registered_at"]) > strtotime("-1 day"));
                    $rowClass = $isNew ? "row-new" : "";
                ?>
                    <tr class="<?= $rowClass ?>">
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row["full_name"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td><?= htmlspecialchars($row["phone"]) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row["course"]) ?></span></td>
                        <td><?= date("d M Y, h:i A", strtotime($row["registered_at"])) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
 
    <a href="index.php" class="btn btn-outline-primary">&larr; Back to Registration Form</a>
</div>
</body>
</html>
<?php $conn->close(); ?>
