<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'onlineshopping');
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$query="CREATE DATABASE IF NOT EXISTS onlineshopping";
if (mysqli_query($conn, $query));
else {
  echo "Error creating database: " . mysqli_error($conn);
}
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$sql1="CREATE TABLE IF NOT EXISTS `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(25) NOT NULL,
 `email` varchar(35) NOT NULL,
 `username` varchar(50) NOT NULL,
 `password` varchar(255) NOT NULL,
 `role` varchar(6) NOT NULL,
 `created_at` datetime DEFAULT current_timestamp(),
 PRIMARY KEY (`id`),
 UNIQUE KEY `username` (`username`),
 UNIQUE KEY `email` (`email`)
)"; 
if (mysqli_query($link, $sql1));
else {
  echo "Error creating table: " . mysqli_error($link);
}
$sql2="CREATE TABLE IF NOT EXISTS`items` (
 `id` int(10) NOT NULL AUTO_INCREMENT,
 `type` varchar(11) NOT NULL,
 `itemname` varchar(20) NOT NULL,
 `description` text NOT NULL,
 `quantity` int(10) NOT NULL,
 `price` int(10) NOT NULL,
 `image` blob NOT NULL,
 `uid` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 FOREIGN KEY (uid) REFERENCES users(id)
)";
if (mysqli_query($link, $sql2));
else {
  echo "Error creating table: " . mysqli_error($link);
}
$sql3="CREATE TABLE IF NOT EXISTS`cart` (
 `iid` int(11) NOT NULL,
 `sid` int(11) NOT NULL,
 `bid` int(11) NOT NULL,
 `quantity` int(10) NOT NULL,
 FOREIGN KEY (sid) REFERENCES users(id),
 FOREIGN KEY (bid) REFERENCES users(id),
 FOREIGN KEY (iid) REFERENCES items(id)
)";
if (mysqli_query($link, $sql3));
else {
  echo "Error creating table: " . mysqli_error($link);
}
$sql4="CREATE TABLE IF NOT EXISTS`purchase` (
 `iid` int(11) NOT NULL,
 `name` varchar(20) NOT NULL,
 `sid` int(11) NOT NULL,
 `buname` varchar(50) NOT NULL,
 `bmailid` varchar(35) NOT NULL,
 `quantity` int(10) NOT NULL,
 `at` datetime NOT NULL DEFAULT current_timestamp(),
 FOREIGN KEY (sid) REFERENCES users(id),
 FOREIGN KEY (name) REFERENCES items(iname),
 FOREIGN KEY (buname) REFERENCES users(username),
 FOREIGN KEY (bmailid) REFERENCES users(email),
 FOREIGN KEY (iid) REFERENCES items(id)
)";
if (mysqli_query($link, $sql4));
else {
  echo "Error creating table: " . mysqli_error($link);
}
?>

