<?php
session_start();
header("Content-Type: application/json");
require_once "../bl/BudgetManager.php";

$budgetManager = new BudgetManager();

if(isset($_POST["budgetAction"]) && $_POST["budgetAction"] == "loadBudgetData"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $payload = $budgetManager->getBudgetPageData($homeID);
    echo json_encode([
        "success" => true,
        "data" => $payload
    ]);
    exit;
}
elseif(isset($_POST["budgetAction"]) && $_POST["budgetAction"] == "addBudgetTransaction"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $categoryID = isset($_POST["categoryID"]) ? (int)$_POST["categoryID"] : 0;
    $amount = isset($_POST["amount"]) ? (float)$_POST["amount"] : 0;
    $note = isset($_POST["note"]) ? $_POST["note"] : "";
    $transactionDate = isset($_POST["transactionDate"]) ? $_POST["transactionDate"] : "";

    if($categoryID <= 0 || $amount <= 0){
        echo json_encode([
            "success" => false,
            "message" => "Invalid transaction input."
        ]);
        exit;
    }

    $added = $budgetManager->addBudgetTransactionFunc($homeID, $categoryID, $amount, $note, $transactionDate);
    if($added){
        echo json_encode([
            "success" => true,
            "message" => "Transaction added successfully."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to add transaction."
    ]);
    exit;
}
elseif(isset($_POST["budgetAction"]) && $_POST["budgetAction"] == "updateBudgetCategory"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $categoryID = isset($_POST["categoryID"]) ? (int)$_POST["categoryID"] : 0;
    $allocatedAmount = isset($_POST["allocatedAmount"]) ? (float)$_POST["allocatedAmount"] : -1;

    $updated = $budgetManager->updateBudgetCategoryAllocationFunc($homeID, $categoryID, $allocatedAmount);
    if($updated){
        echo json_encode([
            "success" => true,
            "message" => "Category allocation updated."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to update category allocation."
    ]);
    exit;
}
elseif(isset($_POST["budgetAction"]) && $_POST["budgetAction"] == "updateMonthlyBudget"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $monthlyBudget = isset($_POST["monthlyBudget"]) ? (float)$_POST["monthlyBudget"] : -1;

    $updated = $budgetManager->updateMonthlyBudgetFunc($homeID, $monthlyBudget);
    if($updated){
        echo json_encode([
            "success" => true,
            "message" => "Monthly budget updated."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to update monthly budget."
    ]);
    exit;
}
elseif(isset($_POST["budgetAction"]) && $_POST["budgetAction"] == "addBudgetCategory"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $categoryName = isset($_POST["categoryName"]) ? $_POST["categoryName"] : "";

    $added = $budgetManager->addBudgetCategoryFunc($homeID, $categoryName);
    if($added){
        echo json_encode([
            "success" => true,
            "message" => "Category added successfully."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to add category. It may already exist or input is invalid."
    ]);
    exit;
}
elseif(isset($_POST["budgetAction"]) && $_POST["budgetAction"] == "addBudgetTopUp"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $amount = isset($_POST["amount"]) ? (float)$_POST["amount"] : 0;

    if($amount <= 0){
        echo json_encode([
            "success" => false,
            "message" => "Top-up amount must be greater than zero."
        ]);
        exit;
    }

    $added = $budgetManager->addBudgetTopUpFunc($homeID, $amount);
    if($added){
        echo json_encode([
            "success" => true,
            "message" => "Funds were added to your remaining balance."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to add funds."
    ]);
    exit;
}
elseif(isset($_POST["budgetAction"]) && $_POST["budgetAction"] == "updateSavingsGoal"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $savingsGoal = isset($_POST["savingsGoal"]) ? (float)$_POST["savingsGoal"] : -1;

    if($savingsGoal < 0){
        echo json_encode([
            "success" => false,
            "message" => "Savings goal must be 0 or higher."
        ]);
        exit;
    }

    $updated = $budgetManager->updateSavingsGoalFunc($homeID, $savingsGoal);
    if($updated){
        echo json_encode([
            "success" => true,
            "message" => "Savings goal updated successfully."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to update savings goal."
    ]);
    exit;
}
elseif(isset($_POST["budgetAction"]) && $_POST["budgetAction"] == "deleteBudgetCategory"){
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 1;
    $categoryID = isset($_POST["categoryID"]) ? (int)$_POST["categoryID"] : 0;

    $deleted = $budgetManager->deleteBudgetCategoryFunc($homeID, $categoryID);
    if($deleted){
        echo json_encode([
            "success" => true,
            "message" => "Category deleted successfully."
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Failed to delete category."
    ]);
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Invalid budget action."
]);
exit;
?>
