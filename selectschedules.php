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
	<li class="breadcrumb-item active">Select</li>
</ol>
<div class="card">
	<div class="card-header">Select Schedules</div>
	<div class="card-body">
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
									<form name="selectemployeeform" method="post" action="viewschedule.php">
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
	</div>
</div>
<script>
$(document).ready( function () {
    $('#selectemployeesTable').DataTable();
} );
</script>
<?php
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
