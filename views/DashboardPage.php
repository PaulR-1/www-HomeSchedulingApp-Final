<?php
    session_start();
    require_once "../bl/userManager.php";
    require_once "../bl/HomeManager.php"; // so what is this for ?
    $usermanager = new UserManager();
    $advancedUsers = $usermanager->getAdvancedUser();
    
    //Given by chatgpt, I think this is done because the homeModel needs a parameter, which is a connection to the database. So basically it makes a connection to the database here
        $db = new Database();
        $conn = $db->connectDB();
        $homeManager = new HomeModel($conn);
    $homes = $homeManager-> cardDepartments(); 
    $totalHomes = $homeManager->getTotalHomes();

    //for the graphs
    $label = array_column($homes, "homeName");
    $data = array_column($homes, "total_users");

    //Ok so now for the totals, the getTotalusers is in userModel, while getTotalHomes is in homeModel
    $userModel = new UserModel($conn);
    $totalOverallUsers = $userModel->getTotalUsers();
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>HomePlanner Admin</title>
    <link rel="stylesheet" href="pages.css">
</head>
<body class="admin-page">

    <div class="topbar">
        <div class="brand-side">
            <img src="../images/logo2.png" alt="HomePlanner Logo" class="brand-logo">
            <div class="brand-name">HomePlanner</div>
        </div>

        <a class="return-btn-top" onclick="redirectFunc(7)">Return</a>
    </div>

    <main>
        <div class="page-wrap">

            <div class="admin-hero">
                <h1>Admin Dashboard</h1>
                <p>Manage, update, and organize users across all HomePlanner homes.</p>
            </div>

            <div class="admin-shell">
                <div class="admin-shell-inner">

                    <div class="admin-top-row">
                        <div class="mini-panel">
                            <h2 class="mini-title">User Management Center</h2>
                            <p class="mini-text">
                                Add new residents, update user details, assign home IDs, and maintain your household records in one clean workspace.
                            </p>
                        </div>

                        <div class="mini-panel">
                            <h2 class="mini-title">Quick Overview</h2>
                            <div class="stats-grid">
                                <div class="stat-box">
                                    <div class="stat-number"><?= count($advancedUsers) ?></div>
                                    <div class="stat-label">Registered Users</div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-number"><?= count($homes) ?></div>
                                    <div class="stat-label">Configured Homes</div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-number">Admin</div>
                                    <div class="stat-label">Control Access</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <h3 class="section-title">User Input Panel</h3>

                        <div class="form-grid">
                            <div class="input-field custom-field grid-span-3">
                                <input id="fName" type="text">
                                <label for="fName">First Name</label>
                            </div>

                            <div class="input-field custom-field grid-span-3">
                                <input id="lName" type="text">
                                <label for="lName">Last Name</label>
                            </div>

                            <div class="input-field custom-field grid-span-6">
                                <input id="email" type="email">
                                <label for="email">Email</label>
                            </div>

                            <div class="input-field custom-field grid-span-6">
                                <input id="password" type="password">
                                <label for="password">Password</label>
                            </div>

                            <div class="input-field custom-field grid-span-6">
                                <input id="updateHomeSelect" type="text" class="validate">
                                <label for="updateHomeSelect">Home ID</label>
                            </div>
                        </div>

                        <div class="helper-text-line">
                            Fill the fields above, then use the action buttons in the table below to update or remove a user.
                        </div>
                    </div>

                    <div class="table-card">
                        <h3 class="section-title">Registered Users</h3>

                        <div class="table-wrap">
                            <table class="highlight centered striped responsive-table">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                        <th>Home</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($advancedUsers)): ?>
                                        <?php foreach($advancedUsers as $index => $user) :?>
                                            <tr>
                                                <td><?= $user['userID'] ?></td>
                                                <td><?= $user['firstName'] ?></td>
                                                <td><?= $user['lastName'] ?></td>
                                                <td><?= $user['email'] ?></td>
                                                <td><?= $user['password'] ?></td>
                                                <td>
                                                    <span class="home-badge">
                                                        <?= $user['homeName'] ?? 'No Home Assigned' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn-small update-btn" onclick="updateFunc(<?= $user['userID'] ?>)">Update</button>
                                                        <button class="btn-small delete-btn" onclick="deleteFunc(<?= $user['userID'] ?>)">Delete</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr class="empty-row">
                                            <td colspan="7">No data found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="page-wrap admin-lower-wrap">
            <section class="admin-section admin-homes-section">
                <div class="section-heading-block">
                    <h2 class="section-heading-title">Home's Information</h2>
                    <p class="section-heading-text">Overview of residents assigned to each home</p>
                </div>

                <div class="cards-row">
                    <?php foreach($homes as $index => $home) : ?>
                    <div class="home-card">
                        <div class="home-card-label">Home</div>
                        <div class="home-card-title"><?= $home['homeName'] ?></div>
                        <div class="home-card-value"><?= $home['total_users'] ?></div>
                        <p class="home-card-text">Total residents assigned</p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="admin-section admin-chart-section">
                <div class="section-heading-block">
                    <h2 class="section-heading-title">Chart Information</h2>
                    <p class="section-heading-text">Bar, doughnut, and line views of users registered per home</p>
                </div>

                <div class="charts-grid">
                    <div class="chart-card">
                        <h3 class="chart-card-title">Bar Chart — Users per Home</h3>
                        <div class="chart-canvas-wrap">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>

                    <div class="charts-row-two">
                        <div class="chart-card">
                            <h3 class="chart-card-title">Doughnut Chart — Share by Home</h3>
                            <div class="chart-canvas-wrap chart-canvas-wrap-sm">
                                <canvas id="myDoughnutChart"></canvas>
                            </div>
                        </div>

                        <div class="chart-card">
                            <h3 class="chart-card-title">Line Chart — Users per Home</h3>
                            <div class="chart-canvas-wrap chart-canvas-wrap-sm">
                                <canvas id="myLineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </main>

    <div class="footer">
        <div>© 2026 HomePlanner</div>
        <div>Administrative control panel for home and user management</div>
    </div>
    
    <script> 
        window.barData = {
            label: <?= json_encode($label) ?>,
            data: <?=  json_encode($data) ?>
        }
    </script>
    <script src="../scripts/Service.js"></script>
    <script src="../scripts/Dashboard.js"></script>
    <script>
        $(document).ready(function(){
            $('select').formSelect();
        });
    </script>
</body>
</html>
