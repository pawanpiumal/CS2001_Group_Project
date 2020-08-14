<html>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Redirect to 404 page if the item number is wrong
if (!isset($_GET['i']) || $_GET['i'] == "" || !is_numeric($_GET['i'])) {
    header("Location:404.php");
    exit();
} else {
    // If item number format is correct get the item from the database
    $iid = htmlspecialchars($_GET['i']);
    require 'config/config.php';
    // Get the item deteails
    // REPLACE(column_name, CHAR(13) + CHAR(10), '<br/>')
    $SQL_GET_ITEM_DETAILS = "SELECT iid,name,price,categories,discount,shippingDetails,
                                description,imageLocation 
                            FROM ITEMS 
                            WHERE iid='$iid'";
    $resultGetItemDetails = $conn->query($SQL_GET_ITEM_DETAILS);
    if ($resultGetItemDetails->num_rows > 0) {
        $row = $resultGetItemDetails->fetch_assoc();
        $iid = htmlentities($row['iid']);
        $itemName =htmlentities($row['name']);
        $itemPrice =htmlentities($row['price']);
        $categories =htmlentities($row['categories']);
        $discount =htmlentities($row['discount']);
        $shipping =htmlentities($row['shippingDetails']);
        $description =nl2br(htmlentities($row['description']));
        $itemImageLocation =htmlentities($row['imageLocation']);
    } else {
        // If the item does not exist in the database redirect to the 404 page
        header("Location:404.php");
        exit();
    }
    // If Item exists get the ratings
    $SQL_GET_ITEM_RATINGS = "SELECT AVG(rating),COUNT(rating) FROM RATINGS WHERE iid='$iid'";
    $resultGetItemRatings = $conn->query($SQL_GET_ITEM_RATINGS);
    $row = $resultGetItemRatings->fetch_assoc();
    $rating = $row['AVG(rating)'];
    $ratingsCount = $row['COUNT(rating)'];
    $conn->close();
}
// Adding a rating
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['starRating'])) {
    $ratingUser = round(htmlentities($_POST['starRating']), 0);
    // Validations
    if (!empty($ratingUser)) {
        if (is_numeric($ratingUser)) {
            if ($ratingUser >= 1 && $ratingUser <= 5) {
                // Insert the data
                // Check if the user already added a rating
                $uid = $_SESSION['uid'];
                require 'config/config.php';
                $SQL_CHECK_USER_RATING = "SELECT rating FROM RATINGS WHERE iid='$iid' AND uid='$uid'";
                $result = $conn->query($SQL_CHECK_USER_RATING);
                if ($result->num_rows > 0) {
                    $SQL_UPDATE_USER_RATING = "UPDATE RATINGS SET rating='$ratingUser' WHERE uid='$uid' AND iid='$iid'";
                    if($conn->query($SQL_UPDATE_USER_RATING)){
                        header("Location:item.php?i=".$iid);
                        exit(); 
                     }else{
                         $errorAlert = "Error updating the rating. Try again later.";
                     }
                } else {
                    // Insert Data
                    $SQL_INSERT_USER_RATING = "INSERT INTO RATINGS(uid,iid,rating) VALUES('$uid','$iid','$ratingUser')";
                    if($conn->query($SQL_INSERT_USER_RATING)){
                       header("Location:item.php?i=".$iid);
                       exit(); 
                    }else{
                        $errorAlert = "Error updating the rating. Try again later.";
                    }
                }
                $conn->close();
            } else {
                $errorAlert = "Rating should be between 1 and 5.";
            }
        } else {
            $errorAlert = "Rating should be a number.";
        }
    } else {
        $errorAlert = "Rating should be a number between 1 and 5.";
    }
}

// Add to card form submission
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['addToCart'])){
    require 'config/config.php';
    // Can add the same item multiple times to the cart
    $uidCart = $_SESSION['uid'];
    $iidCart = $_GET['i'];
    $SQL_ADD_TO_CART = "INSERT INTO CART (uid,iid) VALUES ('$uidCart','$iidCart')";
    if($conn->query($SQL_ADD_TO_CART)){
        header("Location:cart.php");
        exit();
    }else{
        $errorAlert="Adding to cart failed. Try again later.";
    }
    $conn->close();
}

