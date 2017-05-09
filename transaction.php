<!DOCTYPE html>
<?php
	require("functions.php");

	include("header.php");
?>

		<h1 class="w3-wide w3-center">AGENT TRANSACTIONS</h1>
	   	<div>
   		<a id="filters" class="w3-button w3-black">FILTERS</a>
        <div id="filterForm" style="display:none">
        	<form class="o-catalog" style="margin-top:0px">
	        	<h2>Order Number</h2>
	            <input class="w3-input w3-border" type="text" placeholder="Order Number" name="Order Number" required>

	            <h2>Agent Name </h2>
	    		<input class="o-input w3-border o-names" type="text" placeholder="First Name" name="firstAgent" required>
				<input class="o-input w3-border o-lastnames" type="text" placeholder="Last Name" name = "lastAgent" required>

	       		<h2>Customer Name</h2>
				<input class="o-input w3-border o-names" type="text" placeholder="First Name" name =" firstCustomer" required>
				<input class="o-input w3-border o-lastnames" type="text" placeholder="Last Name" name ="lastCustomer" required>

				<h2 style="margin-bottom:0px; padding:0px">Time Period</h2>

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

				<button class="w3-button w3-black w3-section w3-right" type="submit">SEND</button>
			    <button class="w3-button w3-black w3-section w3-right" style="margin-right:5px" type="reset">RESET</button>
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

			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	      </div>
	    </div>

	<script src="report.js"></script>
	<script>
		document.getElementById("filters").onclick = function(){
			var x = document.getElementById("filterForm");
			if(x.style.display === "none"){
				x.style.display = "initial";
			}else{
				x.style.display = "none";
			}
		};
	</script>
</body>
</html>
