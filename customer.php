<!DOCTYPE html>
<?php
	require("functions.php");

	include("header.php");
?>

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
			?>
		</table>
	</div>

	<script src="report.js"></script>
</body>
</html>
