<?php
require_once "../model/database.php";
require_once "../model/TaskModel.php";

class TaskManager
{
    private $taskModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connectDB();
        $this->taskModel = new TaskModel($db);
    }

    public function getTaskPageData($homeID)
    {
        $tasks = $this->taskModel->getTasksByHomeID($homeID);
        $events = $this->taskModel->getCalendarEventsByHomeID($homeID);

        return [
            "tasks" => $tasks,
            "events" => $events
        ];
    }

    public function addTaskFunc($homeID, $taskTitle, $description, $priorityLevel, $dueDate)
    {
        $cleanTitle = trim($taskTitle);
        $cleanDescription = trim($description);
        $cleanPriority = trim($priorityLevel);
        $status = "To Do";
        $assignedTo = null;

        if($cleanTitle == ""){
            return false;
        }

        if($cleanDescription == ""){
            $cleanDescription = "No description provided.";
        }

        if($cleanPriority != "High" && $cleanPriority != "Medium" && $cleanPriority != "Low"){
            $cleanPriority = "Medium";
        }

        if($dueDate == ""){
            $dueDate = null;
        }

        return $this->taskModel->addTask($homeID, $assignedTo, $cleanTitle, $cleanDescription, $cleanPriority, $status, $dueDate);
    }

    public function updateTaskStatusFunc($taskID, $homeID, $status)
    {
        if($status != "To Do" && $status != "In Progress" && $status != "Done"){
            return false;
        }
        return $this->taskModel->updateTaskStatus($taskID, $homeID, $status);
    }

    public function deleteTaskFunc($taskID, $homeID)
    {
        return $this->taskModel->deleteTask($taskID, $homeID);
    }

    public function addCalendarEventFunc($homeID, $eventTitle, $eventNote, $eventDate, $eventTime, $isAllDay, $eventColor)
    {
        $cleanTitle = trim($eventTitle);
        $cleanNote = trim($eventNote);
        $cleanColor = trim($eventColor);
        $createdByUserID = null;

        if($cleanTitle == "" || $eventDate == ""){
            return false;
        }

        if($eventTime == ""){
            $eventTime = "00:00:00";
        } elseif(strlen($eventTime) == 5){
            $eventTime = $eventTime . ":00";
        }

        $startDateTime = $eventDate . " " . $eventTime;
        $endDateTime = null;

        if($cleanColor == ""){
            $cleanColor = "#b3484e";
        }

        if($cleanNote == ""){
            $cleanNote = "No note provided.";
        }

        return $this->taskModel->addCalendarEvent(
            $homeID,
            $cleanTitle,
            $cleanNote,
            $eventDate,
            $eventTime,
            $startDateTime,
            $endDateTime,
            $isAllDay,
            $cleanColor,
            $createdByUserID
        );
    }

    public function deleteCalendarEventFunc($eventID, $homeID)
    {
        return $this->taskModel->deleteCalendarEvent($eventID, $homeID);
    }

    public function updateCalendarEventScheduleFunc($eventID, $homeID, $startDateTime, $endDateTime, $isAllDay)
    {
        if($startDateTime == ""){
            return false;
        }

        $startDateOnly = explode(" ", $startDateTime);
        $eventDate = $startDateOnly[0];
        $eventTime = "00:00:00";

        if(count($startDateOnly) > 1){
            $eventTime = $startDateOnly[1];
        }

        if($isAllDay == 1){
            $eventTime = "00:00:00";
        }

        return $this->taskModel->updateCalendarEventSchedule($eventID, $homeID, $eventDate, $eventTime, $startDateTime, $endDateTime, $isAllDay);
    }
}
?>
