<html>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_GET['s']) || $_GET['s'] == "") {
    header("Location:index.php");
    exit();
} else {
    $search = $_GET['s'];;
}
// If the search is Valid get the resulds
require 'config/config.php';
// $SQL_GET_ITEMS = "SELECT ITEMS.iid as iid, name, price, categories,discount, imageLocation, AVG(rating) AS rating 
$SQL_GET_ITEMS = "SELECT ITEMS.iid as iid, name, price, categories,discount, imageLocation, CASE WHEN AVG(rating) IS NULL THEN 0 ELSE AVG(rating) END AS rating 
                  FROM RATINGS 
                  RIGHT JOIN ITEMS on RATINGS.iid=ITEMS.iid  
                  WHERE ITEMS.name LIKE '%$search%' OR ITEMS.categories LIKE '%$search%' 
                  GROUP by ITEMS.iid 
                  ORDER BY rating DESC";
$resultGetItems = $conn->query($SQL_GET_ITEMS);
if (!$resultGetItems) {
    $_SESSION['errorMsg'] = "Server error. Try again later.";
    header("Location:index.php");
    exit();
}
$conn->close();
?>

<head>
    <title>
        <?php echo ucwords($search) . " | " ?>Fashion Club
    </title>
    <link rel="stylesheet" type="text/css" href="CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="CSS/nav.css" />
    <link rel="stylesheet" type="text/css" href="css/items.css" />
    <link rel="icon" href="images/logo7.png" />
</head>

<body>
    <?php require("header.php") ?>
    <?php require("nav.php") ?>
    <div class="items-body">
        <?php
        if ($resultGetItems->num_rows <= 0) {
            echo "<h1 class=\"nothing\">Nothing found in the Category.</h1>";
        } else {
            while ($row = $resultGetItems->fetch_assoc()) {
                $ratingString ="";
                for($i=1;$i<=5;$i++){
                    if($i<=$row['rating']){
                        $ratingString = $ratingString." <img class=\"star star-full\" src=\"images/star2.png\" alt=\"star\" width=\"20\" height=\"20\" />";
                    }else{
                        $ratingString = $ratingString." <img class=\"star star-empty\" src=\"images/star2.png\" alt=\"star\" width=\"20\" height=\"20\" />";
                    }
                }
                
                if($row['discount']!=0){
                    $priceString = "<h3>LKR ".number_format((float)$row['price']-$row['discount']*$row['price']/100,2,".","")."</h3>&nbsp;"."<h4>LKR ".number_format((float)$row['price'],2,".","")."</h4>";
                }else{
                    $priceString = "<h3>LKR ".number_format((float)$row['price'],2,".","")." </h3>";
                }
                echo "
                    <div class=\"item-card\" onClick=\"location.href='item.php?i=".$row['iid']."'\">
                        <div class=\"card-header\">
                            <h2>".$row['name']."</h2>
                        </div>
                        <div class=\"card-body\">
                            <a href=\"item.php?i=".$row['iid']."\">
                                <img src=\"".$row['imageLocation']."\" alt=\"Item Image\" class=\"item-image\">
                            </a>
                        </div>
                        <div class=\"card-footer\">
                            <div class=\"card-rating\">
                                <div class=\"card-rating-body\">".
                                    $ratingString
                                ."</div>
                            </div>
                            <div class=\"card-price\">
                                ". $priceString."
                            </div>
                        </div>
                    </div>
                ";
            }
        }

        ?>
    </div>
    <?php require('footer.php') ?>
</body>

</html>