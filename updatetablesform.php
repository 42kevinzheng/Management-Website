<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		// Only view this page if it came from the according pages
		if (isset($_POST['updatetablesselection']) || isset($_POST['update'])) {
			// Define table key
			$formfield['tablekey'] = $_POST['tablekey'];
			// Data cleansing
			$formfield['name'] = trim($_POST['name']);
			$formfield['locationkey'] = trim($_POST['location']);
			$formfield['description'] = trim($_POST['description']);
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item">Locations</li>
	<li class="breadcrumb-item">Tables</li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Tables</div>
	<div class="card-body">
		<?php
			// Update button pressed
			if (isset($_POST['update'])) {
				// If a field is empty...
				if (empty($formfield['name']) || empty($formfield['locationkey']) ||
						empty($formfield['description'])) {
							// One or more fields are empty
							echo '<br /><p class="text-warning">Update failed: one or more fields are empty.</p>';
				} else {
					// Attempt to Update
					try {
						// statement
						$sqlupdate = 'UPDATE tables
													SET locationkey=:bvlocation, tablename=:bvname,
															tabledescription=:bvdescription
													WHERE tablekey=:bvtablekey';

						// Prepare and execute
						$result = $db->prepare($sqlupdate);
						$result->bindValue('bvlocation', $formfield['locationkey']);
						$result->bindValue('bvname', $formfield['name']);
						$result->bindValue('bvdescription', $formfield['description']);
						$result->bindValue('bvtablekey', $formfield['tablekey']);
						$result->execute();

						// Success
						echo '<div class="alert alert-success" role="alert">Update successful. <a href="updatetables.php">Back</a></div>';
					} catch (Exception $e) {
						// An error occured
						echo '<br /><p class="text-danger font-weight-bold">Update failed.</p>';
						echo '<br /><p class="text-danger font-weight-bold">' .$e->getMessage() . '</p>';
					}
				}
			}
		?>
		<form class="was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div>
				<div class="row">
					<!-- Name field -->
					<div class="col-6 col-md-4">
						<input name="name" type="text" class="form-control" placeholder="Name" value="<?php echo $formfield['name']; ?>" required>
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
								echo '<option value="'. $rowl['locationkey'] . '"';
									if ($rowl['locationkey'] == $formfield['locationkey']) { echo ' selected'; }
								echo '>' . $rowl['locationname'] . '</option>';
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
						<input name="description" type="text" class="form-control" placeholder="Description" value="<?php echo $formfield['description']; ?>" required>
						<div class="valid-feedback">Valid description</div>
						<div class="invalid-feedback">Invalid description</div>
					</div>
				</div>
				<br />
				<div class="row">
					<!-- Submit button -->
					<div class="col-12">
						<input type="hidden" name="tablekey" value="<?php echo $formfield['tablekey']; ?>"/>
						<button name="update" type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
} else {
	echo '<p>nope</p>'; // page can not be viewed
}
} else {
		echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
