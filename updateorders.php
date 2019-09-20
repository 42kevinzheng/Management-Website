<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/.........................1............../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Orders</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Orders</div>
	<div class="card-body">
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
							<form name="selectorderform" method="post" action="updateorderdetails.php">
								<input type="hidden" name="orderkey" value="' . $row['orderkey'] . '"/>
								<input type="submit" name="updateordersubmit" value="Update"/>
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
} else {
	echo '<p>You do not have permission to view this page</p>';
}
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
