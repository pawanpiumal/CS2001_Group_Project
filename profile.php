<html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//If the user is not logged in redirect the user to the login page
if (!isset($_SESSION['username'])) {
    // $_SESSION['errorMsg'] = "Login before navigating to the Profile page.";
    header("Location:login.php");
    exit();
}
// If an user is logged in get the sql connection and the users detials
require 'config/config.php';
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$uid = $_SESSION['uid'];
$SQL_GET_USER_DETAILS = "SELECT uid,name,email,userType,address,mobileNumber,imageLocation,datetime FROM USERS WHERE uid='$uid'";
$resultGetUserResults = $conn->query($SQL_GET_USER_DETAILS);
if ($resultGetUserResults->num_rows == 1) {
    $row = $resultGetUserResults->fetch_assoc();
    $username = $row['name'];
    $email = $row['email'];
    $address  = $row['address'];
    $userType = $row['userType'];
    $mobileNumber = $row['mobileNumber'];
    $imageLocation  = $row['imageLocation'];
    $dateTime = $row['datetime'];
    $uid = $row['uid'];

    // If the variables are changed in the database change the session variables too.
    // This affects the Navigation file largely because the username and the image are set from the session variables
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['imageLocation'] = $imageLocation;
} else {
    // If an error occured redirect the user and show the error.
    $_SESSION['errorMsg'] = "Server error occured. Logout and Try again later.";
    $_SESSION['destroySession']  = TRUE;
    header("Location:index.php");
    exit();
}

