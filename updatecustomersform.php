<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		// Define customer key
		$formfield['customerkey'] = $_POST['customerkey'];
		// Feedback variable
		$feedback = '';

		// If submit button is pressed
		if (isset($_POST['update'])) {
			// Data cleansing
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
						$sqlupdate = 'UPDATE customer
													SET customerfirstname=:bvfirstname, customerlastname=:bvlastname,
															customerphone=:bvphone, customeraddress=:bvaddress,
															customercity=:bvcity, customerstate=:bvstate,
															customerzip=:bvzip, customeremail=:bvemail,
															customerpassword=:bvpassword
													WHERE customerkey=:bvcustomerkey';

						// Execution
						$result = $db->prepare($sqlupdate);
						$result->bindValue('bvfirstname', $formfield['firstname']);
						$result->bindValue('bvlastname', $formfield['lastname']);
						$result->bindValue('bvphone', $formfield['phone']);
						$result->bindValue('bvaddress', $formfield['address']);
						$result->bindValue('bvcity', $formfield['city']);
						$result->bindValue('bvstate', $formfield['state']);
						$result->bindValue('bvzip', $formfield['zip']);
						$result->bindValue('bvemail', $formfield['email']);
						$result->bindValue('bvpassword', $encpass);
						$result->bindValue('bvcustomerkey', $formfield['customerkey']);
						$result->execute();

						// Success
						$feedback .= '<div class="alert alert-success" role="alert">Update successful. <a href="updatecustomers.php">Back</a></div>';
					} catch (Exception $e) {
						// Exception error
						$feedback .= '<br />
									<p class="text-success font-weight-bold">Update failed.</p>
									<p class="text-danger">' . $e->getMessage() . '</p>';
					}
				}
			}
		}

		// Only view this page if it came from the according pages
		if (isset($_POST['updatecustomerselection']) || isset($_POST['update'])) {
			// Get information from customer
			$sqlselects = 'SELECT *
										 FROM customer
										 WHERE customerkey=:bvkey';
			$result = $db->prepare($sqlselects);
			$result->bindValue(':bvkey', $formfield['customerkey']);
			$result->execute();
			$row = $result->fetch();
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Customers</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Customers</div>
	<div class="card-body">
		<?php if (isset($_POST['update'])) { echo $feedback; } ?>
		<form class="was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div>
				<div class="row">
					<div class="col-12 col-md-4 mb-3">
						<input name="firstname" type="text" class="form-control" placeholder="First Name" value="<?php echo $row['customerfirstname']; ?>" required>
						<div class="valid-feedback">Valid first name</div>
						<div class="invalid-feedback">Invalid first name</div>
					</div>
					<div class="col-12 col-md-4 mb-3">
						<input name="lastname" type="text" class="form-control" placeholder="Last Name" value="<?php echo $row['customerlastname']; ?>" required>
						<div class="valid-feedback">Valid last name</div>
						<div class="invalid-feedback">Invalid last name</div>
					</div>
					<div class="col-12 col-md-4 mb-3">
						<input name="phone" type="text" class="form-control" placeholder="Phone" value="<?php echo $row['customerphone']; ?>" required>
						<div class="valid-feedback">Valid phone</div>
						<div class="invalid-feedback">Invalid phone</div>
					</div>
					<div class="col-12 col-md-6 mb-3">
						<input name="email" type="email" class="form-control" placeholder="Email" value="<?php echo $row['customeremail']; ?>" required>
						<div class="valid-feedback">Valid email</div>
						<div class="invalid-feedback">Invalid email</div>
					</div>
					<div class="col-12 col-md-6 mb-3">
						<input name="address" type="text" class="form-control" placeholder="Address" value="<?php echo $row['customeraddress']; ?>" required>
						<div class="valid-feedback">Valid address</div>
						<div class="invalid-feedback">Invalid address</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-md-4 mb-3">
						<input name="city" type="text" class="form-control" placeholder="City" value="<?php echo $row['customercity']; ?>" required>
						<div class="valid-feedback">Valid city</div>
						<div class="invalid-feedback">Invalid city</div>
					</div>
					<div class="col-6 col-md-4 mb-3">
						<select name="state" class="form-control" required>
							<option disabled selected>State</option>
							<option value="AL" <?php if($row['customerstate'] == "AL") { echo 'selected'; }?>>Alabama</option>
							<option value="AK" <?php if($row['customerstate'] == "AK") { echo 'selected'; }?>>Alaska</option>
							<option value="AZ" <?php if($row['customerstate'] == "AZ") { echo 'selected'; }?>>Arizona</option>
							<option value="AR" <?php if($row['customerstate'] == "AR") { echo 'selected'; }?>>Arkansas</option>
							<option value="CA" <?php if($row['customerstate'] == "CA") { echo 'selected'; }?>>California</option>
							<option value="CO" <?php if($row['customerstate'] == "CO") { echo 'selected'; }?>>Colorado</option>
							<option value="CT" <?php if($row['customerstate'] == "CT") { echo 'selected'; }?>>Connecticut</option>
							<option value="DE" <?php if($row['customerstate'] == "DE") { echo 'selected'; }?>>Delaware</option>
							<option value="DC" <?php if($row['customerstate'] == "DC") { echo 'selected'; }?>>District of Columbia</option>
							<option value="FL" <?php if($row['customerstate'] == "FL") { echo 'selected'; }?>>Florida</option>
							<option value="GA" <?php if($row['customerstate'] == "GA") { echo 'selected'; }?>>Georgia</option>
							<option value="HI" <?php if($row['customerstate'] == "HI") { echo 'selected'; }?>>Hawaii</option>
							<option value="ID" <?php if($row['customerstate'] == "ID") { echo 'selected'; }?>>Idaho</option>
							<option value="IL" <?php if($row['customerstate'] == "IL") { echo 'selected'; }?>>Illinois</option>
							<option value="IN" <?php if($row['customerstate'] == "IN") { echo 'selected'; }?>>Indiana</option>
							<option value="IA" <?php if($row['customerstate'] == "IA") { echo 'selected'; }?>>Iowa</option>
							<option value="KS" <?php if($row['customerstate'] == "KS") { echo 'selected'; }?>>Kansas</option>
							<option value="KY" <?php if($row['customerstate'] == "KY") { echo 'selected'; }?>>Kentucky</option>
							<option value="LA" <?php if($row['customerstate'] == "LA") { echo 'selected'; }?>>Louisiana</option>
							<option value="ME" <?php if($row['customerstate'] == "ME") { echo 'selected'; }?>>Maine</option>
							<option value="MD" <?php if($row['customerstate'] == "MD") { echo 'selected'; }?>>Maryland</option>
							<option value="MA" <?php if($row['customerstate'] == "MA") { echo 'selected'; }?>>Massachusetts</option>
							<option value="MI" <?php if($row['customerstate'] == "MI") { echo 'selected'; }?>>Michigan</option>
							<option value="MN" <?php if($row['customerstate'] == "MN") { echo 'selected'; }?>>Minnesota</option>
							<option value="MS" <?php if($row['customerstate'] == "MS") { echo 'selected'; }?>>Mississippi</option>
							<option value="MO" <?php if($row['customerstate'] == "MO") { echo 'selected'; }?>>Missouri</option>
							<option value="MT" <?php if($row['customerstate'] == "MT") { echo 'selected'; }?>>Montana</option>
							<option value="NE" <?php if($row['customerstate'] == "NE") { echo 'selected'; }?>>Nebraska</option>
							<option value="NV" <?php if($row['customerstate'] == "NV") { echo 'selected'; }?>>Nevada</option>
							<option value="NH" <?php if($row['customerstate'] == "NH") { echo 'selected'; }?>>New Hampshire</option>
							<option value="NJ" <?php if($row['customerstate'] == "NJ") { echo 'selected'; }?>>New Jersey</option>
							<option value="NM" <?php if($row['customerstate'] == "NM") { echo 'selected'; }?>>New Mexico</option>
							<option value="NY" <?php if($row['customerstate'] == "NY") { echo 'selected'; }?>>New York</option>
							<option value="NC" <?php if($row['customerstate'] == "NC") { echo 'selected'; }?>>North Carolina</option>
							<option value="ND" <?php if($row['customerstate'] == "ND") { echo 'selected'; }?>>North Dakota</option>
							<option value="OH" <?php if($row['customerstate'] == "OH") { echo 'selected'; }?>>Ohio</option>
							<option value="OK" <?php if($row['customerstate'] == "OK") { echo 'selected'; }?>>Oklahoma</option>
							<option value="OR" <?php if($row['customerstate'] == "OR") { echo 'selected'; }?>>Oregon</option>
							<option value="PA" <?php if($row['customerstate'] == "PA") { echo 'selected'; }?>>Pennsylvania</option>
							<option value="RI" <?php if($row['customerstate'] == "RI") { echo 'selected'; }?>>Rhode Island</option>
							<option value="SC" <?php if($row['customerstate'] == "SC") { echo 'selected'; }?>>South Carolina</option>
							<option value="SD" <?php if($row['customerstate'] == "SD") { echo 'selected'; }?>>South Dakota</option>
							<option value="TN" <?php if($row['customerstate'] == "TN") { echo 'selected'; }?>>Tennessee</option>
							<option value="TX" <?php if($row['customerstate'] == "TX") { echo 'selected'; }?>>Texas</option>
							<option value="UT" <?php if($row['customerstate'] == "UT") { echo 'selected'; }?>>Utah</option>
							<option value="VT" <?php if($row['customerstate'] == "VT") { echo 'selected'; }?>>Vermont</option>
							<option value="VA" <?php if($row['customerstate'] == "VA") { echo 'selected'; }?>>Virginia</option>
							<option value="WA" <?php if($row['customerstate'] == "WA") { echo 'selected'; }?>>Washington</option>
							<option value="WV" <?php if($row['customerstate'] == "WV") { echo 'selected'; }?>>West Virginia</option>
							<option value="WI" <?php if($row['customerstate'] == "WI") { echo 'selected'; }?>>Wisconsin</option>
							<option value="WY" <?php if($row['customerstate'] == "WY") { echo 'selected'; }?>>Wyoming</option>
						</select>
						<div class="valid-feedback">Valid state</div>
						<div class="invalid-feedback">Invalid state</div>
					</div>
					<div class="col-6 col-md-4 mb-3">
						<input name="zip" type="text" class="form-control" placeholder="ZIP" value="<?php echo $row['customerzip']; ?>" required>
						<div class="valid-feedback">Valid zip</div>
						<div class="invalid-feedback">Invalid zip</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-md-4 mb-3">
						<select name="preferredlocation" class="form-control" required>
							<option disabled selected>Location</option>
							<?php
							$sqlselectl = "SELECT * FROM locations";
							$resultl = $db->prepare($sqlselectl);
							$resultl->execute();

							while ($rowl = $resultl->fetch()) {
								echo '<option value="'. $rowl['locationkey'] . '"';
									if ($rowl['locationkey'] == $row['locationkey']) { echo ' selected'; }
								echo '>' . $rowl['locationname'] . '</option>';
							}
							?>
						</select>
						<div class="valid-feedback">Valid preferred location</div>
						<div class="invalid-feedback">Invalid preferred location</div>
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
						<input type="hidden" name="customerkey" value="<?php echo $formfield['customerkey']; ?>"/>
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
	echo '<p></p>'; // page can not be viewed
}
} else {
		echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
