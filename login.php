<?php
//Starting the session so we can keep the user logged in
session_start();
//Connect to the database
require 'db_connect.php';

//Variable to store any login error message
$error = '';

//Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

//Get username and password from the form
//trim() removes extra spaces from the username
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

//Prepare SQL query to safely get user details from the database
//Prepared statements help prevent SQL injection
    $stmt = $conn->prepare(
        "SELECT id, password_hash, role FROM users WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();

//Get the result and fetch user data
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

//Check if user exists and password is correct
//password_verify compares the entered password with the stored hash
    if ($user && password_verify($password, $user['password_hash'])) {

//Save user information in session variables
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];

//Close database resources
        $stmt->close();
        $conn->close();

//Redirect user to the main page after login
        header('Location: index.php');
        exit;
    } else {
//Show error if login details are incorrect
        $error = 'Invalid username or password.';
    }

//Close the prepared statement
    $stmt->close();
}

//Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Poppleton Dog Show</title>

    <style>
        /* Simple styling to keep the page clean and readable */
        body { font-family: Arial, sans-serif; margin: 20px; }
        .login-box { max-width: 400px; margin: 0 auto; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="password"] {
             width: 100%; padding: 8px; margin-top: 4px;
        }
        button { margin-top: 15px; padding: 8px 16px; }
        .error { color: #b00020; }
    </style>
</head>
<body>

<!-- Skip link for keyboard and screen reader users -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<div class="login-box" id="main-content">
    <h1>Login</h1>

   <!-- Show error message if login fails -->
   <?php if ($error): ?>
       <p class="error" aria-live="polite">
            <?php echo htmlspecialchars($error); ?>
       </p>
   <?php endif; ?>

   <!-- Login form -->
   <form method="post" action="login.php">

       <label for="username">Username
           <input
               id="username"
               name="username"
               type="text"
               required
               autocomplete="username"
           >
       </label>

       <label for="password">Password
           <input
               id="password"
               name="password"
               type="password"
               required
               autocomplete="current-password"
           >
       </label>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
