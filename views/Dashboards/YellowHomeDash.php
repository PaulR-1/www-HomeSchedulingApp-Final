<?php
// Your existing PHP logic remains at the top
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePlanner - Yellow Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-page dashboard-yellow">
    <div class="topbar">
        <div class="brand-side">
            <img src="../../images/logo2.png" alt="HomePlanner Logo" class="brand-logo">
            <div class="brand-name">HomePlanner</div>
        </div>
        <a href="../RegistrationPage.php" class="return-btn">Return</a>
    </div>

    <div class="page-wrap">
        <div class="dashboard-grid">
            <div class="left-column">
                <div class="panel-card home-card">
                    <div class="panel-inner">
                        <div class="home-title">Yellow Home Dashboard</div>
                        <div class="home-desc">A bright and active household centered on errands, routines, and family coordination.</div>
                    </div>
                </div>

                <div class="panel-card residents-card">
                    <div class="panel-inner">
                        <div class="section-heading">Residents</div>
                        <div class="resident-list">
                            <div class="resident-item"><div class="resident-name">Carlos Mendoza</div><div class="resident-role">Home Head • Yellow Home</div></div>
                            <div class="resident-item"><div class="resident-name">Leah Mendoza</div><div class="resident-role">Shopping Planner • Yellow Home</div></div>
                            <div class="resident-item"><div class="resident-name">Ava Mendoza</div><div class="resident-role">Student • Yellow Home</div></div>
                            <div class="resident-item"><div class="resident-name">Jacob Mendoza</div><div class="resident-role">Event Helper • Yellow Home</div></div>
                            <div class="resident-item"><div class="resident-name">Ella Mendoza</div><div class="resident-role">Household Support • Yellow Home</div></div>
                        </div>
                    </div>
                </div>

                <div class="panel-card todo-card">
                    <div class="panel-inner">
                        <div class="section-heading">To Do list</div>
                        <div class="todo-list">
                            <div class="todo-item"><span class="todo-dot"></span><span>Buy fresh produce for the weekend meals.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Prepare supplies for the family outing.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Check remaining household cleaning products.</span></div>
                            <!-- <div class="todo-item"><span class="todo-dot"></span><span>Update the grocery checklist on the kitchen board.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Arrange the dining table decor for Sunday lunch.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Sort pantry items by expiration date.</span></div> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-column">
                <div class="calendar-card">
                    <div class="calendar-content">
                        <div class="calendar-title">Home Calendar</div>
                        <div class="calendar-events">
                            <div class="event-chip"><div class="event-time">Today • 9:00 AM</div><div class="event-name">Market Trip</div><div class="event-note">Buy vegetables, fruits, and weekly meal ingredients.</div></div>
                            <div class="event-chip"><div class="event-time">Today • 7:00 PM</div><div class="event-name">Dinner Prep</div><div class="event-note">Finalize ingredients and table setup.</div></div>
                            <div class="event-chip"><div class="event-time">Thursday • 2:00 PM</div><div class="event-name">Pantry Check</div><div class="event-note">Review cereals, canned goods, and snacks.</div></div>
                            <div class="event-chip"><div class="event-time">Friday • 5:30 PM</div><div class="event-name">Budget Review</div><div class="event-note">Track family spending and remaining grocery funds.</div></div>
                            <div class="event-chip"><div class="event-time">Saturday • 11:00 AM</div><div class="event-name">Home Refresh</div><div class="event-note">Sweep, wipe surfaces, and prepare guest areas.</div></div>
                            <div class="event-chip"><div class="event-time">Sunday • 12:00 PM</div><div class="event-name">Family Lunch</div><div class="event-note">Serve lunch and prepare dessert before 1 PM.</div></div>
                        </div>
                        <div class="calendar-summary">This week centers on groceries, meal preparation, home refresh, and shared family events.</div>
                    </div>
                </div>

                <div class="stats-row">
                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Monthly Budget</div>
                            <div class="metric-big">₱16,900</div>
                            <div class="metric-sub">Remaining household funds after groceries, water bill, and transport expenses.</div>
                        </div>
                    </div>

                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Tasks Done</div>
                            <div class="task-progress-list">
                                <div class="task-progress-item">✓ Refrigerator cleaned</div>
                                <div class="task-progress-item">✓ Weekly food list prepared</div>
                                <div class="task-progress-item">✓ Dining area organized</div>
                                <div class="task-progress-item">✓ Utility receipts filed</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Pending Events</div>
                            <div class="pending-list">
                                <div class="pending-item">Replace kitchen hand towels</div>
                                <div class="pending-item">Sunday lunch prep checklist</div>
                                <div class="pending-item">Buy new pantry containers</div>
                                <div class="pending-item">Confirm family outing schedule</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>© <?= date("Y") ?> HomePlanner</div>
        <div>Administrative control panel for home and user management</div>
    </div>

    <script src="../../scripts/Service.js"></script>
</body>
</html>