<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/....................................1.../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Tickets</a></li>
	<li class="breadcrumb-item active">Delete</li>
</ol>
<div class="card">
	<div class="card-header">Delete Tickets</div>
	<div class="card-body">
		<?php
		// If delete button is pressed
		if (isset($_POST['deletesubmit'])) {
			try {
				// Create delete statement for tickets
				$sqldelete = 'DELETE FROM tickets
											WHERE ticketkey=:bvkey';
				// Prepare and execute
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['ticketkey']);
				$deleteresult->execute();

				// Create delete statement for ticket details
				$sqldelete = 'DELETE FROM ticketdetail
											WHERE ticketkey=:bvkey';
				// Prepare and execute
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['ticketkey']);
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
						<th>Type</th>
						<th>Customer</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselecto = "SELECT * FROM tickets
												 INNER JOIN customer ON tickets.customerkey = customer.customerkey
												 ORDER BY ticketkey ASC";
					$result = $db->prepare($sqlselecto);
					$result->execute();
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['ticketdate'] . '</td><td> ' . $row['tickettime'] .
						'</td><td> ' . $row['tickettype'] . '</td><td> ' . $row['customeremail'] . '</td>
						<td>
						<input type="button" value="Delete" data-toggle="modal" data-target="#delete' . $row['ticketkey'] . 'Modal">
						<div class="modal" id="delete' . $row['ticketkey'] . 'Modal" tabindex="-1" role="dialog" aria-labelledby="delete' . $row['ticketkey'] . 'ModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="delete' . $row['ticketkey'] . 'ModalLabel">Confirmation</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">Are you sure you would like to delete the selected ticket?</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
										<form name="deleteform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
											<input type="hidden" name="ticketkey" value="' . $row['ticketkey'] . '"/>
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
