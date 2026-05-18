<?php
session_start();
require_once "../../../model/database.php";
require_once "../../../model/userModel.php";

$homePagePrefix = "BlueHome";
$homeDisplayName = "Blue Home";
$homeBodyClass = "dashboard-red dashboard-blue";
$currentRoleName = isset($_SESSION["roleName"]) ? trim((string)$_SESSION["roleName"]) : "";
if(strtolower($currentRoleName) == "member"){
    header("Location: ./" . $homePagePrefix . "Home.php");
    exit;
}

$homeID = isset($_SESSION["homeID"]) ? (int)$_SESSION["homeID"] : 1;
$currentUserID = isset($_SESSION["userID"]) ? (int)$_SESSION["userID"] : 0;
$db = new Database();
$conn = $db->connectDB();
$userModel = new UserModel($conn);

$residentUsers = $userModel->getConcurrentUsersByHomeID($homeID);

$totalResidents = count($residentUsers);
$totalActiveUsers = $totalResidents;
$adminCount = 0;
$memberCount = 0;
foreach($residentUsers as $residentUser){
    $roleName = isset($residentUser["roleName"]) && trim((string)$residentUser["roleName"]) != "" ? trim((string)$residentUser["roleName"]) : "Member";
    if($roleName == "Admin"){
        $adminCount++;
    }
    else{
        $memberCount++;
    }
}

