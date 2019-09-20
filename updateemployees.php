<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/......................1................./', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Employees</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>
<div class="card">
	<div class="card-header">Update Employees</div>
	<div class="card-body">
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
						<th>Pay</th>
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
								<td> ' . $row['employeephone'] . '</td><td> ' . $row['employeeaddress'] . '</td>
								<td> ' . $row['employeecity'] . '</td><td> ' . $row['employeestate'] . '</td>
								<td> ' . $row['employeezip'] . '</td><td> ' . $row['employeeemail'] . '</td>
								<td> ' . $row['employeepay'] . '</td>
								<td>
									<form name="updateemployeesselectionform" method="post" action="updateemployeesform.php">
										<input type="hidden" name="employeekey" value="' . $row['employeekey'] . '"/>
										<input type="hidden" name="firstname" value="' . $row['employeefirstname'] . '"/>
										<input type="hidden" name="lastname" value="' . $row['employeelastname'] . '"/>
										<input type="hidden" name="phone" value="' . $row['employeephone'] . '"/>
										<input type="hidden" name="email" value="' . $row['employeeemail'] . '"/>
										<input type="hidden" name="address" value="' . $row['employeeaddress'] . '"/>
										<input type="hidden" name="city" value="' . $row['employeecity'] . '"/>
										<input type="hidden" name="state" value="' . $row['employeestate'] . '"/>
										<input type="hidden" name="zip" value="' . $row['employeezip'] . '"/>
										<input type="hidden" name="username" value="' . $row['employeeusername'] . '"/>
										<input type="hidden" name="pay" value="' . $row['employeepay'] . '"/>
										<input type="hidden" name="type" value="' . $row['employeetypekey'] . '"/>
										<input type="submit" name="updateemployeeselection" value="Update"/>
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
	echo '<p>You do not have permission to view this page</p>';
}
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
