<?php
// Your existing PHP logic remains at the top
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePlanner - Red Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-page dashboard-red">

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
                        <div class="home-title">Red Home Dashboard</div>
                        <div class="home-desc">
                            Modern family home focused on comfort, planning, and smooth day-to-day coordination for everyone.
                        </div>
                    </div>
                </div>

                <div class="panel-card residents-card">
                    <div class="panel-inner">
                        <div class="section-heading">Residents</div>

                        <div class="resident-list">
                            <div class="resident-item">
                                <div class="resident-name">Marcus Rivera</div>
                                <div class="resident-role">Home Head • Red Home</div>
                            </div>

                            <div class="resident-item">
                                <div class="resident-name">Angela Rivera</div>
                                <div class="resident-role">Budget Manager • Red Home</div>
                            </div>

                            <div class="resident-item">
                                <div class="resident-name">Sofia Rivera</div>
                                <div class="resident-role">Student • Red Home</div>
                            </div>

                            <div class="resident-item">
                                <div class="resident-name">Liam Rivera</div>
                                <div class="resident-role">Chore Assistant • Red Home</div>
                            </div>

                            <div class="resident-item">
                                <div class="resident-name">Nina Rivera</div>
                                <div class="resident-role">Schedule Coordinator • Red Home</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-card todo-card">
                    <div class="panel-inner">
                        <div class="section-heading">To Do list</div>

                        <div class="todo-list">
                            <div class="todo-item">
                                <span class="todo-dot"></span>
                                <span>Restock kitchen supplies before Thursday dinner.</span>
                            </div>

                            <div class="todo-item">
                                <span class="todo-dot"></span>
                                <span>Review water bill and electricity usage for this month.</span>
                            </div>

                            <div class="todo-item">
                                <span class="todo-dot"></span>
                                <span>Clean the living room shelves and hallway storage area.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="right-column">

                <div class="calendar-card">
                    <div class="calendar-content">
                        <div class="calendar-title">Home Calendar</div>

                        <div class="calendar-events">
                            <div class="event-chip">
                                <div class="event-time">Today • 8:00 AM</div>
                                <div class="event-name">Weekly Grocery Run</div>
                                <div class="event-note">Essentials, vegetables, and cleaning items.</div>
                            </div>

                            <div class="event-chip">
                                <div class="event-time">Today • 6:30 PM</div>
                                <div class="event-name">Family Dinner</div>
                                <div class="event-note">Dining area preparation starts at 5:45 PM.</div>
                            </div>

                            <div class="event-chip">
                                <div class="event-time">Friday • 4:00 PM</div>
                                <div class="event-name">Budget Check-In</div>
                                <div class="event-note">Review expenses and pending household payments.</div>
                            </div>

                            <div class="event-chip">
                                <div class="event-time">Sunday • 9:00 AM</div>
                                <div class="event-name">General Cleaning</div>
                                <div class="event-note">Bedrooms, kitchen, and shared work area.</div>
                            </div>

                            <div class="event-chip">
                                <div class="event-time">Monday • 7:00 AM</div>
                                <div class="event-name">Laundry Schedule</div>
                                <div class="event-note">Wash bedding, uniforms, and daily wear before noon.</div>
                            </div>

                            <div class="event-chip">
                                <div class="event-time">Tuesday • 5:30 PM</div>
                                <div class="event-name">Pantry Restock Check</div>
                                <div class="event-note">Review rice, canned goods, snacks, and breakfast items.</div>
                            </div>

                            <div class="event-chip">
                                <div class="event-time">Wednesday • 3:00 PM</div>
                                <div class="event-name">Home Supplies Review</div>
                                <div class="event-note">Check cleaning products, tissue stock, and toiletries.</div>
                            </div>

                            <div class="event-chip">
                                <div class="event-time">Saturday • 1:00 PM</div>
                                <div class="event-name">Guest Room Setup</div>
                                <div class="event-note">Prepare fresh linens and organize bedside essentials.</div>
                            </div>
                        </div>

                        <div class="calendar-summary">
                            This week includes 8 scheduled home activities covering groceries, cleaning, budgeting, supplies, and family preparation.
                        </div>
                    </div>
                </div>

                <div class="stats-row">

                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Monthly Budget</div>
                            <div class="metric-big">₱18,500</div>
                            <div class="metric-sub">
                                Current available household budget after bills, groceries, and regular utility payments.
                            </div>
                        </div>
                    </div>

                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Tasks Done</div>

                            <div class="task-progress-list">
                                <div class="task-progress-item">✓ Laundry completed for the week</div>
                                <div class="task-progress-item">✓ Wi-Fi bill paid successfully</div>
                                <div class="task-progress-item">✓ Fridge inventory updated</div>
                                <div class="task-progress-item">✓ Dining table cleaned and arranged</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Pending Events</div>

                            <div class="pending-list">
                                <div class="pending-item">Parent-teacher meeting on Saturday</div>
                                <div class="pending-item">Garage reorganization next week</div>
                                <div class="pending-item">Medicine cabinet restock due Monday</div>
                                <div class="pending-item">Replace hallway light bulb this weekend</div>
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