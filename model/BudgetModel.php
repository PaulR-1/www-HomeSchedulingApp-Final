<?php
class BudgetModel
{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function ensureBudgetSummary($homeID)
    {
        $selectQuery = "SELECT * FROM budget_summary_tbl WHERE homeID = :homeID LIMIT 1";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":homeID", $homeID);
        $response->execute();
        $budgetSummary = $response->fetch(PDO::FETCH_ASSOC);

        if($budgetSummary){
            return $budgetSummary;
        }

        $insertQuery = "INSERT INTO budget_summary_tbl(homeID, monthlyBudget, spentAmount, remainingBalance, savingsGoal, savingsProgress, createdAt, updatedAt)
                        VALUES(:homeID, :monthlyBudget, :spentAmount, :remainingBalance, :savingsGoal, :savingsProgress, :createdAt, :updatedAt)";
        $insertResponse = $this->conn->prepare($insertQuery);

        $monthlyBudget = 0;
        $spentAmount = 0;
        $remainingBalance = 0;
        $savingsGoal = 0;
        $savingsProgress = 0;
        $dateNow = date('Y-m-d H:i:s');

        $insertResponse->bindParam(":homeID", $homeID);
        $insertResponse->bindParam(":monthlyBudget", $monthlyBudget);
        $insertResponse->bindParam(":spentAmount", $spentAmount);
        $insertResponse->bindParam(":remainingBalance", $remainingBalance);
        $insertResponse->bindParam(":savingsGoal", $savingsGoal);
        $insertResponse->bindParam(":savingsProgress", $savingsProgress);
        $insertResponse->bindParam(":createdAt", $dateNow);
        $insertResponse->bindParam(":updatedAt", $dateNow);
        $insertResponse->execute();

