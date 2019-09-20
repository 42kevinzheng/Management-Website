<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/...............1......................../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Orders</a></li>
	<li class="breadcrumb-item active">Insert</li>
</ol>

<div class="card">
	<div class="card-header">Insert Order</div>
	<div class="card-body">
		<?php if (isset($_POST['insertordersubmit'])) { ?>
			<?php
				// Date time
				date_default_timezone_set('UTC');
				$date = date('Y-m-d');
				$time = date('h:i:s');

				//enter data into database
				$sqlinsert = 'INSERT INTO orders (customerkey, orderdate, ordertime, locationkey, ordertype, tablekey, employeekey, ordercomplete)
								VALUES (:bvcustomer, :bvdate, :bvtime, :bvlocation, :bvordertype, :bvtablekey, :bvemployee, 0)';
				$stmtinsert = $db->prepare($sqlinsert);
				$stmtinsert->bindvalue(':bvcustomer', $_POST['customerkey']);
				$stmtinsert->bindvalue(':bvdate', $date);
				$stmtinsert->bindvalue(':bvtime', $time);
				$stmtinsert->bindvalue(':bvlocation', $_POST['locationkey']);
				$stmtinsert->bindvalue(':bvordertype', $_POST['ordertype']);
				$stmtinsert->bindvalue(':bvtablekey', $_POST['tablekey']);
				$stmtinsert->bindvalue(':bvemployee', $_SESSION['employeekey']);
				$stmtinsert->execute();

				$sqlmax = "SELECT MAX(orderkey) AS maxid from orders";
				$resultmax = $db->prepare($sqlmax);
				$resultmax->execute();
				$rowmax = $resultmax->fetch();
				$maxid = $rowmax["maxid"];
			?>
			<p>Selection successful. Please proceed to enter items.</p>
			<form method="post" action="insertorderdetails.php">
				<input type="hidden" name="orderkey" value = "<?php echo $maxid; ?>" />
				<input type="submit" name="ordersubmit" value="Proceed" />
			</form>
		<?php } else if (isset($_POST['orderlocationsubmit']) && $_POST['ordertype'] == 1) { ?>
			<!-- Table selection (if ordertype is dine-in) -->
			<form class="was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
				<div class="row">
					<div class="col-12 col-md-4 mb-3">
						<select name="tablekey" class="form-control" required>
							<option disabled selected>Table</option>
							<?php
							$sqlselectt = "SELECT * FROM tables WHERE locationkey=:bvlocationkey";
							$resultt = $db->prepare($sqlselectt);
							$resultt->bindValue(':bvlocationkey', $_POST['locationkey']);
							$resultt->execute();

							while ($rowt = $resultt->fetch()) {
								echo '<option value="'. $rowt['tablekey'] . '">' . $rowt['tablename'] . '</option>';
							}
							?>
						</select>
						<div class="valid-feedback">Valid table</div>
						<div class="invalid-feedback">Invalid invalid table</div>
					</div>
				</div>
				<!-- Submit button row -->
				<div class="row">
					<div class="col-12">
						<input type="hidden" name="locationkey" value="<?php echo $_POST['locationkey']; ?>" />
						<input type="hidden" name="ordertype" value="<?php echo $_POST['ordertype']; ?>" />
						<button name="orderinfosubmit" type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</form>
		<?php } else if (isset($_POST['orderinfosubmit']) || (isset($_POST['orderlocationsubmit']) && $_POST['ordertype'] == 0)) { ?>
			<!-- Customer selection -->
			<div class="table-responsive">
				<table class="table table-bordered" id="selectcustomersTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Phone</th>
							<th>Email</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if (isset($_POST['tablekey'])) {
							$formfield['tablekey'] = $_POST['tablekey'];
						} else {
							$formfield['tablekey'] = 0;
						}
						$sqlselectc = "SELECT * FROM customer WHERE 1";
						$result = $db->prepare($sqlselectc);
						$result->execute();
							while ( $row = $result-> fetch() )
								{
									echo '<tr><td>' . $row['customerfirstname'] . '</td><td> ' . $row['customerlastname'] .
									'</td><td> ' . $row['customerphone'] . '</td><td> ' . $row['customeremail'] . '</td>
									<td>
										<form name="insertorderform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
											<input type="hidden" name="customerkey" value="' . $row['customerkey'] . '"/>
											<input type="hidden" name="locationkey" value="' . $_POST['locationkey'] . '" />
											<input type="hidden" name="tablekey" value="' . $formfield['tablekey'] . '" />
											<input type="hidden" name="ordertype" value="' . $_POST['ordertype'] . '" />
											<input type="submit" name="insertordersubmit" value="Select"/>
										</form>
									</td>';
								}
								echo '</tr>';
						?>
					</tbody>
				</table>
			</div>
		<?php } else { ?>
			<form class="was-validated" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
				<div class="row">
					<!-- Locations -->
					<div class="col-12 col-md-4 mb-3">
						<select name="locationkey" class="form-control" required>
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
						<div class="valid-feedback">Valid location</div>
						<div class="invalid-feedback">Invalid location</div>
					</div>
					<!-- order type -->
					<div class="col-12 col-md-4 mb-3">
						<select name="ordertype" class="form-control" required>
							<option disabled selected>Order Type</option>
							<option value="1">Dine In</option>
							<option value="0">Carry Out</option>
						</select>
						<div class="valid-feedback">Valid order type</div>
						<div class="invalid-feedback">Invalid order type</div>
					</div>
				</div>
				<!-- Submit button row -->
				<div class="row">
					<div class="col-12">
						<button name="orderlocationsubmit" type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</form>
		<?php } ?>
	</div>
</div>

<script>
$(document).ready( function () {
    $('#selectcustomersTable').DataTable();
} );
</script>

<?php
} else {
	echo '<p>You do not have permission to view this page</p>';
}
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