// The form actions if the user changed the profile image
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['imageFile'])) {
    // Check the file for validity
    // Check for file size if larger than PHP defined size stop the request
    if (!isset($_FILES['imageFile']['error']) || is_array($_FILES['imageFile']['error'])) {
        $error = "Error! File parameters are invalid.";
    } else if ($_FILES['imageFile']['error'] == UPLOAD_ERR_NO_FILE) {
        $error = "Error! No file sent.";
    } else if ($_FILES['imageFile']['error'] == UPLOAD_ERR_FORM_SIZE  || $_FILES['imageFile']['error'] == UPLOAD_ERR_INI_SIZE) {
        $error = "Error! Selected file is too large.";
    } else if ($_FILES['imageFile']['error'] == UPLOAD_ERR_OK) {
        // Directory to upload the image
        $target_dir = "images/users/";
        // Path to the image to be uploaded
        // The image is stored under user id . When a user updates another image the previous image get replaced if the image type is the same.
        $target_file = $target_dir . $uid . "." . pathinfo(basename($_FILES["imageFile"]["name"]), PATHINFO_EXTENSION);
        // Image file type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        //CHeck if the file is an image
        $check = getimagesize($_FILES["imageFile"]["tmp_name"]);
        if ($check == false) {
            $error =  "Error! Selected file is not an image.";
            //CHeck for the image size. 
            //If the size is greater then 2 000 000 bytes (approx 2MB) do not upload the file.
            // The file size is checked twice because the first time the file size is checked to ensure the php standards
            // If they are not met the code gives an error.
        } else if ($_FILES["imageFile"]["size"] > 2000000) {
            $error =  "Error! Selected file is too large.";
            // Only upload selected image types
        } else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $error = "Error! Only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            // Upload the file to the image folder
            if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $target_file)) {
                // Update the record in the user table
                $SQL_UPDATE_USER_IMAGE = "UPDATE USERS SET imageLocation ='$target_file' WHERE uid='$uid'";
                if ($conn->query(($SQL_UPDATE_USER_IMAGE))) {
                    // Refresh the page
                    unset($error);
                    header("Location:profile.php");
                } else {
                    $error = "Error! There was an error uploading your profile picture.";
                }
            } else {
                $error = "Error! There was an error uploading your profile picture.";
            }
        }
    } else {
        $error = "Error! File upload error occured. Try again later.";
    }
}
// Form actions required to edit the profile
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['edit'])) {
    // Assign the form values to variables
    $usernameEdit = htmlspecialchars(trim($_POST['username']));
    $emailEdit = htmlspecialchars($_POST['email']);
    $mobileNumberEdit = preg_replace('/[^0-9]/', '', htmlspecialchars($_POST['mobile']));
    $addressEdit = htmlspecialchars(trim($_POST['address']));
    $passwordEdit = htmlspecialchars($_POST['password']);
    $passwordConfrimEdit = htmlspecialchars($_POST['confirmPassword']);

    if (!empty($usernameEdit) || !empty($emailEdit) || !empty($mobileNumberEdit) || !empty($addressEdit) || !empty($passwordEdit) || !empty($passwordConfrimEdit)) {
        // If email is set check the format
        if (!empty($emailEdit) && !filter_var($emailEdit, FILTER_VALIDATE_EMAIL)) {
            $error = "Error! Email format is incorrect.";
            // if Password entered check the password and confirm-password are matching
        } else if (!empty($passwordEdit) && ($passwordEdit !== $passwordConfrimEdit)) {
            $error = "Error! Password and Confirm-Password mismatch.";
            // Check mobile number is only numbers and length is below 11 and above 9
        } else if (!empty($mobileNumberEdit) && (!is_numeric($mobileNumberEdit) || strlen($mobileNumberEdit) > 11 || strlen($mobileNumberEdit) < 9)) {
            $error = "Error! Invalid mobile number.";
            // If inputs are valid perform other opearations
        } else {
            $success = true; // TO keep record if all the database operations are success
            // If Email is needed to change check if the new mail exists on the database if so show an error
            if (!empty($emailEdit)) {
                $SQL_CHECK_NEW_EMAIL_EXISTS = "SELECT email FROM USERS WHERE email='$emailEdit'";
                if (($conn->query($SQL_CHECK_NEW_EMAIL_EXISTS))->num_rows > 0) {
                    $error = "Error! Email already exists.";
                    $success = false;
                } else {
                    // If the Email doesn't exist on the database update the database.
                    $SQL_UPDATE_EMAIL = "UPDATE USERS SET email = '$emailEdit' WHERE uid='$uid'";
                    if ($conn->query($SQL_UPDATE_EMAIL)) {
                        // Update the session to the new email
                        $_SESSION['email'] = $emailEdit;
                    } else {
                        $error = "Error! Server error while updating email. Try again later.";
                        $success = false;
                    }
                }
            }

            if (!empty($mobileNumberEdit)) {
                if (strlen($mobileNumberEdit) == 9) {
                    $mobileNumberEdit = "94" . $mobileNumberEdit;
                } else if (strlen($mobileNumberEdit) == 10 && substr($mobileNumberEdit, 0, 1) == 0) {
                    $mobileNumberEdit = "94" . substr($mobileNumberEdit, 1);
                }
                $SQL_UPDATE_MOBILE_NUMBER = "UPDATE USERS SET mobileNumber = '$mobileNumberEdit' WHERE uid='$uid'";
                if (!$conn->query($SQL_UPDATE_MOBILE_NUMBER)) {
                    $error = "Error! Server error while updating mobile number. Try again later.";
                    $success = false;
                }
                // If the page does not reload the variables must be updated.
            }
            if (!empty($usernameEdit)) {
                $SQL_UPDATE_USERNAME = "UPDATE USERS SET name='$usernameEdit' WHERE uid='$uid'";
                if (!$conn->query($SQL_UPDATE_USERNAME)) {
                    $error = "Error! Server error while updating username. Try again later.";
                    $success = false;
                } else {
                    $_SESSION['username'] = $usernameEdit;
                }
            }
            if (!empty($passwordEdit) && !empty($passwordConfrimEdit)) {
                $SQL_UPDATE_PASSWORD = "UPDATE USERS SET password ='$passwordEdit' WHERE uid='$uid'";
                if (!$conn->query($SQL_UPDATE_PASSWORD)) {
                    $error = "Error! Server error while updating password. Try again later.";
                    $success = false;
                }
            }
            if (!empty($addressEdit)) {
                $SQL_UPDATE_ADDRESS = "UPDATE USERS SET address ='$addressEdit' WHERE uid='$uid'";
                if (!$conn->query($SQL_UPDATE_ADDRESS)) {
                    $error = "Error! Server error while updating address. Try again later.";
                    $success = false;
                }
            }
            if ($success) {
                header("Location:profile.php");
                exit();
            }
        }
    } else {
        $error = "Error! One or more changes are required.";
    }
}
?>

