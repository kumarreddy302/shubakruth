<?php
include 'header.php';
include 'db_connect.php';

$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($member_id <= 0) { die("Invalid team member ID."); }

$sql = "SELECT name, title, bio, image_path FROM team_members WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if (!$member) { die("Team member not found."); }
?>

<main>
    <section class="page-hero">
        <div class="container">
            <h1 class="page-title"><?php echo htmlspecialchars($member['name']); ?></h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> <i class="fas fa-chevron-right"></i>
                <span>Our Team</span> <i class="fas fa-chevron-right"></i>
                <span><?php echo htmlspecialchars($member['name']); ?></span>
            </nav>
        </div>
    </section>

    <section class="page-content-section">
        <div class="container member-detail-container">
            <div class="member-image-column">
                <img src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
            </div>
            <div class="member-info-column">
                <h2><?php echo htmlspecialchars($member['name']); ?></h2>
                <p class="member-title"><?php echo htmlspecialchars($member['title']); ?></p>
                <div class="member-bio">
                    <?php echo nl2br(htmlspecialchars($member['bio'])); ?>
                </div>
                <a href="index.php#team" class="btn-secondary" style="margin-top: 30px;"><i class="fas fa-arrow-left"></i> Back to Team</a>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>