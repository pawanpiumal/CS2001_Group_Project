<nav>
    <ul class="main-nav">
        <li class="active"><a href="index.php">Home</a></li>
        <li class="dropdown">
            <a href="javascript:void(0)">Clothing <span>▼</span></a>
            <div class="dropdown-content">
                <a href="items.php?s=women">Women's Clothing</a>
                <a href="items.php?s=men">Men's Clothing</a>
                <a href="items.php?s=kid">Kid's Wear</a>
                <a href="items.php?s=party">Party Wear</a>
            </div>
        </li>
        <li class="dropdown">
            <a href="javascript:void(0)">Personal Care <span>▼</span></a>
            <div class="dropdown-content">
                <a href="items.php?s=jewellery">Jewellery</a>
                <a href="items.php?s=watches">Watches</a>
                <a href="items.php?s=cosmetics">Cosmetics</a>
                <a href="items.php?s=shoes">Shoes</a>
            </div>
        </li>
        <li class=""><a href="contactus.php">Contact Us</a></li>
        <li class=""><a href="about.php">About us</a></li>
        <?php
        // Start a Session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['userType']) && $_SESSION['userType'] == 'admin')
            echo "
                <li class=\"dropdown\">
                    <a href=\"javascript:void(0)\">Admin Menu<span>▼</span></a>
                    <div class=\"dropdown-content\">
                        <a href=\"additem.php\">Add Items</a>
                        <a href=\"itemmanagement.php\">Item Management</a>
                        <a href=\"usermanagement.php\">User Management</a>
                        <a href=\"purchases.php\">User Purchases</a>
                        <a href=\"contactusadmin.php\">Contact Messages</a>
                    </div>
                </li>
            ";
        ?>
    </ul>
    <?php

// If the imagelocation for user account is empty show the default user image
if (!empty($_SESSION['imageLocation'])) {
    $imageLocation = $_SESSION['imageLocation'] . "?" . filemtime($_SESSION['imageLocation']);
} else {
    $imageLocation = 'images/users/user.png' . "?" . filemtime("images/users/user.png");
    // The reason to append the file modified time to the path is to make a way around the browsers cache
    // When changing the image the users image is same (if the extension is the same)
    // For an example if the previous file is example.jpg the next file will also be 1.jpg
    // Which makes the browser to not update the image in the session beacuse of the cache
    // Hence after changing the name the borwser thinks this is a new file
}
//If the user is logged in show the User account drop down
// If the user is not logged show the navigations to login and signup
if (isset($_SESSION['username'])) {
    echo "<ul class=\"sign-nav profile-btn\">";
    echo "    <li class=\"dropdown\">";
    echo "        <a href=\"javascript:void(0)\" class=\"user-profile-btn\">";
    echo "            <div style='background-image:url(" . $imageLocation . ");' class=\"img\"></div>";
    //echo "            <img src=\"" . $imageLocation . "\" alt=\"" . $_SESSION['username'] . "\" class=\"user-img\" width=\"40\" height=\"40\" />";
    echo "            <h3 class=\"user-name\">" . $_SESSION['username'] . " <span>▼</span></h3>";
    echo "        </a>";
    echo "        <div class=\"dropdown-content\">";
    echo "            <a href=\"profile.php\">My Profile</a>";
    echo "            <a href=\"cart.php\">Cart</a>";
    echo "            <a href=\"mypurchases.php\">My Purchases</a>";
    echo "            <hr />";
    echo "            <a href=\"logout.php\">Logout</a>";
    echo "        </div>";
    echo "    </li>";
    echo "</ul>";
} else {
    echo "<ul class=\"sign-nav\">";
    echo "        <li><a href=\"login.php\">Login</a></li>";
    echo "        <li><a href=\"register.php\">Register</a></li>";
    echo "</ul>";
}
    ?>


</nav>