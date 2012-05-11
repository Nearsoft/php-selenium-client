<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Form receptor</title>
	</head>
	<body>
		<?php
		if (isset($_POST["btnSubmit"]))
		{
			if (isset($_POST['txtSleepSeconds']) && is_numeric($_POST['txtSleepSeconds']))
			{
				sleep($_POST['txtSleepSeconds']);
				echo "<p>Waited ". $_POST ['txtSleepSeconds']." seconds</p><br/>";
			}
		?>
		
		<h2 id="h2FormReceptor">Form receptor</h2>
		<p>Post received</p>
		
		<div id="divPostVars">
			<?php print_r($_POST);?>
		</div>
		
		<?php } ?>
	</body>
</html>