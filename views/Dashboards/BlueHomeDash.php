<?php
// Your existing PHP logic remains at the top
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePlanner - Blue Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-page dashboard-blue">
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
                        <div class="home-title">Blue Home Dashboard</div>
                        <div class="home-desc">A calm and productive home built for routines, study, and balanced household planning.</div>
                    </div>
                </div>

                <div class="panel-card residents-card">
                    <div class="panel-inner">
                        <div class="section-heading">Residents</div>
                        <div class="resident-list">
                            <div class="resident-item"><div class="resident-name">Adrian Cruz</div><div class="resident-role">Home Head • Blue Home</div></div>
                            <div class="resident-item"><div class="resident-name">Mia Cruz</div><div class="resident-role">Academic Planner • Blue Home</div></div>
                            <div class="resident-item"><div class="resident-name">Ethan Cruz</div><div class="resident-role">Student • Blue Home</div></div>
                            <div class="resident-item"><div class="resident-name">Clara Cruz</div><div class="resident-role">Task Coordinator • Blue Home</div></div>
                            <div class="resident-item"><div class="resident-name">Noah Cruz</div><div class="resident-role">Chore Support • Blue Home</div></div>
                        </div>
                    </div>
                </div>

                <div class="panel-card todo-card">
                    <div class="panel-inner">
                        <div class="section-heading">To Do list</div>
                        <div class="todo-list">
                            <div class="todo-item"><span class="todo-dot"></span><span>Update the weekly study and work schedule.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Sort school materials and desk supplies.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Back up important family documents online.</span></div>
                            <!-- <div class="todo-item"><span class="todo-dot"></span><span>Deep clean the office and shared workspace.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Plan meals for the next five days.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Refill bathroom and laundry essentials.</span></div> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-column">
                <div class="calendar-card">
                    <div class="calendar-content">
                        <div class="calendar-title">Home Calendar</div>
                        <div class="calendar-events">
                            <div class="event-chip"><div class="event-time">Today • 7:30 AM</div><div class="event-name">Morning Routine Reset</div><div class="event-note">Start the day with room checks and breakfast prep.</div></div>
                            <div class="event-chip"><div class="event-time">Today • 8:00 PM</div><div class="event-name">Study Hour</div><div class="event-note">Shared study block for school and work review.</div></div>
                            <div class="event-chip"><div class="event-time">Thursday • 5:00 PM</div><div class="event-name">Expense Review</div><div class="event-note">Track budget usage and review spending.</div></div>
                            <div class="event-chip"><div class="event-time">Friday • 6:30 PM</div><div class="event-name">Family Movie Night</div><div class="event-note">Set up snacks and living room seating.</div></div>
                            <div class="event-chip"><div class="event-time">Saturday • 10:00 AM</div><div class="event-name">Desk Reorganization</div><div class="event-note">Sort supplies, papers, and charging cables.</div></div>
                            <div class="event-chip"><div class="event-time">Sunday • 9:00 AM</div><div class="event-name">Laundry Cycle</div><div class="event-note">Wash uniforms, bedding, and household linens.</div></div>
                        </div>
                        <div class="calendar-summary">This week focuses on organization, study time, budget tracking, and home reset activities.</div>
                    </div>
                </div>

                <div class="stats-row">
                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Monthly Budget</div>
                            <div class="metric-big">₱21,300</div>
                            <div class="metric-sub">Available funds after internet, groceries, and weekly school expenses.</div>
                        </div>
                    </div>

                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Tasks Done</div>
                            <div class="task-progress-list">
                                <div class="task-progress-item">✓ Pantry labels updated</div>
                                <div class="task-progress-item">✓ Study desk cleaned</div>
                                <div class="task-progress-item">✓ Wi-Fi plan renewed</div>
                                <div class="task-progress-item">✓ Bills tracked for the week</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Pending Events</div>
                            <div class="pending-list">
                                <div class="pending-item">Printer ink refill this weekend</div>
                                <div class="pending-item">Organize bookshelf on Friday</div>
                                <div class="pending-item">Replace study lamp bulb</div>
                                <div class="pending-item">Back up laptop files</div>
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