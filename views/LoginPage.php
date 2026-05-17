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
    <title>HomePlanner - Login</title>
    <link rel="stylesheet" href="pages.css">
</head>

<body class="auth-page page-login">

<main>
    <div class="page-shell">
        <div class="page-grid">

            <div class="brand-panel">
                <div class="brand-content">
                    <div class="brand-logo-wrap">
                        <img src="../images/logo.png" class="main-logo" alt="Logo">
                    </div>
                    <h2 class="brand-title">HomePlanner</h2>
                    <p class="brand-subtitle">Your personal home planning assistant</p>
                    <p class="brand-login-text">Dont have an account ?</p>
                    <a class="waves-effect waves-light btn brand-signup-btn" onclick="redirectFunc(7)">
                        Sign Up
                    </a>
                </div>
            </div>

            <div class="login-panel-wrap">
                <div class="login-panel">

                    <div class="login-icon">
                        <i class="material-icons">person_add</i>
                    </div>

                    <h1 class="login-title">Sign in to your Account</h1>
                    <p class="login-subtitle">Secure access to your home planning system.</p>

                    <div class="input-field custom-field">
                        <i class="material-icons prefix icon-small">email</i>
                        <input id="LFNAME" type="email" placeholder="Email">
                        <label for="LFNAME">Email</label>
                    </div>

                    <div class="input-field custom-field">
                        <i class="material-icons prefix icon-small">lock</i>
                        <input id="LLNAME" type="password" placeholder="Password">
                        <label for="LLNAME">Password</label>
                    </div>

                    <div class="input-field custom-field">
                        <i class="material-icons prefix icon-small">home</i>
                        <input id="LhomeSelect" type="text" placeholder="Home ID">
                        <label for="LhomeSelect">Home ID</label>
                    </div>

                    <div class="login-btn-wrap">
                        <a class="waves-effect waves-light btn login-btn" onclick="loginFunc()">
                            Login
                        </a>
                    </div>

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

</body>
</html>