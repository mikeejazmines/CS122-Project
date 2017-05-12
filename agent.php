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

		<h1 class="w3-wide w3-center">AGENT REPORT</h1>
		<form method="post" action="agent.php">
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
				<th>Agent</th>
				<th>No. of Customers</th>
				<th>Orders Taken</th>
				<th>Amount Earned</th>
			</tr>
			<?php
				/*
				* Takes and stores input from input boxes named "date", "week", "month", and "year" into respective variables.
				* Retrieves contents of database throught agent method. Displays contents onto webpage.
				*
				* @Author: Joaquin Jacinto
				*/
				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					$date = getInput("date");
					$week = getInput("week");
					$month = getInput("month");
					$year = getInput("year");
					$agent = agent($date, $week, $month, $year);
					foreach ($agent as $employee) {
						$agent_name = $employee["agent_name"];
						$num_customer = numCustomer($agent_name, $date, $week, $month, $year);
						$customer_count = $num_customer[0] ["customer_count"];
						$orders = $num_customer[0] ["orders"];
						$earnings = $num_customer[0] ["earnings"];
						echo
						"<tr>
						<td>$agent_name</td>
						<td>$customer_count</td>
						<td>$orders</td>
						<td>$earnings</td>
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