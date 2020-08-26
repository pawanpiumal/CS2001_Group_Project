<html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['message'])) {
    $username = htmlentities($_POST['username']);
    $email = htmlentities($_POST['email']);
    $mobile = htmlentities($_POST['mobile']);
    $subject = htmlentities($_POST['subject']);
    $message  = htmlentities($_POST['message']);
    if (!empty($username) && !empty($email) && !empty($mobile) && !empty($subject) && !empty($message)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Error! Email format is incorrect.";
        } else if (strlen($mobile) < 9 || strlen($mobile) > 11 || !is_numeric($mobile)) {
            $error = "Error! Invalid mobile number.";
        } else {
            // Insert the data
            require 'config/config.php';
            $SQL_INSERT_CONTACTUS_MSG = "INSERT INTO CONTACTUS(name,email,mobileNumber,subject,message) 
                                         VALUES (?,?,?,?,?)";
            if ($PREPARE_INSERT_CONTACTUS_STATEMENT = $conn->prepare($SQL_INSERT_CONTACTUS_MSG)) {
                if ($PREPARE_INSERT_CONTACTUS_STATEMENT->bind_param('ssiss', $username, $email, $mobile, $subject, $message)) {
                    if ($PREPARE_INSERT_CONTACTUS_STATEMENT->execute()) {
                        $_SESSION['successContactus'] = "Message sent successfully.";
                        header("Location:contactus.php");
                        exit();
                    } else {
                        $error = "Error! Sending the message failed. Try again later.";
                    }
                } else {
                    $error = "Error! Sending the message failed. Try again later.";
                }
            } else {
                $error = "Error! Sending the message failed. Try again later.";
            }
        }
    } else {
        $error = "Error! All fields must be fille.";
    }
}

?>

<head>
    <title>Contact Us | Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/contactus.css" />
    <link rel="stylesheet" type="text/css" href="css/alert.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php
    $pageName = "contact";
    require('header.php');
    ?>
    <?php require('nav.php') ?>
    <div class="contact-us-heading">
        <h1>Contact Us</h1>
    </div>
    <div class="contact-us-body">
        <div class="details-body">
            <h3>Address</h3>
            <p>200, Colombo Road, <Br /> Colombo-12</p>
            <h3>Phone</h3>
            <p>07123456789 <Br />07123456789</p>
            <h3>Email</h3>
            <p><a href="mailto:example@gmail.com">example@gmail.com</a></p>
        </div>
        <div class="contact-us-form-body">
            <form action="" method="POST" class="contact-us-form">
                <input type="text" name="username" placeholder="Your name" value="<?php echo isset($username) ? $username : "" ?>" class="contact-us-input" required />
                <input type="email" name="email" value="<?php echo isset($email) ? $email : "" ?>" placeholder="Email" class="contact-us-input" required />
                <input type="number" name="mobile" value="<?php echo isset($mobile) ? $mobile : "" ?>" placeholder="Mobile Number" class="contact-us-input" required />
                <input type="text" name="subject" value="<?php echo isset($subject) ? $subject : "" ?>" placeholder="Subject" class="contact-us-input" required />
                <textarea name="message" class="contact-us-input" placeholder="Message" required><?php echo isset($message) ? $message : "" ?></textarea>
                <p class="error"><?php echo isset($error) ?  $error : ""; ?></p>
                <button class="contact-us-form-submit">Submit</button>
            </form>
        </div>
    </div>
    <?php require('footer.php') ?>
    </script>
    <script src="js/alert.js"></script>
    <?php
    if (isset($_SESSION['successContactus'])) {
        echo "<script>showAlertOK('Success','" . $_SESSION['successContactus'] . "','','','Ok');</script>";
        unset($_SESSION['successContactus']);
    }

    ?>
</body>

</html>