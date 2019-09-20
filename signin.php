<?php
	session_start();
	require_once "connect.php";
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"> </script>
		<![endif]-->
		<title>Sign-in</title>
		<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="EZCheezy">

		<!-- Scripts and Libs -->
		<link type="text/css" href="styles/signin.css" rel="stylesheet">
	</head>

	<body>
		<div class="container">
			<div class="signin-container">
				<img width="100%" src="images/logo-transparent.png"/>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
					<table class="form-signin">
						<tr>
							<td><label>Username</label></td>
							<td><input name="username" type="text"/></td>
						</tr>
						<tr>
							<td><label>Password</label></td>
							<td><input name="password" type="password"/></td>
						</tr>
						<tr>
							<td colspan="2"><button name="signin-submit" style="margin-top: 12px;" id="submit" type="submit">Sign In</button></td>
						</tr>
					</table>
				</form>

				<?php
				if (isset($_POST['signin-submit'])) {
					$formfield['ffusername'] = trim($_POST['username']);
					$formfield['ffpassword'] = trim($_POST['password']);
					// make sure the schedule can not have duplicate start dates
					try {
						$sql = 'SELECT * FROM employee
						INNER JOIN employeetype ON employee.employeetypekey = employeetype.employeetypekey
						WHERE employee.employeeusername = :bvusername';

						$s = $db->prepare($sql);
						$s->bindValue(':bvusername', $formfield['ffusername']);
						$s->execute();
						$count = $s->rowCount();
					} catch (PDOException $e) {
						echo $e->getMessage();
						exit();
					}

					if ($count < 1) {
						echo '<p>The email or password is incorrect.</p>';
					} else {
						$row = $s->fetch();
						$confirmeduk = $row['employeeusername'];
						$confirmedpw = $row['employeepassword'];

						// Sign in successful
						if (password_verify($formfield['ffpassword'], $confirmedpw)) {
							// Create session variables
							$_SESSION['employeekey'] = $row['employeekey'];	//key
							$_SESSION['employeeusername'] = $row['employeeusername'];	//username
							$_SESSION['employeefirstname'] = $row['employeefirstname'];	//firstname
							$_SESSION['employeetypekey'] = $row['employeetypekey'];	//typekey
							$_SESSION['permission'] = $row['employeetypepermission'];	//permission
							$_SESSION['signedin'] = 1;	//signed in

							// Redirect accordingly
							//header("Location: frontindex.php");
							echo '<script>document.location.replace("index.php");</script>';
							echo '<p>Login successful. If you are not automatically redirected, <a href="insertmenuitems">click here</a>.</p>';

						} else {
							echo '<p>The username or password is incorrect.</p>';
						}
					}
				}
				?>

			</div>
		</div>
		<!-- <script>alert('Username: emp\nPassword: Password1')</script> -->
	</body>
</html>
