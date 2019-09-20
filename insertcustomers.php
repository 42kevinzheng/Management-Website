<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/..............1........................./', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Customers</a></li>
	<li class="breadcrumb-item active">Insert</li>
</ol>
<div class="card">
	<div class="card-header">Insert Customers</div>
	<div class="card-body">
		<?php
		// If submit button is pressed
		if (isset($_POST['insertcustomer'])) {
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
			$formfield['preferredlocation'] = $_POST['preferredlocation'];

			// If there's an empty field
			if (empty($formfield['firstname']) || empty($formfield['lastname']) ||
					empty($formfield['phone']) || empty($formfield['address']) ||
					empty($formfield['city']) || empty($formfield['state']) ||
					empty($formfield['zip']) || empty($formfield['email']) ||
					empty($formfield['password1']) || empty($formfield['password2']) ||
					empty($formfield['preferredlocation'])) {
						echo '<div class="alert alert-warning" role="alert"><strong>Insert failed: </strong>one or more fields are empty.</div>';
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
							$sqlnewcustomer = "INSERT into
								customer(customerfirstname, customerlastname,
												 customerphone, customeraddress, customercity, customerstate,
												 customerzip, customeremail, customerpassword, locationkey)
								VALUES(:bvfirstname, :bvlastname,
											 :bvphone, :bvaddress, :bvcity, :bvstate,
											 :bvzip, :bvemail, :bvpassword, :bvpreferredlocation)";

							// Execution
							$result = $db->prepare($sqlnewcustomer);
							$result->bindValue('bvfirstname', $formfield['firstname']);
							$result->bindValue('bvlastname', $formfield['lastname']);
							$result->bindValue('bvphone', $formfield['phone']);
							$result->bindValue('bvaddress', $formfield['address']);
							$result->bindValue('bvcity', $formfield['city']);
							$result->bindValue('bvstate', $formfield['state']);
							$result->bindValue('bvzip', $formfield['zip']);
							$result->bindValue('bvemail', $formfield['email']);
							$result->bindValue('bvpassword', $encpass);
							$result->bindValue('bvpreferredlocation', $formfield['preferredlocation']);
							$result->execute();

							// Success
							echo '<div class="alert alert-success" role="alert">Insert successful</div>';
						} catch (Exception $e) {
							// Exception error
							echo '<br /><p class="text-danger font-weight-bold">' . $e->getMessage() . '</p>';
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
						<input name="firstname" type="text" class="form-control" placeholder="First Name" required>
						<div class="valid-feedback">Valid first name</div>
						<div class="invalid-feedback">Invalid first name</div>
					</div>
					<div class="col-12 col-md-4 mb-3">
						<input name="lastname" type="text" class="form-control" placeholder="Last Name" required>
						<div class="valid-feedback">Valid last name</div>
						<div class="invalid-feedback">Invalid last name</div>
					</div>
					<div class="col-12 col-md-4 mb-3">
						<input name="phone" type="text" class="form-control" placeholder="Phone" required>
						<div class="valid-feedback">Valid phone</div>
						<div class="invalid-feedback">Invalid phone</div>
					</div>
					<div class="col-12 col-md-6 mb-3">
						<input name="email" type="text" class="form-control" placeholder="Email" required>
						<div class="valid-feedback">Valid email</div>
						<div class="invalid-feedback">Invalid email</div>
					</div>
					<div class="col-12 col-md-6 mb-3">
						<input name="address" type="text" class="form-control" placeholder="Address" required>
						<div class="valid-feedback">Valid address</div>
						<div class="invalid-feedback">Invalid address</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-md-4 mb-3">
						<input name="city" type="text" class="form-control" placeholder="City" required>
						<div class="valid-feedback">Valid city</div>
						<div class="invalid-feedback">Invalid city</div>
					</div>
					<div class="col-6 col-md-4 mb-3">
						<select name="state" class="form-control" required>
							<option disabled selected>State</option>
							<option value="AL">Alabama</option>
							<option value="AK">Alaska</option>
							<option value="AZ">Arizona</option>
							<option value="AR">Arkansas</option>
							<option value="CA">California</option>
							<option value="CO">Colorado</option>
							<option value="CT">Connecticut</option>
							<option value="DE">Delaware</option>
							<option value="DC">District of Columbia</option>
							<option value="FL">Florida</option>
							<option value="GA">Georgia</option>
							<option value="HI">Hawaii</option>
							<option value="ID">Idaho</option>
							<option value="IL">Illinois</option>
							<option value="IN">Indiana</option>
							<option value="IA">Iowa</option>
							<option value="KS">Kansas</option>
							<option value="KY">Kentucky</option>
							<option value="LA">Louisiana</option>
							<option value="ME">Maine</option>
							<option value="MD">Maryland</option>
							<option value="MA">Massachusetts</option>
							<option value="MI">Michigan</option>
							<option value="MN">Minnesota</option>
							<option value="MS">Mississippi</option>
							<option value="MO">Missouri</option>
							<option value="MT">Montana</option>
							<option value="NE">Nebraska</option>
							<option value="NV">Nevada</option>
							<option value="NH">New Hampshire</option>
							<option value="NJ">New Jersey</option>
							<option value="NM">New Mexico</option>
							<option value="NY">New York</option>
							<option value="NC">North Carolina</option>
							<option value="ND">North Dakota</option>
							<option value="OH">Ohio</option>
							<option value="OK">Oklahoma</option>
							<option value="OR">Oregon</option>
							<option value="PA">Pennsylvania</option>
							<option value="RI">Rhode Island</option>
							<option value="SC">South Carolina</option>
							<option value="SD">South Dakota</option>
							<option value="TN">Tennessee</option>
							<option value="TX">Texas</option>
							<option value="UT">Utah</option>
							<option value="VT">Vermont</option>
							<option value="VA">Virginia</option>
							<option value="WA">Washington</option>
							<option value="WV">West Virginia</option>
							<option value="WI">Wisconsin</option>
							<option value="WY">Wyoming</option>
						</select>
						<div class="valid-feedback">Valid state</div>
						<div class="invalid-feedback">Invalid state</div>
					</div>
					<div class="col-6 col-md-4 mb-3">
						<input name="zip" type="text" class="form-control" placeholder="ZIP" required>
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
								echo '<option value="'. $rowl['locationkey'] . '">' . $rowl['locationname'] . '</option>';
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
					<div class="col-12 mb-3">
						<button name="insertcustomer" type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript" src="scripts/passwordvalidator.js"></script>
<?php
} else {
	echo '<p>You do not have permission to view this page</p>';
}
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
