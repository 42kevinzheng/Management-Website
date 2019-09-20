<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/......................................1./', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Locations</a></li>
	<li class="breadcrumb-item"><a href="#">Tables</a></li>
	<li class="breadcrumb-item active">Delete</li>
</ol>
<div class="card">
	<div class="card-header">Delete Tables</div>
	<div class="card-body">
		<?php
		// If delete button is pressed
		if (isset($_POST['deletesubmit'])) {
			try {
				// Create delete statement
				$sqldelete = 'DELETE FROM tables
											WHERE tablekey=:bvkey';
				// Prepare and execute
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['tablekey']);
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
								<input type="button" value="Delete" data-toggle="modal" data-target="#delete' . $row['tablekey'] . 'Modal">
								<div class="modal" id="delete' . $row['tablekey'] . 'Modal" tabindex="-1" role="dialog" aria-labelledby="delete' . $row['tablekey'] . 'ModalLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="delete' . $row['tablekey'] . 'ModalLabel">Confirmation</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">Are you sure you would like to delete the selected table?</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
												<form name="deleteform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
													<input type="hidden" name="tablekey" value="' . $row['tablekey'] . '"/>
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