$homeCode = "#$1001";
if($homeID == 2){
    $homeCode = "#$1002";
}
elseif($homeID == 3){
    $homeCode = "#$1003";
}
elseif($homeID == 4){
    $homeCode = "#$1004";
}
elseif($homeID == 5){
    $homeCode = "#$FFFFF";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>HomePlanner - <?= htmlspecialchars($homeDisplayName) ?> Admin</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="../../css/Homes.css">
</head>
<body class="dashboard-page <?= htmlspecialchars($homeBodyClass) ?>">
    <div class="page-wrap">
        <div class="dashboard-shell">
            <div class="sidebar-nav">
                <div class="sidebar-brand">
                    <img src="../../../images/logo2.png" alt="HomePlanner Logo" class="sidebar-logo">
                    <div class="sidebar-name">HomePlanner</div>
                </div>

                <div class="sidebar-menu">
                    <div class="sidebar-group">
                        <a href="./<?= htmlspecialchars($homePagePrefix) ?>Home.php" class="sidebar-link">Home</a>
                        <div class="sidebar-submenu">
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Home.php#home-snapshot" class="sidebar-sublink">Snapshot</a>
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Home.php#home-metrics" class="sidebar-sublink">Key Home Metrics</a>
                        </div>
                    </div>

                    <div class="sidebar-group">
                        <a href="./<?= htmlspecialchars($homePagePrefix) ?>Tasks.php" class="sidebar-link">Tasks & Calendar</a>
                        <div class="sidebar-submenu">
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Tasks.php#tasks-calendar" class="sidebar-sublink">Calendar</a>
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Tasks.php#tasks-board" class="sidebar-sublink">Task Board</a>
                        </div>
                    </div>

                    <div class="sidebar-group">
                        <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php" class="sidebar-link">Budget</a>
                        <div class="sidebar-submenu">
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php#budget-overall" class="sidebar-sublink">Overall Budget</a>
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php#budget-allocation" class="sidebar-sublink">Allocation</a>
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php#budget-transactions" class="sidebar-sublink">Transactions</a>
                        </div>
                    </div>

                    <div class="sidebar-group">
                        <a href="./<?= htmlspecialchars($homePagePrefix) ?>Admin.php" class="sidebar-link sidebar-link-active">Admin</a>
                        <div class="sidebar-submenu">
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Admin.php#admin-member-management" class="sidebar-sublink">Member Management</a>
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Admin.php#admin-add-resident" class="sidebar-sublink">Add Resident</a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="dashboard-main">
                <div class="red-top-nav">
                    <a href="./<?= htmlspecialchars($homePagePrefix) ?>Home.php" class="red-top-link">Home</a>
                    <a href="../../RegistrationPage.php" class="red-top-link red-top-link-logout">Logout</a>
                </div>
                <div class="red-admin-page">
                    <section class="red-admin-section red-admin-hero-section">
                        <div class="red-home-eyebrow">Admin Control</div>
                        <h1 class="home-title"><?= htmlspecialchars($homeDisplayName) ?> Administration</h1>
                        <p class="home-desc">
                            Manage residents, monitor roles, and coordinate admin updates for <?= htmlspecialchars($homeDisplayName) ?> from one workspace.
                        </p>
                    </section>

                    <div class="red-admin-cards-grid">
                        <section class="red-admin-section red-admin-member-management-section" id="admin-member-management">
                            <h2 class="section-heading section-heading-left">Member Management</h2>

                            <div class="red-admin-member-grid">
                                <div class="red-admin-member-card red-admin-kpi-card-section">
                                    <div class="red-admin-kpi-label">Total Residents</div>
                                    <div class="red-admin-kpi-value"><?= $totalResidents ?></div>
                                    <div class="red-admin-kpi-note">All registered accounts under <?= htmlspecialchars($homeDisplayName) ?>.</div>
                                </div>

                                <div class="red-admin-member-card red-admin-kpi-card-section">
                                    <div class="red-admin-kpi-label">Active Users</div>
                                    <div class="red-admin-kpi-value"><?= $totalActiveUsers ?></div>
                                    <div class="red-admin-kpi-note">Users currently marked active in the home.</div>
                                </div>

                                <div class="red-admin-member-card red-admin-roles-section">
                                    <h3 class="red-admin-subtitle">Resident Role Directory</h3>
                                    <div class="red-admin-role-list">
                                        <?php if(!empty($residentUsers)): ?>
                                            <?php foreach($residentUsers as $residentUser): ?>
                                                <?php
                                                    $residentRoleName = isset($residentUser["roleName"]) && trim((string)$residentUser["roleName"]) != "" ? trim((string)$residentUser["roleName"]) : "Member";
                                                    $residentRoleClass = strtolower($residentRoleName) == "admin" ? "role-admin" : "role-member";
                                                    $residentFullName = trim((string)$residentUser["firstName"] . " " . (string)$residentUser["lastName"]);
                                                    if($residentFullName == ""){
                                                        $residentFullName = "Unnamed Resident";
                                                    }
                                                    $roleSelectID = "roleSelectUser" . (int)$residentUser["userID"];
                                                ?>
                                                <div class="red-admin-role-item">
                                                    <div class="red-admin-role-top">
                                                        <div class="red-admin-role-name"><?= htmlspecialchars($residentFullName) ?></div>
                                                        <span class="red-admin-role-badge <?= $residentRoleClass ?>"><?= htmlspecialchars($residentRoleName) ?></span>
                                                    </div>
                                                    <div class="red-admin-role-email"><?= htmlspecialchars($residentUser["email"]) ?></div>
                                                    <div class="red-admin-add-form">
                                                        <select id="<?= $roleSelectID ?>" class="red-admin-input browser-default">
                                                            <option value="Admin" <?= $residentRoleName == "Admin" ? "selected" : "" ?>>Admin</option>
                                                            <option value="Member" <?= $residentRoleName != "Admin" ? "selected" : "" ?>>Member</option>
                                                        </select>
                                                        <button
                                                            type="button"
                                                            class="red-admin-send-btn"
                                                            onclick="updateRedHomeRoleFunc(<?= (int)$residentUser['userID'] ?>, <?= $homeID ?>, '<?= $roleSelectID ?>', '<?= htmlspecialchars($residentFullName, ENT_QUOTES) ?>')"
                                                        >
                                                            Save Role
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="red-admin-action-item">No residents assigned to this home yet.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="red-admin-member-card red-admin-chart-section">
                                    <h3 class="red-admin-subtitle">Role Count Chart</h3>
                                    <div class="red-admin-chart-wrap">
                                        <div class="red-admin-chart-row">
                                            <div class="red-admin-chart-label">Admin</div>
                                            <div class="red-admin-chart-bar-shell">
                                                <div class="red-admin-chart-bar red-admin-chart-bar-admin" style="width: <?= $totalResidents > 0 ? max(12, round(($adminCount / $totalResidents) * 100)) : 12 ?>%;"></div>
                                            </div>
                                            <div class="red-admin-chart-count"><?= $adminCount ?></div>
                                        </div>
                                        <div class="red-admin-chart-row">
                                            <div class="red-admin-chart-label">Member</div>
                                            <div class="red-admin-chart-bar-shell">
                                                <div class="red-admin-chart-bar red-admin-chart-bar-member" style="width: <?= $totalResidents > 0 ? max(12, round(($memberCount / $totalResidents) * 100)) : 12 ?>%;"></div>
                                            </div>
                                            <div class="red-admin-chart-count"><?= $memberCount ?></div>
                                        </div>
                                    </div>
                                    <div class="red-admin-chart-note">Live role totals based on <?= htmlspecialchars($homeDisplayName) ?> residents.</div>
                                </div>
                            </div>
                        </section>

                        <section class="red-admin-section red-admin-kpi-card-section">
                            <div class="red-admin-kpi-label">Admin Actions This Week</div>
                            <div class="red-admin-kpi-value" id="adminActionsWeekCount">0</div>
                            <div class="red-admin-kpi-note">Includes role updates and admin changes this week.</div>
                        </section>

                        <section class="red-admin-section red-admin-quick-section" id="admin-add-resident">
                            <h2 class="section-heading section-heading-left">Quick Action - Add Resident</h2>
                            <div class="red-admin-quick-note">
                                Send <?= htmlspecialchars($homeDisplayName) ?> code to a resident by entering an email and confirming the Home ID.
                            </div>
                            <div class="red-admin-add-form">
                                <input id="redHomeResidentEmailInput" type="email" class="red-admin-input" placeholder="Enter resident email">
                                <input type="text" class="red-admin-input" value="<?= htmlspecialchars($homeCode) ?>" readonly>
                                <button type="button" class="red-admin-send-btn" onclick="addRedHomeResidentFunc(<?= $homeID ?>)">Send Home ID</button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>© <?= date("Y") ?> HomePlanner</div>
        <div>Administrative control panel for home and user management</div>
    </div>

    <script>
        window.userAjaxUrl = "../../../Controllers/Controller.php";
        window.redHomeAdminHomeID = <?= (int)$homeID ?>;
    </script>
    <script src="../../../scripts/Service.js?v=<?= filemtime("../../../scripts/Service.js") ?>"></script>
    <script>
        if (typeof setRedHomeAdminActionCountDisplayFunc === "function") {
            setRedHomeAdminActionCountDisplayFunc(window.redHomeAdminHomeID || 1);
        }
    </script>
</body>
</html>
