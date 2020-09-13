<?php
session_start();
require_once "config.php"; 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$id=$_SESSION["id"];
$uname=$_SESSION["username"];
$email=$_SESSION["email"];
if(isset($_POST['cart'])){
		$reg1="INSERT INTO cart(iid,sid,bid,quantity) VALUES (?, ?, ?, ?)";
		if($stmt = mysqli_prepare($link, $reg1)){
			mysqli_stmt_bind_param($stmt,"iiii",$p_iid,$p_sid,$p_bid,$p_quantity);
			$p_iid=trim($_POST["iid"]);
			$p_sid=trim($_POST["sid"]);
			$p_bid=$id;
			$p_quantity=trim($_POST["no"]);
		if(mysqli_stmt_execute($stmt)){
		} else{
			printf("Error: %s.\n", mysqli_stmt_error($stmt));
			echo "Something went wrong. Please try again later.";
		}
		mysqli_stmt_close($stmt);
	}
}
else if(isset($_POST['buy'])){
		$reg2="INSERT INTO purchase(iid,name,sid,buname,bmailid,quantity) VALUES (?, ?, ?, ?, ?, ?)";
		if($stmt = mysqli_prepare($link, $reg2)){
			mysqli_stmt_bind_param($stmt,"isissi",$p_iid,$p_name,$p_sid,$p_buname,$p_bmailid,$p_quantity);
			$p_iid=trim($_POST["iid"]);
			$p_name=trim($_POST["name"]);
			$p_sid=trim($_POST["sid"]);
			$p_buname=$uname;
			$p_bmailid=$email;
			$p_quantity=trim($_POST["quan"]);
		if(mysqli_stmt_execute($stmt)){
		} else{
			printf("Error: %s.\n", mysqli_stmt_error($stmt));
			echo "Something went wrong. Please try again later.";
		}
		$newquan=trim($_POST["newquan"]);
		$reg3="UPDATE items SET quantity='$newquan' WHERE id='$p_iid'";
		mysqli_query($link,$reg3);
		$reg4="DELETE FROM cart WHERE iid='$p_iid' AND bid='$id'";
		mysqli_query($link,$reg4);
		mysqli_stmt_close($stmt); 
	}
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
	#cart{
		display:none;
	}
	#purchases{
		display:none;
	}
	#number{
		width:3em;
	}
	td{
		paddig:10px;
		border: solid black 2px;
		text-align: center;
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
	top:7%;
	left:18%;
	}
	</style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <label class="navbar-brand">ShopMart</label>
        <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="#home" class="nav-link" data-toggle="pill" onclick="fhome();">Home</a>
                </li>
                <li class="nav-item">
                    <a href="#cart" class="nav-link" data-toggle="pill" onclick="fcart();">Cart</a>
                </li>
				<li class="nav-item">
                    <a href="#purchases" class="nav-link" data-toggle="pill" onclick="fpurchase();">Purchases</a>
                </li>
			</ul>
			<ul class="navbar-nav ml-auto">
				<form class="form-inline" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
					<input class="form-control mr-sm-2" name="find" type="search" placeholder="Search" aria-label="Search">
					<button class="btn btn-outline-success my-2 my-sm-0" name="btnsearch" type="submit">Search</button>
				</form>
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
  <div class="tab-content" style="padding:10px;">
    <div id="home">
	<p>Home</p>
      <?php
			echo "<table id='gallery'>";
			$count=$numrow=0;
			$check="";
			$query1="SELECT id,type,itemname,description,quantity,price,image,uid FROM items GROUP BY type";
			if(isset($_POST["btnsearch"])){
				$find=trim($_POST["find"]);
				$query1="SELECT id,type,itemname,description,quantity,price,image,uid FROM items WHERE itemname LIKE '%{$find}%' OR description LIKE '%{$find}%' GROUP BY type";
			}
			$result1= mysqli_query($link,$query1);
			$numrow=0;
			$numrow=mysqli_num_rows($result1);
			if($numrow==0)
				echo "No results found";
			while($data=mysqli_fetch_assoc( $result1) ) {	
				$numrow=0;
				$msg="";
				$type=$data["type"];
				if($check!=$type)
					echo "<tr><td id='head'>$type</td></tr><tr>";
				$iid=$data["id"];
				$name=$data["itemname"];
				$quan=$data["quantity"];
				$price=$data["price"];
				$desc=$data["description"];
				$image=$data["image"];
				$uid=$data["uid"];
				$iscart=mysqli_query($link,"SELECT * FROM cart WHERE iid='$iid' AND bid='$id'");
				$numrow=mysqli_num_rows($iscart);
				echo "<td><a target='_blank' href='viewimg.php?iid=$iid'><img src='".$image."' width='100' height='100'/></a>
						<br><b>$name</b>
						<br>Rs.$price";
				if($quan=='0')
				{
					$msg="Currently unavailable";
					echo "<br>$msg";
				}else{
					echo "<br>$msg";
					if($numrow==0){ ?>
						<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
							<input type='hidden' name='iid' value='<?php echo $iid;?>'>
							<input type='hidden' name='sid' value='<?php echo $uid;?>'>
							<input type='number' id="number" name='no' min='1' max='<?php echo $quan;?>' required>
							<br>
							<div class='form-group'>
							<button class='btn-primary' name='cart' >Add to Cart</button>
							</div>
						</form>
							<?php }else{ ?>
							<div class='form-group'>
								<button class='btn-primary' name='gotocart' onclick="fcart();">Go to Cart</button>
							</div>
				<?php } }?>
						<?php
					echo "</td>";
				$check=$type;
				$count++;
				if($count >=4){
					echo "</tr><tr>";
					$count = 0;
				}
			} 
			echo"</table>";
			?>
	</div>
	<div id="cart">
		<p>CART</p>
		<span><?php
			echo "<table id='gallery'>";
			$count = 0;
			$check="";
			$sql="SELECT iid,quantity FROM cart WHERE bid='$id'";
			$ans=mysqli_query($link,$sql);
			$numrow=0;
			$numrow=mysqli_num_rows($ans);
			if($numrow==0)
				echo "Empty cart!";
			while($item=mysqli_fetch_assoc($ans)) {
				$msg="";
				$iid=$item["iid"];
				$quan=$item["quantity"];
				$query2="SELECT type,itemname,description,quantity,price,image,uid FROM items WHERE id='$iid' GROUP BY type";
				$result2= mysqli_query($link,$query2);
				while($data=mysqli_fetch_assoc($result2)) {
					$type=$data["type"];
					if($check!=$type)
						echo "<tr><td id='head'>$type</td></tr><tr>";
						$name=$data["itemname"];
						$price=$data["price"];
						$desc=$data["description"];
						$available=$data["quantity"];
						$image=$data["image"];
						$uid=$data["uid"];
						echo "<td><a target='_blank' href='viewimg.php?iid=$iid'><img src='".$image."' width='100' height='100'/></a>
						<br><b>$name</b>
						<br>Rs.$price";
						if($quan=='0')
						{	$msg="Currently unavailable";
							echo "<br>$msg";}
						else{
							echo "<br>$msg";
				?>
						<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
							<input type='hidden' name='iid' value='<?php echo $iid;?>'>
							<input type='hidden' name='name' value='<?php echo $name;?>'>
							<input type='hidden' name='sid' value='<?php echo $uid;?>'>
							<input type='hidden' name='quan' value='<?php echo $quan;?>'>
							<input type='hidden' name='newquan' value='<?php echo $available-$quan;?>'>
							<div class='form-group'>
								<br><button class='btn-primary' name='buy'>Buy Now</button>
							</div>
						</form>
						<?php }
					}
			}
			echo"</table>";
			?></span>
	</div>
	<div id="purchases">
		<p>Your past orders</p>
		<span><?php
			echo "<table id='gallery'>";
			$count = 0;
			$check="";
			$sql="SELECT iid,quantity FROM purchase WHERE buname='$uname'";
			$ans=mysqli_query($link,$sql);
			$numrow=0;
			$numrow=mysqli_num_rows($ans);
			if($numrow==0)
				echo "No purchases yet!";
			while($item=mysqli_fetch_assoc($ans)) {
				$iid=$item["iid"];
				$quan=$item["quantity"];
				$query2="SELECT type,itemname,description,quantity,price,image,uid FROM items WHERE id='$iid' GROUP BY type";
				$result2= mysqli_query($link,$query2);
				while($data=mysqli_fetch_assoc($result2)) {
					$type=$data["type"];
					if($check!=$type)
						echo "<tr><td id='head'>$type</td></tr><tr>";
						$name=$data["itemname"];
						$price=$data["price"];
						$desc=$data["description"];
						$available=$data["quantity"];
						$image=$data["image"];
						$uid=$data["uid"];
						echo "<td><a target='_blank' href='viewimg.php?iid=$iid'><img src='".$image."' width='100' height='100'/></a>
						<br><b>$name</b>
						<br>$quan
						<br>Rs.$price";
				}
			}
			?></span>
	</div>
  </div>
     <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
	<script>
		function fhome(){
			document.querySelector("#home").style.display="block";
			document.querySelector("#cart").style.display="none";
			document.querySelector("#purchases").style.display="none";
		}
		function fcart(){
			document.querySelector("#home").style.display="none";
			document.querySelector("#cart").style.display="block";
			document.querySelector("#purchases").style.display="none";
		}
		function fpurchase(){
			document.querySelector("#home").style.display="none";
			document.querySelector("#cart").style.display="none";
			document.querySelector("#purchases").style.display="block";
		}
	</script>
 </body>
</html>
	