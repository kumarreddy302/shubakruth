<div id="top-banner">
    <div class="top-banner-content">
        <p>Is it your birthday today? We are offering a Special Discount on your Birthday. To Know more Get in Touch with Us. Conditions Apply</p>
    </div>
    <button id="close-banner-btn" aria-label="Close banner">&times;</button>
</div>
<?php
    // Include the header file
    // Note: session_start() should be called in header.php if you are using sessions.
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    include 'header.php';
                
            // Example: $conn = new mysqli($host, $user, $pass, $db);
            // Make sure you have columns: id (AI PK), name, mobile, email in contact_message

            if (isset($_POST['popup_ajax'])) {
                header('Content-Type: application/json');

                // Basic validation
                $name   = trim($_POST['name'] ?? '');
                $mobile = trim($_POST['mobile'] ?? '');
                $email  = trim($_POST['email'] ?? '');

                if ($name === '' || $mobile === '' || $email === '') {
                    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
                    exit;
                }

                // Insert (prepared statement)
                $stmt = $conn->prepare("INSERT INTO contact_messages (name, mobile, email) VALUES (?, ?, ?)");
                if (!$stmt) {
                    echo json_encode(['success' => false, 'message' => 'DB error.']);
                    exit;
                }
                $stmt->bind_param('sss', $name, $mobile, $email);
                $ok = $stmt->execute();
                $stmt->close();

                echo json_encode(['success' => $ok, 'message' => $ok ? 'Saved' : 'Failed to save']);
                exit;
            }


    // Subheadings for the title bar animation
    $subheadings = [
        "Gene Health", "Gene Match", "Baby Gene", "DNA RNA Protein Sequencing",
        "Medical Genetics", "Omics Studies", "Ancestry", "Nutrition",
        "Genetic Counseling", "Developmental Genetics", "Infectious Disease Genetics",
        "Regenerative Medicine", "Drug Response", "Molecular Medicine", "Gene Therapy",
        "Stem Cell Therapy", "Drug Molecular Targeted Therapy", "Integumentary System Genes",
        "Skeletal System Genes", "Muscular System Genes", "Lymphatic System Genes",
        "Respiratory System Genes", "Digestive System Genes", "Nervous System Genes",
        "Endocrine System Genes", "Cardiovascular System Genes", "Urinary System Genes",
        "Reproductive System Genes", "Enivornment System Gene"
    ];
?>


    
<section class="title-bar">
    <h1>
        <span class="blue-text">Welcome to</span>
        Shubhakruth Medical Genetics
    </h1>
    
    <div class="animated-subheadings">
        <?php // "We provide" text is now inside the same container as the animation ?>
        <span class="light-blue-text">We provide&nbsp;</span> 
        <span id="changing-text"></span><span class="cursor"></span>
    </div>
    
    <div id="subheading-data" style="display:none;"><?php echo json_encode($subheadings); ?></div>
</section>

<div class="scrolling-text-container">
    <p class="scrolling-text">Gene Health: We strongly recommend adopting a Vegetarian Diet or Pescatarian Diet. Practice 24-hour fasting every two weeks.</p>
</div>