<head>
    <title>Profile | Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="css/main.css" />
    <link rel="stylesheet" type="text/css" href="css/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/profile.css" />
    <link rel="stylesheet" type="text/css" href="css/alert.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require("header.php") ?>
    <?php require("nav.php") ?>
    <div class="profile-heading">
        <h1>My Profile</h1>
    </div>
    <div class="profile-body">
        <div class="profile-details-body">
            <div class="profile-image">
                <?php // Since the nav file appened the filemtime tag to the imagelocation variable the variable does not neeed to changed here  
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);  ?>" id="image-form" method="POST" class="image-form profile-image" enctype="multipart/form-data" style="background-image: url(<?php echo $imageLocation; ?>);">
                    <button type="button" id="image-delete" class="img-btn visibility-btn">
                        <h3>Delete Image</h3>
                    </button>
                    <button type="button" id="image-upload" class="img-btn">
                        <input type="file" name="imageFile" accept="image/x-png,image/jpeg" hidden />
                        <h3>Change Image</h3>
                    </button>
                </form>
            </div>
            <div class="profile-details">
                <div class="profile-name-title">
                    <h2 class="user-name"><?php echo ucwords($username); ?></h2>
                    <h3 class="user-role"><?php echo ucwords($userType); ?></h3>
                </div>
                <div class="other-details">
                    <div class="other-details-row">
                        <h4>UserID</h4>
                        <p><?php echo $uid; ?></p>
                    </div>
                    <div class="other-details-row">
                        <h4>Email</h4>
                        <p><?php echo $email; ?></p>
                    </div>
                    <?php if (!empty($mobileNumber)) {
                        // Show the Mobile Number if the user has provided it
                        echo '<div class="other-details-row">';
                        echo '    <h4>Mobile-Number</h4>';
                        echo '    <p>+' . $mobileNumber . '</p>';
                        echo '</div>';
                    }
                    if (!empty($address)) {
                        // Show the Address if the user has provided it
                        echo '<div class="other-details-row">';
                        echo '    <h4>Address</h4>';
                        echo '    <p>' . ucwords($address) . '</p>';
                        echo '</div>';
                    }
                    ?>
                    <div class="other-details-row">
                        <h4>Member Since</h4>
                        <p><?php echo date("Y-m-d", strtotime($dateTime)); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="edit-profile">
            <h1>Edit Profile</h1>
            <form action="" method="POST" id="edit-details-form" class="edit-details" enctype="multipart/form-data">
                <input type="text" name="username" placeholder="Username" value="<?php echo '' ?>" />
                <input type="email" name="email" placeholder="Email" value="<?php echo '' ?>" />
                <input type="number" name="mobile" placeholder="Mobile Number" value="<?php echo '' ?>" />
                <input type="address" name="address" placeholder="Address" value="<?php echo '' ?>" />
                <input type="password" name="password" placeholder="Password" />
                <input type="password" name="confirmPassword" placeholder="Confirm-Password" />
                <p id="error-edit" class="error"><?php
                                                    if (isset($error)) {
                                                        echo $error;
                                                    } else {
                                                        echo "";
                                                    }
                                                    ?></p>
                <input type="text" name="edit" value="" hidden /> 
                <button type="submit">Change Details</button>
            </form>
        </div>
    </div>
    <?php require('footer.php') ?>

    <script src="js/alert.js"></script>
    <script src="js/profile.js"></script>
</body>

</html>