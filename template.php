<?php
	session_start();
	require_once 'header.php';

if ($_SESSION['signedin'] == 1) {
?>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="#">Menu</a></li>
	<li class="breadcrumb-item"><a href="#">Menu Items</a></li>
	<li class="breadcrumb-item active">Select</li>
</ol>
<div class="card">
	<div class="card-header">Select Menu Items</div>
	<div class="card-body">
		<form>
			<input type="text"/>
		</form>
	</div>
</div>
<script>
<?php
}
else {
	echo $_SESSION['permission'] . '<br />';
	echo '<p>You are not signed in. Click <a href="signin.php">here</a> to sign in.</p>';
}
	require_once 'footer.php';
?>
