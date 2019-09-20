<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item">Schedules</li>
	<li class="breadcrumb-item active">View Schedule</li>
</ol>
<div class="card">
	<div class="card-header">View Schedule</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="scheduleTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Date</th>
						<th>Day</th>
						<th>Start</th>
						<th>End</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselectc = "SELECT * FROM schedules WHERE employeekey=:bvemployeekey ORDER BY schedulekey ASC";
					$result = $db->prepare($sqlselectc);

					if (isset($_POST['selectemployeesubmit'])) {
						$result->bindValue('bvemployeekey', $_POST['employeekey']);
					} else {
						$result->bindValue('bvemployeekey', $_SESSION['employeekey']);
					}

					$result->execute();

					while ($row = $result-> fetch()) {
						for ($i = 0; $i < 7; $i++) {
							$date = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($row['schedulestart'])));

							echo '<tr><td> ' . $date .
							'</td><td> ' . date('l', strtotime($date)) . '</td>
							<td> ';

							// Prevent null values from appearing
							if (is_null($row[strtolower(date('l', strtotime($date))) . 'start'])) {
								echo '';
							} else {
								echo date("g:i a", strtotime($row[strtolower(date('l', strtotime($date))) . 'start']));
							}
							echo '</td><td>';
							if (is_null($row[strtolower(date('l', strtotime($date))) . 'end'])) {
								echo '';
							} else {
								echo date("g:i a", strtotime($row[strtolower(date('l', strtotime($date))) . 'end']));
							}
							echo '</td></tr>';

						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
$(document).ready( function () {
    $('#scheduleTable').DataTable({
			"order": [],
			"ordering": false,
			"lengthMenu": [[7], [7]],
			"lengthChange": false,
			"searching": false
		});
} );
</script>
<?php
}
else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
