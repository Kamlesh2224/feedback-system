<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('../config/db.php');

// ---------------- FILTER ----------------
$category = $_GET['category'] ?? '';
$search   = $_GET['search']   ?? '';

// ---------------- ANALYTICS ----------------

// Total feedback
$total_query = $conn->query("SELECT COUNT(*) as total FROM feedback");
$total       = $total_query->fetch_assoc()['total'];

// Category count
$cat_query     = $conn->query("SELECT category_id, COUNT(*) as count FROM feedback GROUP BY category_id");
$category_data = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
while ($row = $cat_query->fetch_assoc()) {
    $category_data[$row['category_id']] = $row['count'];
}

// Average rating per subject
$avg_query = $conn->query("
    SELECT s.name as subject, AVG(fa.rating) as avg_rating
    FROM feedback_answers fa
    JOIN feedback f       ON fa.feedback_id = f.id
    JOIN subject_teacher st ON f.subject_teacher_id = st.id
    JOIN subjects s       ON st.subject_id = s.id
    GROUP BY s.name
");

// Top 3 teachers
$top_query = $conn->query("
    SELECT t.name as teacher, AVG(fa.rating) as avg_rating
    FROM feedback_answers fa
    JOIN feedback f       ON fa.feedback_id = f.id
    JOIN subject_teacher st ON f.subject_teacher_id = st.id
    JOIN teachers t       ON st.teacher_id = t.id
    GROUP BY t.name
    ORDER BY avg_rating DESC
    LIMIT 3
");

// ---------------- MAIN QUERY ----------------
$query = "
    SELECT f.id, f.category_id, f.description,
           s.name as subject_name, t.name as teacher_name, f.created_at
    FROM feedback f
    LEFT JOIN subject_teacher st ON f.subject_teacher_id = st.id
    LEFT JOIN subjects s         ON st.subject_id = s.id
    LEFT JOIN teachers t         ON st.teacher_id = t.id
    WHERE 1
";

if (!empty($category)) {
    $query .= " AND f.category_id = " . intval($category);
}
if (!empty($search)) {
    $safe   = $conn->real_escape_string($search);
    $query .= " AND (s.name LIKE '%$safe%' OR t.name LIKE '%$safe%' OR f.description LIKE '%$safe%')";
}

$query .= " ORDER BY f.created_at DESC";
$result = $conn->query($query);

// Helper: category label + badge class
function cat_info(int $id): array {
    return match($id) {
        1 => ['Academic',       'badge-academic'],
        2 => ['Infrastructure', 'badge-infrastructure'],
        3 => ['Administrative', 'badge-administrative'],
        4 => ['Bug',            'badge-bug'],
        default => ['Unknown', ''],
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Dashboard CSS (this file) -->
    <link rel="stylesheet" href="../assets/css/admin.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<!-- ── NAVBAR ───────────────────────────────────────────── -->
<nav class="navbar">
    <span class="navbar-brand">Admin Dashboard</span>
    <a href="export.php" class="btn-outline-light">Export</a>
</nav>

<!-- ── CONTENT ──────────────────────────────────────────── -->
<div class="container mt-4">

    <!-- STAT CARDS -->
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card stat-card">
                <p>Total Feedback</p>
                <h3><?= $total ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <p>Academic</p>
                <h3><?= $category_data[1] ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <p>Infrastructure</p>
                <h3><?= $category_data[2] ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <p>Bug Reports</p>
                <h3><?= $category_data[4] ?></h3>
            </div>
        </div>
    </div>

    <!-- CHART + RATINGS + TOP TEACHERS -->
    <div class="row mt-4 g-3">

        <!-- Chart -->
        <div class="col-md-6">
            <div class="card p-3 h-100">
                <div class="section-title">Feedback Overview</div>
                <canvas id="feedbackChart"></canvas>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="col-md-3">
            <div class="card p-3 h-100">
                <div class="section-title">Avg Rating</div>
                <ul>
                    <?php while ($row = $avg_query->fetch_assoc()): ?>
                    <li>
                        <?= htmlspecialchars($row['subject']) ?>
                        <span class="val"><?= round($row['avg_rating'], 1) ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- Top Teachers -->
        <div class="col-md-3">
            <div class="card p-3 h-100">
                <div class="section-title">Top Teachers</div>
                <ul>
                    <?php while ($row = $top_query->fetch_assoc()): ?>
                    <li>
                        <?= htmlspecialchars($row['teacher']) ?>
                        <span class="val"><?= round($row['avg_rating'], 1) ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

    </div>

    <!-- FEEDBACK TABLE -->
    <div class="card mt-4 p-3">
        <div class="section-title">All Feedback</div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Description</th>
                        <th>Ratings</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()):
                    [$label, $cls] = cat_info((int) $row['category_id']);
                ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>

                    <td>
                        <span class="cat-badge <?= $cls ?>"><?= $label ?></span>
                    </td>

                    <td><?= htmlspecialchars($row['subject_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['teacher_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['description']  ?? '—') ?></td>

                    <td>
                        <?php
                        $fid  = (int) $row['id'];
                        $stmt = $conn->prepare(
                            "SELECT question_number, rating FROM feedback_answers WHERE feedback_id = ?"
                        );
                        $stmt->bind_param("i", $fid);
                        $stmt->execute();
                        $ans = $stmt->get_result();

                        $ratings = [];
                        while ($a = $ans->fetch_assoc()) {
                            $ratings[] = "Q" . $a['question_number'] . ":" . $a['rating'];
                        }
                        echo $ratings
                            ? '<span class="rating-text">' . implode('&nbsp;&nbsp;', $ratings) . '</span>'
                            : '<span style="color:var(--text-3)">—</span>';
                        ?>
                    </td>

                    <td style="font-family:'DM Mono',monospace;font-size:12px;white-space:nowrap;">
                        <?= $row['created_at'] ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div><!-- /container -->

<!-- ── CHART SCRIPT ──────────────────────────────────────── -->
<script>
const ctx = document.getElementById('feedbackChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Academic', 'Infrastructure', 'Administrative', 'Bug'],
        datasets: [{
            label: 'Feedback Count',
            data: [
                <?= $category_data[1] ?>,
                <?= $category_data[2] ?>,
                <?= $category_data[3] ?>,
                <?= $category_data[4] ?>
            ],
            backgroundColor: [
                'rgba(123,140,255,0.75)',
                'rgba(255,179,71,0.75)',
                'rgba(92,255,194,0.75)',
                'rgba(255,92,123,0.75)'
            ],
            borderColor: [
                'rgba(123,140,255,1)',
                'rgba(255,179,71,1)',
                'rgba(92,255,194,1)',
                'rgba(255,92,123,1)'
            ],
            borderWidth: 1.5,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1a1e2a',
                borderColor: '#252a38',
                borderWidth: 1,
                titleColor: '#e8eaf2',
                bodyColor: '#8890a8',
                padding: 10,
            }
        },
        scales: {
            x: {
                ticks: { color: '#555d75', font: { family: 'DM Mono', size: 10 } },
                grid:  { color: '#252a38' }
            },
            y: {
                ticks: { color: '#555d75', font: { family: 'DM Mono', size: 10 } },
                grid:  { color: '#252a38' },
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>