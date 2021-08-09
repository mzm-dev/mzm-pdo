<?php
/*
* Example MySQL for PDO PHP
* PHP MySQL API
* PHP 5 >= 5.1.0, PHP 7, PHP 8
* 
* @package    MysqlPDO
* @author     Mohamad Zaki Mustafa <mzm@ns.gov.my>
*/

// Include config file
require_once "config.php";

// Check if the user is already logged in, if no then redirect him to login page
if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"]) {
  header("location: login.php");
  exit;
}

// Define variables and initialize with empty values
$id = $username = $email = "";
$username_err = $email_err = $update_err = "";


// Processing form data when get parameter
if ($_SERVER["REQUEST_METHOD"] == "GET") {

  if (
    isset($_GET["id"]) && !empty($_GET["id"]) //semak jika value wujud
    && filter_var($_GET['id'], FILTER_VALIDATE_INT) //semak jika value adalah integer 
  ) {
    $id = $_GET['id'];


    // 1. Query SQL statement
    $sql = "SELECT * FROM users WHERE id = :fid";

    // 2. Prepare a select statement
    $stmt = $mysqlpdo->prepare($sql);

    // 3. Set parameters
    $param_id = $id;

    // 4. Bind variables to the prepared statement as parameters
    $stmt->bindParam(":fid", $param_id, PDO::PARAM_INT);


    // 5. Attempt to execute the prepared statement
    if ($stmt->execute()) {
      
      // 7. Check if username exists, if yes then verify password
      if ($stmt->rowCount() == 1) {

        /* Fetch result row as an associative array.
      Disebabkan set result hanya ingin gunakan 1 row data sahaja,
      while looping tidak diperlukan */
        $row = $stmt->fetch();

        //Set result parameters
        $id = $row['id'];
        $username =  $row['username'];
        $email =  $row['email'];
      } else {
        //for development purpose
        header("location: lists.php");
        exit;
      }
    } else {
      //for development purpose
      echo $stmt->error;
      die;
    }

    // Close statement
    unset($stmt);

    // Close connection
    unset($mysqlpdo);
  } else {
    header("location: lists.php");
    exit;
  }
}


// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $id = trim($_POST["id"]);

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
  } elseif (strlen(trim($_POST["email"])) < 6) {
    $email_err =  "Please enter email.";
  } else {
    $email = trim($_POST["email"]);
  }


  // Check input errors before inserting in database
  if (empty($username_err) && empty($email_err)) {

    // 1. Query SQL statement
    $sql = "UPDATE users SET username=:fusername, email=:femail WHERE id=:fid";

    // 2. Prepare a select statement
    $stmt = $mysqlpdo->prepare($sql);

    // 3. Set parameters
    $param_username = $username;
    $param_email = $email;
    $param_id = $id;

    // 4. Bind variables to the prepared statement as parameters    
    $stmt->bindParam(":fusername", $param_username, PDO::PARAM_STR);
    $stmt->bindParam(":femail", $param_email, PDO::PARAM_STR | PDO::CASE_LOWER);
    $stmt->bindParam(":fid", $param_id, PDO::PARAM_INT);

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

        <?php echo !empty($update_err) ? '<div class="alert alert-danger text-center">' . $update_err . '</div>' : ''; ?>

        <h2>Update User</h2>
        <p>Please fill this form to update an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>" method="post" autocomplete="off">
          <input type="hidden" name="id" value="<?php echo $id; ?>">
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
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-secondary ml-2" value="Reset">
          </div>

        </form>
      </div>
    </div>
  </div>
</body>

</html>