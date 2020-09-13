<?php
session_start();
require_once "config.php";
$id=$_SESSION["id"];
$error = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$file_tmp = $_FILES["fileImg"]["tmp_name"];
	$file_name = $_FILES["fileImg"]["name"];
	$file_path = "pictures/".$file_name;	
	$data=file_get_contents($_FILES["fileImg"]["tmp_name"]);
	if(file_exists($file_path))
	{
		$error = "Sorry,The <b>".$file_name."</b> image already exist.";
	}
	else
	{
		$reg="INSERT INTO items(type,itemname,description,quantity,price,ipath,uid) VALUES (?, ?, ?, ?, ?, ?, ?)";
		if($stmt = mysqli_prepare($link, $reg)){
			mysqli_stmt_bind_param($stmt, "sssiisi",$param_type,$param_name,$param_description,$param_no,$param_price,$param_img,$param_uid);
			$param_type=trim($_POST["type"]);
			$param_name=trim($_POST["iname"]);
			$param_description=trim($_POST["description"]);
			$param_no=trim($_POST["no"]);
			$param_price=trim($_POST["price"]);
			$param_img=$file_path;
			$param_uid=$id;
		// Attempt to execute the prepared statement
		if(mysqli_stmt_execute($stmt)){
			move_uploaded_file($file_tmp,$file_path);
		} else{
			printf("Error: %s.\n", mysqli_stmt_error($stmt));
			echo "Something went wrong. Please try again later.";
		}
		}
	}
	mysqli_close($link);
	if($error=="")
		header("location: seller.php");
}

?>