?>

<head>
    <title><?php echo ucwords($itemName) . " | " ?>Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/item.css" />
    <link rel="stylesheet" type="text/css" href="css/alert.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require('header.php') ?>
    <?php require('nav.php') ?>
    <div class="item-body">
        <div class="item-image">
            <img src="<?php echo $itemImageLocation;  ?>" alt="Item Image" />
        </div>
        <div class="item-details">
            <h1><?php echo ucwords($itemName) ?></h1>
            <p>Processing Time: <?php echo ucfirst($shipping); ?></p>
            <div class="review-row">
                <div class="star-rating" title="<?php  echo floor($rating)." Stars"; ?>">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo  "<img class=\"star star-full\" src=\"images/star2.png\" alt=\"star\" width=\"20\" height=\"20\" ondragstart=\"return false;\"/>";
                        } else {
                            echo  "<img class=\"star star-empty\" src=\"images/star2.png\" alt=\"star\" width=\"20\" height=\"20\" ondragstart=\"return false;\"/>";
                        }
                    }
                    ?>

                    <p class="star-rating-text" title="Rating Count"><?php echo $ratingsCount;  ?></p>
                </div>
                <?php
                if (isset($_SESSION['username'])) {
                    echo "<a href=\"javascript:void(0)\" class=\"add-rating\">Add Rating</a>";
                }
                ?>

            </div>
            <hr />
            <div class="price-row">
                <?php
                if (!empty($discount)) {
                    echo "<h3>LKR " . number_format((float)$itemPrice - $itemPrice * ($discount / 100), 2, ".", ",") . "</h3>";
                    echo "<h4> LKR " . number_format((float)$itemPrice, 2, ".", ",") . "</h4>";
                    echo "<h3 class=\"discount-precentage\">$discount% OFF</h3>";
                } else {
                    echo "<h3>LKR " . number_format((float)$itemPrice, 2, '.', ',') . "</h3>";
                }
                ?>
            </div>
            <div class="add-cart-row">
                <form action="" method="POST" class="add-to-cart-form">
                    <?php
                    if (isset($_SESSION['username'])) {
                        echo "<input type='text' name='addToCart' value='".$iid."' hidden />";
                        echo "<button class=\"btn-add\">Add to Cart</button>";
                    }
                    ?>
                </form>
            </div>

        </div>
        <div class="desc-row">
            <h2>Description</h2>
            <p><?php echo $description; ?></p>
        </div>
    </div>
    <?php require('footer.php') ?>
    <div class="overlay visibility" id="rating-overlay">
        <div class="alert">
            <form action="" method="POST" id="start-rating-form">
                <h1 class="alert-title">Rate Item</h1>
                <div class="alert-body">
                    <div class="rate-slider">
                        <input type="number" name="starRating" min=0 max=5 id="star-rating-input" hidden readonly value=0 />
                        <img class="star star-empty" id="star-5" src="images/star2.png" alt="star" width="34" height="34" ondragstart="return false;" />
                        <img class="star star-empty" id="star-4" src="images/star2.png" alt="star" width="34" height="34" ondragstart="return false;" />
                        <img class="star star-empty" id="star-3" src="images/star2.png" alt="star" width="34" height="34" ondragstart="return false;" />
                        <img class="star star-empty" id="star-2" src="images/star2.png" alt="star" width="34" height="34" ondragstart="return false;" />
                        <img class="star star-empty" id="star-1" src="images/star2.png" alt="star" width="34" height="34" ondragstart="return false;" />
                    </div>
                </div>
                <div class="btn-row" style="padding-top: 10px;">
                    <button class="btn-close" type="button">Close</button>
                    <button class="btn-save" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/alert.js"></script>
    <script src="js/item.js"></script>
    <?php
    if (isset($errorAlert) && !empty($errorAlert)) {
        echo "<script>showAlertOK('Error','$errorAlert','danger','','');</script>";
        unset($errorAlert);
    }

    ?>

</body>

</html>