<?php
session_start();
require_once "../../../model/database.php";
require_once "../../../model/BudgetModel.php";

$homePagePrefix = "GreenHome";
$homeDisplayName = "Green Home";
$homeBodyClass = "dashboard-red dashboard-green";
$homeBudgetBarAllocatedColor = "#88b98f";
$homeBudgetBarSpentColor = "#5f9467";
$homeBudgetPieColors = ["#5f9467", "#75a77c", "#8cba92", "#a3cca8", "#b9debe"];

$homeID = isset($_SESSION["homeID"]) ? (int)$_SESSION["homeID"] : 1;
$currentRoleName = isset($_SESSION["roleName"]) ? trim((string)$_SESSION["roleName"]) : "";
if(strtolower($currentRoleName) == "member"){
    header("Location: ./" . $homePagePrefix . "Home.php");
    exit;
}
$db = new Database();
$conn = $db->connectDB();
$budgetModel = new BudgetModel($conn);

$budgetSummary = $budgetModel->ensureBudgetSummary($homeID);
$budgetID = (int)$budgetSummary["budgetID"];
$budgetModel->ensureDefaultCategories($budgetID, $homeID);
$budgetModel->refreshBudgetTotals($budgetID);
$budgetSummary = $budgetModel->ensureBudgetSummary($homeID);
$budgetCategories = $budgetModel->getCategoriesByBudgetID($budgetID);
$budgetTransactions = $budgetModel->getTransactionsByBudgetID($budgetID, 8);

$monthlyBudget = (float)$budgetSummary["monthlyBudget"];
$spentAmount = (float)$budgetSummary["spentAmount"];
$remainingBalance = (float)$budgetSummary["remainingBalance"];
$savingsGoal = (float)$budgetSummary["savingsGoal"];
$savingsProgress = (float)$budgetSummary["savingsProgress"];

