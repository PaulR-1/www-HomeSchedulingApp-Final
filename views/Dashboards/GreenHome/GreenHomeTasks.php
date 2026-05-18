<?php
session_start();
require_once "../../../model/database.php";
require_once "../../../model/TaskModel.php";

$homePagePrefix = "GreenHome";
$homeDisplayName = "Green Home";
$homeBodyClass = "dashboard-red dashboard-green";

$homeID = isset($_SESSION["homeID"]) ? (int)$_SESSION["homeID"] : 1;
$currentRoleName = isset($_SESSION["roleName"]) ? trim((string)$_SESSION["roleName"]) : "";
$isMemberRole = strtolower($currentRoleName) == "member";
$db = new Database();
$conn = $db->connectDB();
$taskModel = new TaskModel($conn);

$taskList = $taskModel->getTasksByHomeID($homeID);
$calendarEventList = $taskModel->getCalendarEventsByHomeID($homeID);

$todoTasks = [];
$inProgressTasks = [];
$doneTasks = [];

foreach($taskList as $taskItem){
    $taskStatusValue = isset($taskItem["status"]) ? (string)$taskItem["status"] : "";
    $taskStatusLabel = isset($taskItem["statusLabel"]) ? strtolower(trim((string)$taskItem["statusLabel"])) : "";

    if(
        $taskStatusValue == "2" ||
        $taskStatusValue == "In Progress" ||
        $taskStatusValue == "in_progress" ||
        $taskStatusValue == "inprogress" ||
        $taskStatusLabel == "in progress" ||
        $taskStatusLabel == "in-progress" ||
        $taskStatusLabel == "in_progress" ||
        $taskStatusLabel == "ongoing"
    ){
        $inProgressTasks[] = $taskItem;
    }
    elseif(
        $taskStatusValue == "3" ||
        $taskStatusValue == "Done" ||
        $taskStatusValue == "done" ||
        $taskStatusValue == "completed" ||
        $taskStatusValue == "complete" ||
        $taskStatusLabel == "done" ||
        $taskStatusLabel == "completed" ||
        $taskStatusLabel == "complete"
    ){
        $doneTasks[] = $taskItem;
    }
    else{
        $todoTasks[] = $taskItem;
    }
}

