<?php
	session_start();
	require_once 'header.php';
?>

<?php
	// Message list variable
	$messagelist = '';

	// Select employee information
	$sqlselect = 'SELECT *
								FROM employee
								WHERE employeekey=:bvemployeekey';
	$result = $db->prepare($sqlselect);
	$result->bindValue('bvemployeekey', $_SESSION['employeekey']);
	$result->execute();
	$row = $result->fetch();

	if ($row['employeedefaultpassword'] == 0) {
		$messagelist .= '<div class="alert alert-warning" role="alert"><strong>Warning:</strong> you are still using your default password.
		Please update your password on the <a href="updatemyinformation.php">My Information</a> page.</div>';
	}
?>

<div class="messagelist">
	<?php echo $messagelist; ?>
</div>

<?php
	require_once 'footer.php';
?>
