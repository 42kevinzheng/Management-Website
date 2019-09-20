<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/.................1....................../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Locations</a></li>
	<li class="breadcrumb-item active">Insert</li>
</ol>
<div class="card">
	<div class="card-header">Insert Locations</div>
	<div class="card-body">
		<?php
			// Insert button pressed
			if (isset($_POST['insert'])) {
				// Data cleansing
				$formfield['name'] = trim($_POST['name']);
				$formfield['address'] = trim($_POST['address']);
				$formfield['state'] = trim($_POST['state']);
				$formfield['city'] = trim($_POST['city']);
				$formfield['zip'] = trim($_POST['zip']);
				$formfield['phone'] = trim($_POST['phone']);
				$formfield['description'] = trim($_POST['description']);
				$formfield['locationopen'] = $_POST['locationopen'];
				$formfield['locationclose'] = $_POST['locationclose'];

				// If a field is empty...
				if (empty($formfield['name']) || empty($formfield['address']) ||
						empty($formfield['description']) || empty($formfield['locationopen']) ||
						empty($formfield['locationclose'])) {
							// One or more fields are empty
							echo '<br /><p class="text-warning">Insert failed: one or more fields are empty.</p>';
				} else {
					// Attempt to insert
					try {
						// statement
						$sqlinsert = 'INSERT INTO locations(locationname, locationaddress, locationcity, locationzip, locationstate,
							locationphone, locationdescription, locationopen, locationclose)
							VALUES(:bvname, :bvaddress, :bvcity, :bvzip, :bvstate, :bvphone, :bvdescription, :bvopen, :bvclose)';

						// Prepare and execute
						$result = $db->prepare($sqlinsert);
						$result->bindValue('bvname', $formfield['name']);
						$result->bindValue('bvaddress', $formfield['address']);
						$result->bindValue('bvzip', $formfield['zip']);
						$result->bindValue('bvstate', $formfield['state']);
						$result->bindValue('bvcity', $formfield['city']);
						$result->bindValue('bvphone', $formfield['phone']);
						$result->bindValue('bvdescription', $formfield['description']);
						$result->bindValue('bvopen', $formfield['locationopen']);
						$result->bindValue('bvclose', $formfield['locationclose']);
						$result->execute();

						// Success
						echo '<div class="alert alert-success" role="alert">Insert successful</div>';
					} catch (Exception $e) {
						// An error occured
						echo '<div class="alert alert-warning" role="alert"><strong>Insert failed: </strong>' . $e->getMessage() . '</div>';
					}
				}
			}
		?>
		<form class="was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div>
				<div class="row">
					<!-- Name field -->
					<div class="col-6 col-md-4 mb-4">
						<input name="name" type="text" class="form-control" placeholder="Name" required>
						<div class="valid-feedback">Valid name</div>
						<div class="invalid-feedback">Invalid name</div>
					</div>
					<!-- Address field -->
					<div class="col-6 col-md-6 mb-4">
						<input name="address" type="text" class="form-control" placeholder="Address" required>
						<div class="valid-feedback">Valid address</div>
						<div class="invalid-feedback">Invalid address</div>
					</div>
					<div class="col-12 col-md-4 mb-3">
						<input name="city" type="text" class="form-control" placeholder="City" required>
						<div class="valid-feedback">Valid city</div>
						<div class="invalid-feedback">Invalid city</div>
					</div>
					<div class="col-6 col-md-3 mb-3">
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
					<div class="col-6 col-md-1 mb-3">
						<input name="zip" type="text" class="form-control" placeholder="ZIP" required>
						<div class="valid-feedback">Valid zip</div>
						<div class="invalid-feedback">Invalid zip</div>
					</div>
					<div class="col-12 col-md-2 mb-3">
						<input name="phone" type="text" class="form-control" placeholder="Phone" required>
						<div class="valid-feedback">Valid phone</div>
						<div class="invalid-feedback">Invalid phone</div>
					</div>
				</div>
				<div class="row">
					<!-- Description field -->
					<div class="col-12 mb-4">
						<input name="description" type="text" class="form-control" placeholder="Description" required>
						<div class="valid-feedback">Valid description</div>
						<div class="invalid-feedback">Invalid description</div>
					</div>
				</div>
				<div class="row">
					<!-- Time fields -->
					<div class="col-6 mb-4">
						<input name="locationopen" type="time" class="form-control" required>
						<div class="valid-feedback">Valid open time</div>
						<div class="invalid-feedback">Invalid open time</div>
					</div>
					<div class="col-6 mb-4">
						<input name="locationclose" type="time" class="form-control" required>
						<div class="valid-feedback">Valid close time</div>
						<div class="invalid-feedback">Invalid close time</div>
					</div>
				</div>
				<div class="row">
					<!-- Submit button -->
					<div class="col-12">
						<button name="insert" type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
} else {
	echo '<p>You do not have permission to view this page</p>';
}
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
