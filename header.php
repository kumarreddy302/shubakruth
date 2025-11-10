<?php
    // Start session on all pages
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Contact details
    $phoneNumber = "+91 89783 28127";
    $emailAddress = "info@shubhakruthmedicalgenetics.com";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Shubhakruth Medical Genetics</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body>

<div class="site-header-sticky-wrapper">
    <header class="top-ribbon">
        <div class="container">
            <a href="mailto:<?php echo htmlspecialchars($emailAddress); ?>">
                <i class="fas fa-envelope"></i>
                <span><?php echo htmlspecialchars($emailAddress); ?></span>
            </a>
            <a href="tel:<?php echo htmlspecialchars($phoneNumber); ?>">
                <i class="fas fa-phone"></i>
                <span><?php echo htmlspecialchars($phoneNumber); ?></span>
            </a>
        </div>
    </header>

    <header class="main-header">
        <div class="container">
            <a href="index.php" class="logo-link">
                <img src="images/Logo image.png" alt="Shubhakruth Medical Genetics Logo" class="logo">
            </a>
            
            <div class="header-icons">
                
                <form action="search.php" method="GET" class="header-search-form">
                    <input type="search" name="query" placeholder="Search services..." required>
                    <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
                </form>
                
                <a href="services.php" class="btn-header-book">Book Service</a>
                
                <a href="cart.php" aria-label="Shopping Cart" class="cart-icon icon-link">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                        $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                        if ($cart_count > 0) {
                            echo '<span class="cart-counter">' . $cart_count . '</span>';
                        }
                    ?>
                </a>
                
                <a href="login.php" aria-label="User Account" class="icon-link">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </header>

    <nav class="main-nav">
        <div class="container">
            <button class="mobile-nav-toggle" aria-controls="primary-navigation" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
                <span class="sr-only">Menu</span>
            </button>
            <ul id="primary-navigation" class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#vision-mission-section">About Us</a></li>
                <li class="has-dropdown">
                    <a href="precision-medicine.php">Precision Personalized Medicine</a>
                    <ul class="dropdown">
                        <li><a href="integumentary-system.php">Integumentary system Genes</a></li>
                        <li><a href="skeletal-system.php">Skeletal system Genes</a></li>
                        <li><a href="muscular-system.php">Muscular system Genes</a></li>
                        <li><a href="lymphatic-system.php">Lymphatic system Genes</a></li>
                        <li><a href="respiratory-system.php">Respiratory system Genes</a></li>
                        <li><a href="digestive-system.php">Digestive system Genes</a></li>
                        <li><a href="nervous-system.php">Nervous system Genes</a></li>
                        <li><a href="endocrine-system.php">Endocrine system Genes</a></li>
                        <li><a href="cardiovascular-system.php">Cardiovascular system Genes</a></li>
                        <li><a href="urinary-system.php">Urinary system Genes</a></li>
                        <li><a href="reproductive-system.php">Reproductive system Genes</a></li>
                        <li><a href="environmental-system.php">Environmental System Genes</a></li>
                    </ul>
                </li>
                <li class="has-dropdown">
                    <a href="genetic-disorders.php">Genetic Disorders</a>
                    <ul class="dropdown">
                        <li><a href="chromosome-disorders.php">Chromosome disorders</a></li>
                        <li><a href="single-gene-disorders.php">Single-gene disorders</a></li>
                        <li><a href="multifactorial-disorders.php">Multifactorial disorders</a></li>
                        <li><a href="mitochondrial-disorders.php">Mitochondrial disorders</a></li>
                        <li><a href="rare-genetic-disorders.php">Rare genetic disorders</a></li>
                    </ul>
                </li>
                <li class="has-dropdown">
                    <a href="healthcare-services.php">Healthcare Services</a>
                    <ul class="dropdown">
                        <li><a href="sequencing.php">Sequencing</a></li>
                        <li><a href="preventive-care.php">Predict Preventive Care</a></li>
                        <li><a href="single-gene-disorders-service.php">Single Gene Disorders</a></li>
                        <li><a href="healthcare-counselling.php">Healthcare Counselling</a></li>
                        <li><a href="preconception.php">Survive Reproduce Preconception</a></li>
                        <li><a href="prenatal.php">Pregnancy Prenatal</a></li>
                        <li><a href="paediatrics.php">Paediatrics</a></li>
                        <li><a href="oncology.php">Oncology</a></li>
                    </ul>
                </li>
                <li class="has-dropdown">
                    <a href="medical-research.php">Medical Research</a>
                    <ul class="dropdown">
                        <li class="has-dropdown">
                            <a href="light-and-life.php">Light and Life</a>
                            <ul class="dropdown">
                                <li><a href="omnipotence.php">Omnipotence</a></li>
                             </ul>
                        </li>
                        <li><a href="medical-genetics.php">Medical Genetics</a></li>
                        <li><a href="regenerative-medicine.php">Regenerative Medicine</a></li>
                        <li><a href="molecular-medicine.php">Molecular Medicine</a></li>
                        <li><a href="developmental-genetics.php">Developmental Genetics</a></li>
                        <li><a href="gene-therapy.php">Gene Therapy</a></li>
                        <li><a href="stem-cell-therapy.php">Stem Cell Therapy</a></li>
                        <li><a href="drug-molecular-therapy.php">Drug Molecular Targeted Therapy</a></li>
                    </ul>
                </li>
                
                
                
                
                <li><a href="blog.php">News</a></li>

                <li class="has-dropdown">
                    <a href="medical-health-education.php">Apply Now</a>
                    <ul class="dropdown">
                        <li class="has-dropdown">
                             <a href="career.php">Carrer</a>
                            <a href="medical-health-education.php">Medical Health Education</a>
                                <ul class="dropdown">
                                    <li><a href="education-workshops.php">WORKSHOP</a></li>
                                        <li class="has-dropdown"> <a href="internships.php">INTERNSHIP &raquo;</a>
                                            <ul class="dropdown-submenu">
                                                <li><a href="summer-internship.php">SUMMER INTERNSHIP</a></li>
                                                <li><a href="workshop-1-month.php">1 MONTH</a></li>
                                                <li><a href="workshop-2-month.php">2 MONTH</a></li>
                                                <li><a href="workshop-1-year.php">1 YEAR</a></li>
                                            </ul>
                                        </li>
                                    </li>    
                                </ul>
                        </li>
                    </ul>    
                </li>    

                <li><a href="contactus.php">Contact Us</a></li>

            </ul>
        </div>
    </nav>
</div>