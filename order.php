<!DOCTYPE html>
<?php
	/*
	 * Requires functions.php that contains all required functions for page.
	 * Requires header.php that contains html code for header of the page.
	 *
	 * Responds to different buttons on the screen depending on its value
	 * All buttons have a name of "button".
	 */
	 
	require("header.php");
	require("functions.php");
	
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if($_POST['button'] == "addDetail") {
			//Adds a new item into the cart
			
			$quantity = getInput("quantity");
			$color = getInput("color");
			$personalization = getInput("personalization");
			$discountCode = getInput("discountCode");
			
			addToCart($quantity, $color, $personalization, $discountCode);
			$_SESSION['catalog'] = array();
		}
		else if($_POST['button'] == "cancel") {
			//Empties both the cart and the catalog
			
			$_SESSION["cart"] = array();
			$_SESSION["catalog"] = array();
		}
		else if($_POST['button'] == "submit") {
			//Submits an order.
			
			$firstCustomer = getInput("firstCustomer");
			$lastCustomer = getInput("lastCustomer");
			$sched = getInput("sched");
			$gift = getInput("gift") == "gift_y";
			$recipients = getInput("recipients");
			$HN = getInput("HN");
			$S = getInput("S");
			$C = getInput("C");
			
			if(count($_SESSION['cart']) == 0) $_POST['button'] = "false submit"; //triggers warning message
			else if(explode("T", $sched)[0] <= date("Y-m-d")) $_POST['button'] = "too late"; //triggers warning message
			else submitOrder($firstCustomer, $lastCustomer, $sched, $gift, $recipients, $HN, $S, $C);
		}
		else if(substr($_POST['button'], 0, 1) == 'C') { //C for choose
			//Reduces the contents of the catalog to the item with item_id equal to $id
			
			$id = substr($_POST['button'], 1);
			$arr = array();
			foreach($_SESSION['catalog'] as $item) {
				if($item["item_id"] == $id) {
					$arr[] = $item;
					break;
				}
			}
			$_SESSION['catalog'] = $arr;
		}
		else if(substr($_POST['button'], 0, 1) == 'R') { //R for remove
			//Removes the item with item_id equal to $key from the cart.
			
			$key = substr($_POST['button'], 1);
			unset($_SESSION['cart'][$key]);
			$_SESSION['cart'] = array_values($_SESSION['cart']); //fixes indices
		}
	}
	else {
		$_SESSION['catalog'] = array();
		//empties catalog when the page is refreshed
	}
