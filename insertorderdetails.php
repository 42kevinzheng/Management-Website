<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {

		// Data cleansing?
		$formfield['fforderkey'] = $_POST['orderkey'];
		$formfield['ffmenuitemkey'] = $_POST['menuitemkey'];
		$formfield['fforderitemprice'] = $_POST['orderitemprice'];

		// Set locale for money format
		setlocale(LC_MONETARY, 'en_US.UTF-8');

		if (isset($_POST['ODEnter']))
		{
			$sqlinsert = 'INSERT INTO orderdetail (orderkey, menuitemkey,
					orderdetailprice, orderdetailcomplete) VALUES (:bvorderkey, :bvprodid, :bvorderitemprice, 0)';

				//Prepares the SQL Statement for execution
				$stmtinsert = $db->prepare($sqlinsert);
				//Binds our associative array variables to the bound
				//variables in the sql statement
				$stmtinsert->bindvalue(':bvorderkey', $formfield['fforderkey']);
				$stmtinsert->bindvalue(':bvprodid', $formfield['ffmenuitemkey']);
				//1
				$stmtinsert->bindvalue(':bvorderitemprice', $formfield['fforderitemprice']);

				//Runs the insert statement and query
				$stmtinsert->execute();
		}

		if (isset($_POST['DeleteItem']))
		{
			$sqldelete = 'DELETE FROM orderdetail
						WHERE orderdetailkey = :bvorderdetailkey';
			$stmtdelete = $db->prepare($sqldelete);
			$stmtdelete->bindvalue(':bvorderdetailkey', $_POST['orderdetailkey']);
			$stmtdelete->execute();
		}

		if (isset($_POST['UpdateItem']))
		{
			$sqlupdateoi = 'UPDATE orderdetail
						SET orderdetailprice = :bvitemprice, orderdetailnote = :bvitemnotes
						WHERE orderdetailkey = :bvorderitemid';
			$stmtupdateoi = $db->prepare($sqlupdateoi);
			$stmtupdateoi->bindvalue(':bvorderitemid', $_POST['orderitemid']);
			$stmtupdateoi->bindvalue(':bvitemprice', $_POST['newprice']);
			$stmtupdateoi->bindvalue(':bvitemnotes', $_POST['newnote']);
			$stmtupdateoi->execute();
		}
?>

<?php if(isset($_POST['submitorder'])) { ?>
	<!-- Order Successful -->
	<div class="card">
		<div class="card-header">Order Successful</div>
		<div class="card-body">
			<?php
			// Begin removing items from inventory
			// Update statement
			$sqlupdate = 'UPDATE menuitem
										SET menuitemcount=:bvcount
										WHERE menuitemkey=:bvkey';

			// Select all order details for the order key
			$sqlselecto = 'SELECT *
										 FROM orderdetail
										 WHERE orderkey=:bvorderkey';
			$resulto = $db->prepare($sqlselecto);
			$resulto->bindValue(':bvorderkey', $formfield['fforderkey']);
			$resulto->execute();

			// Select all menu items with the menu item key from the order details
			$sqlselectm = 'SELECT *
										 FROM menuitem
										 WHERE menuitemkey=:bvkey';

			$orderdetailcount = 0;
			$itemcount = 0;
			$itemkey = 0;

			// While there are order detail entries...
			while ($rowo = $resulto->fetch()) {
				// Debug
				//echo 'update that item<br />';
				// Set the menu item key to that order detail's menu item key
				$itemkey = $rowo['menuitemkey'];
				// Prepare and execute the statement to select the menu item
				$resultm = $db->prepare($sqlselectm);
				$resultm->bindValue(':bvkey', $itemkey);
				$resultm->execute();
				// Get the item count for that menu item
				while ($rowm = $resultm->fetch()) {
					// Debug
					//echo 'updating menuitemkey ' . $itemkey . '<br />';
					// Get the item count
					$itemcount = $rowm['menuitemcount'];
				}
				// Create a new item count
				$orderdetailcount = $itemcount - 1;

				// Bind and prepare the update statement
				$resultu = $db->prepare($sqlupdate);
				$resultu->bindValue('bvcount', $orderdetailcount);
				$resultu->bindValue('bvkey', $itemkey);
				$resultu->execute();
			}
			?>
			<!-- Order is successful! :) -->
			<div class="alert alert-success" role="alert"><strong>Order successful!</strong> <a href="closeorders.php">Close order</a></div>
			<?php
			$sqlselecto = 'SELECT *
										 FROM orderdetail
										 INNER JOIN menuitem ON menuitem.menuitemkey=orderdetail.menuitemkey
										 WHERE orderkey=:bvorderkey';
			$resulto = $db->prepare($sqlselecto);
			$resulto->bindValue(':bvorderkey', $formfield['fforderkey']);
			$resulto->execute();

			$ordertotal = 0;
			echo '<table class="table table-bordered">';
			echo '<th>Name</th>';
			echo '<th>Price</th>';
			echo '<th>Notes</th>';
			while ($rowo = $resulto->fetch()) {
			$ordertotal = $ordertotal + $rowo['orderdetailprice'];

			echo '
			<tr>
				<td style="vertical-align: middle;">' . $rowo['menuitemname'] . '</td>
				<td style="vertical-align: middle;">' . money_format('%.2n', $rowo['orderdetailprice']) . '</td>
				<td style="vertical-align: middle;">' . $rowo['orderdetailnote'] . '</td>
			</tr>';
			}
			echo '
			<tr>
				<th>Total:</th>
				<th>' . money_format('%.2n', $ordertotal) . '</th>
			</tr>';
			echo '</table>';
			?>
			<a class="btn btn-primary" href="insertorders.php">New Order</a>
		</div>
	</div>
