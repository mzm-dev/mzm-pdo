<?php
/*
* Example MySQL for PDO PHP
* PHP MySQL API
* PHP 5 >= 5.1.0, PHP 7, PHP 8
* 
* @package    MysqlPDO
* @author     Mohamad Zaki Mustafa <mzm@ns.gov.my>
*/

// Initialize the session
if (session_status() == PHP_SESSION_NONE) {
  //session has not started
  session_start();
}

// Check if the user is already logged in, if no then redirect him to login page
if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"]) {
  header("location: login.php");
  exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $email = $password = "";
$username_err = $email_err = $password_err  = $register_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate username
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username.";
  } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
    $username_err = "Username can only contain letters, numbers, and underscores.";
  } else {
    $username = trim($_POST["username"]);
  }

  // Validate password
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter a email.";
  } else if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
    $email_err = "Email address not valid";
  } else {
    $email = trim($_POST["email"]);
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST["password"])) < 6) {
    $password_err = "Password must have atleast 6 characters.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Check input errors before inserting in database
  if (empty($username_err) && empty($email_err) && empty($password_err)) {

    // 1. Query SQL statement
    $sql = "INSERT INTO users (username,email, password) VALUES (:fusername, :femail, :fpassword)";

    // 2. Prepare a select statement
    $stmt = $mysqlpdo->prepare($sql);

    // 3. Set parameters
    $param_username = $username;
    $param_email = $email;
    $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

    // 4. Bind variables to the prepared statement as parameters
    $stmt->bindParam(":fusername", $param_username, PDO::PARAM_STR);
    $stmt->bindParam(":femail", $param_email, PDO::PARAM_STR | PDO::CASE_LOWER);
    $stmt->bindParam(":fpassword", $param_password, PDO::PARAM_STR);

    // 5. Attempt to execute the prepared statement
    if ($stmt->execute()) {
      // Redirect to login page
      header("location: lists.php");
      exit();
    } else {
      echo "Oops! Something went wrong. Please try again later!!.";
      //for development purpose
      echo $stmt->error;
    }

    // Close statement
    unset($stmt);
  }

  // Close connection
  unset($mysqlpdo);
}
?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

  <title>MySQL PDO Style</title>
</head>

<body>
  <div class="container my-5">
    <div class="row">
      <div class="col-md-4 offset-md-4">

        <?php echo !empty($register_err) ? '<div class="alert alert-danger text-center">' . $register_err . '</div>' : ''; ?>

        <h2>Register New User</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            <?= $username_err ? '<small class="form-text invalid-feedback">' . $username_err . '</small>' : '' ?>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
            <?= $email_err ? '<small class="form-text invalid-feedback">' . $email_err . '</small>' : '' ?>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
            <?= $password_err ? '<small class="form-text invalid-feedback">' . $password_err . '</small>' : '' ?>
          </div>

          <div class="form-group">
            <label>Confirm Password</label>
            <input autocomplete="off" type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
          </div>

          <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-secondary ml-2" value="Reset">
          </div>

        </form>
      </div>
    </div>
  </div>
</body>

</html>