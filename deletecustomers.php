<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/..................................1...../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Customers</a></li>
	<li class="breadcrumb-item active">Delete</li>
</ol>
<div class="card">
	<div class="card-header">Delete Customers</div>
	<div class="card-body">
		<?php
			if (isset($_POST['deletesubmit'])) {
				try {
					$sqldelete = 'DELETE FROM customer
												WHERE customerkey=:bvkey';
					$deleteresult = $db->prepare($sqldelete);
					$deleteresult->bindValue('bvkey', $_POST['customerkey']);
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
			<table class="table table-bordered" id="selectcustomersTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Phone</th>
						<th>Address</th>
						<th>City</th>
						<th>State</th>
						<th>ZIP</th>
						<th>Email</th>
						<th>Preferred Location</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselectc = "SELECT *
					FROM customer
					INNER JOIN locations ON customer.locationkey = locations.locationkey
					ORDER BY customerkey ASC";
					$result = $db->prepare($sqlselectc);
					$result->execute();
						while ( $row = $result-> fetch() )
							{
								echo '<tr><td> ' . $row['customerfirstname'] .
								'</td><td> ' . $row['customerlastname'] . '</td><td> ' . $row['customerphone'] .
								'</td><td> ' . $row['customeraddress'] . '</td><td> ' . $row['customercity'] .
								'</td><td> ' . $row['customerstate'] . '</td><td> ' . $row['customerzip'] .
								'</td><td> ' . $row['customeremail'] . '</td><td> ' . $row['locationname'] . '</td>
								<td>
								<input type="button" value="Delete" data-toggle="modal" data-target="#delete' . $row['customerkey'] . 'Modal">
								<div class="modal" id="delete' . $row['customerkey'] . 'Modal" tabindex="-1" role="dialog" aria-labelledby="delete' . $row['customerkey'] . 'ModalLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="delete' . $row['customerkey'] . 'ModalLabel">Confirmation</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">Are you sure you would like to delete the selected customer?</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
												<form name="deleteform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
													<input type="hidden" name="customerkey" value="' . $row['customerkey'] . '"/>
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
