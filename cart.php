<html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// If the user is not logged in navitage to the login page
// if the user is a user navigate to the 404 page
// If the user is not a user nor admin navigate to the 404 page (Database error)
// If the user is an admin continue operatopns
if (!isset($_SESSION['uid'])) {
    header("Location:login.php");
    exit();
}
$uid = $_SESSION['uid'];
// If logged in get the users cart items
require 'config/config.php';
$SQL_GET_CART_ITEMS = "SELECT cid,ITEMS.iid,name,discount,price,imageLocation FROM ITEMS,CART WHERE CART.iid=ITEMS.iid AND CART.uid='$uid'";
$resultGetItems = $conn->query($SQL_GET_CART_ITEMS);
if (!$resultGetItems) {
    $_SESSION['errorMsg'] = "Error getting the cart details. Try again later.";
    header("Location:index.php");
    exit();
}

// Item delete form submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['itemIdDelete'])) {
    $itemIdDelete = htmlspecialchars($_POST['itemIdDelete']);
    if (is_numeric($itemIdDelete)) {
        // Delete item from the cart
        $SQL_DELETE_ITEM_CART = "DELETE FROM CART WHERE cid='$itemIdDelete' AND uid='$uid'";
        if ($conn->query($SQL_DELETE_ITEM_CART)) {
            header("Location:cart.php");
            exit();
        } else {
            $errorCart = "Error deleting the item from the cart. Try again later.";
        }
    } else {
        $errorCart = "Error deleting the item from the cart. Try again later.";
    }
}

// Cart purchase form submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['buyCIDArray'])) {
    // Get the items as an array of strings
    $buyItem = json_decode($_POST['buyCIDArray']);
    // Initiate the items array which contains the integers of the CID
    $buyItemIDS = array();
    // Check if the Item list is an array
    // Count is checked to see if the array is empty
    if (is_array($buyItem) && count($buyItem)) {
        // Add the each cid to the array as an integer if not an integer detected show an error
        foreach ($buyItem as $element) {
            // Replace the values to number values only
            $cid = intval(preg_replace('/[^0-9]/', '', htmlspecialchars($element)));
            // if not an number intval converts it to 0
            if ($cid > 0) {
                array_push($buyItemIDS, $cid);
            } else {
                $errorCart = "Error buying the selected items. Try again later.";
                // End the foreach loop and stop the form submission
                goto end;
            }
        }
        // If the error is not set continue the code
        if (!isset($errorCart)) {
            // Check all the cids are in the users cart
            $SQL_CHECK_USERS_CART_IDS = "SELECT COUNT(cid) as count FROM CART WHERE cid IN (" . implode(',', $buyItemIDS) . ") AND uid='$uid'";
            $resultCHECK = $conn->query($SQL_CHECK_USERS_CART_IDS);
            $rowCheck = $resultCHECK->fetch_assoc();
            if ($rowCheck['count'] == count($buyItemIDS)) {
                // Insert the items to the purchase table and delete the items from the cart
                $SQL_GET_THE_ITEMS_DETAILS = "SELECT GROUP_CONCAT(name) as namelist,SUM(price) as totalprice,SUM(discount*price/100) as discount 
                                              FROM ITEMS 
                                              RIGHT JOIN CART on ITEMS.iid = CART.iid 
                                              WHERE cid in (" . implode(',', $buyItemIDS) . ")";
                $resultOfItemsList = $conn->query($SQL_GET_THE_ITEMS_DETAILS);
                if ($resultOfItemsList->num_rows > 0) {
                    $rowOfItemsList = $resultOfItemsList->fetch_assoc();
                    $allItemsNames = $rowOfItemsList['namelist'];
                    $totalPrice = $rowOfItemsList['totalprice'];
                    $totalDiscount = $rowOfItemsList['discount'];
                    $SQL_INSERT_PURCHASE_TABLE = "INSERT INTO PURCHASES(uid,itemList,finalPrice,discount) 
                                                  VALUES ('$uid','$allItemsNames','$totalPrice','$totalDiscount')";
                    if($conn->query($SQL_INSERT_PURCHASE_TABLE)){
                        $SQL_DELETE_CART_ITEMS = "DELETE FROM CART WHERE uid='$uid' AND cid in (" . implode(',', $buyItemIDS) . ")";
                        if($conn->query($SQL_DELETE_CART_ITEMS)){
                            $_SESSION['purchaseSuccess'] = "Selected items have been purchased successfully. Fill the user profile details if not alredy filled.";
                            $_SESSION['purchaseSuccessRedirect'] = "()=>window.location.href=\"profile.php\"";
                            header("Location:cart.php");
                            exit();
                        }else{
                            $errorCart = "Error buying the selected items. Try again later.";
                        }
                    }else{
                        $errorCart = "Error buying the selected items. Try again later.";
                    }
                } else {
                    $errorCart = "Error buying the selected items. Try again later.";
                }
            } else {
                $errorCart = "Error buying the selected items. Try again later.";
            }
        }
    } else {
        $errorCart = "Error buying the selected items. Try again later.";
    }
}
end:
?>

