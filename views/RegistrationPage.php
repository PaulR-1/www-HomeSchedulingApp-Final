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
    <link rel="stylesheet" href="pages.css">
</head>
<body class="auth-page page-register">

<main>
    <div class="page-shell">
        <div class="page-grid">

            <div class="register-panel-wrap">
                <div class="register-panel">

                    <div class="register-icon">
                        <i class="material-icons">person_add</i>
                    </div>

                    <h1 class="register-title">Register an Account</h1>

                    <div class="horizontal-name-row">
                        <div class="input-field custom-field">
                            <i class="material-icons prefix icon-small">badge</i>
                            <input id="fName" type="text" placeholder="First Name" maxlength="25">
                            <label for="fName">First Name</label>
                        </div>

                        <div class="input-field custom-field">
                            <i class="material-icons prefix icon-small">badge</i>
                            <input id="lName" type="text" placeholder="Last Name" maxlength="25">
                            <label for="lName">Last Name</label>
                        </div>
                    </div>

                    <div class="input-field custom-field col s12" style="padding-left:0; padding-right:0;">
                        <i class="material-icons prefix icon-small">email</i>
                        <input id="email" type="email" placeholder="Email" maxlength="25">
                        <label for="email">Email</label>
                    </div>

                    <div class="input-field custom-field col s12" style="padding-left:0; padding-right:0;">
                        <i class="material-icons prefix icon-small">lock</i>
                        <input id="password" type="password" placeholder="Password" maxlength="25">
                        <label for="password">Password</label>
                    </div>

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