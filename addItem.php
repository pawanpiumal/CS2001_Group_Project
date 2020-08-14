<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If the user is not logged in navitage to the login page
// if the user is a user navigate to the 404 page
// If the user is not a user nor admin navigate to the 404 page (Database error)
// If the user is an admin continue operatopns
if (!isset($_SESSION['userType'])) {
    header("Location:login.php");
    exit();
} else if ($_SESSION['userType'] == 'user') {
    header("Location:404.php");
    exit();
} else if ($_SESSION['userType'] != 'admin') {
    header("Location:404.php");
    exit();
}
$edit_Item = FALSE;
// If the request is to edit an existing item get the details and fill the form
if (isset($_GET['i']) && !empty($_GET['i']) && is_numeric($_GET['i'])) {
    // Make the Page title to Edit
    $edit_Item = TRUE;
    $iid = htmlspecialchars($_GET['i']);
    $SQL_GET_ITEM = "SELECT iid,name,price,categories,discount,shippingDetails,description,imageLocation 
                     FROM ITEMS 
                     WHERE iid='$iid'";
    require 'config/config.php';
    $resultGetItem = $conn->query($SQL_GET_ITEM);
    if ($resultGetItem && $resultGetItem->num_rows > 0) {
        $row = $resultGetItem->fetch_assoc();
        $iid = $row['iid'];
        $itemName = ($row['name']);
        $itemPrice = $row['price'];
        $categories = ($row['categories']);
        $discount = $row['discount'];
        $shipping = ($row['shippingDetails']);
        $description = ($row['description']);
        $itemImageLocation = $row['imageLocation'];
    } else {
        $errMsgToAlert = "The item selected is not in the database.";
        $errMsgToAlertAction = "()=>window.location.href= \"itemmanagement.php\"";
    }
    $conn->close();
}

// Form Operations of the add form submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && $edit_Item === FALSE) {
    // Assign the form values to variables
    $itemName = (trim($_POST['itemName']));
    $itemPrice = ($_POST['itemPrice']);
    $categories = (trim($_POST['categories']));
    $discount = ($_POST['discount']);
    $shipping = (trim($_POST['shipping']));
    $description = (trim($_POST['description']));

    // Check for empty values
    if (!empty($itemName) && !empty($itemPrice) && !empty($categories) && !empty($shipping) && !empty($description)) {
        // Validate Price and Discount
        if (!is_numeric($itemPrice)) {
            $error = "Error! Pice must be a number.";
        } else if (!empty($discount) && !is_numeric($discount)) {
            $error = "Error! Discount must be a number.";
        } else {
            // Format the discount and price values to 2 decimal points
            if (isset($discount)) {
                $discount = round($discount, 2);
            }
            $itemPrice = round($itemPrice);
            // Insert the Item data after validations
            $SQL_INSERT_ITEM_WITHOUT_IMAGE = "INSERT INTO ITEMS(name,price,categories,discount,shippingDetails,description) 
                                              VALUES (?,?,?,?,?,?)";
            require 'config/config.php';
            if ($PREPARED_INSERT_WITHOUT_IMAGE_STATEMENT = $conn->prepare($SQL_INSERT_ITEM_WITHOUT_IMAGE)) {
                if ($PREPARED_INSERT_WITHOUT_IMAGE_STATEMENT->bind_param('sdsdss', $itemName, $itemPrice, $categories, $discount, $shipping, $description)) {
                    if ($PREPARED_INSERT_WITHOUT_IMAGE_STATEMENT->execute()) {
                        // If Image file is selected
                        // Image File Validations
                        // Check for php file upload errors
                        $iid = $PREPARED_INSERT_WITHOUT_IMAGE_STATEMENT->insert_id;
                        $PREPARED_INSERT_WITHOUT_IMAGE_STATEMENT->close();
                        if (isset($_FILES['itemImage']) && $_FILES['itemImage']['error'] != UPLOAD_ERR_NO_FILE) {
                            if (!isset($_FILES['itemImage']['error']) || is_array($_FILES['itemImage']['error'])) {
                                $error = "Error! File parameters are invalid.";
                                // Check if a file is uploaded
                            } else if ($_FILES['itemImage']['error'] == UPLOAD_ERR_FORM_SIZE  || $_FILES['itemImage']['error'] == UPLOAD_ERR_INI_SIZE) {
                                $error = "Error! Selected file is too large.";
                                // If no errors in php side
                            } else if ($_FILES['itemImage']['error'] == UPLOAD_ERR_OK) {
                                // File Upload directory
                                $target_dir = "images/items/";
                                // File to be uploaded.
                                $target_file = $target_dir . $iid . "." . pathinfo(basename($_FILES["itemImage"]["name"]), PATHINFO_EXTENSION);
                                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                                $check = getimagesize($_FILES["itemImage"]["tmp_name"]);
                                if ($check == false) {
                                    $error =  "Error! Selected file is not an image.";
                                } else if ($_FILES["itemImage"]["size"] > 2000000) {
                                    $error =  "Error! Selected file is too large.";
                                } else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                    $error = "Error! Only JPG, JPEG, PNG & GIF files are allowed.";
                                } else {
                                    if (move_uploaded_file($_FILES["itemImage"]["tmp_name"], $target_file)) {
                                        $SQL_INSERT_ITEM_IMAGE = "UPDATE ITEMS SET imageLocation='$target_file' WHERE iid='$iid'";
                                        if (!$conn->query($SQL_INSERT_ITEM_IMAGE)) {
                                            $error = "Error! Error adding the item image. Try again later.";
                                        } else {
                                            header("Location:item.php?i=" . $iid);
                                            exit();
                                        }
                                    } else {
                                        $error = "Error! There was an error uploading item image.";
                                    }
                                }
                            } else {
                                $error = "Error! File upload error occured. Try again later.";
                            }
                        } else {
                            header("Location:item.php?i=" . $iid);
                            exit();
                        }
                    } else {
                        $error = "Error! Inserting the item details failed. Try again later.";
                    }
                } else {
                    $error = "Error! Inserting the item details failed. Try again later.";
                }
            } else {
                $error = "Error! Inserting the item details failed. Try again later.";
            }
        }
    } else {
        $error = "Error! All fields must be filled.";
    }
}

