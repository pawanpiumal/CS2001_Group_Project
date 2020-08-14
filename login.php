<html>

<?php
// If another PHP session is not already started, start one
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If the form is submitted get the submitted form values and assign them to PHP variables
    // When assigning the variables convert the special characters to increase security.
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);
    // If Email or password is empty show an error message.
    // If the format of the email is incorrect show an error message.
    if (empty($email)) {
        $error = "Error! Email can't be empty.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Error! Email format is incorrect.";
    } else if (empty($password)) {
        $error = "Error! Password can't be empty.";
    } else {
        // If the inputs are correct import the SQL connection
        require 'config/config.php';
        // SQL query related to login the user 
        // PrepareStatements for login
        $SQL_LOGIN_USER =   "SELECT uid,name,email,password,imageLocation,userType 
                            FROM USERS 
                            WHERE email = ? AND password = ?";
        if ($PREPARE_LOGIN_STATEMENT = $conn->prepare($SQL_LOGIN_USER)) {
            if ($PREPARE_LOGIN_STATEMENT->bind_param('ss', $email, $password)) {
                // If the query results success Login the user
                // If the query is unsuccess show an error message to the user
                $PREPARE_LOGIN_STATEMENT->execute();
                if ($resultGetUser = $PREPARE_LOGIN_STATEMENT->get_result()) {
                    // Get the User details from the SQL query dataset.
                    // If there are no Rows in the dataset which means the email or password show an error to user.
                    // Same error is shown to user when the email already exists beacuse of security.
                    if ($resultGetUser->num_rows == 1) {
                        $row = $resultGetUser->fetch_assoc();
                        //If the user is valid. Set the session variables with relavent details.
                        $_SESSION['uid'] = $row['uid'];
                        $_SESSION['username'] = $row['name'];
                        $_SESSION['imageLocation'] = $row['imageLocation'];
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['userType'] = $row['userType'];
                        // After successfully logged-in redirect the user to home page
                        $PREPARE_LOGIN_STATEMENT->close();
                        header("Location:index.php");
                        exit();
                    } else {
                        $error = "Error! Email / Password invalid.";
                    }
                } else {
                    $error = "Error! Try again later.";
                }
            } else {
                $error = "Error! Try again later.";
            }
        } else {
            $error = "Error! Try again later.";
        }
        // Close the Prepared Statement
        $PREPARE_LOGIN_STATEMENT->close();

        // Close the SQL connection.
        $conn->close();
    }
}

?>


<head>
    <title>Login | Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type='text/css' href="css/login.css" />
    <link rel="stylesheet" type='text/css' href="css/nav.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require("header.php") ?>
    <?php require("nav.php") ?>
    <div class="login-body">
        <div class="login">
            <div class="head">
                <h1>Login</h1>
            </div>
            <?php
            // When an error occured after the form is submitted the users previous results are returned.
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="login-form">
                <input type="email" name="email" placeholder="Email" value="<?php if (!empty($_POST['email'])) {
                                                                                echo $_POST['email'];
                                                                            } ?>" required />
                <input type="password" name="password" placeholder="Password" required />
                <p id="error-login" class="error"><?php if (!empty($error)) {
                                                        echo $error;
                                                    }  ?></p>
                <button class="login-btn" type="submit">Login</button>
            </form>
            <a href="register.php" class="signup-a">Don't have a user account?</a>
        </div>
    </div>
    <?php require('footer.php') ?>
    <script src="js/login.js"></script>
</body>

</html>