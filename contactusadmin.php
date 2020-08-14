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
$SQL_GET_CONTACTUS_MESSAGES = "SELECT cuid,name,email,mobileNumber,subject,message 
                               FROM CONTACTUS";
$resultContactUs = $conn->query($SQL_GET_CONTACTUS_MESSAGES);


// Deleting a message
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['deleteCUID'])) {
    $deleteCUID = $_POST['deleteCUID'];
    if (!empty($deleteCUID) && is_numeric($deleteCUID)) {
        // Delete message
        $SQL_DELETE_MESSAGE = "DELETE FROM CONTACTUS
                               WHERE cuid = '$deleteCUID'";
        if ($conn->query($SQL_DELETE_MESSAGE)) {
            if ($conn->affected_rows > 0) {
                $_SESSION['deleteContactus'] = "Message Deleted successfully.";
                header("Location:contactusadmin.php");
                exit();
            } else {
                $errorContactUs = "Error deleting message. Try again later.";
            }
        } else {
            $errorContactUs = "Error deleting message. Try again later.";
        }
    } else {
        $errorContactUs = "Error deleting message. Try again later.";
    }
}

?>

<head>
    <title>Contact Messages| Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/contactusadmin.css" />
    <link rel="stylesheet" type="text/css" href="css/alert.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require('header.php') ?>
    <?php require('nav.php') ?>
    <div class="contact-heading">
        <h1>Contact Messages</h1>
    </div>
    <div class="contact-body">
        <div class="contact-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($resultContactUs->num_rows<=0){
                        echo "<tr><td colspan=\"7\">No Messages to display.</td></tr>";
                    }
                    while ($row = $resultContactUs->fetch_assoc()) {
                        $contactUsSubject = "";
                        if (strlen($row['subject']) > 25) {
                            $contactUsSubject = substr($row['subject'], 0, 25) . "...";
                        } else {
                            $contactUsSubject = $row['subject'];
                        }
                        $contactUsSubjectLong = "";
                        if (strlen($row['subject']) > 400) {
                            $contactUsSubjectLong = substr($row['subject'], 0, 400) . "...";
                        } else {
                            $contactUsSubjectLong = $row['subject'];
                        }
                        $contactUsMessage = "";
                        if (strlen($row['message']) > 25) {
                            $contactUsMessage = substr($row['message'], 0, 25) . "...";
                        } else {
                            $contactUsMessage = $row['message'];
                        }
                        $contactUsMessageLong = "";
                        if (strlen($row['message']) > 400) {
                            $contactUsMessageLong = substr($row['message'], 0, 400) . "...";
                        } else {
                            $contactUsMessageLong = $row['message'];
                        }
                        echo "
                            <tr>
                                <td>" . $row['cuid'] . "</td>
                                <td>" . $row['name'] . "</td>
                                <td><a href=\"mailto:" . $row['email'] . "\">" . $row['email'] . "</a></td>
                                <td>" . $row['mobileNumber'] . "</td>
                                <td>
                                    <div class=\"tooltip\">" . $contactUsSubject . "
                                        <span class=\"tooltiptext\">" . $contactUsSubjectLong . "</span>
                                        <p class= \"subject-long\" hidden>" . $row['subject'] . "</p>
                                    </div>
                                </td>
                                <td>
                                    <div class=\"tooltip\">" . $contactUsMessage . "
                                        <span class=\"tooltiptext\">" . $contactUsMessageLong . "</span>
                                        <p class= \"message-long\" hidden>" . $row['message'] . "</p>                                    
                                    </div>
                                </td>
                                <td>
                                    <div class=\"action-btn-row\">
                                        <form method=\"POST\" class=\"delete-form\">
                                            <input type=\"number\" name=\"deleteCUID\" value=\"" . $row['cuid'] . "\" hidden />
                                            <button class=\"delete\">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require('footer.php') ?>

    <div class="overlay visibility" id="message-alert">
        <div class="alert">
            <div class="alert-body-list">
                <div class="alert-title">Subject</div>
                <p class="alert-subject"></p>
                <div class="alert-title">Message</div>
                <p class="alert-message"></p>
            </div>
            <div class="btn-row" style="margin-top: 10px;">
                <button class="btn-close">OK</button>
            </div>
        </div>
    </div>

    <script src="js/alert.js"></script>
    <script src="js/contactusadmin.js"></script>


    <?php
    if (isset($errorContactUs)) {
        echo "<script>showAlertOK('Error','" . $errorContactUs . "','danger','','Ok');</script>";
        unset($errorContactUs);
    }
    if (isset($_SESSION['deleteContactus'])) {
        echo "<script>showAlertOK('Success','" . $_SESSION['deleteContactus'] . "','','','Ok');</script>";
        unset($_SESSION['deleteContactus']);
    }
    ?>
</body>

</html>