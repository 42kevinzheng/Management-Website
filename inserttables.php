<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/..................1...................../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Locations</a></li>
	<li class="breadcrumb-item"><a href="#">Tables</a></li>
	<li class="breadcrumb-item active">Insert</li>
</ol>
<div class="card">
	<div class="card-header">Insert Tables</div>
	<div class="card-body">
		<?php
			// Insert button pressed
			if (isset($_POST['insert'])) {
				// Data cleansing
				$formfield['name'] = trim($_POST['name']);
				$formfield['locationkey'] = trim($_POST['location']);
				$formfield['description'] = trim($_POST['description']);

				// If a field is empty...
				if (empty($formfield['name']) || empty($formfield['locationkey']) ||
						empty($formfield['description'])) {
							// One or more fields are empty
							echo '<br /><p class="text-warning">Insert failed: one or more fields are empty.</p>';
				} else {
					// Attempt to insert
					try {
						// statement
						$sqlinsert = 'INSERT INTO tables(locationkey, tablename, tabledescription)
													VALUES(:bvlocation, :bvname, :bvdescription)';

						// Prepare and execute
						$result = $db->prepare($sqlinsert);
						$result->bindValue('bvlocation', $formfield['locationkey']);
						$result->bindValue('bvname', $formfield['name']);
						$result->bindValue('bvdescription', $formfield['description']);
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
					<div class="col-6 col-md-4">
						<input name="name" type="text" class="form-control" placeholder="Name" required>
						<div class="valid-feedback">Valid name</div>
						<div class="invalid-feedback">Invalid name</div>
					</div>
					<!-- Location field -->
					<div class="col-6 col-md-4">
						<select name="location" class="form-control" required>
							<option disabled selected>Location</option>
							<?php
							$sqlselectl = "SELECT * FROM locations WHERE 1 ORDER BY locationkey ASC";
							$resultl = $db->prepare($sqlselectl);
							$resultl->execute();

							while ($rowl = $resultl->fetch()) {
								echo '<option value="'. $rowl['locationkey'] . '">' . $rowl['locationname'] . '</option>';
							}
							?>
						</select>
						<div class="valid-feedback">Valid location</div>
						<div class="invalid-feedback">Invalid location</div>
					</div>
				</div>
				<br />
				<div class="row">
					<!-- Description field -->
					<div class="col-12">
						<input name="description" type="text" class="form-control" placeholder="Description" required>
						<div class="valid-feedback">Valid description</div>
						<div class="invalid-feedback">Invalid description</div>
					</div>
				</div>
				<br />
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
