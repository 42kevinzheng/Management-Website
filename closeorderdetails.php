<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		if (isset($_POST['selectordersubmit']) || isset($_POST['closeorderdetailsubmit'])) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Orders</a></li>
	<li class="breadcrumb-item"><a href="#">Order Details</a></li>
	<li class="breadcrumb-item active">Close</li>
</ol>
<div class="card">
	<div class="card-header">Close Order Details</div>
	<div class="card-body">
		<?php
			// Close order detail submission:
			if (isset($_POST['closeorderdetailsubmit'])) {
				try {
					// statement
					$sqlupdate = 'UPDATE orderdetail
												SET orderdetailcomplete=1
												WHERE orderdetailkey=:bvkey';

					// Prepare and execute
					$result = $db->prepare($sqlupdate);
					$result->bindValue('bvkey', $_POST['orderdetailkey']);
					$result->execute();

					// Success
					echo '<div class="alert alert-success" role="alert">Item closed successfully</div>';
				} catch (Exception $e) {
					// An error occured
					echo '<div class="alert alert-danger" role="alert"><strong>Update failed: </strong>' . $e->getMessage() . '</div>';
				}
			}

			// Select statement for the table
			$sqlselecto = 'SELECT *
										 FROM orderdetail
										 INNER JOIN menuitem ON orderdetail.menuitemkey = menuitem.menuitemkey
										 WHERE orderkey = :bvorderkey AND orderdetailcomplete = 0';
			$result = $db->prepare($sqlselecto);
			$result->bindValue('bvorderkey', $_POST['orderkey']);
			$result->execute();

			// If there are no open items, close the order
			$count = $result->rowCount();
			if ($count < 1) {
				try {
					// statement
					$sqlupdate = 'UPDATE orders
												SET ordercomplete=1
												WHERE orderkey=:bvkey';

					// Prepare and execute
					$resultu = $db->prepare($sqlupdate);
					$resultu->bindValue('bvkey', $_POST['orderkey']);
					$resultu->execute();

					// Success
					echo '<div class="alert alert-success" role="alert">Order closed successfully</div>';
				} catch (Exception $e) {
					// An error occured
					echo '<div class="alert alert-danger" role="alert"><strong>Update failed: </strong>' . $e->getMessage() . '</div>';
				}
			}

		?>
		<div class="table-responsive">
			<table class="table table-bordered" id="selectorderdetailsTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Item</th>
						<th>Notes</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ( $row = $result-> fetch() ) {
						echo '<tr><td>' . $row['menuitemname'] . '</td><td> ' . $row['orderdetailnote'] . '</td>
						<td>
							<form name="closeorderdetailsform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
								<input type="hidden" name="orderkey" value="' . $row['orderkey'] . '"/>
								<input type="hidden" name="orderdetailkey" value="' . $row['orderdetailkey'] . '"/>
								<input type="submit" name="closeorderdetailsubmit" value="Close"/>
							</form>
						</td>';
					}
					echo '</tr>';
					?>
				</tbody>
			</table>
		</div>
		<p class="mt-4 mb-0"><a href="closeorders.php">Back</a></p>
	</div>
</div>
<script>
$(document).ready( function () {
    $('#selectorderdetailsTable').DataTable();
} );
</script>
<?php
} else {
	echo '<p>This page can not be viewed.</p>';
}
} else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
