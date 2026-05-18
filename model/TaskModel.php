<?php
class TaskModel
{
    private $conn;
    private $statusIsNumeric;
    private $statusLookupMeta;
    private $statusColumnType;

    public function __construct($db){
        $this->conn = $db;
        $this->statusIsNumeric = null;
        $this->statusLookupMeta = null;
        $this->statusColumnType = null;
    }

    private function isTaskStatusNumericFunc()
    {
        if($this->statusIsNumeric !== null){
            return $this->statusIsNumeric;
        }

        $columnInfo = $this->getTaskStatusColumnInfoFunc();

        if(!$columnInfo || !isset($columnInfo["Type"])){
            $this->statusIsNumeric = false;
            return $this->statusIsNumeric;
        }

        $statusType = strtolower((string)$columnInfo["Type"]);
        $this->statusIsNumeric = (strpos($statusType, "int") !== false);
        return $this->statusIsNumeric;
    }

    private function getTaskStatusColumnInfoFunc()
    {
        $columnInfoQuery = "SHOW COLUMNS FROM task_tbl LIKE 'status'";
        $columnInfoResponse = $this->conn->prepare($columnInfoQuery);
        $columnInfoResponse->execute();
        $columnInfo = $columnInfoResponse->fetch(PDO::FETCH_ASSOC);
        if($columnInfo && isset($columnInfo["Type"])){
            $this->statusColumnType = strtolower((string)$columnInfo["Type"]);
        }
        return $columnInfo;
    }

    private function normalizeStatusTokenFunc($value)
    {
        $normalized = strtolower((string)$value);
        $normalized = str_replace(["-", " "], "_", $normalized);
        return trim($normalized);
    }

    private function getStatusEnumValueFunc($statusLabel)
    {
        if($this->statusColumnType === null){
            $this->getTaskStatusColumnInfoFunc();
        }

        if($this->statusColumnType === null || strpos($this->statusColumnType, "enum(") !== 0){
            return $statusLabel;
        }

        preg_match_all("/'([^']*)'/", $this->statusColumnType, $matches);
        $enumValues = isset($matches[1]) ? $matches[1] : [];
        if(empty($enumValues)){
            return $statusLabel;
        }

        $target = $this->normalizeStatusTokenFunc($statusLabel);
        $aliasMap = [
            "to_do" => ["to_do", "todo", "pending", "open", "not_started"],
            "in_progress" => ["in_progress", "inprogress", "ongoing", "doing", "working"],
            "done" => ["done", "completed", "complete", "closed", "finished"]
        ];

        $targetAliases = [$target];
        if(isset($aliasMap[$target])){
            $targetAliases = $aliasMap[$target];
        }

        foreach($enumValues as $enumValue){
            $enumToken = $this->normalizeStatusTokenFunc($enumValue);
            if(in_array($enumToken, $targetAliases, true)){
                return $enumValue;
            }
        }

        return $enumValues[0];
    }

    private function getTaskStatusDbValueFunc($statusLabel)
    {
        if($this->isTaskStatusNumericFunc()){
            $resolvedStatusID = $this->resolveTaskStatusIDByLabelFunc($statusLabel);
            if($resolvedStatusID !== null){
                return $resolvedStatusID;
            }

            if($statusLabel == "In Progress"){
                return 2;
            }
            elseif($statusLabel == "Done"){
                return 3;
            }
            return 1;
        }

        return $this->getStatusEnumValueFunc($statusLabel);
    }

