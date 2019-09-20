<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/..........1............................./', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Menu</a></li>
	<li class="breadcrumb-item"><a href="#">Menu Items</a></li>
	<li class="breadcrumb-item active">Insert</li>
</ol>
<div class="card">
	<div class="card-header">Insert Menu Items</div>
	<div class="card-body">
		<?php
			// Insert button pressed
			if (isset($_POST['insert'])) {
				// Data cleansing
				$formfield['type'] = $_POST['type'];
				$formfield['name'] = $_POST['name'];
				$formfield['price'] = $_POST['price'];
				$formfield['count'] = $_POST['count'];
				$formfield['description'] = $_POST['description'];

				// If a field is empty...
				if (empty($formfield['type']) || empty($formfield['name']) ||
						empty($formfield['price']) || empty($formfield['count']) ||
						empty($formfield['description'])) {
							// One or more fields are empty
							echo '<br /><p class="text-warning">Insert failed: one or more fields are empty.</p>';
				} else {
					// Attempt to insert
					try {
						$sqlnewitem = "INSERT into menuitem(menutypekey, menuitemname, menuitemprice, menuitemcount, menuitemdesc)
						VALUES (:bvtype, :bvname, :bvprice, :bvcount, :bvdescription)";

						$result = $db->prepare($sqlnewitem);
						$result->bindValue('bvtype', $formfield['type']);
						$result->bindValue('bvname', $formfield['name']);
						$result->bindValue('bvprice', $formfield['price']);
						$result->bindValue('bvcount', $formfield['count']);
						$result->bindValue('bvdescription', $formfield['description']);
						$result->execute();

						echo '<div class="alert alert-success" role="alert">Insert successful</div>';
					} catch (Exception $e) {
						echo '<div class="alert alert-warning" role="alert"><strong>Insert failed: </strong>' . $e->getMessage() . '</div>';
					}
				}
			}
		?>
		<form class="was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div>
				<div class="row">
					<div class="col-12 col-md-6 mb-3">
						<input name="name" type="text" class="form-control" placeholder="Name" required>
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
								echo '<option value="'. $rowt['menutypekey'] . '">' . $rowt['menutypename'] . '</option>';
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
							<input name="price" type="text" class="form-control" placeholder="Price" required>
							<div class="valid-feedback">Valid price</div>
							<div class="invalid-feedback">Invalid price</div>
						</div>
					</div>
					<div class="col-12 col-md-3 mb-3">
						<input name="count" type="text" class="form-control" placeholder="Count" required>
						<div class="valid-feedback">Valid count</div>
						<div class="invalid-feedback">Invalid count</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 mb-3">
						<input name="description" type="text" class="form-control" placeholder="Description" required>
						<div class="valid-feedback">Valid description</div>
						<div class="invalid-feedback">Invalid description</div>
					</div>
				</div>
				<div class="row">
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
