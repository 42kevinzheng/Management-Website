<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		// This function gets the weekdate of a given date
		function getWeekday($date) {
			return date('w', strtotime($date));
		}
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Schedules</a></li>
	<li class="breadcrumb-item active">Delete</li>
</ol>
<div class="card">
	<div class="card-header">Delete Schedules</div>
	<div class="card-body">
		<?php
		if (isset($_POST['deleteschedulesubmit'])) {
			try {
				// Create delete statement for schedules
				$sqldelete = 'DELETE FROM schedules
											WHERE schedulekey=:bvkey';
				// Prepare and execute
				$deleteresult = $db->prepare($sqldelete);
				$deleteresult->bindValue('bvkey', $_POST['schedulekey']);
				$deleteresult->execute();

				// Success
				echo '<div class="alert alert-success" role="alert">Delete successful. <a href="deleteschedules.php">Back</a></div>';
			} catch (Exception $e) {
				// An error occured
				echo '<div class="alert alert-danger" role="alert"><strong>Delete failed: </strong>' . $e->getMessage() . '</div>';
			}
		}
		?>
		<?php if(isset($_POST['selectemployeesubmit']) || isset($_POST['deleteschedulesubmit'])) { ?>
			<div class="table-responsive">
				<table class="table table-bordered" id="selectschedulesTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Week of</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sqlselects = 'SELECT *
													 FROM schedules
													 WHERE employeekey=:bvemployeekey';
						$result = $db->prepare($sqlselects);
						$result->bindValue(':bvemployeekey', $_POST['employeekey']);
						$result->execute();
							while ( $row = $result-> fetch() )
								{
									echo '<tr><td>' . $row['schedulestart'] . '</td>
									<td>
									<input type="button" value="Delete" data-toggle="modal" data-target="#delete' . $row['schedulekey'] . 'Modal">
									<div class="modal" id="delete' . $row['schedulekey'] . 'Modal" tabindex="-1" role="dialog" aria-labelledby="delete' . $row['schedulekey'] . 'ModalLabel" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="delete' . $row['schedulekey'] . 'ModalLabel">Confirmation</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">Are you sure you would like to delete the selected schedule?</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
													<form name="deletescheduleform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
														<input type="hidden" name="schedulekey" value="' . $row['schedulekey'] . '"/>
														<input type="hidden" name="employeekey" value="' . $_POST['employeekey'] . '"/>
														<input type="submit" name="deleteschedulesubmit" class="btn btn-primary" value="Confirm">
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
		<?php } else { ?>
			<!-- Begin by selecting an employee -->
			<div class="table-responsive">
				<table class="table table-bordered" id="selectemployeesTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Username</th>
							<th>Type</th>
							<th>First Name</th>
							<th>Last Name</th>
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
									<td>
										<form name="selectemployeeform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
											<input type="hidden" name="employeekey" value="' . $row['employeekey'] . '"/>
											<input type="submit" name="selectemployeesubmit" value="Select"/>
										</form>
									</td>';
								}
								echo '</tr>';
						?>
					</tbody>
				</table>
			</div>
		<?php } ?>
	</div>
</div>
<script>
$(document).ready( function () {
    $('#selectemployeesTable').DataTable();
		$('#selectschedulesTable').DataTable();
} );
</script>
<?php
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
