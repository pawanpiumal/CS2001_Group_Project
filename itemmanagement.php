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
$SQL_GET_ITEMS = "SELECT iid,name, price,categories,discount, shippingDetails, description, imageLocation, datetime 
                  FROM ITEMS";
$resultItems = $conn->query($SQL_GET_ITEMS);


// Deleting an item
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['deleteIid'])) {
    $deleteIid = htmlspecialchars($_POST['deleteIid']);
    if (!empty($deleteIid)) {
        if (is_numeric($deleteIid)) {
            $deleteIid = preg_replace('/[^0-9]/', '', $deleteIid);
            // Delete the item
            $SQL_DELETE_ITEM = "DELETE FROM ITEMS WHERE iid='$deleteIid'";
            if ($conn->query($SQL_DELETE_ITEM)) {
                $_SESSION['deleteSuccessItems'] = "Item Deleted successfully.";
                header("Location:itemmanagement.php");
                exit();
            } else {
                $errorItemManagement = "Error deleting item. Try again later.";
            }
        } else {
            $errorItemManagement = "Error deleting item. Try again later.";
        }
    } else {
        $errorItemManagement = "Error deleting item. Try again later.";
    }
}

?>

<html>

<head>
    <title>Item Management | Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/itemmanagement.css" />
    <link rel="stylesheet" type="text/css" href="css/alert.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require('header.php') ?>
    <?php require('nav.php') ?>
    <div class="item-management-heading">
        <h1>Item Management</h1>
    </div>
    <div class="item-management-body">
        <div class="item-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Categories</th>
                        <th>Shipping Details</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $resultItems->fetch_assoc()) {
                        if ($row['imageLocation'] == "images/items/NoImage.png") {
                            $itemImageLocation =    "<div>No Image</div>";
                        } else {
                            $itemImageLocation = "  <div class=\"tooltip\">Show
                                                        <img src=\"" . $row['imageLocation'] . "\" alt=\"Item Image\" />
                                                    </div>";
                        }
                        if (strlen($row['shippingDetails']) > 15) {
                            $itemShipping = htmlentities(substr($row['shippingDetails'], 0, 15) . "...");
                        } else {
                            $itemShipping = htmlentities($row['shippingDetails']);
                        }
                        if (strlen($row['description']) > 15) {
                            $itemDescription = htmlentities(substr($row['description'], 0, 15) . "...");
                        } else {
                            $itemDescription = $row['description'];
                        }
                        if (strlen($row['description']) > 1000) {
                            $itemDescriptionTooLong = htmlentities(substr($row['description'], 0, 1000) . "...");
                        } else {
                            $itemDescriptionTooLong = htmlentities($row['description']);
                        }
                        echo "<tr>
                                <td width=\"30\">" . htmlentities($row['iid']) . "</td>
                                <td width=\"150\">" . htmlentities($row['name']) . "</td>
                                <td width=\"100\">
                                    " . $itemImageLocation . "
                                </td>
                                <td width=\"100\">LKR " . htmlentities($row['price']) . "</td>
                                <td width=\"100\">" . htmlentities($row['discount']) . "%</td>
                                <td width=\"200\">" . htmlentities($row['categories']) . "</td>
                                <td width=\"200\">
                                    <div class=\"tooltip\">" .$itemShipping . "
                                        <span class=\"tooltiptext\">" .htmlentities( $row['shippingDetails']) . "</span>
                                    </div>
                                </td>
                                <td width=\"300\">
                                    <div class=\"tooltip\">" . $itemDescription . "
                                        <span class=\"tooltiptext\">" .($itemDescriptionTooLong) . "</span>
                                    </div>
                                </td>
                                <td width=\"150\">
                                    <div class=\"action-btn-row\">
                                        <form action=\"additem.php\" method=\"GET\" class=\"edit-form\">
                                            <input type=\"number\" name=\"i\" value=\"" . htmlentities($row['iid']) . "\" hidden />
                                            <button class=\"edit\">Edit</button>
                                        </form>
                                        <form action=\"\" method=\"POST\" class=\"delete-form\">
                                            <input type=\"number\" name=\"deleteIid\" value=\"" . htmlentities($row['iid']) . "\" hidden />
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
    <script src="js/alert.js"></script>
    <script src="js/itemmanagement.js"></script>
    <?php

    if (isset($errorItemManagement)) {
        echo "<script>showAlertOK('Error','" . $errorItemManagement . "','danger','','Ok');</script>";
        unset($errorItemManagement);
    }
    if (isset($_SESSION['deleteSuccessItems'])) {
        echo "<script>showAlertOK('Success','" . $_SESSION['deleteSuccessItems'] . "','','','Ok');</script>";
        unset($_SESSION['deleteSuccessItems']);
    }
    ?>
</body>

</html>