$chartLabels = [];
$chartAllocated = [];
$chartSpent = [];
foreach($budgetCategories as $category){
    $chartLabels[] = $category["categoryName"];
    $chartAllocated[] = (float)$category["allocatedAmount"];
    $chartSpent[] = (float)$category["spentAmount"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>HomePlanner - <?= htmlspecialchars($homeDisplayName) ?> Budget</title>
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
                        <a href="./<?= htmlspecialchars($homePagePrefix) ?>Budget.php" class="sidebar-link sidebar-link-active">Budget</a>
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
                </div>

            </div>

            <div class="dashboard-main">
                <div class="red-top-nav">
                    <a href="./<?= htmlspecialchars($homePagePrefix) ?>Home.php" class="red-top-link">Home</a>
                    <a href="../../RegistrationPage.php" class="red-top-link red-top-link-logout">Logout</a>
                </div>
                <div class="red-budget-page">
                    <section class="red-budget-section red-budget-hero-section">
                        <div class="red-home-eyebrow">Budget Workspace</div>
                        <h1 class="home-title"><?= htmlspecialchars($homeDisplayName) ?> Budget Management</h1>
                        <p class="home-desc">
                            Track allocation, monitor remaining balance, and prepare transaction entries in one centralized budget flow.
                        </p>
                    </section>

                    <section class="red-budget-section red-budget-summary-section" id="budget-overall">
                        <div class="red-budget-allocation-head">
                            <h2 class="section-heading section-heading-left">Overall Budget</h2>
                            <button type="button" class="red-budget-edit-btn" onclick="showMonthlyBudgetEditFunc(<?= $monthlyBudget ?>, <?= $savingsGoal ?>)">Edit Budget</button>
                        </div>
                        <div class="red-budget-summary-grid">
                            <div class="red-budget-summary-card">
                                <div class="red-budget-summary-head">
                                    <div class="red-budget-summary-label">Monthly Budget</div>
                                </div>
                                <div class="red-budget-summary-value" id="monthlyBudgetValueDisplay">₱<?= number_format($monthlyBudget, 2) ?></div>
                                <div class="red-budget-summary-note">Total monthly allocation for <?= htmlspecialchars($homeDisplayName) ?> operations.</div>
                                <div class="red-budget-inline-edit" id="monthlyBudgetInlineEdit" style="display:none;">
                                    <div class="input-field red-budget-inline-input-wrap">
                                        <input id="monthlyBudgetInputField" type="number" step="0.01" min="0" class="validate">
                                        <label for="monthlyBudgetInputField" class="active">Monthly Budget Amount</label>
                                    </div>
                                    <div class="red-budget-inline-actions">
                                        <button type="button" class="red-budget-card-edit-btn" onclick="saveMonthlyBudgetEditFunc(<?= $homeID ?>)">Save</button>
                                        <button type="button" class="red-budget-card-edit-btn" onclick="cancelMonthlyBudgetEditFunc()">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <div class="red-budget-summary-card">
                                <div class="red-budget-summary-head">
                                    <div class="red-budget-summary-label">Spent This Month</div>
                                </div>
                                <div class="red-budget-summary-value">₱<?= number_format($spentAmount, 2) ?></div>
                                <div class="red-budget-summary-note">Registered outgoing expenses from all categories.</div>
                            </div>

                            <div class="red-budget-summary-card">
                                <div class="red-budget-summary-head">
                                    <div class="red-budget-summary-label">Remaining Balance</div>
                                </div>
                                <div class="red-budget-summary-value">₱<?= number_format($remainingBalance, 2) ?></div>
                                <div class="red-budget-summary-note">Automatically reduced as transactions are added.</div>
                                <div class="red-budget-inline-edit" id="remainingBalanceInlineEdit" style="display:none;">
                                    <div class="input-field red-budget-inline-input-wrap">
                                        <input id="remainingBalanceInputField" type="number" step="0.01" min="0.01" class="validate">
                                        <label for="remainingBalanceInputField" class="active">Add Amount to Remaining Balance</label>
                                    </div>
                                    <div class="red-budget-inline-actions">
                                        <button type="button" class="red-budget-card-edit-btn" onclick="saveRemainingBalanceEditFunc(<?= $homeID ?>)">Save</button>
                                        <button type="button" class="red-budget-card-edit-btn" onclick="cancelMonthlyBudgetEditFunc()">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <div class="red-budget-summary-card">
                                <div class="red-budget-summary-head">
                                    <div class="red-budget-summary-label">Savings Goal Progress</div>
                                </div>
                                <div class="red-budget-summary-value">
                                    <?php
                                        if($savingsGoal > 0){
                                            $computedProgress = ($remainingBalance / $savingsGoal) * 100;
                                            if($computedProgress > 100){
                                                $computedProgress = 100;
                                            }
                                            echo number_format($computedProgress, 2) . "%";
                                        }
                                        else{
                                            echo number_format($savingsProgress, 2) . "%";
                                        }
                                    ?>
                                </div>
                                <div class="red-budget-summary-note">Current savings goal: ₱<?= number_format($savingsGoal, 2) ?></div>
                                <div class="red-budget-summary-note">Progress toward the current monthly savings target.</div>
                                <div class="red-budget-inline-edit" id="savingsGoalInlineEdit" style="display:none;">
                                    <div class="input-field red-budget-inline-input-wrap">
                                        <input id="savingsGoalInputField" type="number" step="0.01" min="0" class="validate">
                                        <label for="savingsGoalInputField" class="active">Savings Goal Amount</label>
                                    </div>
                                    <div class="red-budget-inline-actions">
                                        <button type="button" class="red-budget-card-edit-btn" onclick="saveSavingsGoalEditFunc(<?= $homeID ?>)">Save</button>
                                        <button type="button" class="red-budget-card-edit-btn" onclick="cancelMonthlyBudgetEditFunc()">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="red-budget-section red-budget-allocation-section" id="budget-allocation">
                        <div class="red-budget-allocation-head">
                            <h2 class="section-heading section-heading-left">Budget Allocation by Category</h2>
                            <button type="button" class="red-budget-edit-btn" onclick="toggleBudgetAllocationManagerFunc()">Edit Allocation</button>
                        </div>
                        <div class="red-budget-allocation-grid">
                            <?php if(!empty($budgetCategories)): ?>
                                <?php foreach($budgetCategories as $category): ?>
                                    <div class="red-budget-allocation-card">
                                        <div class="red-budget-allocation-top">
                                            <div class="red-budget-allocation-name"><?= htmlspecialchars($category["categoryName"]) ?></div>
                                        </div>
                                        <div class="red-budget-allocation-row">
                                            <span>Allocated</span><strong>₱<?= number_format((float)$category["allocatedAmount"], 2) ?></strong>
                                        </div>
                                        <div class="red-budget-allocation-row">
                                            <span>Spent</span><strong>₱<?= number_format((float)$category["spentAmount"], 2) ?></strong>
                                        </div>
                                        <button
                                            type="button"
                                            class="red-budget-card-edit-btn"
                                            onclick="updateBudgetCategoryFunc(<?= $homeID ?>, <?= (int)$category["categoryID"] ?>, '<?= htmlspecialchars($category["categoryName"], ENT_QUOTES) ?>', <?= (float)$category["allocatedAmount"] ?>)"
                                        >
                                            Edit
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="red-budget-allocation-card">
                                    <div class="red-budget-allocation-name">No categories available yet.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="red-budget-category-manager" id="budgetCategoryManager" style="display:none;">
                            <div class="red-budget-form-title">Category Manager</div>
                            <div class="red-budget-category-controls">
                                <input id="newBudgetCategoryName" type="text" class="red-budget-input" placeholder="New category name">
                                <button type="button" class="red-budget-submit-btn" onclick="addBudgetCategoryFunc(<?= $homeID ?>)">Add Category</button>
                            </div>
                            <div class="red-budget-category-controls">
                                <select id="deleteBudgetCategorySelect" class="red-budget-input browser-default">
                                    <option value="">Select category to delete</option>
                                    <?php foreach($budgetCategories as $category): ?>
                                        <option value="<?= (int)$category["categoryID"] ?>"><?= htmlspecialchars($category["categoryName"]) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="red-budget-card-edit-btn" onclick="deleteBudgetCategoryFunc(<?= $homeID ?>)">Delete Category</button>
                            </div>
                        </div>
                    </section>

                    <section class="red-budget-section red-budget-chart-section">
                        <h2 class="section-heading section-heading-left">Budget Charts</h2>
                        <div class="red-budget-chart-grid">
                            <div class="red-budget-chart-card">
                                <div class="red-budget-form-title">Allocated vs Spent by Category</div>
                                <canvas id="budgetBarChart"></canvas>
                            </div>
                            <div class="red-budget-chart-card">
                                <div class="red-budget-form-title">Spending Distribution</div>
                                <canvas id="budgetPieChart"></canvas>
                            </div>
                        </div>
                    </section>

                    <section class="red-budget-section red-budget-ledger-section" id="budget-transactions">
                        <h2 class="section-heading section-heading-left">Add Expense and Transaction List</h2>
                        <div class="red-budget-ledger-note">
                            UI behavior target: adding a transaction deducts directly from the overall budget balance.
                        </div>

                        <div class="red-budget-ledger-layout">
                            <div class="red-budget-expense-form">
                                <div class="red-budget-form-title">New Transaction Entry</div>
                                <div class="red-budget-form-grid">
                                    <input id="budgetAmount" type="number" step="0.01" min="0" class="red-budget-input red-budget-input-amount" placeholder="Amount (₱)">
                                    <select id="budgetCategory" class="red-budget-input red-budget-input-category browser-default">
                                        <option value="">Category</option>
                                        <?php foreach($budgetCategories as $category): ?>
                                            <option value="<?= (int)$category["categoryID"] ?>"><?= htmlspecialchars($category["categoryName"]) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input id="budgetDate" type="date" class="red-budget-input red-budget-input-date">
                                    <input id="budgetNote" type="text" class="red-budget-input red-budget-input-note" placeholder="Short note">
                                    <button type="button" class="red-budget-submit-btn red-budget-submit-btn-main" onclick="addBudgetTransactionFunc(<?= $homeID ?>)">Add Transaction</button>
                                </div>
                            </div>

                            <div class="red-budget-transaction-list">
                                <div class="red-budget-form-title">Latest Transactions</div>
                                <?php if(!empty($budgetTransactions)): ?>
                                    <?php foreach($budgetTransactions as $transaction): ?>
                                        <div class="red-budget-transaction-item">
                                            <div>
                                                <div class="red-budget-transaction-name"><?= htmlspecialchars($transaction["note"]) ?></div>
                                                <div class="red-budget-transaction-meta">
                                                    <?= htmlspecialchars($transaction["categoryName"] ?? "Uncategorized") ?> • <?= htmlspecialchars($transaction["transactionDate"]) ?>
                                                </div>
                                            </div>
                                            <div class="red-budget-transaction-amount">- ₱<?= number_format((float)$transaction["amount"], 2) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="red-budget-transaction-item">
                                        <div>
                                            <div class="red-budget-transaction-name">No transactions yet.</div>
                                            <div class="red-budget-transaction-meta">Add your first expense to begin tracking.</div>
                                        </div>
                                    </div>
                                <?php endif; ?>
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
    <script>
        window.budgetAjaxUrl = "../../../Controllers/BudgetController.php";
        window.budgetChartData = {
            labels: <?= json_encode($chartLabels) ?>,
            allocated: <?= json_encode($chartAllocated) ?>,
            spent: <?= json_encode($chartSpent) ?>
        };
    </script>
    <script src="../../../scripts/Service.js?v=<?= filemtime("../../../scripts/Service.js") ?>"></script>
    <script>
        const budgetLabels = window.budgetChartData.labels || [];
        const budgetAllocated = window.budgetChartData.allocated || [];
        const budgetSpent = window.budgetChartData.spent || [];

        const budgetBarChartCtx = document.getElementById("budgetBarChart");
        if (budgetBarChartCtx && typeof Chart !== "undefined") {
            new Chart(budgetBarChartCtx, {
                type: "bar",
                data: {
                    labels: budgetLabels,
                    datasets: [
                        {
                            label: "Allocated",
                            data: budgetAllocated,
                            backgroundColor: <?= json_encode($homeBudgetBarAllocatedColor) ?>
                        },
                        {
                            label: "Spent",
                            data: budgetSpent,
                            backgroundColor: <?= json_encode($homeBudgetBarSpentColor) ?>
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        const budgetPieChartCtx = document.getElementById("budgetPieChart");
        if (budgetPieChartCtx && typeof Chart !== "undefined") {
            new Chart(budgetPieChartCtx, {
                type: "pie",
                data: {
                    labels: budgetLabels,
                    datasets: [{
                        data: budgetSpent,
                        backgroundColor: <?= json_encode($homeBudgetPieColors) ?>,
                        borderColor: "#ffffff",
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom"
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
