<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		// Only view this page if it came from the according pages
		if (isset($_POST['updatemenutypeselection']) || isset($_POST['update'])) {
			// Define menu type key
			$formfield['menutypekey'] = $_POST['menutypekey'];
			// Data cleansing
			$formfield['name'] = $_POST['name'];
			$formfield['description'] = $_POST['description'];
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Menu</a></li>
	<li class="breadcrumb-item"><a href="#">Menu Types</a></li>
	<li class="breadcrumb-item active">Insert</li>
</ol>
<div class="card">
	<div class="card-header">Insert Menu Types</div>
	<div class="card-body">
		<?php
		if (isset($_POST['update'])) {
			// If a field is empty...
			if (empty($formfield['name']) || empty($formfield['description'])) {
				// One or more fields are empty
				echo '<br /><p class="text-warning">Update failed: one or more fields are empty.</p>';
			} else {
				// Attempt to insert
				try {
					$sqlupdate = 'UPDATE menutype
												SET menutypename=:bvname, menutypedescription=:bvdescription
												WHERE menutypekey=:bvtypekey';

					$result = $db->prepare($sqlupdate);
					$result->bindValue('bvname', $formfield['name']);
					$result->bindValue('bvdescription', $formfield['description']);
					$result->bindValue('bvtypekey', $formfield['menutypekey']);
					$result->execute();

					echo '<div class="alert alert-success" role="alert">Update successful. <a href="updatemenutypes.php">Back</a></div>';
				} catch (Exception $e) {
					echo '<br /><p class="text-danger font-weight-bold">Update failed.</p>';
				}
			}
		}
		?>
		<form class="was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div>
				<div class="row">
					<div class="col-3 col-md-3">
						<input name="name" type="text" class="form-control" placeholder="Name" value="<?php echo $formfield['name']; ?>" required>
						<div class="valid-feedback">Valid name</div>
						<div class="invalid-feedback">Invalid name</div>
					</div>
					<div class="col-9 col-md-9">
						<input name="description" type="text" class="form-control" placeholder="Description" value="<?php echo $formfield['description']; ?>" required>
						<div class="valid-feedback">Valid description</div>
						<div class="invalid-feedback">Invalid description</div>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-12">
						<input type="hidden" name="menutypekey" value="<?php echo $formfield['menutypekey']; ?>"/>
						<button name="update" type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
} else {
	echo '<p></p>'; // page can not be viewed
}
} else {
		echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
