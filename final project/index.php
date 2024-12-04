<?php
session_start();
require_once 'config/db_connect.php';

// Fetch all events
$sql = "SELECT * FROM events WHERE date >= CURDATE() ORDER BY date ASC";
$result = mysqli_query($conn, $sql);

// Check for query execution error
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
// Define categories
$categories = ['Music', 'Tech', 'Food', 'Art', 'Occasion', 'Sports'];

function getCategoryName($categoryId) {
    $categories = [
        0 => 'Music',
        1 => 'Tech',
        2 => 'Food',
        3 => 'Art',
        4 => 'Occasion',
        5 => 'Sports'
    ];
    return isset($categories[$categoryId]) ? $categories[$categoryId] : 'Unknown';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management Platform</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/fonts.css">
    <script src="js/script.js"></script>
    <script src="js/index.js"></script>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .category-btn.active {
            background-color: #007bff;
            color: white;
        }
        .event-tile {
            margin-bottom: 20px;
        }
        #calendar-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .fc-event-title {
            white-space: normal;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg" id="navbarID">
    <a id="webTitle" class="navbar-brand" href="index.php">Event Manager</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php" data-text="Home">
                    <i class="fas fa-home fa-lg"></i>
                    <span class="nav-text">Home</span>
                </a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="create_event.php" data-text="Create Event">
                        <i class="fas fa-plus fa-lg"></i>
                        <span class="nav-text">Create Event</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php" data-text="Profile">
                        <i class="fas fa-user fa-lg"></i>
                        <span class="nav-text">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" data-text="Logout">
                        <i class="fas fa-sign-out-alt fa-lg"></i>
                        <span class="nav-text">Logout</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php" data-text="Login">
                        <i class="fas fa-sign-in-alt fa-lg"></i>
                        <span class="nav-text">Login</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php" data-text="Register">
                        <i class="fas fa-user-plus fa-lg"></i>
                        <span class="nav-text">Register</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

    <!-- Display logout message -->
    <?php if (isset($_GET['message'])): ?>
        <div class='alert alert-success'><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <main class="container-fluid p-0">
        <h1 id="FE" class="mb-4">Featured Events</h1>
        <div id="event-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php 
                $first = true;
                if (mysqli_num_rows($result) > 0): 
                    while ($row = mysqli_fetch_assoc($result)): 
                ?>
                    <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                        <div class="featured-event" style="background-image: url('<?php echo htmlspecialchars($row['image_path']); ?>');">
                            <div class="event-details">
                                <h2 class="event-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                                <p class="event-description"><?php echo htmlspecialchars(substr($row['description'], 0, 200)) . '...'; ?></p>
                                <p class="event-date">Date: <?php echo date('F d, Y', strtotime($row['date'])); ?></p>
                                <a href="event_details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php 
                    $first = false;
                    endwhile; 
                else: 
                ?>
                    <div class="carousel-item active">
                        <div class="featured-event">
                            <div class="event-details">
                                <p>No upcoming events found.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <a class="carousel-control-prev" href="#event-carousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#event-carousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

        <div class="container mt-5">
            <div class="d-flex justify-content-center align-items-center mb-4">
                <h2 style="padding-right:5%;" >All Events</h2>
                <button id="calendar-view-btn" class="btn btn-primary">Calendar View</button>
            </div>
            
            <!-- Calendar container -->
            <div id="calendar-container" style="display: none;">
                <div id="calendar"></div>
                <button id="close-calendar-btn" class="btn btn-secondary mt-3">Close Calendar</button>
            </div>

            <!-- Category filter buttons -->
            <div class="mb-4">
                <?php foreach ($categories as $category): ?>
                    <button class="btn btn-outline-primary category-btn mr-2" data-category="<?php echo htmlspecialchars($category); ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </button>
                <?php endforeach; ?>
                <button id="date-filter-btn" class="btn btn-outline-primary mr-2">
                    Filter by Date
                </button>
            </div>

            <!-- Event gallery -->
            <div class="row" id="event-gallery">
                <?php 
                mysqli_data_seek($result, 0);
                while ($event = mysqli_fetch_assoc($result)): 
                    $categoryName = getCategoryName($event['category']);
                ?>
                    <div class="col-md-4 event-tile" 
                         data-category="<?php echo htmlspecialchars($categoryName); ?>"
                         data-date="<?php echo htmlspecialchars($event['date']); ?>"
                         data-time="<?php echo htmlspecialchars($event['time']); ?>">
                         <div class="card h-100 d-flex flex-column">
    <div class="card-body flex-grow-1">
        <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
        <p class="card-text"><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
        <p class="card-text"><small class="text-muted"><?php echo date('F d, Y', strtotime($event['date'])); ?></small></p>
    </div>
    <div class="card-footer p-0">
        <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-primary btn-block rounded-0">View Details</a>
    </div>
</div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>

    <script>
    $(document).ready(function() {
        $('#event-carousel').carousel({
            interval: 10000
        });

        let dateFilterActive = false;
        var calendar;

        $('.category-btn').click(function() {
            $(this).toggleClass('active');
            filterEvents();
        });

        $('#date-filter-btn').click(function() {
            dateFilterActive = !dateFilterActive;
            $(this).toggleClass('active');
            filterEvents();
        });

        $('#calendar-view-btn').click(function() {
            $('#event-gallery').hide();
            $('#calendar-container').show();
            initializeCalendar();
        });

        $('#close-calendar-btn').click(function() {
            $('#calendar-container').hide();
            $('#event-gallery').show();
        });

        function filterEvents() {
            var activeCategories = $('.category-btn.active').map(function() {
                return $(this).data('category');
            }).get();

            console.log('Active categories:', activeCategories);

            let visibleEvents = $('.event-tile').filter(function() {
                var eventCategory = $(this).data('category');
                return activeCategories.length === 0 || activeCategories.includes(eventCategory);
            });

            if (dateFilterActive) {
                visibleEvents.sort(function(a, b) {
                    var dateA = new Date($(a).data('date') + ' ' + $(a).data('time'));
                    var dateB = new Date($(b).data('date') + ' ' + $(b).data('time'));
                    return dateA - dateB;
                });
            }

            $('.event-tile').hide();
            visibleEvents.show().each(function(index) {
                $(this).appendTo('#event-gallery');
            });
        }

        function initializeCalendar() {
            if (calendar) {
                calendar.destroy();
            }

            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    <?php
                    mysqli_data_seek($result, 0);
                    while ($event = mysqli_fetch_assoc($result)):
                        $categoryName = getCategoryName($event['category']);
                    ?>
                    {
                        title: '<?php echo addslashes($event['title']); ?> (<?php echo $categoryName; ?>)',
                        start: '<?php echo $event['date']; ?>',
                        url: 'event_details.php?id=<?php echo $event['id']; ?>'
                    },
                    <?php endwhile; ?>
                ],
                eventClick: function(info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.open(info.event.url, '_blank');
                    }
                }
            });
            calendar.render();
        }
        $('.event-tile').each(function() {
            console.log('Initial category check:', $(this).data('category'));
        });
    });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>