<html>
<?php
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
if (isset($_SESSION['uid'])) {
   $uid = $_SESSION['uid'];
   // Get if the user have not added profile information
   require 'config/config.php';
   $SQL_SELECT_USER_INFORMATION = "SELECT address, mobileNumber FROM USERS WHERE uid='$uid'";
   if ($result = $conn->query($SQL_SELECT_USER_INFORMATION)) {
      $row = $result->fetch_assoc();
      if (empty($row['address']) && empty($row['mobileNumber'])) {
         $emptyMsg = "Please fill the address and mobile number in the <a href=\"profile.php\">profile page.</a>";
      } else if (empty($row['address'])) {
         $emptyMsg = "Please fill the address in the <a href=\"profile.php\">profile page.</a>";
      } else if (empty($row['mobileNumber'])) {
         $emptyMsg = "Please fill the mobile number in the <a href=\"profile.php\">profile page.</a>";
      }
   }
}

?>

<head>
   <title>Fashion Club</title>
   <link rel="stylesheet" type="text/css" href="CSS/main.css" />
   <link rel="stylesheet" type="text/css" href="CSS/index.css" />
   <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
   <link rel="stylesheet" type="text/css" href="CSS/alert.css" />
   <link rel="icon" href="images/logo7.png" />
</head>

<body>
   <?php 
   $pageName = "index";
   require("header.php"); 
   ?>
   <?php require("nav.php") ?>
   <?php
   if (isset($emptyMsg)) {
      echo
         "<div class=\"notification-bar \">
            <p> " . $emptyMsg . "</p>
            <div class=\"close-btn\">
            </div>
         </div>";
   }
   ?>

   <div class="index-body">
      <div class="banner">
         <div class="banner-text ">
            <h2 class="banner-welcome img-text">Welcome to</h2>
            <h1 class="banner-heading">FASHION <span class="img-text">CLUB</span></h1>
            <p class="banner-desc">Stay Home &amp; Keep Shopping</p>
         </div>
      </div>
      <progress id="banner-progress" value="40" max="100"></progress>
      <div class="item-categories">
         <div id="item1"><a href="items.php?s=women">
               <div class="category cat-high" id="cat1">
                  <h3>Women's Clothing</h3>
               </div>
            </a></div>
         <div id="item2"><a href="items.php?s=shoes">
               <div class="category cat-low" id="cat2">
                  <h3>Shoes</h3>
               </div>
            </a></div>
         <div id="item3"><a href="items.php?s=watches">
               <div class="category cat-low" id="cat3">
                  <h3>Watches</h3>
               </div>
            </a></div>
         <div id="item4"><a href="items.php?s=jewellery">
               <div class="category cat-low" id="cat4">
                  <h3>Jewellery</h3>
               </div>
            </a></div>
         <div id="item5"><a href="items.php?s=handbags">
               <div class="category cat-low" id="cat5">
                  <h3>Handbags</h3>
               </div>
            </a></div>
      </div>

      <div class="item-categories" id="item-categories-2">
         <div id="item6"><a href="items.php?s=casual">
               <div class="category cat-high" id="cat6">
                  <h3>Casual Wear</h3>
               </div>
            </a></div>
         <div id="item7"><a href="items.php?s=men">
               <div class="category cat-high" id="cat7">
                  <h3>Men's Clothing</h3>
               </div>
            </a></div>
         <div id="item8"><a href="items.php?s=party">
               <div class="category cat-high" id="cat8">
                  <h3>Party Wear</h3>
               </div>
            </a></div>
      </div>

      <div class="item-categories" id="item-categories-3">
         <div id="item9"><a href="items.php?s=kid">
               <div class="category cat-low" id="cat9">
                  <h3>Kid's Wear</h3>
               </div>
            </a></div>
         <div id="item10"><a href="items.php?s=party">
               <div class="category cat-low" id="cat10">
                  <h3>Party Wear</h3>
               </div>
            </a></div>
         <div id="item11"><a href="items.php?s=outdoor">
               <div class="category cat-low" id="cat11">
                  <h3>Outdoor Wear</h3>
               </div>
            </a></div>
         <div id="item12"><a href="items.php?s=baby">
               <div class="category cat-low" id="cat12">
                  <h3>Baby Wear</h3>
               </div>
            </a></div>
      </div>
   </div>
   <?php require('footer.php') ?>
   <script src="js/alert.js"></script>
   <script src="js/index.js"></script>
   <?php
   // If redirected from another page display the error message as an alert.
   if (isset($_SESSION['errorMsg']) && isset($_SESSION['destroySession']) && $_SESSION['destroySession'] == TRUE) {
      echo "<script>showAlertOK('Error','" . $_SESSION['errorMsg'] . "','danger',()=>window.location.href=\"logout.php\",'Logout');</script>";
      session_destroy();
   } else if (isset($_SESSION['errorMsg'])) {
      echo "<script>showAlertOK('Error','" . $_SESSION['errorMsg'] . "','danger','','Ok');</script>";
      unset($_SESSION['errorMsg']);
   }
   ?>
</body>

</html>