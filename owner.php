<?php
//Start the session so we know which user is logged in
session_start();

//Connect to the database
require 'db_connect.php';

//Make sure the user is logged in before showing owner detail
if (!isset($_SESSION['user_id'])) {
//If not logged in, send them to the login page
    header('Location: login.php');
    exit;
}

//Check that an owner id is provided in the URL and that must a number
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid owner id.");
}

//Convert owner id to an integer
$owner_id = (int) $_GET['id'];

//Prepare a SQL query to get owner details safely
$stmt = $conn->prepare("SELECT * FROM owners WHERE id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();

//Get the result from the database
$result = $stmt->get_result();
$owner = $result->fetch_assoc();
//If no owner is found show an error message
if (!$owner) {
    die("Owner not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>Owner Details</title>

    <style>
        /* Basic styling to keep the page clear and readable */
        body {
             font-family: Arial, sans-serif;
             margin: 40px;
             background-color: #f7f7f7;
        }
        nav {
             margin-bottom: 20px;
             padding-bottom: 10px;
             border-bottom: 1px solid #ccc;
        }
        nav a {
             margin-right: 15px;
             text-decoration: none;
             color: #0066cc;
        }
        nav span {
             margin-left: 20px;
             font-size: 0.9em;
             color: #555;
        }
        table {
             border-collapse: collapse;
             background: #ffffff;
        }
        th, td {
             border: 1px solid #ccc;
             padding: 6px 8px;
        }
        th {
             background: #eee;
        }
    </style>
</head>
<body>

<!-- Navigation links and logged-in user info -->
<nav>
    <a href="index.php">Home</a>
    <a href="logout.php">Logout</a>

    <!-- Show the username of the currently logged-in user -->
    <span>Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
</nav>

<h1>Owner Details</h1>

<!-- Display all owner details in a table -->
<table>
    <?php foreach ($owner as $field => $value): ?>
        <?php
//Skip showing the internal database ID
        if ($field === 'id') continue;
        ?>
        <tr>
             <!-- Convert field names into a readable format -->
             <th><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $field))); ?></th>
             <!-- Output the value safely -->
             <td><?php echo htmlspecialchars((string) $value); ?></td>
         </tr>
    <?php endforeach; ?>
</table>

<p><a href="index.php">Back to main page</a></p>

<?php
//Close the database connection after finishing
$conn->close();
?>
</body>
</html>
