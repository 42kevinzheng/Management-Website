<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/................................1......./', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item">Employees</li>
	<li class="breadcrumb-item active">Delete</li>
</ol>
<div class="card">
	<div class="card-header">Delete Employees</div>
	<div class="card-body">
		<?php
			if (isset($_POST['deletesubmit'])) {
				try {
					// Delete employee
					$sqldelete = 'DELETE FROM employee
												WHERE employeekey=:bvkey';
					$deleteresult = $db->prepare($sqldelete);
					$deleteresult->bindValue('bvkey', $_POST['employeekey']);
					$deleteresult->execute();

					// Delete schedules
					$sqldelete = 'DELETE FROM schedules
												WHERE employeekey=:bvkey';
					$deleteresult = $db->prepare($sqldelete);
					$deleteresult->bindValue('bvkey', $_POST['employeekey']);
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
			<table class="table table-bordered" id="selectemployeesTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Username</th>
						<th>Type</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Phone</th>
						<th>Address</th>
						<th>City</th>
						<th>State</th>
						<th>ZIP</th>
						<th>Email</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselecti = "SELECT * FROM employee INNER JOIN employeetype ON employee.employeetypekey = employeetype.employeetypekey ORDER BY employeekey ASC";
					$result = $db->prepare($sqlselecti);
					$result->execute();
						while ( $row = $result-> fetch() )
							{
								echo '<tr><td>' . $row['employeeusername'] . '</td><td> ' . $row['employeetypename'] .
								'</td><td> ' . $row['employeefirstname'] . '</td><td> ' . $row['employeelastname'] . '</td>
								<td> ' . $row['employeephone'] . '</td><td> ' . $row['employeeaddress'] . '</td><td> ' . $row['employeecity'] . '</td><td> ' . $row['employeestate'] . '</td>
								<td> ' . $row['employeezip'] . '</td><td> ' . $row['employeeemail'] . '</td>
								<td>
								<input type="button" value="Delete" data-toggle="modal" data-target="#delete' . $row['employeekey'] . 'Modal">
								<div class="modal" id="delete' . $row['employeekey'] . 'Modal" tabindex="-1" role="dialog" aria-labelledby="delete' . $row['employeekey'] . 'ModalLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="delete' . $row['employeekey'] . 'ModalLabel">Confirmation</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">Are you sure you would like to delete the selected employee?</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
												<form name="deleteform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
													<input type="hidden" name="employeekey" value="' . $row['employeekey'] . '"/>
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
    $('#selectemployeesTable').DataTable();
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
