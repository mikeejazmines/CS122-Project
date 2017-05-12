<!DOCTYPE html>
<?php
	/*
	* Requires functions.php that contains all required functions for page.
	* Requires header.php that contains html code for header of the page.
	*/
	
	require("functions.php");
	require("header.php");
?>
		<h1 class="w3-wide w3-center">INVENTORY</h1>
		<h2 id="date"><?php echo "As of ". date("F j, Y"); ?></h2>

		<table class="w3-table-all">
			<?php
				/*
				 * Prints out a table representing the inventory.
				 * The rows correspond to each item being sold.
				 * The columns correspond to each color offered.
				 */
				 
				$inventory = array();
				$items = inventory();
				$colors = getColors();
				
				echo "<tr><th>Item</th>";
				foreach($colors as $color)
					echo "<th>" . ucwords($color) . "</th>";
				echo "</tr>";
				
				foreach($items as $item) {
					if(!isset($inventory[$item["item_name"]]))
						$inventory[$item["item_name"]] = array();
					$inventory[$item["item_name"]][$item["color"]] = $item["quantity"];
				}
				
				foreach($inventory as $item_name => $log) {
					echo "<tr><td>" . ucwords($item_name) . "</td>";
					foreach($colors as $color) {
						if(isset($log[$color]))
							echo "<td>$log[$color]</td>";
						else
							echo "<td>0</td>";
					}
					echo "</tr>";
				}
			?>
		</table>
	</div>
</body>
</html>
