<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (isset($_POST['selectticketsubmit']) || isset($_POST['closeticketdetailsubmit'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Tickets</a></li>
	<li class="breadcrumb-item"><a href="#">Ticket Details</a></li>
	<li class="breadcrumb-item active">Close</li>
</ol>
<div class="card">
	<div class="card-header">Close Ticket Details</div>
	<div class="card-body">
		<?php
			// Close ticket detail submission:
			if (isset($_POST['closeticketdetailsubmit'])) {
				try {
					// statement
					$sqlupdate = 'UPDATE ticketdetail
												SET ticketdetailcomplete=1
												WHERE ticketdetailkey=:bvkey';

					// Prepare and execute
					$result = $db->prepare($sqlupdate);
					$result->bindValue('bvkey', $_POST['ticketdetailkey']);
					$result->execute();

					// Success
					echo '<div class="alert alert-success" role="alert">Item closed successfully</div>';
				} catch (Exception $e) {
					// An error occured
					echo '<div class="alert alert-danger" role="alert"><strong>Update failed: </strong>' . $e->getMessage() . '</div>';
				}
			}

			// Select statement for the table
			$sqlselecto = 'SELECT *
										 FROM ticketdetail
										 INNER JOIN menuitem ON ticketdetail.menuitemkey = menuitem.menuitemkey
										 WHERE ticketkey = :bvticketkey AND ticketdetailcomplete = 0';
			$result = $db->prepare($sqlselecto);
			$result->bindValue('bvticketkey', $_POST['ticketkey']);
			$result->execute();

			// If there are no open items, close the ticket
			$count = $result->rowCount();
			if ($count < 1) {
				try {
					// statement
					$sqlupdate = 'UPDATE tickets
												SET ticketcomplete=1
												WHERE ticketkey=:bvkey';

					// Prepare and execute
					$resultu = $db->prepare($sqlupdate);
					$resultu->bindValue('bvkey', $_POST['ticketkey']);
					$resultu->execute();

					// Success
					echo '<div class="alert alert-success" role="alert">Ticket closed successfully</div>';
				} catch (Exception $e) {
					// An error occured
					echo '<div class="alert alert-danger" role="alert"><strong>Update failed: </strong>' . $e->getMessage() . '</div>';
				}
			}

		?>
		<div class="table-responsive">
			<table class="table table-bordered" id="selectticketdetailsTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Item</th>
						<th>Notes</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['menuitemname'] . '</td><td> ' . $row['ticketdetailnote'] . '</td>
						<td>
							<form name="closeticketdetailsform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
								<input type="hidden" name="ticketkey" value="' . $row['ticketkey'] . '"/>
								<input type="hidden" name="ticketdetailkey" value="' . $row['ticketdetailkey'] . '"/>
								<input type="submit" name="closeticketdetailsubmit" value="Close"/>
							</form>
						</td>';
					}
					echo '</tr>';
					?>
				</tbody>
			</table>
		</div>
		<p class="mt-4 mb-0"><a href="closetickets.php">Back</a></p>
	</div>
</div>
<script>
$(document).ready( function () {
    $('#selectticketdetailsTable').DataTable();
} );
</script>
<?php
} else {
	echo '<p>This page can not be viewed.</p>';
}
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
