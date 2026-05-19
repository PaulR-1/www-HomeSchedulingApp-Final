<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>HomePlanner - Welcome</title>
    <link rel="stylesheet" href="css/pages.css">
</head>
<body class="home-page">

<header class="home-header">
    <div class="home-header-inner">
        <a href="HomePage.php" class="home-brand">
            <img src="../images/logo.png" class="home-brand-logo" alt="HomePlanner">
            <span class="home-brand-name">HomePlanner</span>
        </a>
        <div class="home-nav" role="navigation" aria-label="Main navigation">
            <a href="HomePage.php" class="home-nav-link">Home</a>
            <a href="#about-us" class="home-nav-link">AboutUS</a>
            <a href="#features" class="home-nav-link">Features</a>
            <a href="RegistrationPage.php" class="home-nav-btn">Get Started</a>
            <a href="#contact-us" class="home-nav-link">ContactUs</a>
        </div>
    </div>
</header>

<main class="home-main">
    <section class="home-hero">
        <div class="home-hero-copy">
            <p class="home-eyebrow">Household management, simplified</p>
            <h1 class="home-title">Plan your home life in one calm, clear place.</h1>
            <p class="home-description">
                HomePlanner helps families organize tasks, schedules, and members across color-coded home dashboards — so everyone stays on the same page.
            </p>

            <div class="home-actions">
                <a href="RegistrationPage.php" class="btn home-btn-primary">
                    <i class="material-icons left">rocket_launch</i>
                    Get Started
                </a>
                <a href="LoginPage.php" class="btn home-btn-outline">
                    <i class="material-icons left">login</i>
                    Sign In
                </a>
            </div>

            <div class="home-trust-row">
                <span><i class="material-icons">verified</i> Free to register</span>
                <span><i class="material-icons">groups</i> Multi-home support</span>
                <span><i class="material-icons">shield</i> Secure sign-in</span>
            </div>
        </div>

        <div class="home-hero-visual">
            <div class="home-preview-side-grid">
                <div class="home-preview-card home-preview-side">
                    <i class="material-icons">event</i>
                    <p>Family calendar</p>
                </div>

                <div class="home-preview-card home-preview-side">
                    <i class="material-icons">home</i>
                    <p>Built for all kinds of homes</p>
                </div>

                <div class="home-preview-card home-preview-side">
                    <i class="material-icons">checklist</i>
                    <p>Shared task lists for everyone</p>
                </div>

                <div class="home-preview-card home-preview-side">
                    <i class="material-icons">notifications_active</i>
                    <p>Smart reminders and alerts</p>
                </div>
            </div>

            <div class="home-preview-card home-preview-main">
                <div class="home-preview-label">This week at a glance</div>
                <div class="home-preview-stat">
                    <span class="home-preview-value">12</span>
                    <span class="home-preview-caption">Tasks scheduled</span>
                </div>
                <div class="home-preview-bars">
                    <span style="height: 42%"></span>
                    <span style="height: 68%"></span>
                    <span style="height: 55%"></span>
                    <span style="height: 82%"></span>
                    <span style="height: 61%"></span>
                </div>
            </div>
        </div>
    </section>

    <section id="about-us" class="home-about-section">
        <div class="home-section-head">
            <h2>About Us</h2>
            <p>HomePlanner is designed for families who want a cleaner way to plan daily life.</p>
        </div>

        <div class="home-about-grid">
            <article class="home-about-card">
                <i class="material-icons">lightbulb</i>
                <h3>Our Mission</h3>
                <p>Make home coordination simple, visual, and easy to maintain for every family member.</p>
            </article>
            <article class="home-about-card">
                <i class="material-icons">groups</i>
                <h3>Who We Serve</h3>
                <p>Students, parents, and shared households who need one place for reminders and planning.</p>
            </article>
            <article class="home-about-card">
                <i class="material-icons">flag</i>
                <h3>Our Goal</h3>
                <p>Help homes stay organized with less stress and clearer visibility of tasks and schedules.</p>
            </article>
        </div>
    </section>

    <section id="features" class="home-features-section">
        <div class="home-section-head">
            <h2>Everything your household needs</h2>
            <p>Built for busy families who want structure without complexity.</p>
        </div>

        <div class="home-feature-grid">
            <article class="home-feature-card">
                <div class="home-feature-icon"><i class="material-icons">checklist</i></div>
                <h3>Shared to-do lists</h3>
                <p>Track chores and reminders so nothing slips through the cracks.</p>
            </article>
            <article class="home-feature-card">
                <div class="home-feature-icon"><i class="material-icons">calendar_month</i></div>
                <h3>Home calendar</h3>
                <p>See groceries, dinners, and family events in one timeline.</p>
            </article>
            <article class="home-feature-card">
                <div class="home-feature-icon"><i class="material-icons">pie_chart</i></div>
                <h3>Budget snapshots</h3>
                <p>Keep an eye on monthly spending and household priorities.</p>
            </article>
            <article class="home-feature-card">
                <div class="home-feature-icon"><i class="material-icons">dashboard</i></div>
                <h3>Color-coded homes</h3>
                <p>Each home gets its own dashboard theme and member roster.</p>
            </article>
            <article class="home-feature-card">
                <div class="home-feature-icon"><i class="material-icons">groups</i></div>
                <h3>Role-based access</h3>
                <p>Assign member and admin permissions to keep actions organized.</p>
            </article>
            <article class="home-feature-card">
                <div class="home-feature-icon"><i class="material-icons">notifications_active</i></div>
                <h3>Smart reminders</h3>
                <p>Get timely prompts for important tasks, events, and deadlines.</p>
            </article>
            <article class="home-feature-card">
                <div class="home-feature-icon"><i class="material-icons">insights</i></div>
                <h3>Progress insights</h3>
                <p>Track completion patterns and weekly progress at a glance.</p>
            </article>
            <article class="home-feature-card">
                <div class="home-feature-icon"><i class="material-icons">sync</i></div>
                <h3>Live sync updates</h3>
                <p>Keep household plans up to date for everyone in real time.</p>
            </article>
        </div>
    </section>

    <section class="home-cta-band">
        <div class="home-cta-inner">
            <div>
                <h2>Ready to organize your home?</h2>
                <p>Create an account in minutes, then sign in to open your dashboard.</p>
            </div>
            <div class="home-cta-actions">
                <a href="RegistrationPage.php" class="btn home-btn-primary">Create Account</a>
                <a href="LoginPage.php" class="home-btn-secondary">I already have an account</a>
            </div>
        </div>
    </section>

    <section id="contact-us" class="home-contact-section">
        <div class="home-contact-shell">
            <div class="home-contact-copy">
                <h2>Contact Us</h2>
                <p>Questions, feedback, or partnership inquiries? Reach out and our team will get back to you.</p>
            </div>
            <div class="home-contact-cards">
                <div class="home-contact-card">
                    <i class="material-icons">mail</i>
                    <span>support@homeplanner.local</span>
                </div>
                <div class="home-contact-card">
                    <i class="material-icons">call</i>
                    <span>+63 900 123 4567</span>
                </div>
                <div class="home-contact-card">
                    <i class="material-icons">location_on</i>
                    <span>Quezon City, Philippines</span>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="home-footer">
    <p>© 2026 HomePlanner</p>
    <p>Plan smarter. Live better.</p>
</footer>

</body>
</html>