    private function getTaskStatusLookupMetaFunc()
    {
        if($this->statusLookupMeta !== null){
            return $this->statusLookupMeta;
        }

        $dbNameQuery = "SELECT DATABASE() AS dbName";
        $dbNameResponse = $this->conn->prepare($dbNameQuery);
        $dbNameResponse->execute();
        $dbNameRow = $dbNameResponse->fetch(PDO::FETCH_ASSOC);
        $dbName = $dbNameRow ? $dbNameRow["dbName"] : "";

        if($dbName == ""){
            $this->statusLookupMeta = false;
            return $this->statusLookupMeta;
        }

        $tableQuery = "SELECT table_name
                       FROM information_schema.tables
                       WHERE table_schema = :dbName
                         AND (
                            table_name LIKE 'task\\_status\\_lookup%'
                            OR table_name LIKE 'task\\_status%'
                         )
                       ORDER BY
                            CASE
                                WHEN table_name LIKE 'task\\_status\\_lookup%' THEN 1
                                ELSE 2
                            END,
                            table_name ASC
                       LIMIT 1";
        $tableResponse = $this->conn->prepare($tableQuery);
        $tableResponse->bindParam(":dbName", $dbName);
        $tableResponse->execute();
        $tableRow = $tableResponse->fetch(PDO::FETCH_ASSOC);

        if(!$tableRow || !isset($tableRow["table_name"])){
            $this->statusLookupMeta = false;
            return $this->statusLookupMeta;
        }

        $lookupTable = $tableRow["table_name"];
        $columnQuery = "SELECT column_name, data_type, column_key
                        FROM information_schema.columns
                        WHERE table_schema = :dbName
                          AND table_name = :tableName
                        ORDER BY ordinal_position ASC";
        $columnResponse = $this->conn->prepare($columnQuery);
        $columnResponse->bindParam(":dbName", $dbName);
        $columnResponse->bindParam(":tableName", $lookupTable);
        $columnResponse->execute();
        $columns = $columnResponse->fetchAll(PDO::FETCH_ASSOC);

        if(empty($columns)){
            $this->statusLookupMeta = false;
            return $this->statusLookupMeta;
        }

        $idColumn = "";
        $labelColumn = "";

        foreach($columns as $columnInfo){
            $columnName = $columnInfo["column_name"];
            $dataType = strtolower((string)$columnInfo["data_type"]);
            $columnKey = strtoupper((string)$columnInfo["column_key"]);

            if($idColumn == "" && $columnKey == "PRI"){
                $idColumn = $columnName;
            }

            if($labelColumn == "" && ($dataType == "varchar" || $dataType == "char" || $dataType == "text")){
                $lowerName = strtolower($columnName);
                if(strpos($lowerName, "name") !== false || strpos($lowerName, "status") !== false || strpos($lowerName, "label") !== false){
                    $labelColumn = $columnName;
                }
            }
        }

        if($idColumn == ""){
            foreach($columns as $columnInfo){
                $dataType = strtolower((string)$columnInfo["data_type"]);
                if(strpos($dataType, "int") !== false){
                    $idColumn = $columnInfo["column_name"];
                    break;
                }
            }
        }

        if($labelColumn == ""){
            foreach($columns as $columnInfo){
                $dataType = strtolower((string)$columnInfo["data_type"]);
                if($dataType == "varchar" || $dataType == "char" || $dataType == "text"){
                    $labelColumn = $columnInfo["column_name"];
                    break;
                }
            }
        }

        if($idColumn == "" || $labelColumn == ""){
            $this->statusLookupMeta = false;
            return $this->statusLookupMeta;
        }

        $this->statusLookupMeta = [
            "table" => $lookupTable,
            "idColumn" => $idColumn,
            "labelColumn" => $labelColumn
        ];
        return $this->statusLookupMeta;
    }

