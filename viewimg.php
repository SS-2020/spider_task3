<?php 
require_once "config.php";
$name=$_GET['name'];
$query="SELECT * FROM items WHERE name='$name'";
$result= mysqli_query($link,$query);
$row=mysqli_fetch_assoc( $result);
header("Content-Type: image/jpeg");
echo $row['data'];
?>