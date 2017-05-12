<!DOCTYPE html>
<?php
	/*
	 * Requires functions.php that contains all required functions for page.
	 */
	
	require("functions.php");
?>
<html>
<head>
	<link rel="stylesheet" href="stylesheet1.css">
	<link rel="stylesheet" href="stylesheetLato.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="general.css">
  <link rel="stylesheet" href="stylesheetlogin.css">
  
  <style>
	body {font-family: "Lato", sans-serif}
	.mySlides {display: none}
	</style>
</head>

<body>
	<h1>PIXIE DUST ENTERPRISES</h1>
	<div class = "login-page">
		<div class="form">
			<h3 class ="agenter w3-center">AGENT LOGIN</h3>
			
			<?php
				//Logs in an agent if and only if a valid agent_id is inputted
				if($_SERVER["REQUEST_METHOD"] == "POST") {
					$temp_id = getInput("agent_id");
					
					if(isAgentId($temp_id)) {
						$_SESSION['agent_id'] = $temp_id;
						header("location: order.php");
					}
					else 
						echo '<p class="w3-red">Please input a valid Agent ID.</p>';
				}
			?>
			<form class="login-form" action="login.php" method="POST">
				<input type="text" placeholder="agent id" name="agent_id"/>
				<button class="w3-button w3-black w3-section">login</button>
			</form>
		</div>
	</div>
</body>
</html>