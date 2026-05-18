<?php
session_start();
header("Content-Type: application/json");
require_once "../bl/TaskManager.php";

$taskManager = new TaskManager();

if(isset($_POST["taskAction"]) && $_POST["taskAction"] == "loadTaskData"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $payload = $taskManager->getTaskPageData($homeID);
    echo json_encode([
        "success" => true,
        "data" => $payload
    ]);
    exit;
}
elseif(isset($_POST["taskAction"]) && $_POST["taskAction"] == "addTask"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $taskTitle = isset($_POST["taskTitle"]) ? $_POST["taskTitle"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";
    $priorityLevel = isset($_POST["priorityLevel"]) ? $_POST["priorityLevel"] : "Medium";
    $dueDate = isset($_POST["dueDate"]) ? $_POST["dueDate"] : "";

    $added = $taskManager->addTaskFunc($homeID, $taskTitle, $description, $priorityLevel, $dueDate);
    if($added){
        echo json_encode([
            "success" => true,
            "message" => "Task added successfully.",
            "task" => $added
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to add task."
    ]);
    exit;
}
elseif(isset($_POST["taskAction"]) && $_POST["taskAction"] == "updateTaskStatus"){
    $taskID = isset($_POST["taskID"]) ? (int)$_POST["taskID"] : 0;
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $status = isset($_POST["status"]) ? $_POST["status"] : "";

    $updated = $taskManager->updateTaskStatusFunc($taskID, $homeID, $status);
    if($updated){
        echo json_encode([
            "success" => true,
            "message" => "Task status updated."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to update task status."
    ]);
    exit;
}
elseif(isset($_POST["taskAction"]) && $_POST["taskAction"] == "deleteTask"){
    $taskID = isset($_POST["taskID"]) ? (int)$_POST["taskID"] : 0;
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;

    $deleted = $taskManager->deleteTaskFunc($taskID, $homeID);
    if($deleted){
        echo json_encode([
            "success" => true,
            "message" => "Task deleted successfully."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to delete task."
    ]);
    exit;
}
elseif(isset($_POST["taskAction"]) && $_POST["taskAction"] == "addCalendarEvent"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $eventTitle = isset($_POST["eventTitle"]) ? $_POST["eventTitle"] : "";
    $eventNote = isset($_POST["eventNote"]) ? $_POST["eventNote"] : "";
    $eventDate = isset($_POST["eventDate"]) ? $_POST["eventDate"] : "";
    $eventTime = isset($_POST["eventTime"]) ? $_POST["eventTime"] : "";
    $isAllDay = isset($_POST["isAllDay"]) ? (int)$_POST["isAllDay"] : 0;
    $eventColor = isset($_POST["eventColor"]) ? $_POST["eventColor"] : "#b3484e";

    $added = $taskManager->addCalendarEventFunc($homeID, $eventTitle, $eventNote, $eventDate, $eventTime, $isAllDay, $eventColor);
    if($added){
        echo json_encode([
            "success" => true,
            "message" => "Calendar event added successfully.",
            "event" => $added
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to add calendar event."
    ]);
    exit;
}
elseif(isset($_POST["taskAction"]) && $_POST["taskAction"] == "deleteCalendarEvent"){
    $eventID = isset($_POST["eventID"]) ? (int)$_POST["eventID"] : 0;
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;

    $deleted = $taskManager->deleteCalendarEventFunc($eventID, $homeID);
    if($deleted){
        echo json_encode([
            "success" => true,
            "message" => "Calendar event deleted successfully."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to delete calendar event."
    ]);
    exit;
}
elseif(isset($_POST["taskAction"]) && $_POST["taskAction"] == "updateCalendarEventSchedule"){
    $eventID = isset($_POST["eventID"]) ? (int)$_POST["eventID"] : 0;
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $startDateTime = isset($_POST["startDateTime"]) ? $_POST["startDateTime"] : "";
    $endDateTime = isset($_POST["endDateTime"]) ? $_POST["endDateTime"] : "";
    $isAllDay = isset($_POST["isAllDay"]) ? (int)$_POST["isAllDay"] : 0;

    $updated = $taskManager->updateCalendarEventScheduleFunc($eventID, $homeID, $startDateTime, $endDateTime, $isAllDay);
    if($updated){
        echo json_encode([
            "success" => true,
            "message" => "Calendar event schedule updated."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to update calendar event schedule."
    ]);
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Invalid task action."
]);
exit;
?>
