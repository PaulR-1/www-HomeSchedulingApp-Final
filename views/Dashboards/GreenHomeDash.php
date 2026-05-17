<?php
// Your existing PHP logic remains at the top
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePlanner - Green Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-page dashboard-green">
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
                        <div class="home-title">Green Home Dashboard</div>
                        <div class="home-desc">A fresh and balanced household focused on wellness, sustainability, and shared routines.</div>
                    </div>
                </div>

                <div class="panel-card residents-card">
                    <div class="panel-inner">
                        <div class="section-heading">Residents</div>
                        <div class="resident-list">
                            <div class="resident-item"><div class="resident-name">Daniel Flores</div><div class="resident-role">Home Head • Green Home</div></div>
                            <div class="resident-item"><div class="resident-name">Samantha Flores</div><div class="resident-role">Meal Planner • Green Home</div></div>
                            <div class="resident-item"><div class="resident-name">Olivia Flores</div><div class="resident-role">Student • Green Home</div></div>
                            <div class="resident-item"><div class="resident-name">Lucas Flores</div><div class="resident-role">Garden Helper • Green Home</div></div>
                            <div class="resident-item"><div class="resident-name">Emma Flores</div><div class="resident-role">Routine Manager • Green Home</div></div>
                        </div>
                    </div>
                </div>

                <div class="panel-card todo-card">
                    <div class="panel-inner">
                        <div class="section-heading">To Do list</div>
                        <div class="todo-list">
                            <div class="todo-item"><span class="todo-dot"></span><span>Water indoor plants and check balcony pots.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Prepare healthy meal ingredients for the week.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Sort recyclables and reusable containers.</span></div>
                            <!-- <div class="todo-item"><span class="todo-dot"></span><span>Clean kitchen counters and compost area.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Update the shared wellness and task tracker.</span></div>
                            <div class="todo-item"><span class="todo-dot"></span><span>Check garden tools and storage shelves.</span></div> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-column">
                <div class="calendar-card">
                    <div class="calendar-content">
                        <div class="calendar-title">Home Calendar</div>
                        <div class="calendar-events">
                            <div class="event-chip"><div class="event-time">Today • 6:30 AM</div><div class="event-name">Plant Watering</div><div class="event-note">Water the balcony garden and indoor herbs.</div></div>
                            <div class="event-chip"><div class="event-time">Today • 5:00 PM</div><div class="event-name">Healthy Dinner Prep</div><div class="event-note">Prepare vegetables and protein for the evening meal.</div></div>
                            <div class="event-chip"><div class="event-time">Thursday • 4:30 PM</div><div class="event-name">Recycling Check</div><div class="event-note">Sort reusable plastics, bottles, and paper waste.</div></div>
                            <div class="event-chip"><div class="event-time">Friday • 7:00 PM</div><div class="event-name">Family Walk</div><div class="event-note">Outdoor routine for exercise and unwind time.</div></div>
                            <div class="event-chip"><div class="event-time">Saturday • 8:00 AM</div><div class="event-name">Kitchen Refresh</div><div class="event-note">Wipe shelves, clean containers, and check produce.</div></div>
                            <div class="event-chip"><div class="event-time">Sunday • 3:00 PM</div><div class="event-name">Weekly Reset</div><div class="event-note">Review tasks, meals, and goals for the next week.</div></div>
                        </div>
                        <div class="calendar-summary">This week highlights healthy meals, plant care, home reset tasks, and outdoor family time.</div>
                    </div>
                </div>

                <div class="stats-row">
                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Monthly Budget</div>
                            <div class="metric-big">₱19,750</div>
                            <div class="metric-sub">Available budget after fresh produce, utilities, and weekly household essentials.</div>
                        </div>
                    </div>

                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Tasks Done</div>
                            <div class="task-progress-list">
                                <div class="task-progress-item">✓ Herbs watered and trimmed</div>
                                <div class="task-progress-item">✓ Compost area cleaned</div>
                                <div class="task-progress-item">✓ Grocery list updated</div>
                                <div class="task-progress-item">✓ Weekly meal prep started</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-card small-card">
                        <div class="panel-inner">
                            <div class="small-title">Pending Events</div>
                            <div class="pending-list">
                                <div class="pending-item">Buy potting soil on Saturday</div>
                                <div class="pending-item">Refill spice jars</div>
                                <div class="pending-item">Clean balcony drain area</div>
                                <div class="pending-item">Replace storage labels</div>
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