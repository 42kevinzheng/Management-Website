<?php
	session_start();
	require_once 'header.php';

	if ($_SESSION['signedin'] == 1) {
		$formfield['ffticketkey'] = $_POST['ticketkey'];

		if (isset($_POST['DeleteItem'])) {
			$sqldelete = 'DELETE FROM ticketdetail
						WHERE ticketdetailkey = :bvticketdetailkey';
			$stmtdelete = $db->prepare($sqldelete);
			$stmtdelete->bindvalue(':bvticketdetailkey', $_POST['ticketdetailkey']);
			$stmtdelete->execute();
		}

		if (isset($_POST['UpdateItem'])) {
			$sqlupdateoi = 'Update ticketdetail
						set ticketdetailprice = :bvitemprice, ticketdetailnote = :bvitemnotes
						WHERE ticketdetailkey = :bvticketitemid';
			$stmtupdateoi = $db->prepare($sqlupdateoi);
			$stmtupdateoi->bindvalue(':bvticketitemid', $_POST['ticketitemid']);
			$stmtupdateoi->bindvalue(':bvitemprice', $_POST['newprice']);
			$stmtupdateoi->bindvalue(':bvitemnotes', $_POST['newnote']);
			$stmtupdateoi->execute();
		}
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Tickets</a></li>
	<li class="breadcrumb-item"><a href="#">Ticket Details</a></li>
	<li class="breadcrumb-item active">Update</li>
</ol>

<!-- ticket Details -->
<div class="card">
	<div class="card-header">Ticket Details</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="ticketdetailsTable" width="100%" cellspacing="0">
				<tr>
					<th>Item</th>
					<th>Price</th>
					<th>Notes</th>
					<th></th>
					<th></th>
				</tr>
				<?php
					$sqlselecto = 'SELECT *
												 FROM ticketdetail
												 INNER JOIN menuitem ON menuitem.menuitemkey=ticketdetail.menuitemkey
												 WHERE ticketkey=:bvticketkey';
					$resulto = $db->prepare($sqlselecto);
					$resulto->bindValue(':bvticketkey', $formfield['ffticketkey']);
					$resulto->execute();

					$tickettotal = 0;

					while ($rowo = $resulto->fetch()){
					$tickettotal = $tickettotal + $rowo['ticketdetailprice'];

					echo '<tr><td style="vertical-align: middle;">' . $rowo['menuitemname'] . '</td><td style="vertical-align: middle;">' . $rowo['ticketdetailprice'] . '</td>';
					echo '<td style="vertical-align: middle;">' . $rowo['ticketdetailnote'] . '</td><td>';
					echo '<form action = "' . $_SERVER['PHP_SELF'] . '" method = "post">';
					echo '<input type = "hidden" name = "ticketkey" value = "'. $formfield['ffticketkey'] .'">';
					echo '<input type = "hidden" name = "ticketitemid" value = "'. $rowo['ticketdetailkey'] .'">';
					echo '<input type="submit" name="NoteEntry" value="Update">';
					echo '</form></td><td>';
					echo '<form action = "' . $_SERVER['PHP_SELF'] . '" method = "post">';
					echo '<input type = "hidden" name = "ticketkey" value = "'. $formfield['ffticketkey'] .'">';
					echo '<input type = "hidden" name = "ticketdetailkey" value = "'. $rowo['ticketdetailkey'] .'">';
					echo '<input type="submit" name="DeleteItem" value="Delete">';
					echo '</form></td></tr>';
					}
				?>
			<tr>
				<th>Total:</th>
				<th><?php echo $tickettotal; ?></th>
			</tr>
			</table>

			<table class="table table-bordered">
				<?php
					if (isset($_POST['NoteEntry']))
					{
					$sqlselectoi = "SELECT ticketdetail.*, menuitem.menuitemname
						from ticketdetail, menuitem
						WHERE menuitem.menuitemkey = ticketdetail.menuitemkey
						AND ticketdetail.ticketkey = :bvticketkey
						AND ticketdetail.ticketdetailKey = :bvticketitemid";
					$resultoi = $db->prepare($sqlselectoi);
					$resultoi->bindValue(':bvticketkey', $formfield['ffticketkey']);
					$resultoi->bindvalue(':bvticketitemid', $_POST['ticketitemid']);
					$resultoi->execute();
					$rowoi = $resultoi->fetch();

					echo '
					<form action = "' . $_SERVER['PHP_SELF'] . '" method = "post">
						<table class="table table-bordered">
							<tr>
								<th>Price</th>
								<td><input type = "text" name ="newprice" value="'. $rowoi['ticketdetailprice'] . '"></td>
							</tr>
							<tr>
								<th>Notes</th>
								<td><input type="text" name="newnote" value ="'. $rowoi['ticketdetailnote'] . '"></td>
							</tr>
							<tr>
								<td>
									<input type = "hidden" name = "ticketkey" value = "'. $formfield['ffticketkey'] .'">
									<input type = "hidden" name = "ticketitemid" value = "'. $rowoi['ticketdetailkey'] .'" >
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
		<form name="ticketsubmitform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input name="ticketkey" type="hidden" value="<?php echo $formfield['ffticketkey']; ?>"/>
			<button name="submitticket" type="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
</div>

<!-- Script for the select ticket details data table -->
<script>
$(document).ready( function () {
    $('#selectticketdetailsTable').DataTable();
} );
</script>
<?php
	} else {
		echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
	}
	require_once 'footer.php';
?>