    private function resolveTaskStatusIDByLabelFunc($statusLabel)
    {
        $lookupMeta = $this->getTaskStatusLookupMetaFunc();
        if($lookupMeta === false){
            return null;
        }

        $lookupTable = $lookupMeta["table"];
        $idColumn = $lookupMeta["idColumn"];
        $labelColumn = $lookupMeta["labelColumn"];

        $synonyms = [$statusLabel];
        if($statusLabel == "To Do"){
            $synonyms[] = "Todo";
            $synonyms[] = "ToDo";
        }
        elseif($statusLabel == "In Progress"){
            $synonyms[] = "In-Progress";
            $synonyms[] = "InProgress";
            $synonyms[] = "Ongoing";
        }
        elseif($statusLabel == "Done"){
            $synonyms[] = "Completed";
            $synonyms[] = "Complete";
        }

        $statusIDQuery = "SELECT `" . $idColumn . "` AS statusID
                          FROM `" . $lookupTable . "`
                          WHERE LOWER(`" . $labelColumn . "`) = LOWER(:statusLabel)
                          LIMIT 1";
        $statusIDResponse = $this->conn->prepare($statusIDQuery);

        foreach($synonyms as $statusName){
            $statusIDResponse->bindValue(":statusLabel", $statusName);
            $statusIDResponse->execute();
            $statusIDRow = $statusIDResponse->fetch(PDO::FETCH_ASSOC);
            if($statusIDRow && isset($statusIDRow["statusID"])){
                return (int)$statusIDRow["statusID"];
            }
        }

        return null;
    }

