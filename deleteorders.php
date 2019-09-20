<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/...................................1..../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Orders</a></li>
	<li class="breadcrumb-item active">Delete</li>
</ol>
<div class="card">
	<div class="card-header">Delete Orders</div>
	<div class="card-body">
		<?php
		// If delete button is pressed
		if (isset($_POST['deletesubmit'])) {
			try {
				// Create delete statement for orders
				$sqldelete = 'DELETE FROM orders
											WHERE orderkey=:bvkey';
				// Prepare and execute
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['orderkey']);
				$deleteresult->execute();

				// Create delete statement for order details
				$sqldelete = 'DELETE FROM orderdetail
											WHERE orderkey=:bvkey';
				// Prepare and execute
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['orderkey']);
				$deleteresult->execute();

				// Success
				echo '<div class="alert alert-success" role="alert">Delete successful</div>';
			} catch (Exception $e) {
				// An error occured
				echo '<div class="alert alert-danger" role="alert"><strong>Delete failed: </strong>' . $e->getMessage() . '</div>';
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
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselecto = "SELECT * FROM orders
												 INNER JOIN customer ON orders.customerkey = customer.customerkey
												 INNER JOIN employee ON orders.employeekey = employee.employeekey
												 ORDER BY orderkey ASC";
					$result = $db->prepare($sqlselecto);
					$result->execute();
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['orderdate'] . '</td><td> ' . $row['ordertime'] .
						'</td><td> ' . $row['customeremail'] . '</td><td> ' . $row['employeeusername'] . '</td>
						<td>
						<input type="button" value="Delete" data-toggle="modal" data-target="#delete' . $row['orderkey'] . 'Modal">
						<div class="modal" id="delete' . $row['orderkey'] . 'Modal" tabindex="-1" role="dialog" aria-labelledby="delete' . $row['orderkey'] . 'ModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="delete' . $row['orderkey'] . 'ModalLabel">Confirmation</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">Are you sure you would like to delete the selected order?</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
										<form name="deleteform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
											<input type="hidden" name="orderkey" value="' . $row['orderkey'] . '"/>
											<input type="submit" name="deletesubmit" class="btn btn-primary" value="Confirm">
										</form>
									</div>
								</div>
							</div>
						</div>
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
} else {
	echo '<p>You do not have permission to view this page</p>';
}
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
