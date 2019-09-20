<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Tickets</a></li>
	<li class="breadcrumb-item active">Current Tickets</li>
</ol>
<div class="card">
	<div class="card-header">Current Tickets</div>
	<div class="card-body">
		<?php
		// Close ticket submission:
		if (isset($_POST['closeticketsubmit'])) {
			try {
				// Statement for tickets table
				$sqlupdateo = 'UPDATE tickets
											SET ticketcomplete=1
											WHERE ticketkey=:bvkey';

				// Prepare and execute
				$resulto = $db->prepare($sqlupdateo);
				$resulto->bindValue('bvkey', $_POST['ticketkey']);
				$resulto->execute();

				// Statement ticket details table
				$sqlupdated = 'UPDATE ticketdetail
											SET ticketdetailcomplete=1
											WHERE ticketkey=:bvkey';

				// Prepare and execute
				$resultd = $db->prepare($sqlupdated);
				$resultd->bindValue('bvkey', $_POST['ticketkey']);
				$resultd->execute();

				// Success
				echo '<div class="alert alert-success" role="alert">Ticket closed successfully</div>';
			} catch (Exception $e) {
				// An error occured
				echo '<div class="alert alert-danger" role="alert"><strong>Update failed: </strong>' . $e->getMessage() . '</div>';
			}
		}
		?>
		<div class="table-responsive">
			<table class="table table-bordered" id="selectticketsTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Customer</th>
						<th>Type</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselecto = "SELECT * FROM tickets
												 INNER JOIN customer ON tickets.customerkey = customer.customerkey
												 WHERE tickets.ticketcomplete = 0
												 ORDER BY ticketkey ASC";
					$result = $db->prepare($sqlselecto);
					$result->execute();
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['ticketdate'] . '</td><td> ' . $row['tickettime'] .
						'</td><td> ' . $row['customeremail'] . '</td><td> ' . $row['tickettype'] . '</td>
						<td>
							<form name="closeticketform" method="post" action="'. $_SERVER['PHP_SELF'] . '">
								<input type="hidden" name="ticketkey" value="' . $row['ticketkey'] . '"/>
								<input type="submit" name="closeticketsubmit" value="Close"/>
							</form>
						</td>
						<td>
							<form name="selectticketform" method="post" action="closeticketdetails.php">
								<input type="hidden" name="ticketkey" value="' . $row['ticketkey'] . '"/>
								<input type="submit" name="selectticketsubmit" value="Select"/>
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
    $('#selectticketsTable').DataTable();
} );
</script>
<?php
}
else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
