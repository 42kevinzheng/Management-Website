<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Orders</a></li>
	<li class="breadcrumb-item active">Select</li>
</ol>

<!-- Data Table -->
<div class="card mb-3">
	<div class="card-header">Select Orders</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="selectordersTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Customer</th>
						<th>Employee</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sqlselecto = "SELECT * FROM orders
												 INNER JOIN customer ON orders.customerkey = customer.customerkey
												 INNER JOIN employee ON orders.employeekey = employee.employeekey
												 ORDER BY orderkey ASC";
					$result = $db->prepare($sqlselecto);
					$result->execute();
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['orderdate'] . '</td><td> ' . $row['ordertime'] .
						'</td><td> ' . $row['customeremail'] . '</td><td> ' . $row['employeeusername'] . '</td>
						<td>
							<form name="selectorderform" method="post" action="selectorderdetails.php">
								<input type="hidden" name="orderkey" value="' . $row['orderkey'] . '"/>
								<input type="submit" name="selectordersubmit" value="Select"/>
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

<!-- Order History Chart -->
<?php
/*
The order history table gathers the number of orders from the last 7 days from the most recent order date
*/
$sqlselecth = "SELECT MAX(orderdate) AS orderdate FROM orders";
$resulth = $db->prepare($sqlselecth);
$resulth->execute();
$rowh = $resulth->fetch();
$currentdate = $rowh['orderdate'];
?>

<?php
	// Get the number of orders for each date
	$sqlselectn = 'SELECT * FROM orders WHERE orderdate=:bvdate';
	$sqlselectod = 'SELECT * FROM orderdetail WHERE orderkey=:bvorderkey';
	for ($d = 0; $d < 7; $d++) {
		try {
			$date = date('Y-m-d', strtotime($currentdate . ' -' . $d . ' day'));
			$resultn = $db->prepare($sqlselectn);
			$resultn->bindValue(':bvdate', $date, PDO::PARAM_STR);
			$resultn->execute();

			while ($rown = $resultn->fetch()) {
				try {
					$resultod = $db->prepare($sqlselectod);
					$resultod->bindValue(':bvorderkey', $rown['orderkey']);
					$resultod->execute();

					while ($rowod = $resultod->fetch()) {
						$orderTotal[$d] = $orderTotal[$d] + $rowod['orderdetailprice'];
					}

				} catch (Exception $e) {
					echo "ERROR: " . $e-getMessage();
					exit();
				}
			}

			$numberOfOrders[$d] = $resultn->rowCount();
		} catch (Exception $e) {
			echo "ERROR: " . $e-getMessage();
			exit();
		}
	}
?>
<div class="card mb-3">
	<div class="card-header">Order History</div>
	<div class="card-body">
		<canvas id="orderHistoryChart" width="400" height="100"></canvas>
		<script>
		var ctx = document.getElementById('orderHistoryChart');
		var orderHistoryChart = new Chart(ctx, {
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
		            label: 'Number of Orders',
		            data: [
									<?php echo $numberOfOrders[6]; ?>,
									<?php echo $numberOfOrders[5]; ?>,
									<?php echo $numberOfOrders[4]; ?>,
									<?php echo $numberOfOrders[3]; ?>,
									<?php echo $numberOfOrders[2]; ?>,
									<?php echo $numberOfOrders[1]; ?>,
									<?php echo $numberOfOrders[0]; ?>
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
		            label: 'Revenue Total',
		            data: [
									<?php echo $orderTotal[6]; ?>,
									<?php echo $orderTotal[5]; ?>,
									<?php echo $orderTotal[4]; ?>,
									<?php echo $orderTotal[3]; ?>,
									<?php echo $orderTotal[2]; ?>,
									<?php echo $orderTotal[1]; ?>,
									<?php echo $orderTotal[0]; ?>
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
}
else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
