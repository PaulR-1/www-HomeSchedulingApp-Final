<?php
require_once "../model/database.php";
require_once "../model/BudgetModel.php";

class BudgetManager
{
    private $budgetModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connectDB();
        $this->budgetModel = new BudgetModel($db);
    }

    public function getBudgetPageData($homeID)
    {
        $budgetSummary = $this->budgetModel->ensureBudgetSummary($homeID);
        $budgetID = (int)$budgetSummary["budgetID"];
        $this->budgetModel->ensureDefaultCategories($budgetID, $homeID);
        $this->budgetModel->refreshBudgetTotals($budgetID);

        $budgetSummary = $this->budgetModel->ensureBudgetSummary($homeID);
        $categories = $this->budgetModel->getCategoriesByBudgetID($budgetID);
        $transactions = $this->budgetModel->getTransactionsByBudgetID($budgetID, 8);

        return [
            "summary" => $budgetSummary,
            "categories" => $categories,
            "transactions" => $transactions
        ];
    }

    public function addBudgetTransactionFunc($homeID, $categoryID, $amount, $note, $transactionDate)
    {
        $budgetSummary = $this->budgetModel->ensureBudgetSummary($homeID);
        $budgetID = (int)$budgetSummary["budgetID"];
        $this->budgetModel->ensureDefaultCategories($budgetID, $homeID);

        $amount = (float)$amount;
        if($amount <= 0){
            return false;
        }

        $cleanNote = trim($note);
        if($cleanNote == ""){
            $cleanNote = "No note provided";
        }

        if($transactionDate == ""){
            $transactionDate = date('Y-m-d');
        }

        $created = $this->budgetModel->addTransaction(
            $budgetID,
            $homeID,
            $categoryID,
            $amount,
            $cleanNote,
            $transactionDate,
            null
        );

        if($created){
            $this->budgetModel->refreshBudgetTotals($budgetID);
            return true;
        }

        return false;
    }

    public function updateBudgetCategoryAllocationFunc($homeID, $categoryID, $allocatedAmount)
    {
        $budgetSummary = $this->budgetModel->ensureBudgetSummary($homeID);
        $budgetID = (int)$budgetSummary["budgetID"];
        $this->budgetModel->ensureDefaultCategories($budgetID, $homeID);

        $allocatedAmount = (float)$allocatedAmount;
        if($allocatedAmount < 0){
            return false;
        }

        return $this->budgetModel->updateCategoryAllocation($budgetID, $categoryID, $allocatedAmount);
    }

    public function updateMonthlyBudgetFunc($homeID, $monthlyBudget)
    {
        $budgetSummary = $this->budgetModel->ensureBudgetSummary($homeID);
        $budgetID = (int)$budgetSummary["budgetID"];
        $monthlyBudget = (float)$monthlyBudget;

        if($monthlyBudget < 0){
            return false;
        }

        $updated = $this->budgetModel->updateMonthlyBudget($budgetID, $monthlyBudget);
        if($updated){
            $this->budgetModel->refreshBudgetTotals($budgetID);
            return true;
        }
        return false;
    }

    public function addBudgetCategoryFunc($homeID, $categoryName)
    {
        $budgetSummary = $this->budgetModel->ensureBudgetSummary($homeID);
        $budgetID = (int)$budgetSummary["budgetID"];
        $this->budgetModel->ensureDefaultCategories($budgetID, $homeID);

        $cleanName = trim($categoryName);
        if($cleanName == ""){
            return false;
        }

        $created = $this->budgetModel->addCategory($budgetID, $homeID, $cleanName);
        if($created){
            $this->budgetModel->refreshBudgetTotals($budgetID);
            return true;
        }

        return false;
    }

    public function addBudgetTopUpFunc($homeID, $amount)
    {
        $budgetSummary = $this->budgetModel->ensureBudgetSummary($homeID);
        $budgetID = (int)$budgetSummary["budgetID"];
        $amount = (float)$amount;

        if($amount <= 0){
            return false;
        }

        $updated = $this->budgetModel->incrementMonthlyBudget($budgetID, $amount);
        if($updated){
            $this->budgetModel->refreshBudgetTotals($budgetID);
            return true;
        }

        return false;
    }

    public function updateSavingsGoalFunc($homeID, $savingsGoal)
    {
        $budgetSummary = $this->budgetModel->ensureBudgetSummary($homeID);
        $budgetID = (int)$budgetSummary["budgetID"];
        $savingsGoal = (float)$savingsGoal;

        if($savingsGoal < 0){
            return false;
        }

        $updated = $this->budgetModel->updateSavingsGoal($budgetID, $savingsGoal);
        if($updated){
            return true;
        }

        return false;
    }

    public function deleteBudgetCategoryFunc($homeID, $categoryID)
    {
        $budgetSummary = $this->budgetModel->ensureBudgetSummary($homeID);
        $budgetID = (int)$budgetSummary["budgetID"];
        $this->budgetModel->ensureDefaultCategories($budgetID, $homeID);

        if($categoryID <= 0){
            return false;
        }

        $deleted = $this->budgetModel->deleteCategory($budgetID, $categoryID);
        if($deleted){
            $this->budgetModel->refreshBudgetTotals($budgetID);
            return true;
        }
        return false;
    }
}
?>