<head>
    <title>Cart | Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/cart.css" />
    <link rel="stylesheet" type="text/css" href="css/alert.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require('header.php') ?>
    <?php require('nav.php') ?>
    <div class="cart-title">
        <h1>Shopping Cart</h1>
    </div>
    <div class="cart-body">
        <div class="item-list">
            <?php
            if ($resultGetItems->num_rows > 0) {
                echo  "
                        <div class=\"select-bar\">
                            <label class=\"checkbox\">Select All
                                <input type=\"checkbox\" name=\"selectAll\">
                                <span class=\"checkmark\"></span>
                            </label>
                        </div>
                    ";
            }
            ?>
            <?php
            if ($resultGetItems->num_rows <= 0) {
                echo "
                    <div class=\"no-items\"></div>
                ";
            } else {

                while ($row = $resultGetItems->fetch_assoc()) {
                    echo "
                        <div class=\"item-body\">
                            <label class=\"checkbox\">
                                <input type=\"checkbox\" name=\"cartItem[]\"  value=\"" . $row['cid'] . "\" />
                                <span class=\"checkmark\"></span>
                            </label>
                            <div class=\"item-image\" style=\"background-image:url(" . $row['imageLocation'] . ")\"></div>
                            <div class=\"item-details\">
                                <h2 class=\"item-name\">" . $row['name'] . "</h2>
                                <div class=\"row\">
                                    <h4>Discount</h4>
                                    <p>LKR " . number_format((float)$row['discount'] * $row['price'] / 100, 2, ".", "") . "</p>
                                    <input type=\"number\" name=\"discount\" value=\"" . number_format((float)$row['discount'] * $row['price'] / 100, 2, ".", "") . "\" hidden />
                                </div>
                                <div class=\"row\">
                                    <h4>Price</h4>
                                    <p>LKR " . number_format((float)$row['price'], 2, ".", "") . "</p>
                                    <input type=\"number\" name=\"price\" value=\"" . number_format((float)$row['price'], 2, ".", "") . "\" hidden />
                                </div>
                            </div>
                            <form action=" . htmlspecialchars($_SERVER["PHP_SELF"]) . " method=\"POST\" class=\"delete-form\">
                                <div class=\"item-delete\">
                                    <input type=\"number\" name=\"itemIdDelete\" value=\"" . $row['cid'] . "\" hidden />
                                    <img src=\"images/bin.png\" alt=\"Delete\" width=\"25\" height=\"25\" class=\"bin\" ondragstart=\"return false;\" />
                                </div>
                            </form>
                        </div>
                    ";
                }
            }
            ?>
        </div>
        <div class="summery">
            <h1>Order Summery</h1>
            <div class="row">
                <h3>Subtotal</h3>
                <p id="summery-subtotal">LKR 0.00</p>
            </div>
            <div class="row">
                <h3>Discount</h3>
                <p id="summery-discount">LKR 0.00</p>
            </div>
            <hr />
            <div class="row">
                <h3>Total</h3>
                <h2 id="summery-total">LKR 0.00</h2>
            </div>
            <form action="" method="POST" class="buy-form">
                <button type="button" class="cart-button">Buy</button>
            </form>
        </div>
    </div>
    <?php require('footer.php') ?>
    <script src="js/alert.js"></script>
    <script src="js/cart.js"></script>

    <?php
    if (isset($errorCart)) {
        echo "<script>showAlertOK('Error','" . $errorCart . "','danger','','Ok');</script>";
        unset($errorCart);
    }
    if(isset($_SESSION['purchaseSuccess'])){
        echo "<script>showAlertOK('Success','" . $_SESSION['purchaseSuccess'] . "','',".$_SESSION['purchaseSuccessRedirect'].",'Redirect');</script>";
        unset($_SESSION['purchaseSuccess']);
    }
    ?>
</body>

</html>