<?php 
require_once "config.php";
$iid=$_GET['iid'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<style>
	body{ 
		background-color: #663399;
		}
	td{
		padding:10px;
		border: solid black 2px;
		position:absolute;
		left:30%;
		background-color:white;
		text-align: center;
		font-size:20px;
		font-weight:normal;
	}
	</style>
</head>
<body>
<?php
echo "<table id='gallery'><tr>";
$query="SELECT * FROM items WHERE id='$iid'";
$result= mysqli_query($link,$query);
while($data=mysqli_fetch_assoc( $result))
{
	$iid=$data["id"];
	$type=$data["type"];
	$name=$data["itemname"];
	$desc=$data["description"];
	$quan=$data["quantity"];
	$price=$data["price"];
	$desc=$data["description"];
	$image=$data["image"];
	echo "<td><a target='_blank' href='viewimg.php?iid=$iid'><img src='".$image."' width='500' height='500'/></a>
			<br><b>Type:</b>$type
			<br><b>Name:$name</b>
			<br><b>Decription:</b>$desc
			<br>No:$quan
			<br>Rs.$price
			</td>";
}	
echo "</tr></table>";
?>
</body>
</html>