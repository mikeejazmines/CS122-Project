<!DOCTYPE html>
<?php
	require("functions.php");

	include("header.php");

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$fname = trim(filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_STRING));

		echo "$fname";

		if (empty($fname)) {
			$error_message = "Please fill in required fields.";
		} else {
			if (firstName($fname)) {
				header("Location:order.php");
				exit;
			} else {
				$error_message = "Query Failed.";
			}
		}
	}
?>

	<h1 class="w3-wide w3-center">ORDER FORM</h1>
	<form>
		<div style="overflow:auto">
			<div class="o-catalog" style="overflow:auto">
				<div>
					<h2 class="o-catalog-inputs">Item Catalog</h2>
					<div class="o-item" style="margin-right:0.5em">
						<input class="o-input" type="text" name="item_name" placeholder="Item Name">
					</div>

					<div class="o-item">
						<input class="o-input" type="text" name="category" placeholder="Category">
					</div>
				</div>

				<table class="w3-table-all" style="margin-top:0.83em">
					<tr>
						<th>Item</th>
						<th>Features</th>
						<th>Dimensions</th>
						<th>Personalization</th>
						<th>Price</th>
					</tr>

					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>

					<button id="addToCart" class="w3-button w3-black w3-section w3-right" type="submit">ADD TO CART</button>
			</div>

			<div class="o-catalog">
				<h2 class="o-catalog-inputs">Cart</h2>

				<table class="w3-table-all" style="margin-top:0.83em">
					<tr>
						<th>Item</th>
						<th>Features</th>
						<th>Dimensions</th>
						<th>Personalization</th>
						<th>Price</th>
					</tr>

					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</div>

			<div>
				<h2 class="o-amount">Amount Due:</h2>
				<input class="o-amount o-input" type="text" readonly name="amount">
			</div>

		    <a href="#details" id="proceed" class="w3-button w3-black w3-section w3-right">PROCEED</a>
			<button class="w3-button w3-black w3-section w3-right" type="reset" style="margin-right:0.5em">CANCEL</button>
		</div>
	</form>

	<form>
		<div id="details" style="display:none">
			<div>
				<h2>Customer Name</h2>
				<input class="o-input w3-border o-names" type="text" placeholder="First Name" name ="first_name" required>
				<input class="o-input w3-border o-lastnames" placeholder="Last Name" name="last_name" required>
			</div>

			<div>
				<h2>Schedule</h2>
				<input class="w3-input w3-border" type="datetime-local" name="sched" required>
			</div>

			<div>
				<h2>Gift</h2>
				Yes <input class="radio" type="radio" name="gift" name="gift_y">
				No <input class="radio" type="radio" name="gift" checked name="gift_n">
			</div>
			<div>
				<h2>Recipient(s)</h2>
				<input class="w3-input w3-border" type="text" placeholder="Recipient 1 <comma> Recipient 2 ..." name="recipients" required>
			</div>

			<div>
				<h2>Delivery Address</h2>
				<input class="w3-input w3-border" type="text" name="address" required>
			</div>

			<button class="w3-button w3-black w3-section w3-right" type="submit" style="margin-right:0.5em">SUBMIT</button>
		</div>
	</form>
</div>

<script>
	document.getElementById("proceed").onclick = function() {
		document.getElementById("details").style.display = "initial";
	};
</script>
</body>

</html>