<main class="content-area">

    <section class="intro-section">
        <div class="container">
            <div class="intro-grid">
                <div class="intro-content">
                    <h2><span class='orange-bg'>We are addressing human health issues by utilising DNA's ability to restore normal health</span></h2>
                    <p><strong>Gene health</strong> is the foundation of our overall well-being, influencing everything from how our bodies function to how we respond to environmental factors. <strong>Our genes</strong>, inherited from our parents, contain the instructions that guide <strong>our growth, development, and everyday bodily functions</strong>.</p>
                    <p><strong>We are</strong> at the forefront of revolutionizing healthcare through our focus on Precision Personalized Medicine and Regenerative Medicine. Established with a vision to <strong>redefine medical diagnostics, prognostics and treatment strategies</strong>, Shubhakruth Medical Genetics is committed to delivering cutting-edge healthcare services <strong>tailored to individual genetic profiles.</strong></p>
                    <p>The <strong>Human Genome Project (HGP)</strong>, completed in 2003, marked one of the most transformative milestones in biomedical science. It provided the first complete map of all human genes <strong>about 20,000 to 25,000 genes </strong>and their locations on the <strong>23 pairs of chromosomes</strong>. This global effort unlocked the “blueprint” of human biology.</p>
                    <p id="vision-mission-section">We offer a comprehensive range of genetic services, including <strong>Gene Health, Gene Match, and Baby Gene.</strong></p>
                </div>
                <div class="intro-image">
                    <img src="images/baby-image.jpg" alt="A child representing genetic health">
                </div>
            </div>
        </div>
    </section>

    <section class="vision-mission-section" >
        <div class="container" >
            <div class="vm-grid">
                <div class="vm-card">
                    <h3>Our Vision</h3>
                    <p>A world where diseases are not just treated — they are prevented, corrected, or even reversed through the precision of genetic science and the promise of regeneration.</p>
                </div>
                <div class="vm-card">
                    <h3>Our Mission</h3>
                    <p>To transform healthcare through innovation in genetics and regenerative technologies, bringing personalized care closer to every individual.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="genetics-scroller">
      <div class="container">
        <h2 class="section-title">Genetics in Pictures</h2>
    
        <div class="scroll-viewport" data-speed="28s">
          <div class="scroll-track">
            <?php // REMOVED manually duplicated images.
                  // The comment below implies JavaScript handles the duplication for the loop.
                  // Keeping only the unique images here is more efficient. 
            ?>
            <img src="images/WhatsApp Image 2025-11-04 at 16.59.47.jpeg" alt="Human life stages illustration">
            <img src="images/WhatsApp Image 2025-11-04 at 16.59.48.jpeg" alt="Fertilization and XY/XX diagram">
            <img src="images/WhatsApp Image 2025-11-04 at 16.59.48 (1).jpeg" alt="Stem cells differentiating to body tissues">
            <img src="images/WhatsApp Image 2025-11-04 at 16.59.48 (2).jpeg" alt="Sex determination from parents to children">
            <img src="images/WhatsApp Image 2025-11-04 at 16.59.49.jpeg" alt="DNA, genes and chromosomes diagram">
            <img src="images/WhatsApp Image 2025-11-04 at 16.59.49 (1).jpeg" alt="Human chromosomes overview">
        </div>
          </div>
      </div>
    </section>


    <section class="features-section">
        <div class="container">
            <h2>Know Yourself. Transform Your Health.</h2>
            <p class="features-intro">
                At Shubhakruth, we help you understand yourself better through reliable, DNA-based insights for <strong>comprehensive health and wellness</strong>. Whether you're curious about your gut health, fitness, heart health, diabetes risk, beauty, ancestry, or how your body responds to medications, our at-home DNA tests are designed to provide personalized insights tailored just for you. All samples are analyzed in secure, <strong>CAP and NABL-accredited labs</strong>, and each report includes a session with a certified genetic counselor.
            </p>
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-microscope"></i>
                    <h4>24+ Years of Genomics Experience</h4>
                </div>
                <div class="feature-item">
                    <i class="fas fa-dna"></i>
                    <h4>Latest Sequencing Technology</h4>
                </div>
                <div class="feature-item">
                    <i class="fas fa-award"></i>
                    <h4>Accredited Laboratory</h4>
                </div>
                <div class="feature-item">
                    <i class="fas fa-users"></i>
                    <h4>Genetic Counseling</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="scroll-gallery">
      <div class="scroll-track">
        <?php
          $folder = "assets/slider/"; // Folder containing your images
          $images = glob($folder . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
          
          if ($images) {
            // Print all images once.
            // Duplication should be handled by the same JavaScript as the other scroller for consistency.
            foreach ($images as $img_path) {
              $filename = basename($img_path);
              $src_path = $folder . rawurlencode($filename); // Correctly encode for URL
              $alt_text = "Gallery image: " . htmlspecialchars($filename); // Improved, more accessible alt text
              
              echo '<img src="' . $src_path . '" alt="' . $alt_text . '">';
            }
          } else {
            echo "<p style='text-align:center;color:#999;'>No images found in assets/slider/</p>";
          }
        ?>
      </div>
    </div>

    <?php
    // FIX: Include database connection ONCE before all team sections
    include_once 'db_connect.php'; 
    ?>

    <section class="team-section" id="team-board"> <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title">Meet Our Board of Directors</h2>
                <p class="section-subtitle">The experts driving innovation in genetic science.</p>
            </div>
            <div class="team-grid">
                <?php
                // FIX: Added 'WHERE' clause to the SQL query
                $sql_board = "SELECT id, name, title, image_path FROM team_members WHERE category='Board of Directors' ORDER BY name";
                $result_board = $conn->query($sql_board);
                
                if ($result_board && $result_board->num_rows > 0) {
                    while($member = $result_board->fetch_assoc()) {
                ?>
                <a href="team-member.php?id=<?php echo $member['id']; ?>" class="team-card">
                    <div class="team-card-image">
                        <img src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                    </div>
                    <div class="team-card-info">
                        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                        <p><?php echo htmlspecialchars($member['title']); ?></p>
                    </div>
                </a>
                <?php
                    }
                } else {
                    // echo "<p>Our Board of Directors is currently being updated.</p>";
                }
                ?>
            </div>
        </div>
    </section>


    <section class="team-section" id="team-advisory"> <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title">Meet Our Advisory Board</h2>
                <p class="section-subtitle">Guiding our scientific and strategic direction.</p>
            </div>
            <div class="team-grid">
                <?php
                // This query was already correct, just using a new variable for clarity
                $sql_advisory = "SELECT id, name, title, image_path FROM team_members WHERE category='Advisory' ORDER BY name";
                $result_advisory = $conn->query($sql_advisory);
                
                if ($result_advisory && $result_advisory->num_rows > 0) {
                    while($member = $result_advisory->fetch_assoc()) {
                ?>
                <a href="team-member.php?id=<?php echo $member['id']; ?>" class="team-card">
                    <div class="team-card-image">
                        <img src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                    </div>
                    <div class="team-card-info">
                        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                        <p><?php echo htmlspecialchars($member['title']); ?></p>
                    </div>
                </a>
                <?php
                    }
                } else {
                    // echo "<p>Our Advisory Board is currently being updated.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <section class="team-section" id="team-main"> <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title">Meet Our Team</h2>
                <p class="section-subtitle">The experts driving innovation in genetic science.</p>
            </div>
            <div class="team-grid">
                <?php
                // FIX: Added 'WHERE' clause to the SQL query
                $sql_team = "SELECT id, name, title, image_path FROM team_members WHERE category='Team' ORDER BY name";
                $result_team = $conn->query($sql_team);
                
                if ($result_team && $result_team->num_rows > 0) {
                    while($member = $result_team->fetch_assoc()) {
                ?>
                <a href="team-member.php?id=<?php echo $member['id']; ?>" class="team-card">
                    <div class="team-card-image">
                        <img src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                    </div>
                    <div class="team-card-info">
                        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                        <p><?php echo htmlspecialchars($member['title']); ?></p>
                    </div>
                </a>
                <?php
                    }
                } else {
                    // echo "<p>Our team is currently being updated.</p>";
                }
                // $conn->close(); // Connection should be closed in footer.php
                ?>
            </div>
        </div>
    </section>

    <section class="partners-section">
        <div class="container">
            <div class="partner-item">
                <h5>In Collaboration With</h5>
                <div class="logo-grid">
                    <img src="images/mmg-logo.avif" alt="Map My Genome" class="partner-logo-color">
                     <img src="images/ccdc.jpg" alt="CCDC" class="partner-logo-color">
                     <img src="images/CCDC_brandstrapline.png.webp" alt="CCDC" class="partner-logo-color">
                    <img src="images/cambridge.png" alt="University of Cambridge" class="partner-logo-color">
                </div>
            </div>
            
            <div class="partner-item">
                <h5 style="margin-top: 60px;">Incubated At</h5>
                <div class="logo-grid">
                    <img src="images/logo.jpg" alt="AMTZ" class="partner-logo-color">
                    <img src="images/b2.jpg" alt="Bio Valley" class="partner-logo-color">
                    <img src="images/images.png" alt="Vignan Incubator" class="partner-logo-color">
                     <img src="images/Logo with Deemed.svg" alt="Vignan University" class="partner-logo-color">
                </div>
            </div>
        </div>
    </section>


    <section class="contact-section" id="contact-form">
        <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title">Get In Touch</h2>
                <p class="section-subtitle">Have a question or need more information? We'd love to hear from you.</p>
            </div>
            
            <?php 
                // This message display logic is secure and correct.
                if (isset($_SESSION['message'])) {
                    $message_type = htmlspecialchars($_SESSION['message_type'] ?? 'info');
                    $message = htmlspecialchars($_SESSION['message']);
                    
                    echo '<p class="message ' . $message_type . '">' . $message . '</p>';
                    
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }
            ?>
            
            <div class="contact-form-container">
                <form action="process-contact.php" method="POST" class="contact-form">
                    <div class="form-grid">
                        <div class="form-group"><label for="contact-name">Full Name</label><input type="text" id="contact-name" name="name" required></div>
                        <div class="form-group"><label for="contact-email">Email Address</label><input type="email" id="contact-email" name="email" required></div>
                        <div class="form-group full-width"><label for="contact-subject">Subject</label><input type="text" id="contact-subject" name="subject" required></div>
                        <div class="form-group full-width"><label for="contact-message">Message</label><textarea id="contact-message" name="message" rows="6" required></textarea></div>
                    </div>
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </section>
</main>


<!-- IMAGE + FORM POPUP MODAL -->
<div id="image-popup-modal" class="modal-overlay">
  <div class="modal-content">
    <span class="modal-close-btn">&times;</span>

    <!-- ROW 1: text + image -->
    <div class="modal-top">
      <p class="popup-color">
        Gene Geometry of molecular atom consciousness and its various expressions.
        The biological activity of the human body can be ascertained by
        understanding the molecular atomic structure.
      </p>

      <img src="images/Franclin photo.jpg" alt="Popup Image" class="popup-image">
    </div>

    <!-- ROW 2: 3 fields in one row + submit + message -->
    <form id="popup-contact-form" class="modal-bottom">
      <div class="popup-form-row">
        <input type="text"   id="popup-name"   name="name"   placeholder="Name" required>
        <input type="tel"    id="popup-mobile" name="mobile" placeholder="Mobile Number" required pattern="[0-9]{10}" maxlength="10">
        <input type="email"  id="popup-email"  name="email"  placeholder="Email" required>
      </div>

      <button type="submit" class="btn-primary popup-submit">Submit</button>

      <!-- message appears here after submit -->
      <div id="popup-message" aria-live="polite"></div>
    </form>
  </div>
</div>




<?php include 'footer.php'; ?>