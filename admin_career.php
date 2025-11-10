<?php
// Show errors while developing
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// --- Bootstrap (NO OUTPUT BEFORE THIS POINT) ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'db_connect.php';

// Require admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>Access denied</title></head><body style='font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; padding:2rem'><h2>Access denied</h2><p>You must be logged in as an admin to use this page.</p></body></html>";
    exit;
}

/* ---------- Ensure DB schema ---------- */
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

function redirect_self() {
    header('Location: admin_career.php');
    exit;
}

/* ---------- File upload helper ---------- */
function handle_image_upload($field){
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) return ['error' => 'Upload failed (code '.$_FILES[$field]['error'].')'];

    $tmp = $_FILES[$field]['tmp_name'];
    $size = filesize($tmp);
    if ($size > 3*1024*1024) return ['error' => 'Image too large (max 3MB)'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmp);
    finfo_close($finfo);

    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if (!isset($allowed[$mime])) return ['error' => 'Invalid image type. Allowed: JPG, PNG, WEBP'];

    $ext = $allowed[$mime];
    $safeName = 'job_' . bin2hex(random_bytes(8)) . '.' . $ext;

    $destDir = __DIR__ . '/images/jobs';
    if (!is_dir($destDir)) { mkdir($destDir, 0775, true); }
    $dest = $destDir . '/' . $safeName;

    if (!move_uploaded_file($tmp, $dest)) return ['error' => 'Failed to move uploaded file'];

    return ['path' => 'images/jobs/' . $safeName];
}

/* ---------- Actions ---------- */
$action = $_POST['action'] ?? '';

