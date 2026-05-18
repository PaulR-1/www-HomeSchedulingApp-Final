<?php
session_start();
require_once "../../../model/database.php";
require_once "../../../model/TaskModel.php";
require_once "../../../model/BudgetModel.php";
require_once "../../../model/userModel.php";

$homePagePrefix = defined("HOME_PAGE_PREFIX") ? HOME_PAGE_PREFIX : "RedHome";
$homeDisplayName = defined("HOME_DISPLAY_NAME") ? HOME_DISPLAY_NAME : "Red Home";
$homeBodyClass = defined("HOME_BODY_CLASS") ? HOME_BODY_CLASS : "dashboard-red";
$homeChartPrimary = defined("HOME_CHART_PRIMARY") ? HOME_CHART_PRIMARY : "#b3484e";
$homeChartSecondary = defined("HOME_CHART_SECONDARY") ? HOME_CHART_SECONDARY : "#e7b3b6";

$homeID = isset($_SESSION["homeID"]) ? (int)$_SESSION["homeID"] : 1;
$currentUserID = isset($_SESSION["userID"]) ? (int)$_SESSION["userID"] : 0;
$currentFirstName = isset($_SESSION["firstName"]) ? trim((string)$_SESSION["firstName"]) : "";
$currentLastName = isset($_SESSION["lastName"]) ? trim((string)$_SESSION["lastName"]) : "";
$currentRoleName = isset($_SESSION["roleName"]) ? trim((string)$_SESSION["roleName"]) : "";

$db = new Database();
$conn = $db->connectDB();
$taskModel = new TaskModel($conn);
$budgetModel = new BudgetModel($conn);
$userModel = new UserModel($conn);

if(($currentFirstName == "" || $currentLastName == "") && $currentUserID > 0){
    $currentUserData = $userModel->getUserByID($currentUserID);
    if($currentUserData){
        $currentFirstName = isset($currentUserData["firstName"]) ? trim((string)$currentUserData["firstName"]) : "";
        $currentLastName = isset($currentUserData["lastName"]) ? trim((string)$currentUserData["lastName"]) : "";
        $currentRoleName = isset($currentUserData["roleName"]) ? trim((string)$currentUserData["roleName"]) : $currentRoleName;
        $_SESSION["firstName"] = $currentFirstName;
        $_SESSION["lastName"] = $currentLastName;
        $_SESSION["roleName"] = $currentRoleName;
    }
}

$isMemberRole = strtolower($currentRoleName) == "member";

$displayName = trim($currentFirstName . " " . $currentLastName);
if($displayName == ""){
    $displayName = $homeDisplayName . " Resident";
}

$taskList = $taskModel->getTasksByHomeID($homeID);
$calendarEventList = $taskModel->getCalendarEventsByHomeID($homeID);
$budgetSummary = $budgetModel->ensureBudgetSummary($homeID);
$activeUsers = $userModel->getConcurrentUsersByHomeID($homeID);

$monthlyBudget = isset($budgetSummary["monthlyBudget"]) ? (float)$budgetSummary["monthlyBudget"] : 0;
$doneTasksCount = 0;
foreach($taskList as $taskItem){
    $taskStatusValue = isset($taskItem["status"]) ? strtolower(trim((string)$taskItem["status"])) : "";
    $taskStatusLabel = isset($taskItem["statusLabel"]) ? strtolower(trim((string)$taskItem["statusLabel"])) : "";
    if(
        $taskStatusValue == "3" ||
        $taskStatusValue == "done" ||
        $taskStatusValue == "completed" ||
        $taskStatusLabel == "done" ||
        $taskStatusLabel == "completed"
    ){
        $doneTasksCount++;
    }
}
$remainingTasksCount = count($taskList) - $doneTasksCount;
if($remainingTasksCount < 0){
    $remainingTasksCount = 0;
}