?>
	<h1 class="w3-wide w3-center">ORDER FORM</h1>
	<form method="post">
		<div style="overflow:auto">
			<div class="o-catalog">
				<div>
					<h2 class="o-catalog-inputs">Item Catalog</h2>
					<div class="o-item" style="margin-right:0.5em">
						<input class="o-input" type="text" name="item_name" placeholder="Item Name" name ="itemname">

					</div>

					<div class="o-item">
						<select id="category" name="category" class="catdd o-input">
							<option selected hidden value="null">Category</option>
							<option value="none">none</option>
							<?php
								//Places all item categories in the category drop-down menu
							
								foreach($_SESSION['prefixes'] as $key => $value) {
									echo "<option value=\"$key\">" . ucwords($key) . "</option>";
								}
							?>
						</select>
					</div>
				</div>
				<?php
					//Displays an error message when filters for the input catalog are invalid
					//Displays a notice when an order has been placed
					
					if($_SERVER["REQUEST_METHOD"] == "POST") {
						if($_POST['button'] == "search") {
							
							$item_name = getInput("item_name");
							$category = getInput("category");
							
							$_SESSION['catalog'] = getItemCatalog($item_name, $category);
							
							if(($item_name == "") && ($category == "null")) {
								echo "<p class='w3-red w3-center'>Please input an Item Name and/or Category.</p>";
							}
							else if(count($_SESSION['catalog']) == 0) {
								echo "<p class='w3-gray w3-center'>No item matched the details that you indicated.</p>";
							}
						}
						else if($_POST['button'] == "submit") {
							echo "<p class='w3-gray w3-center'>Your order has been placed.</p>";
						}
						else if($_POST['button'] == "false submit") {
							echo "<p class='w3-red w3-center'>Your cannot place an order with no items.</p>";
						}
						else if($_POST['button'] == "too late") {
							echo "<p class='w3-red w3-center'>Please specify a date no earlier than tomorrow.</p>";
						}
					}
				?>
				<table class="w3-table-all" style="margin-top:0.83em" id="itemCatalog">
					<tr>
						<th>Item</th>
						<th>Features</th>
						<th>Dimensions/Slots</th>
						<th>Personalization</th>
						<th>Price</th>
						<th></th>
					</tr>
					
					<?php
						//Displays a row containing the category under which the files that are displayed fall under
						if(count($_SESSION["catalog"]) > 0) {
							$category = getCategory($_SESSION["catalog"][0]["item_type"]);
							
							echo "<tr style='color:white; background-color:black'><td colspan='6'>" . ucwords($category) . "</td></tr>";
						}
						
						//Displays the details of the offered items
						foreach($_SESSION['catalog'] as $item) {
							echo "<tr>";
							echo "<td>" . ucwords($item["item_name"]) . "</td>";
							echo "<td>" . implode("</br>", $item["features"]) . "</td>";
							echo "<td>" . implode("</br>", $item["dimensions_or_slots"]) . "</td>";
							echo "<td>Up to " . $item["personalization_length"] . " letters</td>";
							echo "<td>" . number_format($item["srp"]/100, 2) . "</td>";
							echo "<td><button name=\"button\" value=\"C" . $item["item_id"] . "\">&#10003</button></td>";
							echo "</tr>";
						}
					?>
				</table>
				
				<button name="button" value="search" class="w3-button w3-black w3-section" type="submit">SEARCH</button>
				<a id="addToCart" name="button" value="addToCart" class="w3-button w3-black w3-section w3-right">ADD TO CART</a>

				<div id="specs" class="modal">
					<div class="modal-content">
						<div class="bar">	
							<span class="close">&times;</span>
						</div>
						
						<div class="modal-body">
							<h1 class="w3-wide w3-center">DETAILS</h1>
							<h2>Quantity</h2>
							<input type = "number" class=  "w3-section w3-input w3-border" name= "quantity" required name= "quantity" min="1" value="1">
							<h2>Color</h2>
							<select name="color" class="w3-input w3-border">
								<?php
									//Places all available colors in the color drop-down menu
								
									$colors = getColors();
									
									foreach($colors as $color) {
										echo "<option value=\"$color\">" . ucwords($color) . "</option>";
									}
								?>
							</select>
							<h2>Personalization</h2>
							<input type="text" class="w3-input w3-border" name = "personalization"
								<?php
									//Limits the number of characters of the personalization to the limit stated by the item
								
									echo "placeholder=\"Maximum of " . $_SESSION["catalog"][0]["personalization_length"] . " letter/s\" maxLength = " . $_SESSION["catalog"][0]["personalization_length"];
								?>
							>
							<h2>Discount</h2>
							<input type = "number" class= "w3-section w3-input w3-border" name= "discountCode" placeholder ="In Percentage" min="0" max="50">
							<button id="addDetail" name="button" value="addDetail" class="w3-button w3-black w3-section w3-right" type="submit">ADD</button>
						</div>
					</div>
				</div>		
			</div>

			<div class="o-catalog">
				<h2 class="o-catalog-inputs">Cart</h2>

				<table class="w3-table-all" style="margin-top:0.83em">
					<tr>
						<th>Item</th>
						<th>Description</th>
						<th>Qty</th>
						<th>SRP</th>
						<th>Discount</th>
						<th>Total</th>
						<th></th>
					</tr>
					<?php
						//Says when the cart is empty
						if(count($_SESSION["cart"]) == 0) {
							echo "<p>The cart is empty.</p>";
						}
						
						//Displays the contents of the cart
						foreach($_SESSION["cart"] as $key => $log) {
							echo "<tr>";
							echo "<td>" . ucwords($log["item_name"]) . "</td>";
							echo "<td>Color: " . $log["color"] . "</br>Personalization: " . $log["personalization"] . "</td>";
							echo "<td>" . number_format($log["qty"]) . "</td>";
							echo "<td>" . number_format($log["srp"]/100, 2) . "</td>";
							echo "<td>" . $log["discount"] . "%</td>";
							echo "<td>" . number_format($log["total"]/100, 2) . "</td>";
							echo "<td><button name=\"button\" value=\"R" . $key . "\">&#10006</button></td>";
							echo "</tr>";
						}
					?>
				</table>
			</div>

			<div>
				<h2 class="o-amount">Amount Due:</h2>
				<input class="o-amount o-input" type="text" readonly name="amount" value="<?php
						echo number_format(getTotalAmount()/100, 2);
					?>">
			</div>

		    <a href="#details" id="proceed" class="w3-button w3-black w3-section w3-right">PROCEED</a>
			<button class="w3-button w3-black w3-section w3-right" type="submit" name="button" value="cancel" style="margin-right:0.5em">CANCEL</button>
		</div>
	</form>

	<form method="post">
		<div id="details" style="display:none">
			<div>
				<h2>Customer Name</h2>
				<input class="o-input w3-border o-names" type="text" placeholder="First Name" name ="firstCustomer" required>
				<input class="o-input w3-border o-lastnames" placeholder="Last Name" name="lastCustomer" required>
			</div>

			<div>
				<h2>Schedule</h2>
				<input class="w3-input w3-border" type="datetime-local" name="sched" required>
			</div>

			<div>
				<h2>Gift</h2>
				Yes <input class="radio" type="radio" name="gift" value="gift_y">
				No <input class="radio" type="radio" name="gift" checked value="gift_n">
			</div>
			
			<div>
				<h2>Recipient(s)</h2>
				<input class="w3-input w3-border" type="text" placeholder="Recipient 1 <comma> Recipient 2 ..." name="recipients" required>
			</div>

			<div>
				<h2>Delivery Address</h2>
				<input class="o-input address" type="text" name="HN" required placeholder = "House number">
				<input class="o-input address" type="text" name="S" required placeholder = "Street Name">
				<input class="o-input address" type="text" name="C" required placeholder = "City">
			</div>

			<button class="w3-button w3-black w3-section w3-right" type="submit" name="button" value="submit" style="margin-right:0.5em">SUBMIT</button>
		</div>
	</form>
