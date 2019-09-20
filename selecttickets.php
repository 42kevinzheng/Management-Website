<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (preg_match('/......1................................./', $_SESSION['permission'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Tickets</a></li>
	<li class="breadcrumb-item active">Select</li>
</ol>
<div class="card mb-3">
	<div class="card-header">Select Tickets</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="selectordersTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Type</th>
						<th>Customer</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselectt = "SELECT * FROM tickets
												 INNER JOIN customer ON tickets.customerkey = customer.customerkey
												 ORDER BY ticketkey ASC";
					$result = $db->prepare($sqlselectt);
					$result->execute();
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['ticketdate'] . '</td><td> ' . $row['tickettime'] .
						'</td><td> ' . $row['tickettype'] . '</td><td> ' . $row['customeremail'] . '</td>
						<td>
							<form name="selectticketform" method="post" action="selectticketdetails.php">
								<input type="hidden" name="ticketkey" value="' . $row['ticketkey'] . '"/>
								<input type="submit" name="selectticketsubmit" value="Select"/>
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


<!-- Ticket History Chart -->
<?php
/*
The order history table gathers the number of orders from the last 7 days from the most recent order date
*/
$sqlselecth = "SELECT MAX(ticketdate) AS ticketdate FROM tickets";
$resulth = $db->prepare($sqlselecth);
$resulth->execute();
$rowh = $resulth->fetch();
$currentdate = $rowh['ticketdate'];
?>

<?php
	// Get the number of orders for each date
	$sqlselectn = 'SELECT * FROM tickets WHERE ticketdate=:bvdate';
	$sqlselectod = 'SELECT * FROM ticketdetail WHERE ticketkey=:bvticketkey';
	for ($d = 0; $d < 7; $d++) {
		try {
			$date = date('Y-m-d', strtotime($currentdate . ' -' . $d . ' day'));
			$resultn = $db->prepare($sqlselectn);
			$resultn->bindValue(':bvdate', $date, PDO::PARAM_STR);
			$resultn->execute();

			while ($rown = $resultn->fetch()) {
				try {
					$resultod = $db->prepare($sqlselectod);
					$resultod->bindValue(':bvticketkey', $rown['ticketkey']);
					$resultod->execute();

					while ($rowod = $resultod->fetch()) {
						$ticketTotal[$d] = $ticketTotal[$d] + $rowod['ticketdetailprice'];
					}

				} catch (Exception $e) {
					echo "ERROR: " . $e-getMessage();
					exit();
				}
			}

			$numberOfTickets[$d] = $resultn->rowCount();
		} catch (Exception $e) {
			echo "ERROR: " . $e-getMessage();
			exit();
		}
	}
?>
<div class="card mb-3">
	<div class="card-header">Ticket History</div>
	<div class="card-body">
		<canvas id="ticketHistoryChart" width="400" height="100"></canvas>
		<script>
		var ctx = document.getElementById('ticketHistoryChart');
		var ticketHistoryChart = new Chart(ctx, {
		    type: 'line',
		    data: {
		        labels: [
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -6 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -5 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -4 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -3 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -2 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -1 day')); ?>',
							'<?php echo $currentdate; ?>'
						],
		        datasets: [{
		            label: 'Number of Tickets',
		            data: [
									<?php echo $numberOfTickets[6]; ?>,
									<?php echo $numberOfTickets[5]; ?>,
									<?php echo $numberOfTickets[4]; ?>,
									<?php echo $numberOfTickets[3]; ?>,
									<?php echo $numberOfTickets[2]; ?>,
									<?php echo $numberOfTickets[1]; ?>,
									<?php echo $numberOfTickets[0]; ?>
								],
		            backgroundColor: [
									'#CCE3F7'
		            ],
		            borderColor: [
		              '#0275D8'
		            ],
								pointBackgroundColor: [
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8'
								],
								pointBorderColor: [
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8'
								],
		            borderWidth: 2
		        }]
		    },
		    options: {
		        scales: {
		            yAxes: [{
		                ticks: {
		                    beginAtZero: true
		                }
		            }]
		        }
		    }
		});
		</script>
	</div>
</div>

<div class="card mb-3">
	<div class="card-header">Revenue Report</div>
	<div class="card-body">
		<canvas id="revenueReportChart" width="400" height="100"></canvas>
		<script>
		var ctx = document.getElementById('revenueReportChart');
		var revenueReportChart = new Chart(ctx, {
		    type: 'line',
		    data: {
		        labels: [
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -6 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -5 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -4 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -3 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -2 day')); ?>',
							'<?php echo date('Y-m-d', strtotime($currentdate . ' -1 day')); ?>',
							'<?php echo $currentdate; ?>'
						],
		        datasets: [{
		            label: 'Ticket Revenue',
		            data: [
									<?php echo $ticketTotal[6]; ?>,
									<?php echo $ticketTotal[5]; ?>,
									<?php echo $ticketTotal[4]; ?>,
									<?php echo $ticketTotal[3]; ?>,
									<?php echo $ticketTotal[2]; ?>,
									<?php echo $ticketTotal[1]; ?>,
									<?php echo $ticketTotal[0]; ?>
								],
		            backgroundColor: [
									'#CCE3F7'
		            ],
		            borderColor: [
		              '#0275D8'
		            ],
								pointBackgroundColor: [
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8'
								],
								pointBorderColor: [
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8',
									'#0275D8'
								],
		            borderWidth: 2
		        }]
		    },
		    options: {
		        scales: {
		            yAxes: [{
		                ticks: {
		                    beginAtZero: true
		                }
		            }]
		        }
		    }
		});
		</script>
	</div>
</div>


<script>
$(document).ready( function () {
    $('#selectordersTable').DataTable();
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
