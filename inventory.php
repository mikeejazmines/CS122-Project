<!DOCTYPE html>
<?php
	require("functions.php");

	include("header.php");
?>

		<h1 class="w3-wide w3-center">INVENTORY</h1>
		<h2 id="date"></h2>

		<table class="w3-table-all">
			<?php
				$inventory = array();
				$items = inventory();
				$colors = array("red", "orange", "yellow", "green", "blue", "purple", "pink", "black");

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
					echo "<tr><td>$item_name</td>";
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
	<script>
		var d = new Date();
		var message = "As of " + d.toDateString().slice(4);
		document.getElementById("date").innerHTML = message;
	</script>
</body>
</html>