</div>

<script>
	document.getElementById("proceed").onclick = function() {
		document.getElementById("details").style.display = "initial";
	};

	var modal = document.getElementById('specs');

	var btn = document.getElementById("addToCart");

	var btn2 = document.getElementById("addDetail");

	var span = document.getElementsByClassName("close")[0];

	btn.onclick = function() {
		if(document.getElementById("itemCatalog").rows.length===3){modal.style.display = "block";}
	};

	btn2.onclick = function() {modal.style.display = "none"};
	span.onclick = function() {modal.style.display = "none"};

	window.onclick = function(event) {
	    if (event.target == modal) {
	        modal.style.display = "none";
	    }
	}

	var sel = document.getElementById("category");
	sel.onchange = function() {
		if(sel.value === "none"){
			sel.value = "null";
			sel.style.color = "#ccc";
		}else{
			sel.style.color = "#000";
		}
	};
	function deleteThis(r, tableName) {
		var i = r.rowIndex;
		document.getElementById(tableName).deleteRow(i);
	}

	function deleteOthers(r, tableName) {
		var x = document.getElementById(tableName).rows.length;
		var y = r.rowIndex;
		for(i = 0; i < x; i++) {
			if(i !== y && i !== 0) {
				document.getElementById(tableName).deleteRow(i);
			}
		}
	}
</script>
</body>
</html>