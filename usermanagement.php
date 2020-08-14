<html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['userType'])) {
    header("Location:login.php");
    exit();
}
if ($_SESSION['userType'] == 'user') {
    header("Location:404.php");
    exit();
}
require 'config/config.php';
$SQL_GET_USERS = "SELECT uid,name, email,mobileNumber,address, userType 
                  FROM USERS";
$resultUsers = $conn->query($SQL_GET_USERS);

// Delete User
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['deleteUID'])) {
    $deleteUID = htmlentities($_POST['deleteUID']);
    if (!empty($deleteUID)) {
        if (is_numeric($deleteUID)) {
            // Delete User
            $SQL_DELETE_USER = "DELETE FROM USERS WHERE uid='$deleteUID'";
            if ($conn->query($SQL_DELETE_USER)) {
                if ($conn->affected_rows > 0) {
                    $_SESSION['successUsers'] = "User Deleted successfully.";
                    header("Location:usermanagement.php");
                    exit();
                } else {
                    $errorUsers = "Error deleting user. Try again later.";
                }
            } else {
                $errorUsers = "Error deleting user. Try again later.";
            }
        } else {
            $errorUsers = "Error deleting user. Try again later.";
        }
    } else {
        $errorUsers = "Error deleting user. Try again later.";
    }
}

// Update User Details
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['editUID'])) {
    $editUID = htmlentities($_POST['editUID']);
    $usernameEdit = htmlentities($_POST['username']);
    $emailEdit = htmlentities($_POST['email']);
    $mobileNumberEdit = htmlentities($_POST['mobile']);
    $addressEdit = htmlentities($_POST['address']);
    $passwordEdit = htmlentities($_POST['password']);
    $confirmPasswordEdit = htmlentities($_POST['confirmPassword']);
    // Input validations
    if (!empty($editUID) && !empty($usernameEdit) && !empty($emailEdit)) {
        if (!filter_var($emailEdit, FILTER_VALIDATE_EMAIL)) {
            $errorUsers = "Email format is incorrect.";
        } else if (!empty($mobileNumberEdit) && (!is_numeric($mobileNumberEdit) || strlen($mobileNumberEdit) > 11 || strlen($mobileNumberEdit) < 9)) {
            $errorUsers = "Invalid mobile number.";
        } else if (!empty($passwordEdit) && ($passwordEdit !== $confirmPasswordEdit)) {
            $errorUsers = "Password and Confirm-Password mismatch.";
        } else if (!is_numeric($editUID)) {
            $errorUsers = "User id is invalid.";
        } else {
            // Update user details
            // Prepared Statements
            if (empty($mobileNumberEdit)) {
                $mobileNumberEdit = NULL;
            }
            if (empty($addressEdit)) {
                $addressEdit = NULL;
            }
            if (empty($passwordEdit)) {
                $passwordEdit = NULL;
            }
            $SQL_USER_DATA_UPDATE = "UPDATE USERS 
                                     SET name= ? , email = ? , mobileNumber = IFNULL(?,mobileNumber) , address = IFNULL(?,address) , password = IFNULL(?,password) 
                                     WHERE uid = ? ";
            // Check if the email already exists
            $SQL_CHECK_EMAIL_EXISTS = "SELECT email FROM USERS WHERE email='$emailEdit' AND uid!='$editUID'";
            if ($conn->query($SQL_CHECK_EMAIL_EXISTS)->num_rows <= 0) {
                if ($PREPARE_USER_DATA_UPDATE_STATEMENT = $conn->prepare($SQL_USER_DATA_UPDATE)) {
                    if ($PREPARE_USER_DATA_UPDATE_STATEMENT->bind_param('ssissi', $usernameEdit, $emailEdit, $mobileNumberEdit, $addressEdit, $passwordEdit, $editUID)) {
                        if ($PREPARE_USER_DATA_UPDATE_STATEMENT->execute()) {
                            if ($PREPARE_USER_DATA_UPDATE_STATEMENT->affected_rows > 0) {
                                if ($_SESSION['uid'] == $editUID) {
                                    $_SESSION['username'] = $usernameEdit;
                                    $_SESSION['email'] = $emailEdit;
                                    $_SESSION['successUsers'] = "User updated successfully.";
                                    header("Location:usermanagement.php");
                                    exit();
                                } else {
                                    $_SESSION['successUsers'] = "User updated successfully.";
                                    header("Location:usermanagement.php");
                                    exit();
                                }
                                $PREPARE_USER_DATA_UPDATE_STATEMENT->close();
                            } else {
                                $errorUsers = "Nothing to update.";
                            }
                        } else {
                            $errorUsers = "User data updating failed. Try again later.";
                        }
                    } else {
                        $errorUsers = "User data updating failed. Try again later.";
                    }
                } else {
                    $errorUsers = "User data updating failed. Try again later.";
                }
            } else {
                $errorUsers = "Email already belong to another account.";
            }
        }
    } else {
        $errorUsers = "All fields must be filled.";
    }
}

