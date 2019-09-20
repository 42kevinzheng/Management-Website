<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		// Define order key
		$formfield['fforderkey'] = $_POST['orderkey'];

		// Set locale for money format
		setlocale(LC_MONETARY, 'en_US.UTF-8');

		if (isset($_POST['DeleteItem'])) {
			$sqldelete = 'DELETE FROM orderdetail
						WHERE orderdetailkey = :bvorderdetailkey';
			$stmtdelete = $db->prepare($sqldelete);
			$stmtdelete->bindvalue(':bvorderdetailkey', $_POST['orderdetailkey']);
			$stmtdelete->execute();
		}

		if (isset($_POST['UpdateItem'])) {
			$sqlupdateoi = 'Update orderdetail
						set orderdetailprice = :bvitemprice, orderdetailnote = :bvitemnotes
						WHERE orderdetailkey = :bvorderitemid';
			$stmtupdateoi = $db->prepare($sqlupdateoi);
			$stmtupdateoi->bindvalue(':bvorderitemid', $_POST['orderitemid']);
			$stmtupdateoi->bindvalue(':bvitemprice', $_POST['newprice']);
			$stmtupdateoi->bindvalue(':bvitemnotes', $_POST['newnote']);
			$stmtupdateoi->execute();
		}
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item">Orders</li>
	<li class="breadcrumb-item">Order Details</li>
	<li class="breadcrumb-item active">Update</li>
</ol>

<!-- Order Details -->
<div class="card">
	<div class="card-header">Order Details</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="orderdetailsTable" width="100%" cellspacing="0">
				<tr>
					<th>Item</th>
					<th>Price</th>
					<th>Notes</th>
					<th></th>
					<th></th>
				</tr>
				<?php
					$sqlselecto = 'SELECT *
												 FROM orderdetail
												 INNER JOIN menuitem ON menuitem.menuitemkey=orderdetail.menuitemkey
												 WHERE orderkey=:bvorderkey';
					$resulto = $db->prepare($sqlselecto);
					$resulto->bindValue(':bvorderkey', $formfield['fforderkey']);
					$resulto->execute();

					$ordertotal = 0;

					while ($rowo = $resulto->fetch()){
					$ordertotal = $ordertotal + $rowo['orderdetailprice'];

					echo '<tr><td style="vertical-align: middle;">' . $rowo['menuitemname'] . '</td><td style="vertical-align: middle;">' . money_format('%.2n', $rowo['orderdetailprice']) . '</td>';
					echo '<td style="vertical-align: middle;">' . $rowo['orderdetailnote'] . '</td><td>';
					echo '<form action = "' . $_SERVER['PHP_SELF'] . '" method = "post">';
					echo '<input type = "hidden" name = "orderkey" value = "'. $formfield['fforderkey'] .'">';
					echo '<input type = "hidden" name = "orderitemid" value = "'. $rowo['orderdetailkey'] .'">';
					echo '<input type="submit" name="NoteEntry" value="Update">';
					echo '</form></td><td>';
					echo '<form action = "' . $_SERVER['PHP_SELF'] . '" method = "post">';
					echo '<input type = "hidden" name = "orderkey" value = "'. $formfield['fforderkey'] .'">';
					echo '<input type = "hidden" name = "orderdetailkey" value = "'. $rowo['orderdetailkey'] .'">';
					echo '<input type="submit" name="DeleteItem" value="Delete">';
					echo '</form></td></tr>';
					}
				?>
			<tr>
				<th>Total:</th>
				<th><?php echo money_format('%.2n', $ordertotal); ?></th>
			</tr>
			</table>

			<table class="table table-bordered">
				<?php
					if (isset($_POST['NoteEntry']))
					{
					$sqlselectoi = "SELECT orderdetail.*, menuitem.menuitemname
						from orderdetail, menuitem
						WHERE menuitem.menuitemkey = orderdetail.menuitemkey
						AND orderdetail.orderkey = :bvorderkey
						AND orderdetail.orderdetailKey = :bvorderitemid";
					$resultoi = $db->prepare($sqlselectoi);
					$resultoi->bindValue(':bvorderkey', $formfield['fforderkey']);
					$resultoi->bindvalue(':bvorderitemid', $_POST['orderitemid']);
					$resultoi->execute();
					$rowoi = $resultoi->fetch();

					echo '
					<form action = "' . $_SERVER['PHP_SELF'] . '" method = "post">
						<table class="table table-bordered">
							<tr>
								<th>Price</th>
								<td>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">$</div>
									</div>
									<input name="newprice" type="text" value="'. $rowoi['orderdetailprice'] . '" required>
								</div>
								</td>
							</tr>
							<tr>
								<th>Notes</th>
								<td><input type="text" name="newnote" value ="'. $rowoi['orderdetailnote'] . '"></td>
							</tr>
							<tr>
								<td>
									<input type = "hidden" name = "orderkey" value = "'. $formfield['fforderkey'] .'">
									<input type = "hidden" name = "orderitemid" value = "'. $rowoi['orderdetailkey'] .'" >
									<input type="submit" name="UpdateItem" value="Update"/>
								</td>
							</tr>
						</table>
					</form>
					';
					}
					?>
			</table>
		</div>

		<!-- <form name="ordersubmitform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input name="orderkey" type="hidden" value="<?php echo $formfield['fforderkey']; ?>"/>
			<button name="submitorder" type="submit" class="btn btn-primary">Submit</button>
		</form> -->
	</div>
</div>

<!-- Script for the select order details data table -->
<script>
$(document).ready( function () {
    $('#selectorderdetailsTable').DataTable();
} );
</script>
<?php
	} else {
		echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
	}
	require_once 'footer.php';
?>
