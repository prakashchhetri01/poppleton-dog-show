<?php
 //Start the session to check login details
 session_start();

 //If the user is not logged in, send them to the login page
 if (!isset($_SESSION['user_id'])) {
     header('Location: login.php');
     exit;
 }

 //Connect to the database
 require 'db_connect.php';

 // --------------------------------------------------
 //BASIC COUNTS FOR THE WELCOME MESSAGE
 // --------------------------------------------------

 //Count total owners
 $ownersResult = $conn->query("SELECT COUNT(*) AS total FROM owners");
$owners = $ownersResult->fetch_assoc()['total'];

//Count total dogs
$dogsResult = $conn->query("SELECT COUNT(*) AS total FROM dogs");
$dogs = $dogsResult->fetch_assoc()['total'];

//Count total events
$eventsResult = $conn->query("SELECT COUNT(*) AS total FROM events");
$events = $eventsResult->fetch_assoc()['total'];

// --------------------------------------------------
//CHECK IF THE USER IS AN ADMIN
// --------------------------------------------------
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

//Variable for admin-only statistics
$totalCompetitions = null;
$overallAvgScore = null;
$distinctBreeds = null;

//If the user is an admin, show extra statistics
if ($isAdmin) {

    //Count total competitions
    $compRes = $conn->query("SELECT COUNT(*) AS total FROM competitions");
    $totalCompetitions = $compRes->fetch_assoc()['total'];

    //Calculate average score across all entries
    $avgRes = $conn->query("SELECT AVG(score) AS avg_score FROM entries");
    $overallAvgScore = $avgRes->fetch_assoc()['avg_score'];

    //Count how many different breeds are in the show
    $breedRes = $conn->query("SELECT COUNT(DISTINCT breed_id) AS breeds FROM dogs");
    $distinctBreeds = $breedRes->fetch_assoc()['breeds'];
}

// --------------------------------------------------
//SORTING AND SEARCH LOGIC
// --------------------------------------------------

//Allowed columns for sorting to avoid invalid input
$allowedSort = [
    'dog'   => 'dog_name',
    'breed' => 'breed_name',
    'score' => 'avg_score',
    'owner' => 'owner_name'
];

//Get sort option from URL (default is score)
$sortKey = $_GET['sort'] ?? 'score';

//If sort option is invalid, use default
if (!array_key_exists($sortKey, $allowedSort)) {
    $sortKey = 'score';
}

//Set column and direction for sorting
$orderColumn = $allowedSort[$sortKey];
$orderDirection = ($sortKey === 'score') ? 'DESC' : 'ASC';

//Get search term from URL
$searchTerm = trim($_GET['q'] ?? '');
$searchSql = '';

//Add search condition if a search term is entered
if ($searchTerm !== '') {
    $searchSql = "WHERE dogs.name LIKE ? OR owners.name LIKE ?";
}
// --------------------------------------------------
//MAIN QUERY TO GET TOP 10 DOGS
// --------------------------------------------------
$query = "
    SELECT
        dogs.id AS dog_id,
        dogs.name AS dog_name,
        breeds.name AS breed_name,
        owners.id AS owner_id,
        owners.name AS owner_name,
        owners.email AS owner_email,
        AVG(entries.score) AS avg_score
    FROM dogs
    JOIN breeds ON dogs.breed_id = breeds.id
    JOIN owners ON dogs.owner_id = owners.id
    JOIN entries ON dogs.id = entries.dog_id
    $searchSql
    GROUP BY dogs.id, dogs.name, breeds.name, owners.id, owners.name, owners.email
    HAVING COUNT(entries.score) > 1
    ORDER BY $orderColumn $orderDirection
    LIMIT 10
";

//If searching, use prepared statement
if ($searchTerm !== '') {
    $like = '%' . $searchTerm . '%';
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    //Otherwise run the query directly
    $result = $conn->query($query);
}

//Description used for screen reader
$sortDescriptions = [
    'dog'   => 'sorted by dog name',
    'breed' => 'sorted by breed name',
    'score' => 'sorted by average score',
    'owner' => 'sorted by owner name'
];

$currentSortDescription = $sortDescriptions[$sortKey] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Poppleton Dog Show Dashboard</title>

    <style>
        /* Basic layout and accessibility-friendly styling */
        body { font-family: Arial, sans-serif; margin: 0; }

        .skip-link {
            position: absolute;
            left: -999px;
            background: #000;
            color: #fff;
            padding: 8px;
        }
        .skip-link:focus { left: 10px; }

        nav {
            background: #004080;
            color: #fff;
            padding: 10px 20px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin-right: 15px;
        }
        nav .right { float: right; }

        main { padding: 20px; }

        table {
             border-collapse: collapse;
             width: 100%;
             margin-top: 10px;
        }
        th, td {
             border: 1px solid #ccc;
             padding: 6px 8px;
        }
        th { background: #f0f0f0; }
    </style>
</head>
<body>

<a href="#main-content" class="skip-link">Skip to main content</a>

<!-- Navigation bar -->
<nav>
    <a href="index.php">Home</a>

    <?php if ($isAdmin): ?>
        <a href="register.php">Register User</a>
    <?php endif; ?>

    <a href="logout.php">Logout</a>

    <span class="right">
        Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?>
    </span>
</nav>

<main id="main-content">

    <h1>Poppleton Dog Show</h1>

    <!-- Welcome message using database values -->
    <p>
         Welcome to the Poppleton Dog Show! This year
         <?php echo $owners; ?> owners entered
         <?php echo $dogs; ?> dogs in
         <?php echo $events; ?> events!
    </p>

    <!-- Extra admin-only statistics -->
    <?php if ($isAdmin): ?>
        <h2>Admin Statistics</h2>
        <p>Total competitions: <?php echo (int)$totalCompetitions; ?></p>
        <p>Average score: <?php echo number_format((float)$overallAvgScore, 2); ?></p>
        <p>Different breeds: <?php echo (int)$distinctBreeds; ?></p>
    <?php endif; ?>

    <h2>Top 10 Dogs</h2>

    <!-- Search form -->
    <form method="get" action="index.php">
        <label>
            Search by dog or owner:
            <input type="search" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>">
        </label>
        <button type="submit">Search</button>
        <?php if ($searchTerm !== ''): ?>
            <a href="index.php">Clear</a>
        <?php endif; ?>
    </form>

    <!-- Results table -->
    <table>
        <thead>
        <tr>
             <th>Dog</th>
             <th>Breed</th>
             <th>Average Score</th>
             <th>Owner</th>
             <th>Email</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
             <tr>
                 <td><?php echo htmlspecialchars($row['dog_name']); ?></td>
                 <td><?php echo htmlspecialchars($row['breed_name']); ?></td>
                 <td><?php echo number_format($row['avg_score'], 2); ?></td>
                 <td>
                      <a href="owner.php?id=<?php echo (int)$row['owner_id']; ?>">
                          <?php echo htmlspecialchars($row['owner_name']); ?>
                      </a>
                 </td>
                 <td>
                      <a href="mailto:<?php echo htmlspecialchars($row['owner_email']); ?>">
                          <?php echo htmlspecialchars($row['owner_email']); ?>
                      </a>
                 </td>
             </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</main>

<?php
//Close prepared statement if used
if (isset($stmt)) {
    $stmt->close();
}

//Close database connection
$conn->close();
?>
</body>
</html>
