<!DOCTYPE html>
<?php
	/*
	* Requires functions.php that contains all required functions for page.
	* Requires header.php that contains html code for header of the page.
	*
	* @Author: Joaquin Jacinto
	*/
	
	require("functions.php");
	require("header.php");
?>
		<h1 class="w3-wide w3-center">CUSTOMER REPORT</h1>
		<form method="post" action="customer.php" style="overflow:auto">
			<a class="w3-button w3-black w3-section" id="dayB">DAY</a>
			<a class="w3-button w3-black w3-section" id="weekB">WEEK</a>
			<a class="w3-button w3-black w3-section" id="monthB">MONTH</a>
			<a class="w3-button w3-black w3-section" id="yearB">YEAR</a>

			<div id="day">
				<input class="w3-input w3-border" id="dayInput" type="date" name="date">
			</div>
			<div id="week" style="display:none">
				<input class="w3-input w3-border" id="weekInput" type="week" name="week">
			</div>
			<div id="month" style="display:none">
				<input class="w3-input w3-border" id="monthInput" type="month" name="month">
			</div>
			<div id="year" style="display:none">
				<input class="w3-input w3-border" id="yearInput" type="number" placeholder="yyyy" size="4" max="9999" min="1000" name="year">
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
				/*
				* Takes and stores input from input boxes named "date", "week", "month", and "year" into respective variables.
				* Retrieves contents of database throught customer method. Displays contents onto webpage.
				*
				* @Author: Joaquin Jacinto
				*/
				
				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					$date = getInput("date");
					$week = getInput("week");
					$month = getInput("month");
					$year = getInput("year");
					$customer = customer($date, $week, $month, $year);
					
					if($customer === null)
						echo "<p class='w3-red w3-center'>Please input a date filter.</p>";
					else {
						if(count($customer) == 0)
							echo "<p class='w3-gray w3-center'>No sales were made in the inputted time.</p>";
					
						foreach ($customer as $client) {
							$customer_name = $client["customer_name"];
							$agent_name = $client["agent_name"];
							$orders = orders($customer_name, $date, $week, $month, $year);
							$orders_made = $orders[0] ["orders_made"];
							$total_amount = $orders[0] ["total_amount"];
							echo
							"<tr>
							<td>$customer_name</td>
							<td>$agent_name</td>
							<td>" . number_format($orders_made) . "</td>
							<td>" . number_format($total_amount/100, 2) . "</td>
							</tr>
							";
						}
					}
				}
			?>
		</table>
	</div>
	<script src="report.js"></script>
</body>
</html>