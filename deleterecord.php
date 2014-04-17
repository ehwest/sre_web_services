<?
	require_once("setup.php"); 
	$ix = $_REQUEST['projectkey'];
	print $ix;
	$q = "DELETE from sreinfodb where ix='" . $ix . "' LIMIT 1";
	mysql_query($q);
	header("Location:  showprojects.htm");
	exit();
?>
