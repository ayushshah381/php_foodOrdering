<?php

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
	include("includes/config.php");
	include("includes/classes/User.php");

	if(isset($_GET['userLoggedIn'])) {
		$userLoggedIn = new User($con, $_GET['userLoggedIn']);
	}
	else {
		echo "Username variable was not passed into page. Check the openPage JS function";
		exit();
	}
}
else {
	include("includes/header.php");
}

?>


<?php 
if(isset($_POST["add_to_cart"]))
{
	if(isset($_SESSION["shopping_cart"]))
	{
		$item_array_id = array_column($_SESSION["shopping_cart"], "item_id");
		if(!in_array($_GET["id"], $item_array_id))
		{
			$count = count($_SESSION["shopping_cart"]);
			$item_array = array(
				'item_id'			=>	$_GET["id"],
				'item_name'			=>	$_POST["hidden_name"],
				'item_price'		=>	$_POST["hidden_price"],
				'item_quantity'		=>	$_POST["quantity"]
			);
			$_SESSION["shopping_cart"][$count] = $item_array;
			// $userId = mysqli_query($con,"SELECT id FROM users WHERE username = ".$userLoggedIn->getUserName());
			// echo $userId;
			$query = "INSERT INTO cart VALUES('',''," . $_GET['id'] . ",' " . $_POST['hidden_name'] . "' ," . $_POST['quantity'] .   ", " . $_POST['hidden_price'] . ")";
			mysqli_query($con,$query);
		}
		else
		{
			echo '<script>alert("Item Already Added")</script>';
		}
	}
	else
	{
		$item_array = array(
			'item_id'			=>	$_GET["id"],
			'item_name'			=>	$_POST["hidden_name"],
			'item_price'		=>	$_POST["hidden_price"],
			'item_quantity'		=>	$_POST["quantity"]
		);
		$_SESSION["shopping_cart"][0] = $item_array;
	}
}

if(isset($_GET["action"]))
{
	if($_GET["action"] == "delete")
	{
		foreach($_SESSION["shopping_cart"] as $keys => $values)
		{
			if($values["item_id"] == $_GET["id"])
			{
				unset($_SESSION["shopping_cart"][$keys]);
				mysqli_query($con,"DELETE FROM cart WHERE fid = ". $values["item_id"]);
				echo '<script>alert("Item Removed")</script>';
				echo '<script>window.location="index.php"</script>';
			}
		}
	}
}
?>

<div class="container">
            <h1 class='welcomeText'>MENU</h1>            
			<?php
				$query = "SELECT * FROM fooditems ORDER BY id ASC";
                $result = mysqli_query($con, $query);
                $i=1;
				if(mysqli_num_rows($result) > 0)
				{
                    echo "<table style='margin: 0 auto;'>
                    <th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </th>                    
                    ";
					while($row = mysqli_fetch_array($result))
					{
				?>
			<tr>
				<form method="post" action="index.php?action=add&id=<?php echo $row["id"]; ?>">
					    <td ><?php echo $i++; ?>)</td>
						<td style="padding-left:4px;"><?php echo $row["name"]; ?></td>
						<td style="padding-left:8px;padding-right:6px;">Rs <?php echo $row["price"]; ?></td>
						<td style="padding-left:8px;"><input type="text" name="quantity" value="1" class="quantityInput" /></td>
						<input type="hidden" name="hidden_name" value="<?php echo $row["name"]; ?>" />
						<input type="hidden" name="hidden_price" value="<?php echo $row["price"]; ?>" />
						<td><input type="submit" name="add_to_cart" style="background:white;border:none;cursor:pointer"  value="Add to Cart" /></td>
				</form>
			</tr>
			<?php
                    }
                echo"</table>";
				}
			?>
			<div style="clear:both"></div>
			<br />
			<h1 class='welcomeText'>CART</h1>
				<?php
                if(!empty($_SESSION["shopping_cart"]))
                {
                ?>
                <table style='margin: 0 auto;'>
					<tr>
						<th style="padding-right:6px;">Sr.No</th>
						<th style="padding-right:6px;">Item Name</th>
						<th style="padding-right:6px;">Quantity</th>
						<th style="padding-right:6px;">Price</th>
						<th style="padding-right:6px;">Total</th>
						<th style="padding-right:6px;"></th>
					</tr>
					<?php
                    $j=1;
						$total = 0;
						foreach($_SESSION["shopping_cart"] as $keys => $values)
						{
					?>
					<tr>
                        <td style="padding-left:10px;"><?php echo $j++; ?></td>
    					<td><?php echo $values["item_name"]; ?></td>
						<td style="padding-left:25px;"><?php echo $values["item_quantity"]; ?></td>
						<td style="padding-left:25px;"> <?php echo $values["item_price"]; ?></td>
						<td> <?php echo number_format($values["item_quantity"] * $values["item_price"], 2);?></td>
						<td style="padding-left:10px;"><a style="text-decoration:none;color:black;" href="index.php?action=delete&id=<?php echo $values["item_id"]; ?>"><span class="text-danger">Remove</span></a></td>
					</tr>
					<?php
							$total = $total + ($values["item_quantity"] * $values["item_price"]);
						}
					?>
					<tr>
						<td colspan="3" text-align="right" style="padding-top:10px;">Total</td>
						<td text-align="right" style="padding-top:10px;">Rs. <?php echo number_format($total, 2); ?></td>
						<td></td>
					</tr>
					<?php
                    }
                    else
                    {
                        echo "<h3 class='welcomeText'>The Cart is empty!</h3>";
                    }
					?>
						
				</table>
		</div>

<?php
    include("includes/footer.php");
	$url = $_SERVER['REQUEST_URI'];
	echo "<script>openPage('$url')</script>";
    exit();
?>