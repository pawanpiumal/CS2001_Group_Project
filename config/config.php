<?php
	// $servername = "localhost";
	// $DBusername = "root";
	// $DBpassword = "";
	// $dbName = "Fashion_Club";


$servername = "fashionclubsql.mysql.database.azure.com";
	$DBusername = "gppa@fashionclubsql";
	$DBpassword = "gpagroup@123";
	$dbName = "Fashion_Club";


	$conn = new mysqli($servername, $DBusername, $DBpassword);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	// If database is not exist create one
	if (!mysqli_select_db($conn, $dbName)) {
		$sql = "CREATE DATABASE IF NOT EXISTS " . $dbName;
		if ($conn->query($sql)) {
			$conn->select_db($dbName);
		} else {
			echo "Error creating database: " . $conn->error;
		}
	}

	$SQL_CREATE_USER_TABLE = "CREATE TABLE IF NOT EXISTS USERS
									(uid INT AUTO_INCREMENT PRIMARY KEY ,
									name VARCHAR(100),
									email VARCHAR(100) NOT NULL UNIQUE,
									password VARCHAR(100) NOT NULL,
									userType VARCHAR(10) DEFAULT 'user' NOT NULL,
									address VARCHAR(1000),
									mobileNumber CHAR(11),
									imageLocation VARCHAR(1000) DEFAULT 'images/users/user.png',
									datetime DATETIME DEFAULT NOW()
									)";

	$SQL_CREATE_ITEM_TABLE = "CREATE TABLE IF NOT EXISTS ITEMS(
									iid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
									name VARCHAR(100) NOT NULL,
									price FLOAT NOT NULL,
									categories VARCHAR(1000) NOT NULL,
									discount float default '0',
									shippingDetails VARCHAR(1000) NOT NULL,
									description VARCHAR(100000) NOT NULL,
									imageLocation VARCHAR(1000) DEFAULT 'images/items/NoImage.png',
									datetime DATETIME DEFAULT NOW()
									)";

	$SQL_CREATE_CART_TABLE = "CREATE TABLE IF NOT EXISTS CART(
									cid INT AUTO_INCREMENT PRIMARY KEY,
									uid INT NOT NULL,
									iid INT NOT NULL,
									datetime DATETIME DEFAULT NOW(),
									CONSTRAINT FK_CART_USERID  FOREIGN KEY (uid) REFERENCES USERS(uid) ON DELETE CASCADE ON UPDATE CASCADE,
									CONSTRAINT FK_CART_ITEMID FOREIGN KEY (iid) REFERENCES ITEMS(iid) ON DELETE CASCADE ON UPDATE CASCADE
									)";

	$SQL_CREATE_PURCHASE_TABLE ="CREATE TABLE IF NOT EXISTS PURCHASES(
									pid INT AUTO_INCREMENT PRIMARY KEY,
									uid INT NOT NULL,
									itemList VARCHAR(10000) NOT NULL,
									finalPrice FLOAT NOT NULL,
									discount FLOAT NOT NULL DEFAULT '0',
									datetime DATETIME DEFAULT NOW(),
									status BOOLEAN DEFAULT FALSE,
									CONSTRAINT FK_PURCHASES_USERID FOREIGN KEY (uid) REFERENCES USERS (uid) ON DELETE CASCADE ON UPDATE CASCADE
									)";
	$SQL_CREATE_CONTACT_TABLE ="CREATE TABLE IF NOT EXISTS CONTACTUS(
									cuid INT PRIMARY KEY AUTO_INCREMENT,
									name VARCHAR(100) NOT NULL,
									email VARCHAR(100) NOT NULL,
									mobileNumber CHAR(11) NOT NULL,
									subject VARCHAR(1000) NOT NULL,
									message VARCHAR(10000) NOT NULL,
									datetime DATETIME DEFAULT NOW()
									)";

	$SQL_CREATE_RATING_TABLE = "CREATE TABLE IF NOT EXISTS RATINGS(
									rid INT PRIMARY KEY AUTO_INCREMENT,
									uid INT NOT NULL,
									iid INT NOT NULL,
									rating INT NOT NULL DEFAULT 0,
									datetime DATETIME DEFAULT NOW(),
									CONSTRAINT FK_RATING_USERID FOREIGN KEY (uid) REFERENCES USERS(uid) ON DELETE CASCADE ON UPDATE CASCADE,
									CONSTRAINT FK_RATING_ITEMID FOREIGN KEY (iid) REFERENCES ITEMS(iid) ON DELETE CASCADE ON UPDATE CASCADE,
									CONSTRAINT UNIQUE_RATING UNIQUE KEY (uid,iid)
									)";

	if($conn->query($SQL_CREATE_USER_TABLE) && $conn->query($SQL_CREATE_ITEM_TABLE) 
	&& $conn->query($SQL_CREATE_CART_TABLE) && $conn->query($SQL_CREATE_PURCHASE_TABLE)
	&& $conn->query($SQL_CREATE_CONTACT_TABLE) && $conn->query($SQL_CREATE_RATING_TABLE)){
		// echo "done";
	}else{
		die("Error creating tables: " . $conn->error);
	}
	
?>