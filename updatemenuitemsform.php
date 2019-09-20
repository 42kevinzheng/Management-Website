<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		// Only view this page if it came from the according pages
		if (isset($_POST['updatemenuitemselection']) || isset($_POST['update'])) {
			// Define menu item key
			$formfield['menuitemkey'] = $_POST['menuitemkey'];
			// Data cleansing
			$formfield['type'] = $_POST['type'];
			$formfield['name'] = $_POST['name'];
			$formfield['price'] = $_POST['price'];
			$formfield['count'] = $_POST['count'];
			$formfield['description'] = $_POST['description'];
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Menu</a></li>
	<li class="breadcrumb-item"><a href="#">Menu Items</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Menu Items</div>
	<div class="card-body">
		<?php
			// Insert button pressed
			if (isset($_POST['update'])) {
				// If a field is empty...
				if (empty($formfield['type']) || empty($formfield['name']) ||
						empty($formfield['price']) || empty($formfield['count']) ||
						empty($formfield['description'])) {
							// One or more fields are empty
							echo '<br /><p class="text-warning">Insert failed: one or more fields are empty.</p>';
				} else {
					// Attempt to update
					try {
						$sqlupdate = 'UPDATE menuitem
													SET menutypekey=:bvtype, menuitemname=:bvname,
															menuitemprice=:bvprice, menuitemcount=:bvcount,
															menuitemdesc=:bvdescription
													WHERE menuitemkey=:bvitemkey';

						$result = $db->prepare($sqlupdate);
						$result->bindValue('bvtype', $formfield['type']);
						$result->bindValue('bvname', $formfield['name']);
						$result->bindValue('bvprice', $formfield['price']);
						$result->bindValue('bvcount', $formfield['count']);
						$result->bindValue('bvdescription', $formfield['description']);
						$result->bindValue('bvitemkey', $formfield['menuitemkey']);
						$result->execute();

						echo '<div class="alert alert-success" role="alert">Update successful. <a href="updatemenuitems.php">Back</a></div>';
					} catch (Exception $e) {
						echo '<br /><p class="text-danger font-weight-bold">Update failed.</p>';
					}
				}
			}
		?>
		<form class="was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div>
				<div class="row">
					<div class="col-12 col-md-6 mb-3">
						<input name="name" type="text" class="form-control" placeholder="Name" value="<?php echo $formfield['name']; ?>" required>
						<div class="valid-feedback">Valid name</div>
						<div class="invalid-feedback">Invalid name</div>
					</div>
					<div class="col-12 col-md-4 mb-3">
						<select name="type" class="form-control" required>
							<option disabled selected>Type</option>
							<?php
							$sqlselectt = "SELECT * FROM menutype";
							$resultt = $db->prepare($sqlselectt);
							$resultt->execute();

							while ($rowt = $resultt->fetch()) {
								echo '<option value="'. $rowt['menutypekey'] . '"';
									if ($rowt['menutypekey'] == $formfield['type']) { echo ' selected'; }
								echo '>' . $rowt['menutypename'] . '</option>';
							}
							?>
						</select>
						<div class="valid-feedback">Valid type</div>
						<div class="invalid-feedback">Invalid type</div>
					</div>
					<div class="col-12 col-md-4 mb-3">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">$</div>
							</div>
							<input name="price" type="text" class="form-control" placeholder="Price" value="<?php echo $formfield['price']; ?>" required>
							<div class="valid-feedback">Valid price</div>
							<div class="invalid-feedback">Invalid price</div>
						</div>
					</div>
					<div class="col-12 col-md-3 mb-3">
						<input name="count" type="text" class="form-control" placeholder="Count" value="<?php echo $formfield['count']; ?>" required>
						<div class="valid-feedback">Valid count</div>
						<div class="invalid-feedback">Invalid count</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 mb-3">
						<input name="description" type="text" class="form-control" placeholder="Description" value="<?php echo $formfield['description']; ?>" required>
						<div class="valid-feedback">Valid description</div>
						<div class="invalid-feedback">Invalid description</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<input type="hidden" name="menuitemkey" value="<?php echo $formfield['menuitemkey']; ?>"/>
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