$fullCalendarEvents = [];
foreach($calendarEventList as $eventItem){
    $eventDate = isset($eventItem["eventDate"]) ? $eventItem["eventDate"] : "";
    $eventTime = isset($eventItem["eventTime"]) ? $eventItem["eventTime"] : "";
    $startDateTime = isset($eventItem["startDateTime"]) ? $eventItem["startDateTime"] : "";
    $endDateTime = isset($eventItem["endDateTime"]) ? $eventItem["endDateTime"] : "";

    if($startDateTime == "" && $eventDate != ""){
        if($eventTime != ""){
            $startDateTime = $eventDate . "T" . $eventTime;
        }
        else{
            $startDateTime = $eventDate . "T00:00:00";
        }
    }

    $fullCalendarEvents[] = [
        "id" => (int)$eventItem["eventID"],
        "title" => $eventItem["eventTitle"],
        "start" => $startDateTime != "" ? str_replace(" ", "T", $startDateTime) : null,
        "end" => $endDateTime != "" ? str_replace(" ", "T", $endDateTime) : null,
        "allDay" => ((int)$eventItem["isAllDay"] === 1),
        "backgroundColor" => $eventItem["eventColor"] != "" ? $eventItem["eventColor"] : "#b3484e",
        "borderColor" => $eventItem["eventColor"] != "" ? $eventItem["eventColor"] : "#b3484e",
        "extendedProps" => [
            "eventID" => (int)$eventItem["eventID"],
            "eventNote" => $eventItem["eventNote"]
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePlanner - <?= htmlspecialchars($homeDisplayName) ?> Tasks & Calendar</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
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
                        <a href="./<?= htmlspecialchars($homePagePrefix) ?>Tasks.php" class="sidebar-link sidebar-link-active">Tasks & Calendar</a>
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
                    <a href="./<?= htmlspecialchars($homePagePrefix) ?>Home.php" class="red-top-link">Home</a>
                    <a href="../../RegistrationPage.php" class="red-top-link red-top-link-logout">Logout</a>
                </div>
                <div class="red-tasks-page">
                    <section class="red-tasks-section red-tasks-hero-section">
                        <div class="red-home-eyebrow">Tasks & Calendar Workspace</div>
                        <h1 class="home-title"><?= htmlspecialchars($homeDisplayName) ?> Task Planner</h1>
                        <p class="home-desc">
                            Manage household tasks and schedules in one place with clean status lanes, quick filters, and dual calendar views.
                        </p>
                    </section>

                    <div class="red-tasks-layout">
                        <section class="red-tasks-section red-calendar-manager-section" id="tasks-calendar">
                            <div class="red-tasks-section-head red-calendar-head">
                                <h2 class="section-heading section-heading-left">Home Calendar</h2>
                            </div>

                            <div class="task-input-bar">
                                <input id="calendarEventTitleInput" type="text" class="task-input" placeholder="Event title">
                                <input id="calendarEventDateInput" type="date" class="task-date-input">
                                <input id="calendarEventTimeInput" type="time" class="task-date-input">
                                <button type="button" class="task-action-btn" onclick="addCalendarEventFunc(<?= $homeID ?>)">Add Event</button>
                            </div>

                            <div class="task-input-bar">
                                <input id="calendarEventNoteInput" type="text" class="task-input" placeholder="Event note">
                                <select id="calendarEventColorInput" class="task-select browser-default">
                                    <option value="#b3484e">Red</option>
                                    <option value="#dd7076">Rose</option>
                                    <option value="#64748b">Slate</option>
                                    <option value="#166534">Green</option>
                                </select>
                                <select id="calendarEventAllDayInput" class="task-select browser-default">
                                    <option value="0">Timed Event</option>
                                    <option value="1">All Day</option>
                                </select>
                                <div></div>
                            </div>

                            <div class="red-fullcalendar-wrap">
                                <div id="redHomeCalendar"></div>
                            </div>
                        </section>

                        <section class="red-tasks-section red-task-manager-section" id="tasks-board">
                            <div class="red-tasks-section-head">
                                <h2 class="section-heading section-heading-left">Task Board</h2>
                            </div>

                            <div class="task-input-bar">
                                <input id="taskTitleInput" type="text" class="task-input" placeholder="Enter a new task title">
                                <select id="taskPriorityInput" class="task-select browser-default">
                                    <option value="Medium">Priority: Medium</option>
                                    <option value="High">Priority: High</option>
                                    <option value="Low">Priority: Low</option>
                                </select>
                                <input id="taskDueDateInput" type="date" class="task-date-input">
                                <button type="button" class="task-action-btn" onclick="addTaskFunc(<?= $homeID ?>)">Add Task</button>
                            </div>

                            <div class="task-input-bar">
                                <input id="taskDescriptionInput" type="text" class="task-input" placeholder="Task description">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>

                            <div class="task-filter-row">
                                <button type="button" class="task-filter-chip task-filter-chip-active" data-task-filter="all" onclick="applyTaskFilterFunc('all', this)">All</button>
                                <button type="button" class="task-filter-chip" data-task-filter="today" onclick="applyTaskFilterFunc('today', this)">Today</button>
                                <button type="button" class="task-filter-chip" data-task-filter="week" onclick="applyTaskFilterFunc('week', this)">This Week</button>
                                <button type="button" class="task-filter-chip" data-task-filter="overdue" onclick="applyTaskFilterFunc('overdue', this)">Overdue</button>
                            </div>

                            <div class="task-status-grid">
                                <div class="task-status-column" id="taskTodoColumn">
                                    <div class="task-status-title">To Do</div>
                                    <?php if(!empty($todoTasks)): ?>
                                        <?php foreach($todoTasks as $taskItem): ?>
                                            <div class="task-board-item task-item-row" data-task-id="<?= (int)$taskItem["taskID"] ?>" data-task-title="<?= htmlspecialchars($taskItem["taskTitle"]) ?>" data-task-description="<?= htmlspecialchars($taskItem["description"]) ?>" data-task-priority="<?= htmlspecialchars($taskItem["priorityLevel"]) ?>" data-task-due="<?= htmlspecialchars((string)$taskItem["dueDate"]) ?>">
                                                <div class="task-board-main"><?= htmlspecialchars($taskItem["taskTitle"]) ?></div>
                                                <div class="calendar-list-note"><?= htmlspecialchars($taskItem["description"]) ?></div>
                                                <div class="task-board-meta">
                                                    <span class="task-priority-tag task-priority-<?= strtolower(str_replace(' ', '-', $taskItem["priorityLevel"])) ?>">
                                                        <?= htmlspecialchars($taskItem["priorityLevel"]) ?>
                                                    </span>
                                                    <span class="task-due-tag">Due: <?= $taskItem["dueDate"] ? htmlspecialchars($taskItem["dueDate"]) : "No Date" ?></span>
                                                </div>
                                                <div class="task-board-meta">
                                                    <button type="button" class="calendar-view-btn" onclick="updateTaskStatusFunc(<?= (int)$taskItem["taskID"] ?>, <?= $homeID ?>, 'In Progress', this)">Start</button>
                                                    <button type="button" class="calendar-view-btn" onclick="deleteTaskFunc(<?= (int)$taskItem["taskID"] ?>, <?= $homeID ?>, this)">Delete</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="task-empty-state">No tasks in To Do.</div>
                                    <?php endif; ?>
                                </div>

                                <div class="task-status-column" id="taskInProgressColumn">
                                    <div class="task-status-title">In Progress</div>
                                    <?php if(!empty($inProgressTasks)): ?>
                                        <?php foreach($inProgressTasks as $taskItem): ?>
                                            <div class="task-board-item task-item-row" data-task-id="<?= (int)$taskItem["taskID"] ?>" data-task-title="<?= htmlspecialchars($taskItem["taskTitle"]) ?>" data-task-description="<?= htmlspecialchars($taskItem["description"]) ?>" data-task-priority="<?= htmlspecialchars($taskItem["priorityLevel"]) ?>" data-task-due="<?= htmlspecialchars((string)$taskItem["dueDate"]) ?>">
                                                <div class="task-board-main"><?= htmlspecialchars($taskItem["taskTitle"]) ?></div>
                                                <div class="calendar-list-note"><?= htmlspecialchars($taskItem["description"]) ?></div>
                                                <div class="task-board-meta">
                                                    <span class="task-priority-tag task-priority-<?= strtolower(str_replace(' ', '-', $taskItem["priorityLevel"])) ?>">
                                                        <?= htmlspecialchars($taskItem["priorityLevel"]) ?>
                                                    </span>
                                                    <span class="task-due-tag">Due: <?= $taskItem["dueDate"] ? htmlspecialchars($taskItem["dueDate"]) : "No Date" ?></span>
                                                </div>
                                                <div class="task-board-meta">
                                                    <button type="button" class="calendar-view-btn" onclick="updateTaskStatusFunc(<?= (int)$taskItem["taskID"] ?>, <?= $homeID ?>, 'Done', this)">Mark Done</button>
                                                    <button type="button" class="calendar-view-btn" onclick="updateTaskStatusFunc(<?= (int)$taskItem["taskID"] ?>, <?= $homeID ?>, 'To Do', this)">Back</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="task-empty-state">No additional tasks are currently in progress.</div>
                                    <?php endif; ?>
                                </div>

                                <div class="task-status-column" id="taskDoneColumn">
                                    <div class="task-status-title">Done</div>
                                    <?php if(!empty($doneTasks)): ?>
                                        <?php foreach($doneTasks as $taskItem): ?>
                                            <div class="task-board-item task-board-item-done task-item-row" data-task-id="<?= (int)$taskItem["taskID"] ?>" data-task-title="<?= htmlspecialchars($taskItem["taskTitle"]) ?>" data-task-description="<?= htmlspecialchars($taskItem["description"] ?? "") ?>" data-task-priority="<?= htmlspecialchars($taskItem["priorityLevel"] ?? "Low") ?>" data-task-due="<?= htmlspecialchars((string)$taskItem["dueDate"]) ?>">
                                                <div class="task-board-main"><?= htmlspecialchars($taskItem["taskTitle"]) ?></div>
                                                <div class="task-board-meta">
                                                    <button type="button" class="calendar-view-btn" onclick="updateTaskStatusFunc(<?= (int)$taskItem["taskID"] ?>, <?= $homeID ?>, 'To Do', this)">Reopen</button>
                                                    <button type="button" class="calendar-view-btn" onclick="deleteTaskFunc(<?= (int)$taskItem["taskID"] ?>, <?= $homeID ?>, this)">Delete</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="task-empty-state">No completed tasks yet.</div>
                                    <?php endif; ?>
                                </div>
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
        window.taskAjaxUrl = "../../../Controllers/TaskController.php";
        window.redHomeID = <?= (int)$homeID ?>;
        window.redHomeCalendarEvents = <?= json_encode($fullCalendarEvents, JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="../../../scripts/Service.js?v=<?= filemtime("../../../scripts/Service.js") ?>"></script>
    <script>
        if (typeof initRedHomeCalendarFunc === "function") {
            initRedHomeCalendarFunc();
        }
    </script>
</body>
</html>
