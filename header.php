<html>
<head>
	<link rel="stylesheet" href="stylesheet1.css">
	<link rel="stylesheet" href="stylesheetLato.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="general.css">

	<style>
		body {font-family: "Lato", sans-serif}
		.mySlides {display: none}
	</style>
</head>

<body>
	<div class="w3-top">
	  	<div class="w3-bar w3-black w3-card-2">
		    <a class="w3-bar-item w3-button w3-padding-large w3-hide-medium w3-hide-large w3-right" href="javascript:void(0)" onclick="myFunction()" title="Toggle Navigation Menu"><i class="fa fa-bars"></i></a>
		    <a href="order.php" class="w3-bar-item w3-button w3-padding-large w3-hide-small">ORDER</a>
		    <a href="transaction.php" class="w3-bar-item w3-button w3-padding-large w3-hide-small">TRANSACTIONS</a>
		    <a href="inventory.php" class="w3-bar-item w3-button w3-padding-large w3-hide-small">INVENTORY</a>
	    	<div class="w3-dropdown-hover w3-hide-small">
	     		 <button class="w3-padding-large w3-button" title="More">REPORT <i class="fa fa-caret-down"></i></button>
	      		<div class="w3-dropdown-content w3-bar-block w3-card-4">
			        <a href="sales.php" class="w3-bar-item w3-button">Sales</a>
			        <a href="customer.php" class="w3-bar-item w3-button">Customers</a>
			        <a href="agent.php" class="w3-bar-item w3-button">Agent</a>
	      		</div>
	    	</div>
	    	<form method="post">
				<button name="button" value="log out" class="w3-padding-large w3-button w3-right">LOG OUT</button>
			</form>
			<?php
				//Logs agent out
				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					if(isset($_POST['button']) && $_POST['button'] == "log out") {
						$_SESSION['agent_id'] = null;
						$_SESSION['cart'] = array();
						header("location: login.php");
					}
				}
			?>
	  	</div>
	</div>

	<div class="w3-container w3-content w3-padding-64" style="max-width:800px" id="tour">