?>

<head>
    <title>User Management | Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/usermanagement.css" />
    <link rel="stylesheet" type="text/css" href="css/alert.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require('header.php') ?>
    <?php require('nav.php') ?>
    <div class="user-management-heading">
        <h1>User Management</h1>
    </div>
    <div class="user-management-body">
        <div class="user-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $resultUsers->fetch_assoc()) {
                        echo "
                        <tr>
                            <td width=\"30\">" . $row['uid'] . "</td>
                            <td width=\"250\">" . $row['name'] . "</td>
                            <td width=\"250\">" . $row['email'] . "</td>
                            <td width=\"150\">" . $row['mobileNumber'] . "</td>
                            <td width=\"300\">" . $row['address'] . "</td>
                            <td width=\"150\">
                                <div class=\"action-btn-row\">
                                    <form action=\"\" method=\"POST\" class=\"edit-form\">
                                        <input type=\"number\" name=\"uid\" value=\"" . $row['uid'] . "\" hidden />
                                        <input type=\"text\" name=\"username\" value=\"" . $row['name'] . "\" hidden />
                                        <input type=\"email\" name=\"email\" value=\"" . $row['email'] . "\" hidden />
                                        <input type=\"number\" name=\"mobile\" value=\"" . $row['mobileNumber'] . "\" hidden />
                                        <input type=\"text\" name=\"address\" value=\"" . $row['address'] . "\" hidden />
                                        <button class=\"edit\">Edit</button>
                                    </form>
                                    <form method=\"POST\" class=\"delete-form\">
                                        <input type=\"number\" name=\"deleteUID\" value=\"" . $row['uid'] . "\" hidden />
                                        <button class=\"delete\">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        ";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php require('footer.php') ?>
    <div class="overlay visibility" id="overlay-edit">
        <div class="alert">
            <h1 class="alert-title">Edit Profile</h1>
            <form action="" method="POST" class="edit-info">
                <div class="alert-body-inputs">
                    <div class="row">
                        <label>User ID</label>
                        <input type="hidden" name="editUID" />
                        <input type="number" name="uid" class="edit-input" placeholder="UserID" disabled />
                    </div>
                    <div class="row">
                        <label>Username</label>
                        <input type="text" name="username" class="edit-input" placeholder="Username" required />
                    </div>
                    <div class="row">
                        <label>Email</label>
                        <input type="email" name="email" class="edit-input" placeholder="Email" required />
                    </div>
                    <div class="row">
                        <label>Mobile Number</label>
                        <input type="number" name="mobile" class="edit-input" placeholder="Mobile Number" />
                    </div>
                    <div class="row">
                        <label>Address</label>
                        <input type="text" name="address" class="edit-input" placeholder="Address" />
                    </div>
                    <div class="row">
                        <label>Password</label>
                        <input type="password" name="password" class="edit-input" placeholder="Password" />
                    </div>
                    <div class="row">
                        <label>Confirm-Password</label>
                        <input type="password" name="confirmPassword" class="edit-input" placeholder="Confirm-Password" />
                    </div>
                </div>
                <p class="error" id="edit-dialog-error"></p>
                <div class="btn-row" style="padding-top: 10px;">
                    <button type="button" class="btn-close">Close</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/alert.js"></script>
    <script src="js/usermanagement.js"></script>
    <?php
    if (isset($errorUsers)) {
        echo "<script>showAlertOK('Error','" . $errorUsers . "','danger','','Ok');</script>";
        unset($errorUsers);
    }
    if (isset($_SESSION['successUsers'])) {
        echo "<script>showAlertOK('Success','" . $_SESSION['successUsers'] . "','','','Ok');</script>";
        unset($_SESSION['successUsers']);
    }
    ?>
</body>

</html>