// Form operations for edit form submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && $edit_Item == TRUE) {
    // Get the form values and assign them to variables
    $itemNameEdit = (trim($_POST['itemName']));
    $itemPriceEdit = ($_POST['itemPrice']);
    $categoriesEdit = (trim($_POST['categories']));
    $discountEdit = ($_POST['discount']);
    $shippingEdit = (trim($_POST['shipping']));
    $descriptionEdit = (trim($_POST['description']));

    // Check for empty fields
    if (!empty($itemNameEdit) && !empty($itemPriceEdit) && !empty($categoriesEdit) && !empty($shippingEdit) && !empty($descriptionEdit)) {
        // Input validations
        if (!is_numeric($itemPriceEdit)) {
            $error = "Error! Pice must be a number.";
        } else if (!empty($discountEdit) && !is_numeric($discountEdit)) {
            $error = "Error! Discount must be a number.";
        } else {
            // Format the price and discount values to 2 decimal points
            if (!empty($discountEdit)) {
                $discountEdit = round($discountEdit, 2);
            }
            $itemPriceEdit = round($itemPriceEdit, 2);
            // Update the details
            require 'config/config.php';
            $SQL_UPDATE_ITEM_DETAILS = "UPDATE ITEMS 
                                        SET name=?,price=?,categories=?,discount=?,shippingDetails=?,description=?  
                                        WHERE iid=?";
            if ($PREPARED_EDIT_WITHOUT_IMAGE = $conn->prepare($SQL_UPDATE_ITEM_DETAILS)) {
                if ($PREPARED_EDIT_WITHOUT_IMAGE->bind_param('sdsdssi', $itemNameEdit, $itemPriceEdit, $categoriesEdit, $discountEdit, $shippingEdit, $descriptionEdit, $iid)) {
                    if ($PREPARED_EDIT_WITHOUT_IMAGE->execute()) {
                        // If Image file selected update
                        if (isset($_FILES['itemImage']) && $_FILES['itemImage']['error'] != UPLOAD_ERR_NO_FILE) {
                            if (!isset($_FILES['itemImage']['error']) || is_array($_FILES['itemImage']['error'])) {
                                $error = "Error! File parameters are invalid.";
                                // Check if a file is uploaded
                            } else if ($_FILES['itemImage']['error'] == UPLOAD_ERR_FORM_SIZE  || $_FILES['itemImage']['error'] == UPLOAD_ERR_INI_SIZE) {
                                $error = "Error! Selected file is too large.";
                                // If no errors in php side
                            } else if ($_FILES['itemImage']['error'] == UPLOAD_ERR_OK) {
                                // File Upload directory
                                $target_dir = "images/items/";
                                // File to be uploaded.
                                $target_file = $target_dir . $iid . "." . pathinfo(basename($_FILES["itemImage"]["name"]), PATHINFO_EXTENSION);
                                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                                $check = getimagesize($_FILES["itemImage"]["tmp_name"]);
                                if ($check == false) {
                                    $error =  "Error! Selected file is not an image.";
                                } else if ($_FILES["itemImage"]["size"] > 2000000) {
                                    $error =  "Error! Selected file is too large.";
                                } else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                    $error = "Error! Only JPG, JPEG, PNG & GIF files are allowed.";
                                } else {
                                    if (move_uploaded_file($_FILES["itemImage"]["tmp_name"], $target_file)) {
                                        $SQL_INSERT_ITEM_IMAGE = "UPDATE ITEMS SET imageLocation='$target_file' WHERE iid='$iid'";
                                        if (!$conn->query($SQL_INSERT_ITEM_IMAGE)) {
                                            $error = "Error! Error adding the item image. Try again later.";
                                        } else {
                                            header("Location:item.php?i=$iid");
                                            exit();
                                            // $error = "Item updated <a href=\"item.php?i=$iid\">Go to item</a>";
                                        }
                                    } else {
                                        $error = "Error! There was an error uploading item image.";
                                    }
                                }
                            } else {
                                $error = "Error! File upload error occured. Try again later.";
                            }
                        } else {
                            header("Location:item.php?i=" . $iid);
                            exit();
                        }
                    } else {
                        $error = "Error! Updating item details failed. Try again later.";
                    }
                } else {
                    $error = "Error! Updating item details failed. Try again later.";
                }
            } else {
                $error = "Error! Updating item details failed. Try again later.";
            }
        }
    } else {
        $error = "Error! All fields must be filled.";
    }
}

