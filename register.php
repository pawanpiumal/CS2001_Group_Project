<html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Check whiether the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // After submitted assign the form values to variables.
    $username = htmlentities(trim($_POST['username']));
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);
    $confirmPassword = htmlentities($_POST['confirmPassword']);
    $userType = htmlentities($_POST['userType']);
    // If relavent details are missing show relavent errors to the user.
    if (empty($username)) {
        $error = "Error! Username can't be empty.";
    } else if (empty($email)) {
        $error = "Error! Email can't be empty.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Error! Email format is incorrect.";
    } else if (empty($password)) {
        $error = "Error! Password can't be empty.";
    } else if (empty($confirmPassword)) {
        $error = "Error! Confirm-Password can't be empty.";
    } else if ($password !== $confirmPassword) {
        $error = "Error! Password and Confirm-Password mismatch.";
    } else if (empty($userType) || ($userType != "user" && $userType != "admin")) {
        $error = "Error! User type should be User or Admin.";
    } else {
        //If there are no errors in the user inputs import the mysql connection.
        require 'config/config.php';
        // SQL query to check if the email is already assigned to another account in the database.
        $SQL_CHECK_EMAIL_EXISTS = "SELECT email FROM USERS WHERE email = ?";
        if ($PREPARE_REGISTER_EMAIL_CHECK_STATEMENT = $conn->prepare($SQL_CHECK_EMAIL_EXISTS)) {
            if ($PREPARE_REGISTER_EMAIL_CHECK_STATEMENT->bind_param('s', $email)) {
                $PREPARE_REGISTER_EMAIL_CHECK_STATEMENT->execute();
                if ($resultCheckExists = $PREPARE_REGISTER_EMAIL_CHECK_STATEMENT->get_result()) {
                    $PREPARE_REGISTER_EMAIL_CHECK_STATEMENT->close();
                    //If there is another user account show an error to the user.
                    if ($resultCheckExists->num_rows > 0) {
                        $error = "Error! This email is already registered with another account.";
                    } else {
                        // If all the details are complete create an user account and assign the session variables
                        $SQL_REGISTER_USER =   "INSERT INTO USERS(name,email,password,userType) 
                                                VALUES (?,?,?,?)";
                        if ($PREPARE_REGISTER_STATEMENT = $conn->prepare($SQL_REGISTER_USER)) {
                            if ($PREPARE_REGISTER_STATEMENT->bind_param('ssss', $username, $email, $password, $userType)) {
                                if ($PREPARE_REGISTER_STATEMENT->execute()) {
                                    $_SESSION['uid'] = $PREPARE_REGISTER_STATEMENT->insert_id;
                                    $_SESSION['username'] = $username;
                                    $_SESSION['email'] = $email;
                                    $_SESSION['userType'] = $userType;
                                    //After successfull user account creation redirect the user.
                                    header("Location:index.php");
                                    exit();
                                } else {
                                    $error = "Error! Try again later.";
                                }
                            } else {
                                $error = "Error! Try again later.";
                            }
                        } else {
                            $error = "Error! Try again later.";
                        }
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
        $conn->close();
    }
}

?>

<head>
    <title>Register | Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type='text/css' href="css/login.css" />
    <link rel="stylesheet" type='text/css' href="css/nav.css" />
    <link rel="icon" href="images/logo7.png">
</head>

<body>
    <?php
    $pageName = "register";
    require("header.php");
    ?>
    <?php require("nav.php") ?>
    <div class="login-body">
        <div class="login">
            <div class="head">
                <h1>Register</h1>
            </div>
            <?php
            // If there are errors in the submitted form keep the users inputs and fill the form with them
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="login-form signup-form">
                <input type="text" name="username" placeholder="Username" value="<?php if (!empty($_POST['username'])) {
                                                                                        echo $_POST['username'];
                                                                                    } ?>" required />
                <input type="email" name="email" placeholder="Email" value="<?php if (!empty($_POST['email'])) {
                                                                                echo $_POST['email'];
                                                                            } ?>" required />
                <input type="password" name="password" placeholder="Password" required />
                <input type="password" name="confirmPassword" placeholder="Confirm-Password" required />
                <select id="user-type" name="userType" required>
                    <option value="" <?php if (empty($_POST['userType'])) {
                                            echo "selected";
                                        } ?>>Choose User Type:</option>
                    <option value="user" <?php if (!empty($_POST['userType']) &&  $_POST['userType'] == 'user') {
                                                echo "selected";
                                            } ?>>User</option>
                    <option value="admin" <?php if (!empty($_POST['userType']) &&  $_POST['userType'] == 'admin') {
                                                echo "selected";
                                            } ?>>Admin</option>
                </select>
                <p id="errorRegister" class="error"><?php if (!empty($error)) {
                                                        echo $error;
                                                    }   ?></p>
                <button class="login-btn" type="submit">Register</button>
            </form>
            <a href="login.php" class="signup-a">Already have a user account?</a>
        </div>
    </div>
    <?php require('footer.php') ?>
    <script src="js/signup.js"></script>
</body>

</html>