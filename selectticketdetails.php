<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (isset($_POST['selectticketsubmit'])) {

			// Set locale for money format
			setlocale(LC_MONETARY, 'en_US.UTF-8');
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Tickets</a></li>
	<li class="breadcrumb-item"><a href="#">Ticket Details</a></li>
	<li class="breadcrumb-item active">Select</li>
</ol>
<div class="card">
	<div class="card-header">Select Ticket Details</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="selectticketdetailsTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Item</th>
						<th>Price</th>
						<th>Notes</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselecto = 'SELECT *
												 FROM ticketdetail
												 INNER JOIN menuitem ON ticketdetail.menuitemkey = menuitem.menuitemkey
												 WHERE ticketkey = :bvticketkey';
					$result = $db->prepare($sqlselecto);
					$result->bindValue('bvticketkey', $_POST['ticketkey']);
					$result->execute();
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['menuitemname'] . '</td><td> ' . money_format('%.2n', $row['ticketdetailprice']) .
						'</td><td> ' . $row['ticketdetailnote'] . '</td>';
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
