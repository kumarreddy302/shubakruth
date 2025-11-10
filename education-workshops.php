<?php include 'header.php'; ?>

<main>
    <!-- HERO -->
    <section class="page-hero">
        <div class="container">
            <h1 class="page-title">Medical Health Education & Workshops</h1>
            <p>Structured programs in genetics and medical health — designed for students, clinicians, and allied health professionals.</p>
        </div>
    </section>

    <!-- INTRO -->
    <section class="page-content-section">
        <div class="container">
            <h2 class="section-title">Choose Your Program</h2>
            <p class="features-intro">
                Learn fundamentals to advanced applications in **medical genetics**, **molecular diagnostics**, **bioinformatics basics**, 
                and **clinical workflows**. Hands-on activities, case discussions, and assessments are built in.
            </p>

            <!-- PLANS GRID (re-using services-grid/service-card styles) -->
            <div class="services-grid">
                <!-- 1-Month -->
                <a class="service-card" href="workshop-1-month.php">
                    <div class="service-card-image">
                        <img src="images/workshop-1m.jpg" alt="1-Month Workshop">
                    </div>
                    <div class="service-card-content">
                        <h3>1-Month Certificate Program</h3>
                        <p>Intensive foundation in medical genetics and lab orientation.</p>
                        <div class="service-card-details">
                            <span class="price">₹ 6,999</span>
                            <span class="tat">Duration: 4 Weeks</span>
                        </div>
                    </div>
                    <div class="service-card-actions">
                        <a class="btn-secondary" href="workshop-1-month.php">View Details</a>
                        <a class="btn-primary" href="checkout.php?plan=1m">Enroll Now</a>
                    </div>
                </a>

                <!-- 2-Month -->
                <a class="service-card" href="workshop-2-month.php">
                    <div class="service-card-image">
                        <img src="images/workshop-2m.jpg" alt="2-Month Workshop">
                    </div>
                    <div class="service-card-content">
                        <h3>2-Month Professional Workshop</h3>
                        <p>Deeper clinical focus, case reviews, and guided mini-project.</p>
                        <div class="service-card-details">
                            <span class="price">₹ 11,999</span>
                            <span class="tat">Duration: 8 Weeks</span>
                        </div>
                    </div>
                    <div class="service-card-actions">
                        <a class="btn-secondary" href="workshop-2-month.php">View Details</a>
                        <a class="btn-primary" href="checkout.php?plan=2m">Enroll Now</a>
                    </div>
                </a>

                <!-- 1-Year -->
                <a class="service-card" href="workshop-1-year.php">
                    <div class="service-card-image">
                        <img src="images/workshop-1y.jpg" alt="1-Year Program">
                    </div>
                    <div class="service-card-content">
                        <h3>1-Year Advanced Program</h3>
                        <p>Capstone projects, research immersion, and clinical rotation support.</p>
                        <div class="service-card-details">
                            <span class="price">₹ 49,999</span>
                            <span class="tat">Duration: 12 Months</span>
                        </div>
                    </div>
                    <div class="service-card-actions">
                        <a class="btn-secondary" href="workshop-1-year.php">View Details</a>
                        <a class="btn-primary" href="checkout.php?plan=1y">Enroll Now</a>
                    </div>
                </a>
            </div>

            <div style="margin-top:40px; text-align:center;">
                <a class="btn-primary" style="width:auto; max-width:280px;" href="contact.php">Talk to a Program Counselor</a>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
