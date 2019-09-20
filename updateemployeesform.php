<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		// Only view this page if it came from the according pages
		if (isset($_POST['updateemployeeselection']) || isset($_POST['update'])) {
			// Define employee key
			$formfield['employeekey'] = $_POST['employeekey'];
			// Data cleansing
			$formfield['username'] = $_POST['username'];
			$formfield['typekey'] = $_POST['type'];
			$formfield['firstname'] = $_POST['firstname'];
			$formfield['lastname'] = $_POST['lastname'];
			$formfield['phone'] = $_POST['phone'];
			$formfield['address'] = $_POST['address'];
			$formfield['city'] = $_POST['city'];
			$formfield['state'] = $_POST['state'];
			$formfield['zip'] = $_POST['zip'];
			$formfield['email'] = $_POST['email'];
			$formfield['password1'] = $_POST['password1'];
			$formfield['password2'] = $_POST['password2'];
			$formfield['pay'] = $_POST['pay'];
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Employees</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Employees</div>
	<div class="card-body">
		<?php
		// If submit button is pressed
		if (isset($_POST['update'])) {

			// If there's an empty field
			if (empty($formfield['username']) || empty($formfield['typekey']) ||
					empty($formfield['firstname']) || empty($formfield['lastname']) ||
					empty($formfield['phone']) || empty($formfield['address']) ||
					empty($formfield['city']) || empty($formfield['state']) ||
					empty($formfield['zip']) || empty($formfield['email']) ||
					empty($formfield['password1']) || empty($formfield['password2'])) {
						echo '<br /><p class="text-warning">Insert failed: one or more fields are empty.</p>';
			} else {
				// If the two passwords are the same
				if ($formfield['password1'] == $formfield['password2']) {
					// If the password is invalid
					if(strlen($formfield['password1']) < 8
						 && !preg_match("#[0-9]+#", $formfield['password1'])
						 && !preg_match("#[a-z]+#", $formfield['password1'])
						 && !preg_match("#[A-Z]+#", $formfield['password1'])
						 && !preg_match("#\W+#", $formfield['password1'])) {
						echo '<br /><p class="text-warning">Insert failed: password is invalid.</p>';
					} else {
						// Options...
						$options = [
							'cost' => 12,
							'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
						];
						// Generate an encrypted password
						$encpass = password_hash($formfield['password1'], PASSWORD_BCRYPT, $options);

						// Try to insert
						try {
							// SQL statement
							$sqlupdate = 'UPDATE employee
														SET employeeusername=:bvusername, employeetypekey=:bvtypekey,
																employeefirstname=:bvfirstname, employeelastname=:bvlastname,
																employeephone=:bvphone, employeeaddress=:bvaddress,
																employeecity=:bvcity, employeestate=:bvstate,
																employeezip=:bvzip, employeeemail=:bvemail,
																employeepassword=:bvpassword, employeepay=:bvpay
														WHERE employeekey=:bvemployeekey';

							// Execution
							$result = $db->prepare($sqlupdate);
							$result->bindValue('bvusername', $formfield['username']);
							$result->bindValue('bvtypekey', $formfield['typekey']);
							$result->bindValue('bvfirstname', $formfield['firstname']);
							$result->bindValue('bvlastname', $formfield['lastname']);
							$result->bindValue('bvphone', $formfield['phone']);
							$result->bindValue('bvaddress', $formfield['address']);
							$result->bindValue('bvcity', $formfield['city']);
							$result->bindValue('bvstate', $formfield['state']);
							$result->bindValue('bvzip', $formfield['zip']);
							$result->bindValue('bvemail', $formfield['email']);
							$result->bindValue('bvpassword', $encpass);
							$result->bindValue('bvpay', $formfield['pay']);
							$result->bindValue('bvemployeekey', $formfield['employeekey']);
							$result->execute();

							// Success
							echo '<div class="alert alert-success" role="alert">Update successful. <a href="updateemployees.php">Back</a></div>';
						} catch (Exception $e) {
							// Exception error
							echo '<br />
										<p class="text-success font-weight-bold">Update failed.</p>
										<p class="text-danger">' . $e->getMessage() . '</p>';
						}
					}
				}
			}
		}
		?>
		<form class="was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div>
				<div class="row">
					<div class="col-12 col-md-4 mb-3">
						<input name="firstname" type="text" class="form-control" placeholder="First Name" value="<?php echo $formfield['firstname']; ?>" required>
						<div class="valid-feedback">Valid first name</div>
						<div class="invalid-feedback">Invalid first name</div>
					</div>
					<div class="col-12 col-md-4 mb-3">
						<input name="lastname" type="text" class="form-control" placeholder="Last Name" value="<?php echo $formfield['lastname']; ?>" required>
						<div class="valid-feedback">Valid last name</div>
						<div class="invalid-feedback">Invalid last name</div>
					</div>
					<div class="col-12 col-md-4 mb-3">
						<input name="phone" type="text" class="form-control" placeholder="Phone" value="<?php echo $formfield['phone']; ?>" required>
						<div class="valid-feedback">Valid phone</div>
						<div class="invalid-feedback">Invalid phone</div>
					</div>
					<div class="col-12 col-md-6 mb-3">
						<input name="email" type="email" class="form-control" placeholder="Email" value="<?php echo $formfield['email']; ?>" required>
						<div class="valid-feedback">Valid email</div>
						<div class="invalid-feedback">Invalid email</div>
					</div>
					<div class="col-12 col-md-6 mb-3">
						<input name="address" type="text" class="form-control" placeholder="Address" value="<?php echo $formfield['address']; ?>" required>
						<div class="valid-feedback">Valid address</div>
						<div class="invalid-feedback">Invalid address</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-md-4 mb-3">
						<input name="city" type="text" class="form-control" placeholder="City" value="<?php echo $formfield['city']; ?>" required>
						<div class="valid-feedback">Valid city</div>
						<div class="invalid-feedback">Invalid city</div>
					</div>
					<div class="col-6 col-md-4 mb-3">
						<select name="state" class="form-control" required>
							<option disabled selected>State</option>
							<option value="AL" <?php if($formfield['state'] == "AL") { echo 'selected'; }?>>Alabama</option>
							<option value="AK" <?php if($formfield['state'] == "AK") { echo 'selected'; }?>>Alaska</option>
							<option value="AZ" <?php if($formfield['state'] == "AZ") { echo 'selected'; }?>>Arizona</option>
							<option value="AR" <?php if($formfield['state'] == "AR") { echo 'selected'; }?>>Arkansas</option>
							<option value="CA" <?php if($formfield['state'] == "CA") { echo 'selected'; }?>>California</option>
							<option value="CO" <?php if($formfield['state'] == "CO") { echo 'selected'; }?>>Colorado</option>
							<option value="CT" <?php if($formfield['state'] == "CT") { echo 'selected'; }?>>Connecticut</option>
							<option value="DE" <?php if($formfield['state'] == "DE") { echo 'selected'; }?>>Delaware</option>
							<option value="DC" <?php if($formfield['state'] == "DC") { echo 'selected'; }?>>District of Columbia</option>
							<option value="FL" <?php if($formfield['state'] == "FL") { echo 'selected'; }?>>Florida</option>
							<option value="GA" <?php if($formfield['state'] == "GA") { echo 'selected'; }?>>Georgia</option>
							<option value="HI" <?php if($formfield['state'] == "HI") { echo 'selected'; }?>>Hawaii</option>
							<option value="ID" <?php if($formfield['state'] == "ID") { echo 'selected'; }?>>Idaho</option>
							<option value="IL" <?php if($formfield['state'] == "IL") { echo 'selected'; }?>>Illinois</option>
							<option value="IN" <?php if($formfield['state'] == "IN") { echo 'selected'; }?>>Indiana</option>
							<option value="IA" <?php if($formfield['state'] == "IA") { echo 'selected'; }?>>Iowa</option>
							<option value="KS" <?php if($formfield['state'] == "KS") { echo 'selected'; }?>>Kansas</option>
							<option value="KY" <?php if($formfield['state'] == "KY") { echo 'selected'; }?>>Kentucky</option>
							<option value="LA" <?php if($formfield['state'] == "LA") { echo 'selected'; }?>>Louisiana</option>
							<option value="ME" <?php if($formfield['state'] == "ME") { echo 'selected'; }?>>Maine</option>
							<option value="MD" <?php if($formfield['state'] == "MD") { echo 'selected'; }?>>Maryland</option>
							<option value="MA" <?php if($formfield['state'] == "MA") { echo 'selected'; }?>>Massachusetts</option>
							<option value="MI" <?php if($formfield['state'] == "MI") { echo 'selected'; }?>>Michigan</option>
							<option value="MN" <?php if($formfield['state'] == "MN") { echo 'selected'; }?>>Minnesota</option>
							<option value="MS" <?php if($formfield['state'] == "MS") { echo 'selected'; }?>>Mississippi</option>
							<option value="MO" <?php if($formfield['state'] == "MO") { echo 'selected'; }?>>Missouri</option>
							<option value="MT" <?php if($formfield['state'] == "MT") { echo 'selected'; }?>>Montana</option>
							<option value="NE" <?php if($formfield['state'] == "NE") { echo 'selected'; }?>>Nebraska</option>
							<option value="NV" <?php if($formfield['state'] == "NV") { echo 'selected'; }?>>Nevada</option>
							<option value="NH" <?php if($formfield['state'] == "NH") { echo 'selected'; }?>>New Hampshire</option>
							<option value="NJ" <?php if($formfield['state'] == "NJ") { echo 'selected'; }?>>New Jersey</option>
							<option value="NM" <?php if($formfield['state'] == "NM") { echo 'selected'; }?>>New Mexico</option>
							<option value="NY" <?php if($formfield['state'] == "NY") { echo 'selected'; }?>>New York</option>
							<option value="NC" <?php if($formfield['state'] == "NC") { echo 'selected'; }?>>North Carolina</option>
							<option value="ND" <?php if($formfield['state'] == "ND") { echo 'selected'; }?>>North Dakota</option>
							<option value="OH" <?php if($formfield['state'] == "OH") { echo 'selected'; }?>>Ohio</option>
							<option value="OK" <?php if($formfield['state'] == "OK") { echo 'selected'; }?>>Oklahoma</option>
							<option value="OR" <?php if($formfield['state'] == "OR") { echo 'selected'; }?>>Oregon</option>
							<option value="PA" <?php if($formfield['state'] == "PA") { echo 'selected'; }?>>Pennsylvania</option>
							<option value="RI" <?php if($formfield['state'] == "RI") { echo 'selected'; }?>>Rhode Island</option>
							<option value="SC" <?php if($formfield['state'] == "SC") { echo 'selected'; }?>>South Carolina</option>
							<option value="SD" <?php if($formfield['state'] == "SD") { echo 'selected'; }?>>South Dakota</option>
							<option value="TN" <?php if($formfield['state'] == "TN") { echo 'selected'; }?>>Tennessee</option>
							<option value="TX" <?php if($formfield['state'] == "TX") { echo 'selected'; }?>>Texas</option>
							<option value="UT" <?php if($formfield['state'] == "UT") { echo 'selected'; }?>>Utah</option>
							<option value="VT" <?php if($formfield['state'] == "VT") { echo 'selected'; }?>>Vermont</option>
							<option value="VA" <?php if($formfield['state'] == "VA") { echo 'selected'; }?>>Virginia</option>
							<option value="WA" <?php if($formfield['state'] == "WA") { echo 'selected'; }?>>Washington</option>
							<option value="WV" <?php if($formfield['state'] == "WV") { echo 'selected'; }?>>West Virginia</option>
							<option value="WI" <?php if($formfield['state'] == "WI") { echo 'selected'; }?>>Wisconsin</option>
							<option value="WY" <?php if($formfield['state'] == "WY") { echo 'selected'; }?>>Wyoming</option>
						</select>
						<div class="valid-feedback">Valid state</div>
						<div class="invalid-feedback">Invalid state</div>
					</div>
					<div class="col-6 col-md-4 mb-3">
						<input name="zip" type="text" class="form-control" placeholder="ZIP" value="<?php echo $formfield['zip']; ?>" required>
						<div class="valid-feedback">Valid zip</div>
						<div class="invalid-feedback">Invalid zip</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-md-6 mb-3">
						<input name="username" type="text" class="form-control" placeholder="Username" value="<?php echo $formfield['username']; ?>" required>
						<div class="valid-feedback">Valid username</div>
						<div class="invalid-feedback">Invalid username</div>
					</div>
					<div class="col-12 col-md-6 mb-3">
						<select name="type" class="form-control" required>
							<option disabled selected>User Type</option>
							<?php
							$sqlselectet = "SELECT * FROM employeetype WHERE 1";
							$resultet = $db->prepare($sqlselectet);
							$resultet->execute();

							while ($rowet = $resultet->fetch()) {
								echo '<option value="'. $rowet['employeetypekey'] . '"';
								if ($rowet['employeetypekey'] == $formfield['typekey']) { echo ' selected'; };
								echo '>' . $rowet['employeetypename'] . '</option>';
							}
							?>
						</select>
						<div class="valid-feedback">Valid user type</div>
						<div class="invalid-feedback">Invalid user type</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-md-2 mb-3">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">$</div>
							</div>
							<input name="pay" type="text" class="form-control" placeholder="Pay" value="<?php echo $formfield['pay']; ?>" required>
							<div class="valid-feedback">Valid pay</div>
							<div class="invalid-feedback">Invalid pay</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-md-6 mb-3">
						<input id="password1" name="password1" type="text" class="form-control" placeholder="Password" required>
						<div class="valid-feedback">Valid password</div>
						<div id="password1-feedback" class="invalid-feedback">Invalid password</div>
					</div>
					<div class="col-12 col-md-6 mb-3">
						<input id="password2" name="password2" type="text" class="form-control" placeholder="Confirm Password" required>
						<div id="password2-valid-feedback" class="valid-feedback">Passwords match</div>
						<div id="password2-invalid-feedback" class="invalid-feedback">Passwords do not match</div>
					</div>
				</div>
				<p id="passwordtip" class="mt-3 text-danger mb-3">Passwords must contain an uppercase, lowercase, digit, and 8 characters.</p>
				<div class="row">
					<div class="col-12">
						<input type="hidden" name="employeekey" value="<?php echo $formfield['employeekey']; ?>"/>
						<button name="update" type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript" src="scripts/passwordvalidator.js"></script>
<?php
} else {
	echo '<p>not this time</p>'; // page can not be viewed
}
} else {
		echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
