<footer>
    <div class="logo">
        <img src="images/logo7.png" alt="Logo" class="logo-img" width="64px" />
        <h1>Fashion <span>Club</span></h1>
        <p>200, Colombo Rd, Colombo-12</p>
    </div>
    <div class="item-links" id="store">
        <h2>Store</h2>
        <ul>
            <li>
                <a href="items.php?s=women">Women's Clothing</a>
            </li>
            <li>
                <a href="items.php?s=men">Men's Clothing</a>
            </li>
            <li>
                <a href="items.php?s=watches">Watches</a>
            </li>
            <li>
                <a href="items.php?s=shoes">Shoes</a>
            </li>
        </ul>
    </div>
    <div class="item-links" id="information">
        <h2>Information</h2>
        <ul>
            <li>
                <a href="contactus.html">Contact Us</a>
            </li>
            <li>
                <a href="about.html">About Us</a>
            </li>
        </ul>
    </div>
    <div class="item-links" id="account">
        <h2>My Account</h2>
        <?php
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['username'])) {
            echo "<ul>";
            echo "    <li>";
            echo "        <a href=\"login.php\">Login</a>";
            echo "    </li>";
            echo "    <li>";
            echo "        <a href=\"register.php\">Register</a>";
            echo "    </li>";
            echo "</ul>";
        } else {
            echo "<ul>";
            echo "    <li>";
            echo "        <a href=\"profile.php\">My Profile</a>";
            echo "    </li>";
            echo "    <li>";
            echo "        <a href=\"cart.php\">Cart</a>";
            echo "    </li>";
            echo "</ul>";
        }

        ?>
    </div>
    <div class="footer">
        © 2020 Fashion Club . All rights reserved | Design by "".
    </div>
</footer>