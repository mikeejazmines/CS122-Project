<!DOCTYPE html>
<?php
	require("functions.php");

	include("header.php");
?>

		<h1 class="w3-wide w3-center">SALES REPORT</h1>
		<form method="post" action="sales.php">
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
				<th>Product</th>
				<th>No. of Customers</th>
				<th>Units Sold</th>
				<th>Amount Earned</th>
			</tr>

			<?php
				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					$date = trim(filter_input(INPUT_POST, "date", FILTER_SANITIZE_STRING));
					$week = trim(filter_input(INPUT_POST, "week", FILTER_SANITIZE_STRING));
		      $month = trim(filter_input(INPUT_POST, "month", FILTER_SANITIZE_STRING));
		      $year = trim(filter_input(INPUT_POST, "year", FILTER_SANITIZE_STRING));

					$sales = sales($date, $week, $month, $year);

					foreach ($sales as $item) {
						$item_name = $item["item_name"];
						$sold = $item["units_sold"];
						$earnings = $item["earnings"];

						$customerCount = customerCount($item_name, $date, $week, $month, $year);

						$count = $customerCount[0] ["count"];

						echo
						"<tr>
						<td>$item_name</td>
						<td>$count</td>
						<td>$sold</td>
						<td>$earnings</td>
						</tr>";
					}
				}
			?>
		</table>
	</div>

	<script src="report.js"></script>
</body>
</html>
