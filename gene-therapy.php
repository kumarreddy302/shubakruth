<?php include 'header.php'; ?>

<main>
    <!-- HERO -->
    <section class="page-hero">
        <div class="container">
            <h1 class="page-title">Gene Therapy</h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> <i class="fas fa-chevron-right"></i>
                <a href="precision-medicine.php">Precision Personalized Medicine</a> <i class="fas fa-chevron-right"></i>
                <span>Gene Therapy</span>
            </nav>
        </div>
    </section>

    <!-- INTRO: TEXT LEFT • IMAGE RIGHT -->
    <section class="page-content-section">
        <div class="container content-wrapper">
            <div class="content-main">
                <p>
                    Human gene therapy aims to modify or manipulate gene expression or the biological properties of
                    living cells for therapeutic purposes. It’s a treatment approach that works by altering a person’s
                    genes to address the underlying cause of disease.
                </p>
                <p>
                    Gene therapies can work in several ways, including using a healthy copy of a gene to replace a
                    disease-causing gene, inactivating a faulty gene, or introducing a new or modified gene to help
                    treat a condition. Gene therapy products are being studied (and, in some cases, used) for cancer,
                    genetic diseases, and infectious diseases.
                </p>

                <h3 style="margin-top: 30px;">How Gene Therapy Works</h3>
                <p>
                    Most strategies deliver genetic material to target cells either directly in the body (in&nbsp;vivo)
                    or to cells outside the body followed by re-infusion (ex&nbsp;vivo). The choice of vector, target
                    tissue, and editing approach depends on the disease biology and safety considerations.
                </p>
            </div>

            <aside class="content-sidebar">
                <div class="sidebar-image">
                    <!-- Replace with your actual image file -->
                    <img src="images/gene-therapy.jpg" alt="Illustration of gene therapy and vectors">
                </div>
            </aside>
        </div>
    </section>

    <!-- COMPONENTS / KEY AREAS -->
    <section class="page-content-section" style="background-color: var(--bg-light); border-top: 1px solid var(--border-color);">
        <div class="container">
            <h2 class="section-title">Approaches & Delivery</h2>

            <div class="component-grid">
                <div class="sidebar-widget">
                    <h4>Therapeutic Approaches</h4>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> <strong>Gene Addition / Replacement</strong> — supply a functional gene copy</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Gene Inactivation</strong> — silence a harmful gene</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Gene Editing</strong> — CRISPR/Cas, base/prime editing to correct variants</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Gene Regulation</strong> — RNAi / antisense oligos to modulate expression</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Cell-Based Therapy</strong> — engineer cells ex&nbsp;vivo (e.g., CAR-T)</li>
                    </ul>
                </div>

                <div class="sidebar-widget">
                    <h4>Delivery Platforms</h4>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> <strong>Viral Vectors</strong> — AAV, lentiviral, adenoviral</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Non-Viral</strong> — lipid nanoparticles, polymers, electroporation</li>
                        <li><i class="fas fa-check-circle"></i> <strong>In&nbsp;vivo</strong> vs <strong>Ex&nbsp;vivo</strong> delivery strategies</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Tissue Targeting</strong> — liver, eye, muscle, hematopoietic cells</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Safety</strong> — dosing, immunogenicity, off-target minimization</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- LIST SECTION -->
    <section class="key-areas-section">
        <div class="container">
            <h2 class="section-title">Clinical Areas & Example Indications</h2>
            <ul class="disease-list">
                <li>Cancer (hematologic &amp; solid tumors)</li>
                <li>Inherited Retinal Dystrophies</li>
                <li>Spinal Muscular Atrophy (SMA)</li>
                <li>Hemophilia A / B</li>
                <li>β-Thalassemia and Sickle Cell Disease</li>
                <li>Cystic Fibrosis</li>
                <li>Duchenne Muscular Dystrophy</li>
                <li>Metabolic / Urea-cycle Disorders</li>
                <li>Pompe Disease</li>
                <li>HIV and Other Infectious Diseases</li>
            </ul>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