$pendingEventsCount = count($calendarEventList);
$upcomingEvents = [];
foreach($calendarEventList as $eventItem){
    $eventTitle = isset($eventItem["eventTitle"]) ? trim((string)$eventItem["eventTitle"]) : "";
    if($eventTitle != ""){
        $upcomingEvents[] = $eventTitle;
    }
    if(count($upcomingEvents) >= 4){
        break;
    }
}

$concurrentUsersCount = count($activeUsers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePlanner - <?= htmlspecialchars($homeDisplayName) ?></title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
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
                        <a href="./<?= htmlspecialchars($homePagePrefix) ?>Home.php" class="sidebar-link sidebar-link-active">Home</a>
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

                    <?php if(!$isMemberRole): ?>
                        <div class="sidebar-group">
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php" class="sidebar-link">Budget</a>
                            <div class="sidebar-submenu">
                                <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php#budget-overall" class="sidebar-sublink">Overall Budget</a>
                                <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php#budget-allocation" class="sidebar-sublink">Allocation</a>
                                <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php#budget-transactions" class="sidebar-sublink">Transactions</a>
                            </div>
                        </div>

                        <div class="sidebar-group">
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Admin.php" class="sidebar-link">Admin</a>
                            <div class="sidebar-submenu">
                                <a href="./<?= htmlspecialchars($homePagePrefix) ?>Admin.php#admin-member-management" class="sidebar-sublink">Member Management</a>
                                <a href="./<?= htmlspecialchars($homePagePrefix) ?>Admin.php#admin-add-resident" class="sidebar-sublink">Add Resident</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <div class="dashboard-main">
                <div class="red-top-nav">
                    <a href="./<?= htmlspecialchars($homePagePrefix) ?>Home.php" class="red-top-link red-top-link-active">Home</a>
                    <a href="../../RegistrationPage.php" class="red-top-link red-top-link-logout">Logout</a>
                </div>
                <div class="red-home-page">
                    <section class="red-home-section red-home-intro-section" id="home-overview">
                        <div class="red-home-eyebrow">Home Overview</div>
                        <h1 class="home-title"><?= htmlspecialchars($homeDisplayName) ?> Dashboard</h1>
                        <div class="home-welcome-line">Welcome Back, <?= htmlspecialchars($displayName) ?></div>
                        <p class="home-desc">
                            Modern family home focused on comfort, planning, and smooth day-to-day coordination for everyone.
                        </p>
                    </section>

                    <section class="red-home-section red-home-snapshot-section" id="home-snapshot">
                        <h2 class="section-heading">Home Snapshot</h2>
                        <div class="home-snapshot-cards">
                            <?php if(!$isMemberRole): ?>
                                <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php#budget-overall" class="home-snapshot-card">
                                    <div class="home-snapshot-card-title">Budget Status</div>
                                    <div class="home-snapshot-card-text">Monthly budget currently tracked at ₱<?= number_format($monthlyBudget, 2) ?>.</div>
                                </a>
                            <?php endif; ?>
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Tasks.php#tasks-board" class="home-snapshot-card">
                                <div class="home-snapshot-card-title">Task Completion</div>
                                <div class="home-snapshot-card-text"><?= $doneTasksCount ?> household tasks marked complete so far.</div>
                            </a>
                            <a href="./<?= htmlspecialchars($homePagePrefix) ?>Tasks.php#tasks-calendar" class="home-snapshot-card">
                                <div class="home-snapshot-card-title">Pending Reminders</div>
                                <div class="home-snapshot-card-text"><?= $pendingEventsCount ?> reminders or calendar events scheduled.</div>
                            </a>
                            <?php if(!$isMemberRole): ?>
                                <a href="./<?= htmlspecialchars($homePagePrefix) ?>Admin.php#admin-member-management" class="home-snapshot-card">
                                    <div class="home-snapshot-card-title">Manage Users</div>
                                    <div class="home-snapshot-card-text">Open the admin page to manage <?= htmlspecialchars($homeDisplayName) ?> members.</div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section class="red-home-section red-home-metrics-section" id="home-metrics">
                        <h2 class="section-heading section-heading-left">Key Home Metrics</h2>
                        <div class="home-metrics-layout">
                            <div class="home-metrics-row home-metrics-row-single">
                                <div class="home-metric-block home-metric-budget-block" id="home-detail-budget">
                                    <div class="home-metric-label">Monthly Budget</div>
                                    <div class="home-metric-value">₱<?= number_format($monthlyBudget, 2) ?></div>
                                    <div class="home-metric-note">
                                        Live value from the <?= htmlspecialchars($homeDisplayName) ?> budget summary table.
                                    </div>
                                </div>
                            </div>

                            <div class="home-metrics-row home-metrics-row-double">
                                <div class="home-metric-block home-metric-reminders-block" id="home-detail-reminders">
                                    <div class="home-metric-label">Pending Events</div>
                                    <?php if(!empty($upcomingEvents)): ?>
                                        <div class="home-pending-events-list">
                                            <?php foreach($upcomingEvents as $eventTitle): ?>
                                                <div class="home-pending-event-item"><?= htmlspecialchars($eventTitle) ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="home-metric-note">No pending reminders yet for this home.</div>
                                    <?php endif; ?>
                                </div>

                                <div class="home-metric-block home-metric-chart-block" id="home-detail-tasks">
                                    <div class="home-metric-label">Tasks Done Chart</div>
                                    <div class="home-chart-wrap">
                                        <canvas id="homeTasksChart"></canvas>
                                    </div>
                                    <div class="home-metric-note">Live task ratio: <?= $doneTasksCount ?> completed and <?= $remainingTasksCount ?> remaining.</div>
                                </div>
                            </div>

                            <div class="home-metrics-row home-metrics-row-single" id="home-concurrent-users">
                                <div class="home-metric-block home-metric-users-block">
                                    <div class="home-users-head">
                                        <div class="home-metric-label">Concurrent Users in <?= htmlspecialchars($homeDisplayName) ?></div>
                                        <div class="home-users-count"><?= $concurrentUsersCount ?></div>
                                    </div>
                                    <div class="home-metric-note">Current users assigned to this home based on live records.</div>
                                    <?php if(!empty($activeUsers)): ?>
                                        <div class="home-users-grid">
                                            <?php foreach($activeUsers as $homeUser): ?>
                                                <?php
                                                    $fullName = trim((string)$homeUser["firstName"] . " " . (string)$homeUser["lastName"]);
                                                    $initials = "";
                                                    if(isset($homeUser["firstName"]) && $homeUser["firstName"] !== ""){
                                                        $initials .= strtoupper(substr(trim((string)$homeUser["firstName"]), 0, 1));
                                                    }
                                                    if(isset($homeUser["lastName"]) && $homeUser["lastName"] !== ""){
                                                        $initials .= strtoupper(substr(trim((string)$homeUser["lastName"]), 0, 1));
                                                    }
                                                    if($initials == ""){
                                                        $initials = "U";
                                                    }
                                                ?>
                                                <div class="home-user-pill">
                                                    <div class="home-user-initials"><?= htmlspecialchars($initials) ?></div>
                                                    <div class="home-user-name"><?= htmlspecialchars($fullName) ?></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="home-metric-note">No users found for this home yet.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>© <?= date("Y") ?> HomePlanner</div>
        <div>Administrative control panel for home and user management</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../../scripts/Service.js"></script>
    <script>
        const tasksChartCanvas = document.getElementById("homeTasksChart");
        if (tasksChartCanvas && typeof Chart !== "undefined") {
            new Chart(tasksChartCanvas, {
                type: "pie",
                data: {
                    labels: ["Completed Tasks", "Remaining Tasks"],
                    datasets: [{
                        data: [<?= (int)$doneTasksCount ?>, <?= (int)$remainingTasksCount ?>],
                        backgroundColor: ["<?= htmlspecialchars($homeChartPrimary) ?>", "<?= htmlspecialchars($homeChartSecondary) ?>"],
                        borderColor: ["#ffffff", "#ffffff"],
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                boxWidth: 12,
                                color: "#334155",
                                font: {
                                    size: 11,
                                    weight: "700"
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
