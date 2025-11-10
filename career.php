<?php
include 'header.php';
include 'db_connect.php';

/* ---------- Ensure DB schema (safe if already exists) ---------- */
$conn->query("CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) DEFAULT NULL,
    job_type VARCHAR(100) DEFAULT NULL,
    apply_url VARCHAR(500) DEFAULT NULL,
    apply_email VARCHAR(255) DEFAULT NULL,
    deadline DATE DEFAULT NULL,
    description TEXT,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$col = $conn->query("SHOW COLUMNS FROM jobs LIKE 'image_path'");
if ($col && $col->num_rows === 0) {
    $conn->query("ALTER TABLE jobs ADD COLUMN image_path VARCHAR(500) DEFAULT NULL");
}

/* ---------- Filters ---------- */
$search = trim($_GET['q'] ?? '');
$type   = trim($_GET['type'] ?? '');
$loc    = trim($_GET['loc'] ?? '');

/* ---------- Query (prepared) ---------- */
$sql = "SELECT * FROM jobs WHERE is_published = 1";
$params = [];
$types  = "";

if ($search !== '') {
    $sql .= " AND (title LIKE CONCAT('%', ?, '%') OR description LIKE CONCAT('%', ?, '%'))";
    $params[] = $search; $params[] = $search; $types .= "ss";
}
if ($type !== '') {
    $sql .= " AND job_type = ?";
    $params[] = $type; $types .= "s";
}
if ($loc !== '') {
    $sql .= " AND location LIKE CONCAT('%', ?, '%')";
    $params[] = $loc; $types .= "s";
}
$sql .= " ORDER BY created_at DESC, id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res  = $stmt->get_result();
$jobs = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>

<style>
:root{
  --line:#e5e7eb; --muted:#64748b; --ink:#0f172a; --btn:#111827; --btn2:#2563eb;
}
.wrap{max-width:1100px;margin:2rem auto;padding:1rem}
h1{margin:0 0 .75rem;color:var(--ink)}
.filter{display:grid;grid-template-columns:1.2fr 1fr 1fr auto;gap:.6rem;margin:0 0 1rem}
.input, select{width:100%;padding:.7rem;border:1px solid var(--line);border-radius:10px}
.btn{display:inline-block;padding:.6rem 1rem;border-radius:10px;background:var(--btn2);color:#fff;border:none;text-decoration:none;cursor:pointer}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 1px 2px rgba(0,0,0,.04);padding:1rem 1.2rem}
.thumb-wrap{width:100%;border:1px solid var(--line);border-radius:10px;overflow:hidden;background:#f8fafc;padding:.5rem;text-align:center}
.thumb{max-width:100%;height:auto;object-fit:contain;border-radius:8px}
.badge{padding:.2rem .55rem;border-radius:999px;border:1px solid var(--line);font-size:.8rem}
.meta{display:flex;gap:.6rem;flex-wrap:wrap;color:#374151}
.desc{margin-top:.6rem;white-space:pre-line;line-height:1.6}
.apply{display:inline-block;margin-top:.7rem;padding:.55rem .9rem;border-radius:10px;background:var(--btn);color:#fff;text-decoration:none}
.empty{padding:2rem;text-align:center;border:1px dashed var(--line);border-radius:12px}
@media (max-width: 900px){
  .grid{grid-template-columns:1fr}
  .filter{grid-template-columns:1fr}
}
</style>


<div class="wrap">
  <h1>Careers</h1>

  <form class="filter" method="get" action="">
    <input class="input" type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by title or keyword...">
    <select class="input" name="type">
      <option value="">All types</option>
      <?php
        $typesList = ['Full-time','Part-time','Contract','Internship','Volunteer'];
        foreach($typesList as $t){
          $sel = ($type === $t) ? 'selected' : '';
          echo "<option $sel>".htmlspecialchars($t)."</option>";
        }
      ?>
    </select>
    <input class="input" type="text" name="loc" value="<?= htmlspecialchars($loc) ?>" placeholder="Location">
    <button class="btn" type="submit">Filter</button>
  </form>

  <?php if (count($jobs) === 0): ?>
    <div class="empty">No openings found right now. Please check back soon.</div>
  <?php endif; ?>

  <div class="grid">
    <?php foreach($jobs as $job): ?>
      <div class="card">
        <?php if(!empty($job['image_path'])): ?>
          <img class="thumb" src="<?= htmlspecialchars($job['image_path']) ?>" alt="Job image">
        <?php endif; ?>

        <h2 style="margin:.2rem 0"><?= htmlspecialchars($job['title']) ?></h2>

        <div class="meta">
          <?php if(!empty($job['job_type'])): ?><span class="badge"><?= htmlspecialchars($job['job_type']) ?></span><?php endif; ?>
          <?php if(!empty($job['location'])): ?><span>📍 <?= htmlspecialchars($job['location']) ?></span><?php endif; ?>
          <?php if(!empty($job['created_at']) && strtotime($job['created_at'])): ?>
            <span>Posted <?= htmlspecialchars(date('M d, Y', strtotime($job['created_at']))) ?></span>
          <?php endif; ?>
          <?php if(!empty($job['deadline']) && $job['deadline'] !== '0000-00-00'): ?>
            <span>Deadline <?= htmlspecialchars(date('M d, Y', strtotime($job['deadline']))) ?></span>
          <?php endif; ?>
        </div>

        <div class="desc"><?= nl2br(htmlspecialchars($job['description'] ?? '')) ?></div>

        <?php if(!empty($job['apply_url'])): ?>
          <a class="apply" href="<?= htmlspecialchars($job['apply_url']) ?>" target="_blank" rel="noopener">Apply Now</a>
        <?php elseif(!empty($job['apply_email'])): ?>
          <a class="apply" href="mailto:<?= htmlspecialchars($job['apply_email']) ?>">Apply via Email</a>
        <?php else: ?>
          <a class="apply" href="contact.php">Contact Us to Apply</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
