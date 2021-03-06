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
// Get items
require 'config/config.php';
$SQL_GET_PURCHASES_TABLE = "SELECT pid,PURCHASES.uid as uid, name, email, mobileNumber,address,itemList,finalPrice,discount,status
                            FROM PURCHASES,USERS
                            WHERE PURCHASES.uid = USERS.uid";

if (!$result = $conn->query($SQL_GET_PURCHASES_TABLE)) {
    $errorPurchases = "Error getting the purchases. Try again later.";
}


if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['completePID'])) {
    $completePID = round($_POST['completePID'], 0);
    if (!empty($completePID) && is_numeric($completePID)) {
        $SQL_COMPLETE_PURCHASE = "UPDATE PURCHASES 
                                SET status=TRUE 
                                WHERE pid='$completePID'";
        if ($conn->query($SQL_COMPLETE_PURCHASE)) {
            if ($conn->affected_rows > 0) {
                $_SESSION['successPurchases'] = "Purchase completed.";
                header("Location:purchases.php");
                exit();
            } else {
                $errorPurchases = "Error updating the purchase. Try again later.";
            }
        } else {
            $errorPurchases = "Error updating the purchase. Try again later.";
        }
    } else {
        $errorPurchases = "Error updating the purchase. Try again later.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['deletePID'])) {
    $deletePID = round($_POST['deletePID'], 0);
    if (!empty($deletePID) && is_numeric($deletePID)) {
        $SQL_DELETE_PURCHASE = "DELETE FROM PURCHASES 
                                WHERE pid='$deletePID'";
        if ($conn->query($SQL_DELETE_PURCHASE)) {
            if ($conn->affected_rows > 0) {
                $_SESSION['successPurchases'] = "Purchase deleted.";
                header("Location:purchases.php");
                exit();
            } else {
                $errorPurchases = "Selected purchase is already deleted.";
            }
        } else {
            $errorPurchases = "Error deleting the purchase. Try again later.";
        }
    } else {
        $errorPurchases = "Error deleting the purchase. Try again later.";
    }
}


?>

<head>
    <title>User Purchases | Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/purchases.css" />
    <link rel="stylesheet" type="text/css" href="css/alert.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require('header.php') ?>
    <?php require('nav.php') ?>
    <div class="purchases-heading">
        <h1>My Purchases</h1>
    </div>
    <div class="purchases-body">
        <div class="purchases-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Address</th>
                        <th>Item List</th>
                        <th>Final Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        $itemListArray = explode(",", $row['itemList']);
                        $itemList = "";
                        $itemListP = "";
                        $itemCount = 0;
                        $countIterations = 0;
                        for ($i = 0; $i < count($itemListArray); $i++) {
                            $itemCount++;
                            if (count($itemListArray) - 1 != $i && $itemListArray[$i] == $itemListArray[$i + 1]) {
                                continue;
                            } else {
                                $countIterations++;
                                $itemList = $itemList . " <input type=\"text\" class=\"item-list-item\" value=\"" . $itemListArray[$i] . " x " . $itemCount . "\" hidden />";
                                if ($countIterations <= 5)
                                    $itemListP = $itemListP . " <p>" . $itemListArray[$i] . " x " . $itemCount  . "</p>";
                                $itemCount = 0;
                            }
                        }
                        if($countIterations >5){
                            $itemListP = $itemListP."<p>See More...</p>";
                        }
                        // foreach ($itemListArray as $itemName) {
                        //     $itemList = $itemList . " <input type=\"text\" class=\"item-list-item\" value=\"" . $itemName . "\" hidden />";
                        // }
                        // $itemListP = "";
                        // if(count($itemListArray)<=5){
                        //     for($i=0;$i<count($itemListArray);$i++)  {
                        //         $itemListP = $itemListP . " <p>" . $itemListArray[$i] . "</p>";
                        //     }
                        // }else{
                        //     for($i=0;$i<5;$i++)  {
                        //         $itemListP = $itemListP . " <p>" . $itemListArray[$i] . "</p>";
                        //     }
                        //     $itemListP = $itemListP."<p>See More...</p>";
                        // }
                        echo "
                            <tr>
                                <td width=\"30\">" . ($row['pid']) . "</td>
                                <td width=\"150\">" . ($row['name']) . "</td>
                                <td width=\"100\">" . $row['email'] . "</td>
                                <td width=\"100\">" . ($row['mobileNumber']) . "</td>
                                <td width=\"200\">" . ($row['address']) . "</td>
                                <td width=\"100\">
                                    <div class=\"list-btn tooltip\">
                                       " . $itemList . "
                                        Item List
                                        <span class=\"tooltiptext\">
                                            ".$itemListP."
                                        </span>
                                    </div>
                                </td>
                                <td width=\"100\">
                                    <div class=\"tooltip\">LKR " . (number_format($row["finalPrice"] - $row['discount'], 2, ".", ",")) . "
                                        <span class=\"tooltiptext\">
                                            <p>Subtotal : LKR " . (number_format($row["finalPrice"], 2, ".", ",")) . "</p>
                                            <p>Discount : LKR " . (number_format($row['discount'], 2, ".", ",")) . "</p>
                                        </span>
                                    </div>
                                </td>
                                <td width=\"100\">" . ($row['status'] ? "Completed" : "Incomplete") . "</td>
                                <td width=\"150\">
                                    <div class=\"action-btn-row\">
                                        <form action=\"\" method=\"POST\" class=\"completed-form\">
                                            <input type=\"number\" name=\"completePID\" value=\"" . $row['pid'] . "\" hidden />
                                            <button class=\"complete\" ".($row['status'] ? "disabled" : "").">Complete</button>
                                        </form>
                                        <form action=\"\" method=\"POST\" class=\"delete-form\">
                                            <input type=\"number\" name=\"deletePID\" value=\"" . $row['pid'] . "\" hidden />
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
    <div class="overlay visibility" id="item-list-overlay">
        <div class="alert">
            <h1 class="alert-title">Item List</h1>
            <div class="alert-body-list">
            </div>
            <div class="btn-row" style="padding-top: 10px;">
                <button class="btn-close">Close</button>
            </div>
        </div>
    </div>
    <script src="js/alert.js"></script>
    <script src="js/purchases.js"></script>
    <?php

    if (isset($errorPurchases)) {
        echo "<script>showAlertOK('Error','" . $errorPurchases . "','danger','','Ok');</script>";
        unset($errorPurchases);
    }
    if (isset($_SESSION['successPurchases'])) {
        echo "<script>showAlertOK('Success','" . $_SESSION['successPurchases'] . "','','','Ok');</script>";
        unset($_SESSION['successPurchases']);
    }
    ?>
</body>

</html>