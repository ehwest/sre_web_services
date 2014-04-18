<?
        error_reporting(E_PARSE);

	include("setup.php");

	$q = "SELECT * from sreinfodb where istatus='data loaded'  AND results='analysis enqueued'";
	$dbr = mysql_query($q);
	$row = mysql_fetch_object($dbr);
	$ix = $row->ix;
	$n = mysql_num_rows($dbr);
	
	if($n>0 && $ix > 0){
	   $q1 = "UPDATE  sreinfodb set istatus='processing', results='analyzing' WHERE ix='" . $ix . "';";
	   print $q1;
	   mysql_query($q1);
   	   require("phase2b.php");
   	   require("phase3.php");
	}
	sleep( 10);
	exit();
?>