        $selectInserted = $this->conn->prepare($selectQuery);
        $selectInserted->bindParam(":homeID", $homeID);
        $selectInserted->execute();
        return $selectInserted->fetch(PDO::FETCH_ASSOC);
    }

    public function ensureDefaultCategories($budgetID, $homeID)
    {
        $defaultCategories = [
            ["Groceries", "member"],
            ["Utilities", "admin"],
            ["Education", "member"],
            ["Maintenance", "admin"],
            ["Emergency", "admin"]
        ];

        foreach($defaultCategories as $category){
            $categoryName = $category[0];
            $roleScope = $category[1];

            $checkQuery = "SELECT categoryID FROM budget_category_tbl WHERE budgetID = :budgetID AND categoryName = :categoryName LIMIT 1";
            $checkResponse = $this->conn->prepare($checkQuery);
            $checkResponse->bindParam(":budgetID", $budgetID);
            $checkResponse->bindParam(":categoryName", $categoryName);
            $checkResponse->execute();

            if(!$checkResponse->fetch(PDO::FETCH_ASSOC)){
                $insertQuery = "INSERT INTO budget_category_tbl(budgetID, homeID, categoryName, allocatedAmount, spentAmount, roleScope, createdAt, updatedAt)
                                VALUES(:budgetID, :homeID, :categoryName, :allocatedAmount, :spentAmount, :roleScope, :createdAt, :updatedAt)";
                $insertResponse = $this->conn->prepare($insertQuery);
                $allocatedAmount = 0;
                $spentAmount = 0;
                $dateNow = date('Y-m-d H:i:s');

                $insertResponse->bindParam(":budgetID", $budgetID);
                $insertResponse->bindParam(":homeID", $homeID);
                $insertResponse->bindParam(":categoryName", $categoryName);
                $insertResponse->bindParam(":allocatedAmount", $allocatedAmount);
                $insertResponse->bindParam(":spentAmount", $spentAmount);
                $insertResponse->bindParam(":roleScope", $roleScope);
                $insertResponse->bindParam(":createdAt", $dateNow);
                $insertResponse->bindParam(":updatedAt", $dateNow);
                $insertResponse->execute();
            }
        }
    }

    public function getCategoriesByBudgetID($budgetID)
    {
        $selectQuery = "SELECT bc.categoryID, bc.categoryName, bc.allocatedAmount, bc.roleScope,
                            COALESCE(tx.totalSpent, 0) AS spentAmount
                        FROM budget_category_tbl bc
                        LEFT JOIN (
                            SELECT categoryID, SUM(amount) AS totalSpent
                            FROM budget_transaction_tbl
                            GROUP BY categoryID
                        ) tx ON tx.categoryID = bc.categoryID
                        WHERE bc.budgetID = :budgetID
                        ORDER BY bc.categoryID ASC";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":budgetID", $budgetID);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransactionsByBudgetID($budgetID, $limit = 10)
    {
        $selectQuery = "SELECT bt.transactionID, bt.amount, bt.note, bt.transactionDate,
                            bc.categoryName
                        FROM budget_transaction_tbl bt
                        LEFT JOIN budget_category_tbl bc ON bt.categoryID = bc.categoryID
                        WHERE bt.budgetID = :budgetID
                        ORDER BY bt.transactionDate DESC, bt.transactionID DESC
                        LIMIT :rowLimit";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":budgetID", $budgetID, PDO::PARAM_INT);
        $response->bindParam(":rowLimit", $limit, PDO::PARAM_INT);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function refreshBudgetTotals($budgetID)
    {
        $totalsQuery = "SELECT COALESCE(SUM(amount), 0) AS totalSpent
                        FROM budget_transaction_tbl
                        WHERE budgetID = :budgetID";
        $totalsResponse = $this->conn->prepare($totalsQuery);
        $totalsResponse->bindParam(":budgetID", $budgetID);
        $totalsResponse->execute();
        $totalsRow = $totalsResponse->fetch(PDO::FETCH_ASSOC);
        $spentAmount = (float)$totalsRow["totalSpent"];

        $summaryQuery = "SELECT monthlyBudget FROM budget_summary_tbl WHERE budgetID = :budgetID LIMIT 1";
        $summaryResponse = $this->conn->prepare($summaryQuery);
        $summaryResponse->bindParam(":budgetID", $budgetID);
        $summaryResponse->execute();
        $summaryRow = $summaryResponse->fetch(PDO::FETCH_ASSOC);
        $monthlyBudget = (float)$summaryRow["monthlyBudget"];
        $remainingBalance = $monthlyBudget - $spentAmount;
        $dateNow = date('Y-m-d H:i:s');

        $updateSummaryQuery = "UPDATE budget_summary_tbl
                               SET spentAmount = :spentAmount,
                                   remainingBalance = :remainingBalance,
                                   updatedAt = :updatedAt
                               WHERE budgetID = :budgetID";
        $updateSummaryResponse = $this->conn->prepare($updateSummaryQuery);
        $updateSummaryResponse->bindParam(":spentAmount", $spentAmount);
        $updateSummaryResponse->bindParam(":remainingBalance", $remainingBalance);
        $updateSummaryResponse->bindParam(":updatedAt", $dateNow);
        $updateSummaryResponse->bindParam(":budgetID", $budgetID);
        $updateSummaryResponse->execute();

        $updateCategoryQuery = "UPDATE budget_category_tbl bc
                                LEFT JOIN (
                                    SELECT categoryID, COALESCE(SUM(amount), 0) AS totalSpent
                                    FROM budget_transaction_tbl
                                    GROUP BY categoryID
                                ) tx ON tx.categoryID = bc.categoryID
                                SET bc.spentAmount = COALESCE(tx.totalSpent, 0),
                                    bc.updatedAt = :updatedAt
                                WHERE bc.budgetID = :budgetID";
        $updateCategoryResponse = $this->conn->prepare($updateCategoryQuery);
        $updateCategoryResponse->bindParam(":updatedAt", $dateNow);
        $updateCategoryResponse->bindParam(":budgetID", $budgetID);
        $updateCategoryResponse->execute();
    }

    public function addTransaction($budgetID, $homeID, $categoryID, $amount, $note, $transactionDate, $addedByUserID = null)
    {
        $insertQuery = "INSERT INTO budget_transaction_tbl(budgetID, homeID, categoryID, addedByUserID, amount, note, transactionDate, createdAt, updatedAt)
                        VALUES(:budgetID, :homeID, :categoryID, :addedByUserID, :amount, :note, :transactionDate, :createdAt, :updatedAt)";
        $response = $this->conn->prepare($insertQuery);
        $dateNow = date('Y-m-d H:i:s');

        $response->bindParam(":budgetID", $budgetID);
        $response->bindParam(":homeID", $homeID);
        $response->bindParam(":categoryID", $categoryID);
        $response->bindParam(":addedByUserID", $addedByUserID);
        $response->bindParam(":amount", $amount);
        $response->bindParam(":note", $note);
        $response->bindParam(":transactionDate", $transactionDate);
        $response->bindParam(":createdAt", $dateNow);
        $response->bindParam(":updatedAt", $dateNow);
        return $response->execute();
    }

    public function updateCategoryAllocation($budgetID, $categoryID, $allocatedAmount)
    {
        $updateQuery = "UPDATE budget_category_tbl
                        SET allocatedAmount = :allocatedAmount,
                            updatedAt = :updatedAt
                        WHERE budgetID = :budgetID AND categoryID = :categoryID";
        $response = $this->conn->prepare($updateQuery);
        $dateNow = date('Y-m-d H:i:s');
        $response->bindParam(":allocatedAmount", $allocatedAmount);
        $response->bindParam(":updatedAt", $dateNow);
        $response->bindParam(":budgetID", $budgetID);
        $response->bindParam(":categoryID", $categoryID);
        return $response->execute();
    }

    public function updateMonthlyBudget($budgetID, $monthlyBudget)
    {
        $updateQuery = "UPDATE budget_summary_tbl
                        SET monthlyBudget = :monthlyBudget,
                            updatedAt = :updatedAt
                        WHERE budgetID = :budgetID";
        $response = $this->conn->prepare($updateQuery);
        $dateNow = date('Y-m-d H:i:s');
        $response->bindParam(":monthlyBudget", $monthlyBudget);
        $response->bindParam(":updatedAt", $dateNow);
        $response->bindParam(":budgetID", $budgetID);
        return $response->execute();
    }

    public function incrementMonthlyBudget($budgetID, $amount)
    {
        $updateQuery = "UPDATE budget_summary_tbl
                        SET monthlyBudget = monthlyBudget + :amount,
                            updatedAt = :updatedAt
                        WHERE budgetID = :budgetID";
        $response = $this->conn->prepare($updateQuery);
        $dateNow = date('Y-m-d H:i:s');
        $response->bindParam(":amount", $amount);
        $response->bindParam(":updatedAt", $dateNow);
        $response->bindParam(":budgetID", $budgetID);
        return $response->execute();
    }

    public function updateSavingsGoal($budgetID, $savingsGoal)
    {
        $summaryQuery = "SELECT remainingBalance FROM budget_summary_tbl WHERE budgetID = :budgetID LIMIT 1";
        $summaryResponse = $this->conn->prepare($summaryQuery);
        $summaryResponse->bindParam(":budgetID", $budgetID);
        $summaryResponse->execute();
        $summaryRow = $summaryResponse->fetch(PDO::FETCH_ASSOC);
        $remainingBalance = $summaryRow ? (float)$summaryRow["remainingBalance"] : 0;

        $computedProgress = 0;
        if($savingsGoal > 0){
            $computedProgress = ($remainingBalance / $savingsGoal) * 100;
            if($computedProgress > 100){
                $computedProgress = 100;
            }
            if($computedProgress < 0){
                $computedProgress = 0;
            }
        }

        $updateQuery = "UPDATE budget_summary_tbl
                        SET savingsGoal = :savingsGoal,
                            savingsProgress = :savingsProgress,
                            updatedAt = :updatedAt
                        WHERE budgetID = :budgetID";
        $response = $this->conn->prepare($updateQuery);
        $dateNow = date('Y-m-d H:i:s');
        $response->bindParam(":savingsGoal", $savingsGoal);
        $response->bindParam(":savingsProgress", $computedProgress);
        $response->bindParam(":updatedAt", $dateNow);
        $response->bindParam(":budgetID", $budgetID);
        return $response->execute();
    }

    public function addCategory($budgetID, $homeID, $categoryName)
    {
        $checkQuery = "SELECT categoryID FROM budget_category_tbl WHERE budgetID = :budgetID AND categoryName = :categoryName LIMIT 1";
        $checkResponse = $this->conn->prepare($checkQuery);
        $checkResponse->bindParam(":budgetID", $budgetID);
        $checkResponse->bindParam(":categoryName", $categoryName);
        $checkResponse->execute();
        if($checkResponse->fetch(PDO::FETCH_ASSOC)){
            return false;
        }

        $insertQuery = "INSERT INTO budget_category_tbl(budgetID, homeID, categoryName, allocatedAmount, spentAmount, roleScope, createdAt, updatedAt)
                        VALUES(:budgetID, :homeID, :categoryName, :allocatedAmount, :spentAmount, :roleScope, :createdAt, :updatedAt)";
        $response = $this->conn->prepare($insertQuery);
        $allocatedAmount = 0;
        $spentAmount = 0;
        $roleScope = "member";
        $dateNow = date('Y-m-d H:i:s');

        $response->bindParam(":budgetID", $budgetID);
        $response->bindParam(":homeID", $homeID);
        $response->bindParam(":categoryName", $categoryName);
        $response->bindParam(":allocatedAmount", $allocatedAmount);
        $response->bindParam(":spentAmount", $spentAmount);
        $response->bindParam(":roleScope", $roleScope);
        $response->bindParam(":createdAt", $dateNow);
        $response->bindParam(":updatedAt", $dateNow);
        return $response->execute();
    }

    public function deleteCategory($budgetID, $categoryID)
    {
        $deleteTxQuery = "DELETE FROM budget_transaction_tbl WHERE budgetID = :budgetID AND categoryID = :categoryID";
        $deleteTxResponse = $this->conn->prepare($deleteTxQuery);
        $deleteTxResponse->bindParam(":budgetID", $budgetID);
        $deleteTxResponse->bindParam(":categoryID", $categoryID);
        $deleteTxResponse->execute();

        $deleteCategoryQuery = "DELETE FROM budget_category_tbl WHERE budgetID = :budgetID AND categoryID = :categoryID";
        $deleteCategoryResponse = $this->conn->prepare($deleteCategoryQuery);
        $deleteCategoryResponse->bindParam(":budgetID", $budgetID);
        $deleteCategoryResponse->bindParam(":categoryID", $categoryID);
        return $deleteCategoryResponse->execute();
    }
}
?>
