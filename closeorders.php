<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Orders</a></li>
	<li class="breadcrumb-item active">Current Orders</li>
</ol>
<div class="card">
	<div class="card-header">Current Orders</div>
	<div class="card-body">
		<?php
		// Close order submission:
		if (isset($_POST['closeordersubmit'])) {
			try {
				// Statement for orders table
				$sqlupdateo = 'UPDATE orders
											SET ordercomplete=1
											WHERE orderkey=:bvkey';

				// Prepare and execute
				$resulto = $db->prepare($sqlupdateo);
				$resulto->bindValue('bvkey', $_POST['orderkey']);
				$resulto->execute();

				// Statement order details table
				$sqlupdated = 'UPDATE orderdetail
											SET orderdetailcomplete=1
											WHERE orderkey=:bvkey';

				// Prepare and execute
				$resultd = $db->prepare($sqlupdated);
				$resultd->bindValue('bvkey', $_POST['orderkey']);
				$resultd->execute();

				// Success
				echo '<div class="alert alert-success" role="alert">Order closed successfully</div>';
			} catch (Exception $e) {
				// An error occured
				echo '<div class="alert alert-danger" role="alert"><strong>Update failed: </strong>' . $e->getMessage() . '</div>';
			}
		}
		?>
		<div class="table-responsive">
			<table class="table table-bordered" id="selectordersTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Customer</th>
						<th>Employee</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselecto = "SELECT * FROM orders
												 INNER JOIN customer ON orders.customerkey = customer.customerkey
												 INNER JOIN employee ON orders.employeekey = employee.employeekey
												 WHERE orders.ordercomplete = 0
												 ORDER BY orderkey ASC";
					$result = $db->prepare($sqlselecto);
					$result->execute();
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['orderdate'] . '</td><td> ' . $row['ordertime'] .
						'</td><td> ' . $row['customeremail'] . '</td><td> ' . $row['employeeusername'] . '</td>
						<td>
							<form name="closeorderform" method="post" action="'. $_SERVER['PHP_SELF'] . '">
								<input type="hidden" name="orderkey" value="' . $row['orderkey'] . '"/>
								<input type="submit" name="closeordersubmit" value="Close"/>
							</form>
						</td>
						<td>
							<form name="selectorderform" method="post" action="closeorderdetails.php">
								<input type="hidden" name="orderkey" value="' . $row['orderkey'] . '"/>
								<input type="submit" name="selectordersubmit" value="Select"/>
							</form>
						</td>';
					}
					echo '</tr>';
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
$(document).ready( function () {
    $('#selectordersTable').DataTable();
} );
</script>
<?php
}
else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
