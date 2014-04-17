<?
	
	require_once("setup.php");

//Establish a unique cooke for the user's IP address
        $cookiename ="sre_analysis" ;
        $ipaddress  = $_SERVER["REMOTE_ADDR"];
        $userid     = $_COOKIE[$cookiename];
	$licensekey = $userid;


//Log the useage
        $q = "INSERT into userlog set ";
        $q .= "pageurl='"  . "viewinputdata.htm" . "', ";
        $q .= "userid='"  . $userid . "', ";
        $q .= "tod='"  . time() . "', ";
        $q .= "ipaddress='"  . $_SERVER["REMOTE_ADDR"] . "'; ";
        //print $q;
        mysql_query($q,$dbLink);


	$ix = $_GET['projectkey'];

        $q = "SELECT * from sreinfodb where ix='" . $ix . "';" ;
	//print $q;

        $dbr = mysql_query($q,$dbLink);
        $row=mysql_fetch_object($dbr);
	//print_r($row);
	$json = $row->inputparams;
	//print base64_decode($json);

	require_once 'json.php';
	$phpdecode = json_decode(unserialize(base64_decode($json)));
	//print "<PRE>";
	//print_r($phpdecode);
	//print "</PRE>";

        $timeseries = $phpdecode->inputimeseries;
        //print "<PRE>";
        //print_r($timeseries);
        //print "</PRE>";

        header("Cache-Control: ");
        header("Pragma: ");
        header("Content-Type: application/vnd.ms-excel");
        $datestring = date("n_j_Y");
        header("Content-Disposition: attachment; filename=input_check" . $datestring . ".csv");

        print "Testing Unit of Effort (e.g. day number -or- hour number),Number of New Defects Discovered.\n";

        foreach($timeseries as $key=>$value){
                print $value->effort;
                print ",";
                print $value->defectcount;
                print "\n";
        }

?>
