<!DOCTYPE html>
<?php require("functions.php"); ?>
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
	  	</div>
	</div>

	<div class="w3-container w3-content w3-padding-64" style="max-width:800px" id="tour">
		<h1 class="w3-wide w3-center">CUSTOMER REPORT</h1>
		<form>
			<a class="w3-button w3-black w3-section" id="dayB">DAY</a>
			<a class="w3-button w3-black w3-section" id="weekB">WEEK</a>
			<a class="w3-button w3-black w3-section" id="monthB">MONTH</a>
			<a class="w3-button w3-black w3-section" id="yearB">YEAR</a>

			<div  id="day">
				<input class="w3-input w3-border" type="date" name="date">
			</div>
			<div id="week" style="display:none">
				<input class="w3-input w3-border" type="week" name="week">
			</div>
			<div id="month" style="display:none">
				<input class="w3-input w3-border" type="month" name="month">
			</div>
			<div id="year" style="display:none">
				<input class="w3-input w3-border" type="number" placeholder="yyyy" size="4" max="9999" min="1000" name="year">
			</div>

		    <button class="w3-button w3-black w3-section w3-right" type="submit">SEARCH</button>
		</form>

		<table class="w3-table-all">
			<tr>
				<th>Customer</th>
				<th>Agent</th>
				<th>Orders Made</th>
				<th>Amount Spent</th>
			</tr>

			<?php
				$customer = customer();

				if ($customer == NULL) {
					echo "<p class='w3-red w3-center'>NO DATA FOUND</p>";
				} else {
					foreach ($customer as $client) {
						$customer_name = $client["customer_name"];
						$agent_name = $client["agent_name"];

						$orders = orders($customer_name);

						$orders_made = $orders[0] ["orders_made"];
						$total_amount = $orders[0] ["total_amount"];

						echo
						"<tr>
							<td>$customer_name</td>
							<td>$agent_name</td>
							<td>$orders_made</td>
							<td>$total_amount</td>
						</tr>
						";
					}
				}
			?>
		</table>
	</div>

	<script src="report.js"></script>
</body>
</html>
