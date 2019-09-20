<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/...........................1............/', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Locations</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Locations</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="selectlocationsTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Name</th>
						<th>Address</th>
						<th>Description</th>
						<th>Opening time</th>
						<th>Closing time</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselectc = "SELECT * FROM locations ORDER BY locationkey ASC";
					$result = $db->prepare($sqlselectc);
					$result->execute();
						while ( $row = $result-> fetch() )
							{
								echo '<tr><td> ' . $row['locationname'] .
								'</td><td> ' . $row['locationaddress'] . '</td><td> ' . $row['locationdescription'] . '</td>
								<td>' . date("g:i a", strtotime($row['locationopen'])) . '</td>
								<td>' . date("g:i a", strtotime($row['locationclose'])) . '</td>
								<td>
									<form name="updalocationsselectionform" method="post" action="updatelocationsform.php">
										<input type="hidden" name="locationkey" value="' . $row['locationkey'] . '"/>
										<input type="submit" name="updatelocationselection" value="Update"/>
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
    $('#selectlocationsTable').DataTable();
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
