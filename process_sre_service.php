<?
        error_reporting(E_PARSE);

<<<<<<< HEAD
        $dbhostname = "localhost";
        $dblogin= "root";
        $dbpassword= "root";
        $dbname="sre";


        if(!($dbLink = mysql_connect($dbhostname,$dblogin,$dbpassword)))
                {
                print("Failed to connect to db server.");
                exit();
                }
        if(!mysql_select_db($dbname,$dbLink))
                {
                print("Failed to select database.");
                exit();
                }
=======
	include("setup.php");
>>>>>>> 9b22ae84e3625350d25458ef6a22933390bdd519

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
