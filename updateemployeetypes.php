<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/.......................1................/', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Employees</a></li>
	<li class="breadcrumb-item"><a href="#">Employee Types</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Employee Types</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="selectemployeetypesTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Name</th>
						<th>Description</th>
						<th>Default Pay</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselecti = "SELECT * FROM employeetype WHERE 1 ORDER BY employeetypekey ASC";
					$result = $db->prepare($sqlselecti);
					$result->execute();
						while ( $row = $result-> fetch() ) {
							echo '<tr><td>' . $row['employeetypename'] . '</td><td> ' . $row['employeetypedescription'] . '</td>
							<td> ' . $row['defaultpay'] . '</td>
							<td>
								<form name="updateemployeetypesselectionform" method="post" action="updateemployeetypesform.php">
									<input type="hidden" name="employeetypekey" value="' . $row['employeetypekey'] . '"/>
									<input type="submit" name="updateemployeetypeselection" value="Update"/>
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
    $('#selectemployeetypesTable').DataTable();
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
