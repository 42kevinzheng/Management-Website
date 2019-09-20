<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/............................1.........../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Locations</a></li>
	<li class="breadcrumb-item"><a href="#">Tables</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Tables</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="selecttablesTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Name</th>
						<th>Location</th>
						<th>Description</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselectt = "SELECT * FROM tables INNER JOIN locations ON tables.locationkey = locations.locationkey ORDER BY tablekey ASC";
					$result = $db->prepare($sqlselectt);
					$result->execute();
						while ( $row = $result-> fetch() )
							{
								echo '<tr><td> ' . $row['tablename'] .
								'</td><td> ' . $row['locationname'] . '</td><td> ' . $row['tabledescription'] . '</td>
								<td>
									<form name="updatetablesselectionform" method="post" action="updatetablesform.php">
										<input type="hidden" name="tablekey" value="' . $row['tablekey'] . '"/>
										<input type="hidden" name="name" value="' . $row['tablename'] . '"/>
										<input type="hidden" name="location" value="' . $row['locationkey'] . '"/>
										<input type="hidden" name="description" value="' . $row['tabledescription'] . '"/>
										<input type="submit" name="updatetablesselection" value="Update"/>
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
    $('#selecttablesTable').DataTable();
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