<?php } else { ?>
	<!-- Menu Items Selection -->
	<div class="card" style="margin-bottom: 1rem;">
		<div class="card-header">Select Items for Order Number <?php echo $formfield['fforderkey']; ?></div>
		<div class="card-body">
			<div class="table-responsive">
				<table id="selectmenuitemsTable" width="100%" cellspacing="0">
					<?php
					$rowcounter = 0; // row counter
						$sqlselectc = "SELECT * from menutype";
						$resultc = $db->prepare($sqlselectc);
						$resultc->execute();
						echo '<tr>';
						while ($rowc = $resultc->fetch()) {
							echo '<th valign="top" align="center">' . $rowc['menutypename'] . '<br />';
							echo '<table style="width: 100%;">';
							$sqlselectp = "SELECT * from menuitem WHERE menutypekey = :bvtypekey";
							$resultp = $db->prepare($sqlselectp);
							$resultp->bindvalue(':bvtypekey', $rowc['menutypekey']);
							$resultp->execute();

							$rowcounter = $rowcounter + 1;

							while ($rowp = $resultp->fetch()) {
								$orderdetailcount = 0;

								// Prevent inventory count from reaching 0
								$sqlselectod = 'SELECT *
																FROM orderdetail
																WHERE orderkey = :bvorderkey
																AND menuitemkey = :bvmenuitemkey';
								$resultod = $db->prepare($sqlselectod);
								$resultod->bindValue(':bvorderkey', $formfield['fforderkey']);
								$resultod->bindValue(':bvmenuitemkey', $rowp['menuitemkey']);
								$resultod->execute();
								while($rowod = $resultod->fetch()) {
									$orderdetailcount = $orderdetailcount + 1;
								}

								echo '<tr><td>';
								echo '<form action = "' . $_SERVER['PHP_SELF'] . '" method="post">';
								echo '<input type="hidden" name="orderkey" value="' . $formfield['fforderkey'] . '" />';
								echo '<input type="hidden" name="menuitemkey" value="' . $rowp['menuitemkey'] . '" />';
								echo '<input type="hidden" name="orderitemprice" value="' . $rowp['menuitemprice'] . '" />';
								echo '<input style="height: 52px; width: 100%;" type="submit" name="ODEnter" value="' .$rowp['menuitemname'] . '" ';
								if ($rowp['menuitemcount'] < 1 || ($rowp['menuitemcount'] - $orderdetailcount) < 1) { echo 'disabled'; }
								echo '/>';
								echo '</form>';
								echo '</td></tr>';
							}
							echo '</table></th>';
							if ($rowcounter == 3) {
								echo '</tr>';
								$rowcounter = 0;
							}
						}
						echo '</tr>';
					?>
				</table>
			</div>
		</div>
	</div>

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
			<form name="ordersubmitform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input name="orderkey" type="hidden" value="<?php echo $formfield['fforderkey']; ?>"/>
				<button name="submitorder" type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>
<?php } ?>

<script>
$(document).ready( function () {
		$('#orderdetails').DataTable();
} );
</script>
<?php
}
else {
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
