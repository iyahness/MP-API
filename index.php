<?php
	//require_once("lib/mp/MP_API.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>MinistryPlatform API Examples</title>
		<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
		<script>
			$(document).ready(function() {
				$("#submit").click(function(){
					var formData = $("#login_form").serialize();
					$.ajax({
						type: "POST",
						url: "login.php",
						cache: false,
						dataType: 'json',
						data: formData,
						success: onSuccess,
						error: onError
					});
			
					return false;
				});
			});
			
			function onSuccess(data, status) {
				var loginsuccess = $.trim(data.success);
				var message = $.trim(data.message);
				$("#error_message").text(message);
			}
			
			function onError(data, status) {
				$("#error_message").text("Your server connection failed. Please check your settings.");
			}
			
		</script>
	</head>
	<body>
		<div id='container'>
			<div id='login'>
				<form id='login_form' method="POST" action="login.php">
					<div id="error_message"></div>
					<fieldset>
						<div class='field'>
							<label for="username">Username:</label>
							<input type="text" name="username" id="username" value="" placeholder="Username"/>
						</div>
						<div class='field'>
							<label for="password">Password:</label>
							<input type="password" name="password" id="password" value="" placeholder="Password"/>
						</div>
						<div class='field'>
							<button type="submit" id="submit">Submit</button>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</body>
</html>