<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/...............................1......../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Menu</a></li>
	<li class="breadcrumb-item"><a href="#">Menu Types</a></li>
	<li class="breadcrumb-item active">Delete</li>
</ol>
<div class="card">
	<div class="card-header">Delete Menu Types</div>
	<div class="card-body">
		<?php
		// If delete button is pressed
		if (isset($_POST['deletesubmit'])) {
			try {
				// Delete menu type
				$sqldelete = 'DELETE FROM menutype
											WHERE menutypekey=:bvkey';
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['menutypekey']);
				$deleteresult->execute();

				// Delete all menu items with that menu type
				$sqldelete = 'DELETE FROM menuitem
											WHERE menutypekey=:bvkey';
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['menutypekey']);
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
						<input type="button" value="Delete" data-toggle="modal" data-target="#delete' . $row['menutypekey'] . 'Modal">
						<div class="modal" id="delete' . $row['menutypekey'] . 'Modal" tabindex="-1" role="dialog" aria-labelledby="delete' . $row['menutypekey'] . 'ModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="delete' . $row['menutypekey'] . 'ModalLabel">Confirmation</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">Are you sure you would like to delete the selected menu type? <strong>This will delete all menu items associated with this menu type.</strong></div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
										<form name="deleteform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
											<input type="hidden" name="menutypekey" value="' . $row['menutypekey'] . '"/>
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
