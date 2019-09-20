<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/.................................1....../', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Employees</a></li>
	<li class="breadcrumb-item"><a href="#">Employee Types</a></li>
	<li class="breadcrumb-item active">Delete</li>
</ol>
<div class="card">
	<div class="card-header">Delete Employee Types</div>
	<div class="card-body">
		<?php
		// If delete button is pressed
		if (isset($_POST['deletesubmit'])) {
			try {
				// Delete employee type
				$sqldelete = 'DELETE FROM employeetype
											WHERE employeetypekey=:bvkey';
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['employeetypekey']);
				$deleteresult->execute();

				// Delete all employees under that type
				$sqldelete = 'DELETE FROM employee
											WHERE employeetypekey=:bvkey';
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['employeetypekey']);
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
			<table class="table table-bordered" id="selectemployeetypesTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Name</th>
						<th>Description</th>
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
							<td>
							<input type="button" value="Delete" data-toggle="modal" data-target="#delete' . $row['employeetypekey'] . 'Modal">
							<div class="modal" id="delete' . $row['employeetypekey'] . 'Modal" tabindex="-1" role="dialog" aria-labelledby="delete' . $row['employeetypekey'] . 'ModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="delete' . $row['employeetypekey'] . 'ModalLabel">Confirmation</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body">Are you sure you would like to delete the selected employee type? <strong>This will delete all employees associated with this employee type.</strong></div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
											<form name="deleteform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
												<input type="hidden" name="employeetypekey" value="' . $row['employeetypekey'] . '"/>
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
