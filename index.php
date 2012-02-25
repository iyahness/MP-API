<?php
	include_once("lib/mp/MP_API.php");
	$API = new MP_API();
 ?>
<!DOCTYPE html>
<html>
	<head>
		<title>MinistryPlatform API Examples</title>
		<link rel="stylesheet" href="resources/css/base.css" />
		<link rel="stylesheet" href="resources/css/formalize.css" />
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
				$("#error_message").removeClass("hide");
				$("#error_message").text("Your server connection failed. Please check your settings.");
			}

		</script>
	</head>
	<body>
		<div id='container'>
			<?php /*
			<div id='function list'>
				<p><pre><?php var_dump($API->getFunctionList()); ?></pre></p>
			</div>
			*/ ?>
			<div id='login' class='example'>
				<form id='login_form' method="POST" action="login.php">
					<fieldset>
					<legend>AuthenticateUser()</legend>
						<div class='field'>
							<input type="text" name="username" id="username" value="" placeholder="Username"/>
						</div>
						<div class='field'>
							<input type="password" name="password" id="password" value="" placeholder="Password"/>
						</div>
						<div class='field'>
							<button type="submit" id="submit">Submit</button>
						</div>
						<div id="error_message" class='field'>Your response will appear here.</div>
					</fieldset>
				</form>
			</div>
			<hr />
			<div id='addrecord' class='example'>
				<form id='add_record_form' method="POST" action="addrecord.php">
					<fieldset>
					<legend>AddRecord() - (using Contacts table)</legend>	
					<p>
						<b>Table Name</b>: Contacts<br />
						<b>Primary Key Field Name</b>: Contact_ID
					</p>
					<div class='fields'>
						<div class='field'>
							<input type="text" name="First_Name" id="first_name" value="" placeholder="First Name"/>
						</div>
						<div class='field'>
							<input type="text" name="Nickname" id="nickname" value="" placeholder="Nickname"/>
						</div>
						<div class='field'>
							<input type="text" name="Last_Name" id="last_name" value="" placeholder="Last Name"/>
						</div>
						<div class='field'>
							<input type="text" name="Display_Name" id="last_name" value="" placeholder="Display Name"/>
						</div>
						<div class='field'>
							<input type="hidden" name="Company" id="Company" value="0" />
						</div>
						<div class='field'>
								<input type="hidden" name="Contact_Status_ID" id="Contact_Status_ID" value="1" />
							</div>
						<div class='field'>
							<button type="submit" id="submit">Submit</button>
						</div>
					</div>
					<div id="error_message" class='field'>Your response will appear here.</div>
					</fieldset>
				</form>
			</div>
		</div>
	</body>
</html>