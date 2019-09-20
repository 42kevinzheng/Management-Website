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
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Schedules</div>
	<div class="card-body">
		<?php if(isset($_POST['selectemployeesubmit'])) { ?>
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
										<form name="selectscheduleform" method="post" action="updateschedulesform.php">
											<input type="hidden" name="schedulekey" value="' . $row['schedulekey'] . '"/>
											<input type="submit" name="selectschedulesubmit" value="Select"/>
										</form>
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
