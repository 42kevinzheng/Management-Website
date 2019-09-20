<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/.....................1................../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Menu</a></li>
	<li class="breadcrumb-item"><a href="#">Menu Types</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Menu Types</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="selectmenutypesTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Name</th>
						<th>Description</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselecti = "SELECT * FROM menutype WHERE 1 ORDER BY menutypekey ASC";
					$result = $db->prepare($sqlselecti);
					$result->execute();
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['menutypename'] . '</td><td> ' . $row['menutypedescription'] . '</td>
						<td>
							<form name="updatemenutypesselectionform" method="post" action="updatemenutypesform.php">
								<input type="hidden" name="menutypekey" value="' . $row['menutypekey'] . '"/>
								<input type="hidden" name="name" value="' . $row['menutypename'] . '"/>
								<input type="hidden" name="description" value="' . $row['menutypedescription'] . '"/>
								<input type="submit" name="updatemenutypeselection" value="Update"/>
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
    $('#selectmenutypesTable').DataTable();
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