?>
<html>

<head>
    <title><?php if ($edit_Item == TRUE) {
                echo "Edit Item";
            } else {
                echo "Add Item";
            } ?> | Fashion Club</title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/additem.css" />
    <link rel="stylesheet" type="text/css" href="css/alert.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require('header.php') ?>
    <?php require('nav.php') ?>
    <div class="add-item-heading">
        <h1><?php if ($edit_Item == TRUE) {
                echo "Edit Item";
            } else {
                echo "Add Item";
            } ?></h1>
    </div>
    <div class="add-item-body">
        <div class="image-body">
            <?php
            if (!isset($itemImageLocation)) {
                $itemImageLocation = "images/items/NoImage.png";
            }
            ?>
            <div id="item-image" class="item-image" style="background-image: url(<?php echo $itemImageLocation; ?>);">
                <div class="remove-image" id="remove-image-btn">
                </div>
                <div class="change-image" id="image-change-btn">
                    Change Image
                </div>
                <input type="text" value="<?php echo $itemImageLocation; ?>" hidden />
            </div>
        </div>
        <div class="item-desc">
            <form action="" method="POST" class="add-item" id="add-item-form" enctype="multipart/form-data">
                <div class="row">
                    <label>Item Name</label>
                    <input type="text" name="itemName" placeholder="Item Name" <?php if (isset($itemName)) {
                                                                                    echo "value='$itemName'";
                                                                                } ?> required />
                </div>
                <div class="row">
                    <label>Price</label>
                    <input type="number" name="itemPrice" step="0.01" placeholder="Price (LKR)" <?php if (isset($itemPrice)) {
                                                                                                    echo "value='$itemPrice'";
                                                                                                } ?> min=0 required />
                </div>
                <div class="row">
                    <label>Categories</label>
                    <input type="text" name="categories" placeholder="Categories (Eg:- Jewellery , Women  )" <?php if (isset($categories)) {
                                                                                                                    echo "value='$categories'";
                                                                                                                } ?> required />
                </div>
                <div class="row">
                    <label>Discount</label>
                    <input type="number" name="discount" step="0.01" placeholder="Discount Precentage" min=0 max=100 <?php if (isset($discount)) {
                                                                                                                            echo "value='$discount'";
                                                                                                                        } ?> />
                </div>
                <!-- <div class="row-2"> -->
                <!-- <label>Image</label> -->
                <input type="file" name="itemImage" id="item-image-selector" accept="image/x-png,image/jpeg" hidden />
                <!-- </div> -->
                <div class="row-2">
                    <label>Shipping Details</label>
                    <input type="text" name="shipping" placeholder="Eg:- Item will be shipped out within 2-3 working days." <?php if (isset($shipping)) {
                                                                                                                                echo "value='$shipping'";
                                                                                                                            } ?> required />
                </div>
                <div class="row-2">
                    <label>Description</label>
                    <textarea name="description" placeholder="Description" required><?php if (isset($description)) {
                                                                                        echo $description;
                                                                                    } ?></textarea>
                </div>

                <div class="row-2">
                    <p class="error" id="add-item-error"><?php if (isset($error)) {
                                                                echo $error;
                                                            } ?></p>
                </div>
                <div class="row-2">
                    <button type="submit" class="add-item-btn">
                        <?php if ($edit_Item == TRUE) {
                            echo "Edit Item";
                        } else {
                            echo "Add Item";
                        } ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php require('footer.php') ?>
    <script src="js/alert.js"></script>
    <script src="js/additem.js"></script>
    <script>
        <?php
        if (isset($errMsgToAlert) && !empty($errMsgToAlert) && !empty($errMsgToAlertAction)) {
            echo "showAlertOK(\"Error\",'" . $errMsgToAlert . "',\"danger\"," . $errMsgToAlertAction . ",\"Redirect\");";
            unset($errMsgToAlert);
            unset($errMsgToAlertAction);
        } else if (isset($errMsgToAlert) && !empty($errMsgToAlert)) {
            echo "showAlertOK(\"Error\",'" . $errMsgToAlert . "',\"danger\",'',\"Redirect\");";
            unset($errMsgToAlert);
        }
        ?>
    </script>
</body>

</html>