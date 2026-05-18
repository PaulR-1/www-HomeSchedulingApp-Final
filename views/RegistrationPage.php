<?php
    session_start();
    require_once "../bl/userManager.php";
    require_once "../bl/HomeManager.php";

    $usermanager = new UserManager();
    $advancedUsers = $usermanager->getAdvancedUser();
    $homeManager = new HomeManager();
    $homes = $homeManager->getHome();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>HomePlanner - Register</title>
    <link rel="stylesheet" href="css/pages.css">
</head>
<body class="auth-page page-register">

<header class="home-header">
    <div class="home-header-inner">
        <a href="HomePage.php" class="home-brand">
            <img src="../images/logo.png" class="home-brand-logo" alt="HomePlanner">
            <span class="home-brand-name">HomePlanner</span>
        </a>
        <div class="home-nav" role="navigation" aria-label="Main navigation">
            <a href="HomePage.php" class="home-nav-link">Home</a>
            <a href="HomePage.php#about-us" class="home-nav-link">AboutUS</a>
            <a href="HomePage.php#features" class="home-nav-link">Features</a>
            <a href="RegistrationPage.php" class="home-nav-btn">Get Started</a>
            <a href="HomePage.php#contact-us" class="home-nav-link">ContactUs</a>
        </div>
    </div>
</header>

<main>
    <div class="page-shell">
        <div class="page-grid">

            <div class="register-panel-wrap">
                <div class="register-panel">

                    <div class="register-icon">
                        <i class="material-icons">person_add</i>
                    </div>

                    <h1 class="register-title">Register an Account</h1>

                    <p class="register-required-note">Fields with a red outline are required. Password minimum is 12 characters.</p>

                    <div class="horizontal-name-row">
                        <div class="input-field custom-field required-field">
                            <i class="material-icons prefix icon-small">badge</i>
                            <input id="fName" type="text" placeholder="First Name (e.g. Juan)" maxlength="25" required>
                            <label for="fName">First Name</label>
                        </div>

                        <div class="input-field custom-field required-field">
                            <i class="material-icons prefix icon-small">badge</i>
                            <input id="lName" type="text" placeholder="Last Name (e.g. Dela Cruz)" maxlength="25" required>
                            <label for="lName">Last Name</label>
                        </div>
                    </div>

                    <div class="input-field custom-field required-field col s12">
                        <i class="material-icons prefix icon-small">email</i>
                        <input id="email" type="email" placeholder="Email (e.g. juan.delacruz@gmail.com)" maxlength="50" required>
                        <label for="email">Email</label>
                    </div>
                    <p class="register-help-text">Use a valid email format (example: name@gmail.com, name@yahoo.com, name@ust.edu.ph).</p>

                    <div class="input-field custom-field required-field col s12">
                        <i class="material-icons prefix icon-small">lock</i>
                        <input id="password" type="password" placeholder="Password (e.g. HomePlan2026!)" minlength="12" maxlength="50" required>
                        <label for="password">Password</label>
                    </div>
                    <p class="register-help-text">Password must be 12 to 50 characters.</p>

                    <div class="input-field custom-field required-field col s12">
                        <i class="material-icons prefix icon-small">lock_outline</i>
                        <input id="confirmPassword" type="password" placeholder="Confirm Password (e.g. HomePlan2026!)" minlength="12" maxlength="50" required>
                        <label for="confirmPassword">Confirm Password</label>
                    </div>
                    <p class="register-help-text">Confirmation must exactly match your password.</p>

                    <div class="submit-wrap">
                        <a class="waves-effect waves-light btn submit-btn" onclick="addFunc()">
                            Sign Up
                        </a>
                    </div>

                </div>
            </div>

            <div class="brand-panel">
                <div class="brand-content">
                    <div class="brand-logo-wrap">
                        <img src="../images/logo.png" class="main-logo" alt="Logo">
                    </div>
                    <h2 class="brand-title">HomePlanner</h2>
                    <p class="brand-subtitle">Your personal home planning assistant</p>
                    <p class="brand-login-text">Already Have an Account ?</p>
                    <a class="waves-effect waves-light btn brand-login-btn" onclick="redirectFunc(5)">
                        Login
                    </a>
                </div>
            </div>

        </div>
    </div>
</main>

<footer>
    <p>© 2026 HomePlanner</p>
    <p>Plan smarter. Live better.</p>
</footer>

<script src="../scripts/Service.js"></script>
<script>
    $(document).ready(function(){
        $('select').formSelect();
    });
</script>

</body>
</html>