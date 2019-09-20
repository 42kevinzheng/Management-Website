<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/..........................1............./', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Tickets</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Tickets</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="selectticketsTable" width="100%" cellspacing="0">
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
							<form name="selectticketform" method="post" action="updateticketdetails.php">
								<input type="hidden" name="ticketkey" value="' . $row['ticketkey'] . '"/>
								<input type="submit" name="updateticketsubmit" value="Update"/>
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
} else {
	echo '<p>You do not have permission to view this page</p>';
}
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
