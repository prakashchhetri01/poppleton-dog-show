<?php
//Start the session tocheck who is logged in
session_start();

//Only allow admin users to access this page
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
//If the user is not an admin, send them to the login page
   header('Location: login.php');
   exit;
}

//Connect to the database
require 'db_connect.php';

//Message to show success or error
$message = '';

//Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

//Get the values entered in the form
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

//Make sure the role is either admin or user
  $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';

//Check that username and password are not empty
  if ($username === '' || $password === '') {
      $message = 'Username and password are required.';
  } else {

//Hash the password before saving it to the database
   $hash = password_hash($password, PASSWORD_DEFAULT);

//Prepare SQL query to safely insert a new user
    $stmt = $conn->prepare(
      "INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $username, $hash, $role);

//Try to create the new user
     if ($stmt->execute()) {
//Show success message if user is created
         $message = "User '$username' created successfully with role '$role'.";
     } else {
//Handle duplicate username error
        if ($conn->errno === 1062) {
            $message = "Username '$username' is already taken.";
         } else {
            // Show any other database error
            $message = "Error creating user. Check local configuration.";
         }
     }

 //Close the prepared statement
     $stmt->close();
   }
}

//Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register User - Poppleton Dog Show</title>

  <style>
    /* Simple styling to keep the form neat and readable */
    body { font-family: Arial, sans-serif; margin: 20px; }
    .container { max-width: 500px; margin: 0 auto; }
    label { display: block; margin-top: 10px; }
    input, select { width: 100%; padding: 8px; margin-top: 4px; }
    button { margin-top: 15px; padding: 8px 16px; }
    .message { margin-top: 10px; }
  </style>
</head>
<body>

<!-- Skip link for accessibility -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<div class="container" id="main-content">
  <h1>Register New User</h1>

  <!-- Link back to the main dashboard -->
  <p><a href="index.php">Back to dashboard</a></p>
 <!-- Show feedback message after form submission -->
 <?php if ($message): ?>
   <p class="message" aria-live="polite">
      <?php echo htmlspecialchars($message); ?>
   </p>
 <?php endif; ?>

 <!-- User registration form -->
 <form method="post" action="register.php">

   <label for="username">Username
     <input id="username" name="username" type="text" required>
   </label>

   <label for="password">Password
     <input id="password" name="password" type="password" required>
   </label>

   <!-- Admin chooses the role for the new user -->
   <label for="role">Role
     <select id="role" name="role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
     </select>
   </label>

    <button type="submit">Create User</button>
  </form>
</div>

</body>
</html>
