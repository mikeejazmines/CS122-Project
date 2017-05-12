<!DOCTYPE html>
<?php
	/*
	 * Requires functions.php that contains all required functions for page.
	 */
	 
	require("functions.php");
?>
<html>
<title> Jamal Website </title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="stylesheet1.css">
<link rel="stylesheet" href="stylesheetLato.css">
<link rel ="stylesheet" href= "general.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
	body {font-family: "Lato", sans-serif}
	.mySlides {display: none}
</style>

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
	
	  <div class="w3-container w3-content w3-padding-64" style="max-width:800px">
		<h1 class="w3-wide w3-center">AGENT TRANSACTIONS</h1>
	   	<div>
   		<a id="filters" class="w3-button w3-black">FILTERS</a>
        <div id="filterForm" class="modal">
        	<form class="modal-content" style="margin-top:0px" method="post">
        		<div class="bar">	
					<span class="close">&times;</span>
				</div>

	        	<div class="modal-body">
	        		<h2>Order Number</h2>
		            <input class="w3-input w3-border" type="text" placeholder="Order Number" name="orderNumber">

		            <h2>Agent Name </h2>
		    		<input class="o-input w3-border o-names" type="text" placeholder="First Name" name="firstAgent">  
					<input class="o-input w3-border o-lastnames" type="text" placeholder="Last Name" name = "lastAgent">

		       		<h2>Customer Name</h2>
					<input class="o-input w3-border o-names" type="text" placeholder="First Name" name =" firstCustomer">
					<input class="o-input w3-border o-lastnames" type="text" placeholder="Last Name" name ="lastCustomer">

					<h2 style="margin-bottom:0px; padding:0px">Time Period</h2>

					<a class="w3-button w3-black w3-section" id="dayB">DAY</a>
					<a class="w3-button w3-black w3-section" id="weekB">WEEK</a>
					<a class="w3-button w3-black w3-section" id="monthB">MONTH</a>
					<a class="w3-button w3-black w3-section" id="yearB">YEAR</a>

					<div id="day">
						<input id="dayInput" class="w3-input w3-border" type="date" name="day">
					</div>
					<div id="week" style="display:none">
						<input id="weekInput" class="w3-input w3-border" type="week" name="week">
					</div>
					<div id="month" style="display:none">
						<input id="monthInput" class="w3-input w3-border" type="month" name="month">
					</div>
					<div id="year" style="display:none">
						<input id="yearInput" class="w3-input w3-border" type="number" placeholder="yyyy" size="4" max="9999" min="1000" name="year">
					</div>

					<button class="w3-button w3-black w3-section w3-right" type="submit">SEND</button>
				    <button class="w3-button w3-black w3-section w3-right" style="margin-right:5px" type="reset">RESET</button>
	        	</div>
			</form>
        </div>

		<table class="w3-table-all w3-section">
			<tr>
				<th>Date</th>
				<th>Agent Name </th>
				<th>Customer Name</th>
				<th>Order Number</th>
				<th>Total Amount</th>
			</tr>
			<?php
				//Displays all the transactions which satisfy the filters stated by the user.
			
				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					$orderNumber = getInput("orderNumber");
					$firstAgent = getInput("firstAgent");
					$lastAgent = getInput("lastAgent");
					$firstCustomer = getInput("firstCustomer");
					$lastCustomer = getInput("lastCustomer");
					
					$intervals = array("day", "week", "month", "year");
					$dateType = "";
					$date = "";
					foreach($intervals as $interval) {
						if(getInput($interval) != "") {
							$dateType = $interval;
							$date = getInput($interval);
							break;
						}
					}
					$transactions = getTransactions($orderNumber, $firstAgent, $lastAgent, $firstCustomer, $lastCustomer, $dateType, $date);
					
					if($transactions === null)
						echo "<p class='w3-red w3-center'>Please input at least one filter.</p>";
					else {
						if(count($transactions) == 0)
							echo "<p class='w3-gray w3-center'>No transactions matched your filters.</p>";
						
						foreach($transactions as $transaction) {
							echo "<tr>";
							echo "<td>" . $transaction["date"] . "</td>";
							echo "<td>" . $transaction["agent name"] . "</td>";
							echo "<td>" . $transaction["customer name"] . "</td>";
							echo "<td>" . $transaction["order number"] . "</td>";
							echo "<td>" . number_format($transaction["total amount"]/100, 2) . "</td>";
							echo "</tr>";
						}
					}
				}
			?>
		</table>  
	    </div>
	    </div>
		
	<script src="report.js"></script>
	<script>
		var btn = document.getElementById("filters");
		var modal = document.getElementById("filterForm");
		var span = document.getElementsByClassName("close")[0];
		
		btn.onclick = function(){modal.style.display = "block"};
		span.onclick = function() {modal.style.display = "none"};

		window.onclick = function(event) {
		    if (event.target == modal) {
		        modal.style.display = "none";
		    }
		}
	</script>
</body>
</html>