if ($action === 'add_job') {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $job_type = trim($_POST['job_type'] ?? '');
    $apply_url = trim($_POST['apply_url'] ?? '');
    $apply_email = trim($_POST['apply_email'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
$deadline = ($deadline === '') ? null : $deadline;

    $description = trim($_POST['description'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if ($title === '' || $description === '') { $_SESSION['message']='Title and Description are required.'; $_SESSION['message_type']='error'; redirect_self(); }
    if ($apply_email !== '' && !filter_var($apply_email, FILTER_VALIDATE_EMAIL)) { $_SESSION['message']='Invalid apply email format.'; $_SESSION['message_type']='error'; redirect_self(); }

    $img = handle_image_upload('image');
    if (is_array($img) && isset($img['error'])) { $_SESSION['message']=$img['error']; $_SESSION['message_type']='error'; redirect_self(); }
    $image_path = is_array($img) ? $img['path'] : null;

    $stmt = $conn->prepare("INSERT INTO jobs (title, location, job_type, apply_url, apply_email, deadline, description, is_published, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssis", $title, $location, $job_type, $apply_url, $apply_email, $deadline, $description, $is_published, $image_path);
    if($stmt->execute()){ $_SESSION['message']='Job created successfully.'; $_SESSION['message_type']='success'; } else { $_SESSION['message']='Error creating job: '.$stmt->error; $_SESSION['message_type']='error'; }
    redirect_self();
}

if ($action === 'delete_job') {
    $id = intval($_POST['id'] ?? 0);
    if ($id>0){
        $res = $conn->prepare("SELECT image_path FROM jobs WHERE id=?");
        $res->bind_param("i", $id);
        $res->execute();
        $row = $res->get_result()->fetch_assoc();
        if ($row && !empty($row['image_path'])) { $p = __DIR__ . '/' . $row['image_path']; if (is_file($p)) @unlink($p); }

        $stmt = $conn->prepare("DELETE FROM jobs WHERE id=?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){ $_SESSION['message']='Job deleted.'; $_SESSION['message_type']='success'; } else { $_SESSION['message']='Error deleting: '.$stmt->error; $_SESSION['message_type']='error'; }
    }
    redirect_self();
}

if ($action === 'toggle_publish') {
    $id = intval($_POST['id'] ?? 0);
    $new = intval($_POST['new'] ?? 0);
    $stmt = $conn->prepare("UPDATE jobs SET is_published=? WHERE id=?");
    $stmt->bind_param("ii", $new, $id);
    if($stmt->execute()){ $_SESSION['message']='Publish status updated.'; $_SESSION['message_type']='success'; } else { $_SESSION['message']='Error updating: '.$stmt->error; $_SESSION['message_type']='error'; }
    redirect_self();
}

if ($action === 'update_job') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $job_type = trim($_POST['job_type'] ?? '');
    $apply_url = trim($_POST['apply_url'] ?? '');
    $apply_email = trim($_POST['apply_email'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    $image_sql = '';
    $img = handle_image_upload('image');
    if (is_array($img) && isset($img['error'])) { $_SESSION['message']=$img['error']; $_SESSION['message_type']='error'; redirect_self(); }
    if (is_array($img)) {
        // delete old image
        $res = $conn->prepare("SELECT image_path FROM jobs WHERE id=?");
        $res->bind_param("i", $id);
        $res->execute();
        $row = $res->get_result()->fetch_assoc();
        if ($row && !empty($row['image_path'])) { $p = __DIR__ . '/' . $row['image_path']; if (is_file($p)) @unlink($p); }
        $image_path = $img['path'];
        $image_sql = ', image_path = ?';
    }

    if ($image_sql) {
        $stmt = $conn->prepare("UPDATE jobs SET title=?, location=?, job_type=?, apply_url=?, apply_email=?, deadline=?, description=?, is_published=? {$image_sql} WHERE id=?");
        $stmt->bind_param("sssssssisi", $title, $location, $job_type, $apply_url, $apply_email, $deadline, $description, $is_published, $image_path, $id);
    } else {
        $stmt = $conn->prepare("UPDATE jobs SET title=?, location=?, job_type=?, apply_url=?, apply_email=?, deadline=?, description=?, is_published=? WHERE id=?");
        $stmt->bind_param("sssssssii", $title, $location, $job_type, $apply_url, $apply_email, $deadline, $description, $is_published, $id);
    }

    if($stmt->execute()){ $_SESSION['message']='Job updated.'; $_SESSION['message_type']='success'; } else { $_SESSION['message']='Error updating: '.$stmt->error; $_SESSION['message_type']='error'; }
    redirect_self();
}

/* ---------- Fetch for display ---------- */
$res = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC, id DESC");
$jobs = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

// Flash message
$msg = $_SESSION['message'] ?? '';
$msg_type = $_SESSION['message_type'] ?? 'success';
unset($_SESSION['message'], $_SESSION['message_type']);

// Output page
include 'header.php';
?>
<style>
:root{--bg:#f8fafc;--card:#ffffff;--text:#0f172a;--muted:#64748b;--line:#e2e8f0;--brand:#0f766e;--brand2:#111827;}
.admin-wrap{max-width:1100px;margin:2rem auto;padding:1rem}
h1{font-size:1.75rem;margin:.2rem 0 1rem;color:var(--text)}
.card{background:var(--card);border:1px solid var(--line);border-radius:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);overflow:hidden}
.card h3{margin:0;padding:1rem 1.2rem;border-bottom:1px solid var(--line);background:#fafafa}
.card .body{padding:1.2rem}
.grid{display:grid;grid-template-columns:1fr;gap:1rem}
label{display:block;font-weight:600;margin:.5rem 0 .25rem;color:var(--text)}
.input, textarea, select{width:100%;padding:.75rem;border:1px solid var(--line);border-radius:10px;background:white}
textarea{min-height:140px;line-height:1.6}
.row{display:grid;grid-template-columns:1fr 1fr;gap:.8rem}
.actions{display:flex;gap:.6rem;flex-wrap:wrap;margin-top:.8rem}
.btn{padding:.6rem 1rem;border-radius:10px;border:1px solid transparent;cursor:pointer;font-weight:600}
.btn.primary{background:var(--brand2);color:#fff}
.btn.warn{background:#f59e0b;color:#111}
.btn.ghost{background:#fff;border-color:var(--line);color:var(--text)}
.msg{padding:.8rem 1rem;border-radius:10px;margin:1rem 0 0}
.msg.success{background:#ecfdf5;border:1px solid #10b981;color:#065f46}
.msg.error{background:#fef2f2;border:1px solid #ef4444;color:#7f1d1d}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:.75rem;border-bottom:1px solid var(--line);text-align:left;vertical-align:top;font-size:.95rem}
.badge{padding:.2rem .55rem;border-radius:999px;border:1px solid var(--line);font-size:.8rem;background:#fff}
.thumb{width:90px;height:60px;object-fit:cover;border:1px solid var(--line);border-radius:8px;background:#f1f5f9}
.muted{color:var(--muted);font-size:.85rem}
@media (max-width: 820px){
 .row{grid-template-columns:1fr}
 .table .actions form, .table details { display:block; margin:.25rem 0; }
}
</style>

<div class="admin-wrap">
    <h1>Careers — Admin</h1>
    <?php if($msg): ?><div class="msg <?= htmlspecialchars($msg_type) ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="grid">
        <div class="card">
            <h3>Add New Job</h3>
            <div class="body">
                <form method="post" action="admin_career.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_job">
                    <label>Title*<input class="input" type="text" name="title" required></label>
                    <div class="row">
                        <div><label>Location<input class="input" type="text" name="location" placeholder="e.g., Visakhapatnam / Remote"></label></div>
                        <div><label>Job Type<select class="input" name="job_type">
                            <option value="">— Select —</option>
                            <option>Full-time</option><option>Part-time</option><option>Contract</option><option>Internship</option><option>Volunteer</option>
                        </select></label></div>
                    </div>
                    <div class="row">
                        <div><label>Apply URL<input class="input" type="url" name="apply_url" placeholder="https://..."></label></div>
                        <div><label>Apply Email<input class="input" type="text" name="apply_email" placeholder="jobs@example.com"></label></div>
                    </div>
                    <div class="row">
                        <div><label>Deadline<input class="input" type="date" name="deadline"></label></div>
                        <div><label>Poster / Image<input class="input" type="file" name="image" accept="image/*"></label></div>
                    </div>
                    <label>Description*<textarea class="input" name="description" required placeholder="Role summary, responsibilities, and requirements..."></textarea></label>
                    <label><input type="checkbox" name="is_published" checked> Published</label>
                    <div class="actions">
                        <button class="btn primary" type="submit">Save Job</button>
                        <a class="btn ghost" href="career.php" target="_blank">View public page</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <h3>All Jobs</h3>
            <div class="body">
                <table class="table">
                    <thead><tr><th>Image</th><th>Title</th><th>Meta</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach($jobs as $job): ?>
                        <tr>
                            <td><?php if(!empty($job['image_path'])): ?><img class="thumb" src="<?= htmlspecialchars($job['image_path']) ?>" alt="thumb"><?php else: ?><div class="thumb" style="display:flex;align-items:center;justify-content:center">—</div><?php endif; ?></td>
                            <td><strong><?= htmlspecialchars($job['title']) ?></strong><div class="muted">#<?= $job['id'] ?> · <?= htmlspecialchars(date('M d, Y', strtotime($job['created_at'] ?? 'now'))) ?></div></td>
                            <td>
                                <div class="muted"><?= htmlspecialchars($job['job_type'] ?? '') ?><?= ($job['job_type'] && $job['location']) ? ' · ' : '' ?><?= htmlspecialchars($job['location'] ?? '') ?></div>
                                <?php if($job['deadline']): ?><div class="muted">Deadline <?= htmlspecialchars(date('M d, Y', strtotime($job['deadline']))) ?></div><?php endif; ?>
                            </td>
                            <td><span class="badge"><?= $job['is_published'] ? 'Published' : 'Draft' ?></span></td>
                            <td class="actions">
                                <form method="post" action="admin_career.php" style="display:inline">
                                    <input type="hidden" name="action" value="toggle_publish">
                                    <input type="hidden" name="id" value="<?= $job['id'] ?>">
                                    <input type="hidden" name="new" value="<?= $job['is_published']?0:1 ?>">
                                    <button class="btn ghost" type="submit"><?= $job['is_published']?'Unpublish':'Publish' ?></button>
                                </form>
                                <details>
                                    <summary class="btn ghost">Edit</summary>
                                    <form method="post" action="admin_career.php" enctype="multipart/form-data" style="margin-top:.6rem">
                                        <input type="hidden" name="action" value="update_job">
                                        <input type="hidden" name="id" value="<?= $job['id'] ?>">
                                        <label>Title<input class="input" type="text" name="title" value="<?= htmlspecialchars($job['title']) ?>"></label>
                                        <label>Location<input class="input" type="text" name="location" value="<?= htmlspecialchars($job['location']) ?>"></label>
                                        <label>Job Type<input class="input" type="text" name="job_type" value="<?= htmlspecialchars($job['job_type']) ?>"></label>
                                        <label>Apply URL<input class="input" type="url" name="apply_url" value="<?= htmlspecialchars($job['apply_url']) ?>"></label>
                                        <label>Apply Email<input class="input" type="text" name="apply_email" value="<?= htmlspecialchars($job['apply_email']) ?>"></label>
                                        <label>Deadline<input class="input" type="date" name="deadline" value="<?= htmlspecialchars($job['deadline']) ?>"></label>
                                        <label>Description<textarea class="input" name="description"><?= htmlspecialchars($job['description']) ?></textarea></label>
                                        <div class="row">
                                            <div><?php if(!empty($job['image_path'])): ?><img class="thumb" src="<?= htmlspecialchars($job['image_path']) ?>" alt="thumb"><?php endif; ?></div>
                                            <div><label>Replace Image<input class="input" type="file" name="image" accept="image/*"></label></div>
                                        </div>
                                        <label><input type="checkbox" name="is_published" <?= $job['is_published']?'checked':'' ?>> Published</label>
                                        <div class="actions"><button class="btn primary" type="submit">Update</button></div>
                                    </form>
                                </details>
                                <form method="post" action="admin_career.php" onsubmit="return confirm('Delete this job?')" style="display:inline">
                                    <input type="hidden" name="action" value="delete_job">
                                    <input type="hidden" name="id" value="<?= $job['id'] ?>">
                                    <button class="btn warn" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