    public function getTasksByHomeID($homeID)
    {
        $selectQuery = "SELECT t.taskID, t.homeID, t.assignedTo, t.taskTitle, t.description, t.priorityLevel, t.status, t.dueDate, t.createdAt, t.updatedAt,
                               t.status AS statusLabel
                        FROM task_tbl t
                        WHERE t.homeID = :homeID
                        ORDER BY
                            CASE t.status
                                WHEN 1 THEN 1
                                WHEN '1' THEN 1
                                WHEN 'To Do' THEN 1
                                WHEN 2 THEN 2
                                WHEN '2' THEN 2
                                WHEN 'In Progress' THEN 2
                                WHEN 3 THEN 3
                                WHEN '3' THEN 3
                                WHEN 'Done' THEN 3
                                ELSE 4
                            END ASC,
                            t.dueDate ASC,
                            t.taskID DESC";

        if($this->isTaskStatusNumericFunc()){
            $lookupMeta = $this->getTaskStatusLookupMetaFunc();
            if($lookupMeta !== false){
                $lookupTable = $lookupMeta["table"];
                $idColumn = $lookupMeta["idColumn"];
                $labelColumn = $lookupMeta["labelColumn"];

                $selectQuery = "SELECT t.taskID, t.homeID, t.assignedTo, t.taskTitle, t.description, t.priorityLevel, t.status, t.dueDate, t.createdAt, t.updatedAt,
                                       l.`" . $labelColumn . "` AS statusLabel
                                FROM task_tbl t
                                LEFT JOIN `" . $lookupTable . "` l ON t.status = l.`" . $idColumn . "`
                                WHERE t.homeID = :homeID
                                ORDER BY
                                    CASE LOWER(COALESCE(l.`" . $labelColumn . "`, ''))
                                        WHEN 'to do' THEN 1
                                        WHEN 'todo' THEN 1
                                        WHEN 'in progress' THEN 2
                                        WHEN 'in-progress' THEN 2
                                        WHEN 'done' THEN 3
                                        WHEN 'completed' THEN 3
                                        ELSE 4
                                    END ASC,
                                    t.dueDate ASC,
                                    t.taskID DESC";
            }
        }

        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTask($homeID, $assignedTo, $taskTitle, $description, $priorityLevel, $status, $dueDate)
    {
        $statusDbValue = $this->getTaskStatusDbValueFunc($status);
        $insertQuery = "INSERT INTO task_tbl(homeID, assignedTo, taskTitle, description, priorityLevel, status, dueDate, createdAt, updatedAt)
                        VALUES(:homeID, :assignedTo, :taskTitle, :description, :priorityLevel, :status, :dueDate, :createdAt, :updatedAt)";
        $response = $this->conn->prepare($insertQuery);
        $dateNow = date('Y-m-d H:i:s');

        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        if($assignedTo === null){
            $response->bindValue(":assignedTo", null, PDO::PARAM_NULL);
        }
        else{
            $response->bindValue(":assignedTo", $assignedTo, PDO::PARAM_INT);
        }
        $response->bindParam(":taskTitle", $taskTitle);
        $response->bindParam(":description", $description);
        $response->bindParam(":priorityLevel", $priorityLevel);
        $response->bindValue(":status", $statusDbValue);
        if($dueDate === null || $dueDate === ""){
            $response->bindValue(":dueDate", null, PDO::PARAM_NULL);
        }
        else{
            $response->bindValue(":dueDate", $dueDate);
        }
        $response->bindParam(":createdAt", $dateNow);
        $response->bindParam(":updatedAt", $dateNow);
        $created = $response->execute();
        if(!$created){
            return false;
        }

        $taskID = (int)$this->conn->lastInsertId();
        return $this->getTaskByID($taskID);
    }

    public function updateTaskStatus($taskID, $homeID, $status)
    {
        $statusDbValue = $this->getTaskStatusDbValueFunc($status);
        $updateQuery = "UPDATE task_tbl
                        SET status = :status,
                            updatedAt = :updatedAt
                        WHERE taskID = :taskID AND homeID = :homeID";
        $response = $this->conn->prepare($updateQuery);
        $dateNow = date('Y-m-d H:i:s');
        $response->bindValue(":status", $statusDbValue);
        $response->bindParam(":updatedAt", $dateNow);
        $response->bindParam(":taskID", $taskID, PDO::PARAM_INT);
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        $executed = $response->execute();

        if(!$executed){
            return false;
        }

        if($response->rowCount() > 0){
            return true;
        }

        // Fallback for rows with mismatched/legacy homeID values.
        $fallbackQuery = "UPDATE task_tbl
                          SET status = :status,
                              updatedAt = :updatedAt
                          WHERE taskID = :taskID";
        $fallbackResponse = $this->conn->prepare($fallbackQuery);
        $fallbackResponse->bindValue(":status", $statusDbValue);
        $fallbackResponse->bindParam(":updatedAt", $dateNow);
        $fallbackResponse->bindParam(":taskID", $taskID, PDO::PARAM_INT);
        $fallbackExecuted = $fallbackResponse->execute();
        if(!$fallbackExecuted){
            return false;
        }
        return $fallbackResponse->rowCount() > 0;
    }

    public function getTaskByID($taskID)
    {
        $selectQuery = "SELECT taskID, homeID, assignedTo, taskTitle, description, priorityLevel, status, dueDate, createdAt, updatedAt
                        FROM task_tbl
                        WHERE taskID = :taskID
                        LIMIT 1";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":taskID", $taskID, PDO::PARAM_INT);
        $response->execute();
        return $response->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteTask($taskID, $homeID)
    {
        $deleteQuery = "DELETE FROM task_tbl WHERE taskID = :taskID AND homeID = :homeID";
        $response = $this->conn->prepare($deleteQuery);
        $response->bindParam(":taskID", $taskID, PDO::PARAM_INT);
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        return $response->execute();
    }

    public function getCalendarEventsByHomeID($homeID)
    {
        $selectQuery = "SELECT eventID, homeID, taskID, eventTitle, eventNote, eventDate, eventTime, startDateTime, endDateTime, isAllDay, eventColor, createdByUserID, createdAt, updatedAt
                        FROM calendarpage_tbl
                        WHERE homeID = :homeID
                        ORDER BY COALESCE(startDateTime, createdAt) ASC, eventID ASC";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCalendarEvent($homeID, $eventTitle, $eventNote, $eventDate, $eventTime, $startDateTime, $endDateTime, $isAllDay, $eventColor, $createdByUserID)
    {
        $insertQuery = "INSERT INTO calendarpage_tbl(homeID, taskID, eventTitle, eventNote, eventDate, eventTime, startDateTime, endDateTime, isAllDay, eventColor, createdByUserID, createdAt, updatedAt)
                        VALUES(:homeID, :taskID, :eventTitle, :eventNote, :eventDate, :eventTime, :startDateTime, :endDateTime, :isAllDay, :eventColor, :createdByUserID, :createdAt, :updatedAt)";
        $response = $this->conn->prepare($insertQuery);
        $dateNow = date('Y-m-d H:i:s');
        $taskID = null;

        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        $response->bindValue(":taskID", null, PDO::PARAM_NULL);
        $response->bindParam(":eventTitle", $eventTitle);
        $response->bindParam(":eventNote", $eventNote);
        $response->bindParam(":eventDate", $eventDate);
        $response->bindParam(":eventTime", $eventTime);
        $response->bindParam(":startDateTime", $startDateTime);
        if($endDateTime === null || $endDateTime === ""){
            $response->bindValue(":endDateTime", null, PDO::PARAM_NULL);
        }
        else{
            $response->bindValue(":endDateTime", $endDateTime);
        }
        $response->bindParam(":isAllDay", $isAllDay, PDO::PARAM_INT);
        $response->bindParam(":eventColor", $eventColor);
        if($createdByUserID === null){
            $response->bindValue(":createdByUserID", null, PDO::PARAM_NULL);
        }
        else{
            $response->bindValue(":createdByUserID", $createdByUserID, PDO::PARAM_INT);
        }
        $response->bindParam(":createdAt", $dateNow);
        $response->bindParam(":updatedAt", $dateNow);
        $created = $response->execute();
        if(!$created){
            return false;
        }

        $eventID = (int)$this->conn->lastInsertId();
        return $this->getCalendarEventByID($eventID);
    }

    public function deleteCalendarEvent($eventID, $homeID)
    {
        $deleteQuery = "DELETE FROM calendarpage_tbl WHERE eventID = :eventID AND homeID = :homeID";
        $response = $this->conn->prepare($deleteQuery);
        $response->bindParam(":eventID", $eventID, PDO::PARAM_INT);
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        return $response->execute();
    }

    public function getCalendarEventByID($eventID)
    {
        $selectQuery = "SELECT eventID, homeID, taskID, eventTitle, eventNote, eventDate, eventTime, startDateTime, endDateTime, isAllDay, eventColor, createdByUserID, createdAt, updatedAt
                        FROM calendarpage_tbl
                        WHERE eventID = :eventID
                        LIMIT 1";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":eventID", $eventID, PDO::PARAM_INT);
        $response->execute();
        return $response->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCalendarEventSchedule($eventID, $homeID, $eventDate, $eventTime, $startDateTime, $endDateTime, $isAllDay)
    {
        $updateQuery = "UPDATE calendarpage_tbl
                        SET eventDate = :eventDate,
                            eventTime = :eventTime,
                            startDateTime = :startDateTime,
                            endDateTime = :endDateTime,
                            isAllDay = :isAllDay,
                            updatedAt = :updatedAt
                        WHERE eventID = :eventID AND homeID = :homeID";
        $response = $this->conn->prepare($updateQuery);
        $dateNow = date('Y-m-d H:i:s');

        $response->bindParam(":eventDate", $eventDate);
        $response->bindParam(":eventTime", $eventTime);
        $response->bindParam(":startDateTime", $startDateTime);
        if($endDateTime === null || $endDateTime === ""){
            $response->bindValue(":endDateTime", null, PDO::PARAM_NULL);
        }
        else{
            $response->bindValue(":endDateTime", $endDateTime);
        }
        $response->bindParam(":isAllDay", $isAllDay, PDO::PARAM_INT);
        $response->bindParam(":updatedAt", $dateNow);
        $response->bindParam(":eventID", $eventID, PDO::PARAM_INT);
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        return $response->execute();
    }
}
?>
