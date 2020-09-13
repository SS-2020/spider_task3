<?php
session_start();
require_once "config.php"; 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$id=$_SESSION["id"];
	if(isset($_POST['btn'])){
		$error = $file_path=$file_name="";
		if(!empty($_FILES["fileImg"])){
			$file_tmp = $_FILES["fileImg"]["tmp_name"];
			$file_name = $_FILES["fileImg"]["name"];
			if($file_name=="")
				$file_path="pictures/standard.jpg";	
			else
				$file_path = "pictures/".$file_name;		
			if(file_exists($file_path))
			{
				$error = "Sorry,The <b>".$file_name."</b> image already exist.";
			}
		}
		$reg="INSERT INTO items(type,itemname,description,quantity,price,image,uid) VALUES (?, ?, ?, ?, ?, ?, ?)";
		if($stmt = mysqli_prepare($link, $reg)){
			mysqli_stmt_bind_param($stmt, "sssiisi",$param_type,$param_name,$param_description,$param_no,$param_price,$param_img,$param_uid);
			$param_type=trim($_POST["type"]);
			$param_name=trim($_POST["iname"]);
			$param_description=trim($_POST["description"]);
			$param_no=trim($_POST["no"]);
			$param_price=trim($_POST["price"]);
			$param_img=$file_path;
			$param_uid=$id;
		if(mysqli_stmt_execute($stmt)){
			move_uploaded_file($file_tmp,$file_path);
		} else{
			printf("Error: %s.\n", mysqli_stmt_error($stmt));
			echo "Something went wrong. Please try again later.";
		}
		mysqli_stmt_close($stmt);
		}
		}
		else if(isset($_POST['update'])){
			$name=trim($_POST["iname"]);
			$description=trim($_POST["description"]);
			$no=trim($_POST["no"]);
			$price=trim($_POST["price"]);
			$img=$file_path;
			$id=trim($_POST["id"]);
			$reg="UPDATE items SET itemname='$name',description='$description',quantity='$no',price='$price' WHERE id='$id'";
			mysqli_query($link,$reg);
		}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<style>
	#add{
		display:none;
	}
	#sold{
		display:none;
	}
	.form-popup {
		display: none;
		border: 3px solid #f1f1f1;
		z-index: 9;
	}
	td{
		padding:10px;
		border: solid black 2px;
		text-align: center;
		
		font-weight:normal;
	}
	#head{
		border:none;
		font-size:20px;
		font-weight:bold;
	}
	.page-header{
	position:fixed;
	left:0px;
	top:9%;
	width:15%;
	height:100%;
	text-align:center;
	background-color: pink;
	cursor: pointer;
	}
	.tab-content{
	position:absolute;
	top:9%;
	left:18%;
	}
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top" >
        <label class="navbar-brand">ShopMart</label>
        <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="pill" onclick="fhome();">Your Products</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="pill" onclick="fadd();">ADD Product</a>
                </li>
				<li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="pill" onclick="fsold();">Sold Products</a>
                </li>
				<li class="nav-item">
                    <a href="logout.php" class="nav-link">LogOut</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-header">
        <h3>Welcome  <b><?php echo htmlspecialchars($_SESSION["username"]); ?>! </h3>
		<em><h5 style="color: green;">ID:<?php echo htmlspecialchars($_SESSION["id"]); ?><br></h5>
		<h5 style="color: blue;"><?php echo htmlspecialchars($_SESSION["email"]); ?><br></h5>
		<h5 style="color: red;"><?php echo htmlspecialchars($_SESSION["role"]); ?><br></h5></em>
  </div>
  <div class="tab-content">
    <div id="home">
	<h4>Your Products</h4>
		<?php
			echo "<table id='gallery'><tr>";
			$count = 0;
			$query="SELECT id,itemname,description,quantity,price,image FROM items WHERE uid='$id'";
			$result= mysqli_query($link,$query);
			while($data=mysqli_fetch_assoc( $result) ) {
				$iid=$data["id"];
				$name=$data["itemname"];
				$quan=$data["quantity"];
				$price=$data["price"];
				$desc=$data["description"];
				$image=$data["image"];
				echo "<td><a target='_blank' href='viewimg.php?name=".$image."'><img src='".$image."' width='80' height='100'/></a>
						<br><b>$name</b>
						<br>No:$quan
						<br>Rs.$price
						<div class='form-group'>
						<br><button class='btn-primary' onclick='openForm($iid);'>Update</button>
						</div>
					  </td>";
		?>
		<div class="form-popup" id="<?php echo $iid;?>">
			<form method="post" enctype='multipart/form-data'>
			<div class="form-group">
				<label><b>Item Name:</label>
				<input type="text" name="iname" class="form-control" value="<?php echo $name;?>">
			</div>
			<br>
			<div class="form-group">
				<label>Item Description:</label>
				<input type="text" name="description" class="form-control" value="<?php echo $desc;?>">
			</div>
			<br>
			<div class="form-group">
				<label>Quantity:</label>
				<input type="number" name="no" min="1" class="form-control" value="<?php echo $quan;?>">
			</div>
			<div class="form-group">
				<label>Price:</label>
				<input type="text" name="price" class="form-control" value="<?php echo $price;?>">
			</div>
			<input type="hidden" name="id" value="<?php echo $iid;?>">
			<div class="form-group">
				<button class='btn-primary' name="update">Turn in</button>
				<button type="button" class="btn-cancel" onclick="closeForm(<?php echo $iid;?>);">Close</button>
			</div>
			</form>
		</div>
		<?php
				$count++;
				if($count >=4){
					echo "</tr><tr>";
					$count = 0;
				}
			}
		echo "</table>";
		?>
	</div>
    <div id="add">
	<h4>ADD Products</h4>
      <h5> Fill the Product details</h5>
			<form method="post" enctype='multipart/form-data'>
			<div class="form-group">
				<label ><b>Category*:</b></label>
				<input type="radio" name="type" value="Groc" required>Grocery
				<input type="radio" name="type" value="Stat">Stationary
				<input type="radio" name="type" value="Elec">Electronics
				<input type="radio" name="type" value="Cloth">Clothing	
				<input type="radio" name="type" value="Other">Other			
			</div>
			<div class="form-group">
				<label><b>Item Name*:</label>
				<input type="text" name="iname" class="form-control" required>
			</div>
			<br>
			<div class="form-group">
				<label>Item Description:</label>
				<input type="text" name="description" class="form-control">
			</div>
			<br>
			<div class="form-group">
				<label>Quantity*:</label>
				<input type="number" name="no" min="1" class="form-control" required>
			</div>
			<div class="form-group">
				<label>Price*:</label>
				<input type="text" name="price" class="form-control" required>
			</div>
			<div class="form-group">
				<label>Image:</label>
				<input type="file" name="fileImg" class="file_input" >
			</div>
			<button name="btn">Turn in your response</button><br>
			</form>
	</div>
	<div id="sold">
	<h4>Sold Products</h4>
		<table class="table table-striped table-bordered table-sm"  width="100%">
		<thead>
		<tr>
			<th class="th-sm">Item name</th>
			<th class="th-sm">Quantity</th>
			<th class="th-sm">Customer username</th>
			<th class="th-sm">Mail id</th>
			<th class="th-sm">Purchase time</th>
		</tr>
		</thead>
		<tbody>
		<?php
			$count = 0;
			$query1="SELECT iid,name,buname,bmailid,quantity,at FROM purchase WHERE sid ='$id'";
			$result1= mysqli_query($link,$query1);
			while($row=mysqli_fetch_assoc( $result1) ) {
				echo"<tr>";
				$iid=$row["iid"];
				$quan=$row["quantity"];
				$buname=$row["buname"];
				$bmailid=$row["bmailid"];
				$at=$row["at"];
				$name=$row["name"];
					echo "<td>$name</td>
						  <td>$quan</td>
						  <td>$buname</td>
						  <td>$bmailid</td>
						  <td>$at</td>";
					echo "</tr>";
			}
		?>
		</tbody>
		</table>
	</div>
</div>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script>
	function openForm(id) {
		document.getElementById(id).style.display = "block";
	}
	function closeForm(id) {
		document.getElementById(id).style.display = "none";
	}
	function fhome(){
		document.querySelector("#home").style.display="block";
		document.querySelector("#add").style.display="none";
		document.querySelector("#sold").style.display="none";
	}
	function fadd(){
		document.querySelector("#home").style.display="none";
		document.querySelector("#add").style.display="block";
		document.querySelector("#sold").style.display="none";
	}
	function fsold(){
		document.querySelector("#home").style.display="none";
		document.querySelector("#add").style.display="none";
		document.querySelector("#sold").style.display="block";
	}
</script>
</body>
</html>