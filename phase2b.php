<?
// AT&T Public Source License Version 1.1 (6/2013)
// The Initial Developer of the Original Code is AT&T the Original Code is governed by the AT&T Public Source License. 
// Copyright AT&T 2013. All Rights Reserved.

// This file contains Original Code and/or Modifications of Original Code as defined in and that are subject to the
// AT&T Public Source License Version 1.1 (the 'License').

// You may not use this file except in compliance with the License. Please obtain a copy of the License at
// http://www.att.developer.com/ and read it before using this file.

// The Original Code and all software distributed under the License are distributed on an 'AS IS' basis,
// WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, AND AT&T HEREBY DISCLAIMS ALL SUCH WARRANTIES,
// INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE,
// QUIET ENJOYMENT OR NON-INFRINGEMENT.  Please see the AT&T Public Source License Version 1.1 for the
// specific language governing rights and limitations under the License.


//This program executes the openbugs model and saves all the output.
//The essential output of the model, however is in the files containing
//the deviants of the model having run.
//These include... CODAindex.txt and CODAchain1.txt and CODAchain2.txt

        error_reporting(E_PARSE);
        unset($cumsum);
        unset($record);
        unset($tally);
        unset($cumtallies);
        unset($histogram);
        unset($cdf);
        unset($i);
        unset($row);
        unset($tol95);

	require("uniformdeviates.php");
	require_once("setup.php");

	if(trim($ix)=="") $ix = $_REQUEST['ix'];

	//$ix="30908";
	print "ix=$ix\n";
	$nowtime = time();
	$randomnumber = rand(0,$nowtime);
	$tempfolder = "/opt/lampp/temp/";
	$filenameprefix =   $nowtime . "-" . $randomnumber ;

	$q0 = "update sreinfodb set ";
	$q0 .= " istatus='" . "processing" . "', ";
	$q0 .= " tempfilename='" . $filenameprefix . "', ";
	$q0 .= " modified='" . $nowtime . "', ";
	$q0 .= " t1='" . $nowtime . "' ";
	$q0 .= " where ix='" . $ix . "' limit 1;";
	print "\n$q0\n";
	$dbr0 = mysql_query($q0);
	$row0 = mysql_fetch_object($dbr0);

        require_once 'json.php';

	$q1 = "SELECT * from sreinfodb where ix='" . $ix . "';";
	$dbr1 = mysql_query($q1);
	$row1 = mysql_fetch_object($dbr1);
	$json = unserialize(base64_decode($row1->inputparams));
	$filenameprefix = $row1->tempfilename;
        $metadata = json_decode($json);


	print "<br>filenameprefix=$filenameprefix";


// Expected metadata contents
//stdClass Object
//(
//    [inputimeseries] => Array
//        (
//            [0] => stdClass Object
//                (
//                    [effort] => 1
//                    [defectcount] => 0
//                )
//
//            [1] => stdClass Object
//                (
//                    [effort] => 2
//                    [defectcount] => 3
//                )
//
//            [2] => stdClass Object
//                (
//                    [effort] => 3
//                    [defectcount] => 7
//                )
//
//        )
//
//    [maxeffort] => 31
//    [datapoints] => 31
//    [totaldefects] => 184
//    [teststartdate] => 1359676800
//    [status] => new project
//    [userid] => 1395411382_id_135.91.194.234
//    [tod] => 1395411423
//    [filename] => case_2_4_14_20_20130318.xlsx
//    [modified] => 1395411423
//    [projectname] => STB 2_4_14_20
//    [sev1] => yes
//    [sev2] => yes
//    [sev3] => yes
//    [sev4] => no
//    [sev5] => no
//
		//print_r($metadata);
		$timeseries = $metadata->inputimeseries;
		//print_r($timeseries);
		$dayonedate = $metadata->teststartdate;
		$severityone = $metadata->sev1;
		$severitytwo = $metadata->sev2;
		$severitythree = $metadata->sev3;
		$severityfour = $metadata->sev4;
		$severityfive = $metadata->sev5;
		$projectname =  $metadata->projectname;

		$debug=1;
		if($debug==1) print "<PRE>";
		if($debug==1) print "<br><br>dayonedate=$dayonedate<br>";
		if($debug==1) print "Date translates to " . date("n/j/Y", $dayonedate) . "<br>";;
		if($debug==1) print "<br>severityone =$severityone<br>";
		if($debug==1) print "severitytwo =$severitytwo<br>";
		if($debug==1) print "severitythree =$severitythree<br>";
		if($debug==1) print "severityfour=$severityfour<br>";
		if($debug==1) print "severityfive=$severityfive<br>";
		if($debug==1) print "<br>projectname=$projectname<br>";

		if($debug==1) print "<br>";
		if($debug==1) print_r($ssrows);

		if($debug==1) print "</PRE>";

		$debug = 0;
		$i = 0;
		unset($day);
		unset($count);
		foreach($timeseries as $onerow){
				$effort = trim($onerow->effort);
				$defectcount = trim($onerow->defectcount);
				if($effort> 0  && $defectcount !=""){
				  $record["effort"]  = trim($effort);
				  $record["defectcount"] = trim($defectcount);
				  $records[] = $record;
				  $day[$i] =  trim($effort);
				  $count[$i] =  trim($defectcount);
				  $i +=1;
				}
		}//foreach row

                if($day[0]>0) {  //prepend these arrays with zero
                         array_unshift($day,0);
                         array_unshift($count,0);
                }//prepending needed

		$debug = 0;
		if($debug==1){
		print "<br><br>Prepended vector of day numbers.<br>";
		print "<PRE>";
		print_r($day);
		print "</PRE>";

		print "<br><br> Prepended vector of defects found on numbered days.<br>";
		print "<PRE>";
		print_r($count);
		print "</PRE>";
		}

		$debug_filename = $tempfolder . $filenameprefix . "-SRE_debug.txt";
		$s = "Contents of count array\n";
		$f9 = fopen($debug_filename,"w");
		foreach($count as $key=>$value){
			$debuginfo .=  "$key | $value  \n";
		}
		$s .= "\n\nContents of day array\n";
		foreach($day as $key=>$value){
			$debuginfo .= "$key | $value  \n";
		}


		$debug = 0;
		if($debug==1){
		  print "<br><br>Vector of day numbers.<br>";
		  print "<PRE>";
		  print_r($day);
		  print "</PRE>";

		  print "<br><br>Vector of defects found on numbered days.<br>";
		  print "<PRE>";
		  print_r($count);
		  print "</PRE>";
		}


		$m = array_sum($count);
		$n = count($day)-1; //recall php arrays started at zero

		print "m=$m; n=$n\n";
		$u = new uniform;

		//The z vector is the time between defects founc
		$z[1] = 0;
		$debuginfo .= "\nCycling thru count vector.\n";
		for($i=0;  $i<=$n; $i++) {   //look at each day's data
			if($count[$i]  > 0 )  {
				$debuginfo .= "\ni=$i; day[" . $i . "]=" . $day[$i] . "; [count" . $i . "]=" . $count[$i] ;
				unset($randnos);
				for($j=1; $j<= $count[$i]; $j++ ){
					$randnos[] = $u->pullone($day[$i-1],$day[$i]);
				}
				$debuginfo .= "\n" . "Random number between " . $day[$i-1] . " and " .  $day[$i] . " \n";
				sort($randnos);
				foreach($randnos as $onerandno)  $z[] = $onerandno;
			}
		}


		$debuginfo .= "array count has " . count($count) . " values\n";
		$lastcount = count($count) + 1;
		$debuginfo .= "The $lastcount value is " .  $count[$lastcount] . "\n";
		
		$lastdaydefects = 1;
		if($count[count($count)-1] == 0){   //defects at the last day/hour of effort
			$lasthour = $z[count($z)-1];//last random number
			$debuginfo .= "lasthour=$lasthour\n";
			$lastinterval = $day[count($day)-1] - $lasthour;
			$debuginfo .= "lastinterval=$lastinterval\n";
			$z[] = $lastinterval + $lasthour;
			$m = $m + 1;
			$lastdaydefects = "0";
		}

		$debuginfo .= "z items:\n";
		foreach($z as $onez){
			$debuginfo .= $z . "\n";
		}
		

		$debuginfo .=  "<br>Vector of m=$m ordered times suggesting absolute time of defect found.<br>";
		foreach($z as $key=>$onez) $debuginfo .=  "z[" . $key . "]= " . $z[$key] . "\n";
		fputs($f9,$debuginfo);
		fclose($f9);

		$dat1_filename = $tempfolder . $filenameprefix . "-SRE_dat1.txt";
		$dat2_filename = $tempfolder . $filenameprefix . "-SRE_dat2.txt";
		$in1_filename =  $tempfolder . $filenameprefix .  "-SRE_in1.txt";
		$in2_filename =  $tempfolder . $filenameprefix .  "-SRE_in2.txt";
		$mod_filename =  $tempfolder . "SRE_mod.txt";
		$odc_filename =  $tempfolder . $filenameprefix .  "-SRE_odc.txt";
		$output_filename =  $tempfolder . $filenameprefix .  "-SRE_output.txt";
		$outputstem =  $filenameprefix;

		$f1 = fopen($dat1_filename,"w");
		fputs($f1,"list(n= " . $m . " )\n");
		fclose($f1);

		$mplus50 = $m + 50;
		$mplus25 = $m + 25;
		$mplus75 = $m + 75;
		$f3 = fopen($in1_filename,"w");
		fputs($f3,"list(N=c( " . $mplus50 . ", " . $mplus50 . " ))\n");
		fclose($f3);

		$f4 = fopen($in2_filename,"w");
		fputs($f4,"list(N=c( " . $mplus75 . ", " . $mplus75 . " ))\n");
		fclose($f4);

		$f2 = fopen($dat2_filename,"w");
                fputs($f2,"y[] tau[]\n");

		for($i=1; $i<=$m; $i++){
			$dz[$i] = $z[$i+1] - $z[$i];
			if($i < $m ) fputs($f2,"1 " . $dz[$i] . "\n");
			if($i == $m  && $lastdaydefects==1) fputs($f2,"1 " . $dz[$i] . "\n");
			if($i == $m  && $lastdaydefects==0) fputs($f2,"0 " . $dz[$i] . "\n");
		}
		fputs($f2,"0 0.001\nEND\n");
		fclose($f2);


		//print "<br>FILES generated:<br>";
		//print "filename=$dat1_filename<br>";
		$s = file_get_contents($dat1_filename);
		//print "<PRE>";
		//print $s;
		//print "</PRE><br>";
		
		//print "<br>FILES generated:<br>";
		//print "filename=$dat2_filename<br>";
		$s = file_get_contents($dat2_filename);
		//print "<PRE>";
		//print $s;
		//print "</PRE><br>";
		
		//print "<br>FILES generated:<br>";
		//print "filename=$in1_filename<br>";
		$s = file_get_contents($in1_filename);
		//print "<PRE>";
		//print $s;
		//print "</PRE><br>";
		
		//print "<br>FILES generated:<br>";
		//print "filename=$in2_filename<br>";
		$s = file_get_contents($in2_filename);
		//print "<PRE>";
		//print $s;
		//print "</PRE><br>";
		

		//print "<br>FILES generated:<br>";
		//print "filename=$mod_filename<br>";
		$s = file_get_contents($mod_filename);
		//print "<PRE>";
		//print $s;
		//print "</PRE><br>";
		
		print "\noutputstem is $outputstem\n";

		    //$sre_script_odc   = "/home/ew8463/sre/openbugs/usr/bin/OpenBUGS\n";
		$sre_script_odc   = "/opt/lampp/bin/OpenBUGS\n";
		    //$sre_script_odc  .= "modelSetWD('/home/ew8463/sretempdata/')\n";
		$sre_script_odc  .= "modelSetWD('/opt/lampp/temp/')\n";

		$sre_script_odc  .= "modelCheck('SRE_mod.txt')\n";
		$sre_script_odc  .= "modelData('" .  $filenameprefix . "-SRE_dat1.txt" . "')\n";
		$sre_script_odc  .= "modelData('" .  $filenameprefix . "-SRE_dat2.txt" . "')\n";
		$sre_script_odc  .= "modelCompile(2)\n";
		$sre_script_odc  .= "modelInits('" . $filenameprefix . "-SRE_in1.txt" . "')\n";
		$sre_script_odc  .= "modelInits('" . $filenameprefix . "-SRE_in2.txt" . "')\n";
		$sre_script_odc  .= "modelGenInits()\n";
 		$sre_script_odc  .= "modelUpdate(20000)\n";
		$sre_script_odc  .= "samplesSet('Deploy[1]')\n";
		$sre_script_odc  .= "samplesSet('Deploy[2]')\n";
		$sre_script_odc  .= "samplesSet('Deploy[3]')\n";
		//$sre_script_odc  .= "samplesSet('LIKE[1]')\n";
		//$sre_script_odc  .= "samplesSet('LIKE[2]')\n";
		//$sre_script_odc  .= "samplesSet('LIKE[3]')\n";
		$sre_script_odc  .= "samplesSet('N[1]')\n";
		$sre_script_odc  .= "samplesSet('N[2]')\n";
		$sre_script_odc  .= "samplesSet('M[1]')\n";
		$sre_script_odc  .= "samplesSet('M[2]')\n";
		$sre_script_odc  .= "samplesSet('b[1]')\n";
		$sre_script_odc  .= "samplesSet('b[2]')\n";
		$sre_script_odc  .= "samplesSet('b[3]')\n";
		$sre_script_odc  .= "samplesSet('d[1]')\n";
		$sre_script_odc  .= "samplesSet('d[2]')\n";
		//$sre_script_odc  .= "samplesSet('d[3]')\n";
		$sre_script_odc  .= "modelUpdate(20000)\n";
		$sre_script_odc  .= "samplesStats('*')\n";
		$sre_script_odc  .= "samplesCoda('*','" . $outputstem . "-')\n";
		$sre_script_odc  .= "modelQuit()\n";


		print "sre_script_odc=$sre_script_odc";

		$f6 = fopen($odc_filename,"w");
		fputs($f6, $sre_script_odc);
		fclose($f6);

		//$cmd = "/home/ew8463/sre/openbugs/usr/bin/OpenBUGSCli < $odc_filename \n";
		$cmd = "/opt/lampp/bin/OpenBUGSCli < $odc_filename \n";
		print "Now executing this command:<br>";
		print "<PRE>";
		print $cmd;
		print "</PRE>";

		$output = exec($cmd,$cmdout);

		$f7 = fopen($output_filename,"w");
		fputs($f7, $output);
		fclose($f7);

		print "<PRE>";
		print $output . "<br>\n";;
		print_r($cmdout);
		print "</PRE>";
//
//    [21] => OpenBUGS> monitor set
//    [22] => OpenBUGS> monitor set
//    [23] => OpenBUGS> 20000 updates took 84 s
//    [24] => OpenBUGS>           mean    sd      MC_error        val2.5pc        median  val97.5pc       startsample
//    [25] =>Deploy[1]       1.503   2.243   0.04145 -3.58   1.74    5.217   20001   40000
//    [26] =>     Deploy[2]       4.556   1.594   0.02837 0.8156  4.798   7.288   20001   40000
//    [27] =>     Deploy[3]       3.054   2.555   0.05109 -1.84   2.934   8.514   20001   40000
//    [28] =>     M[1]    81.08   20.76   0.445   46.0    79.0    127.0   20001   40000
//    [29] =>     M[2]    30.26   12.13   0.3299  12.0    28.0    59.0    20001   40000
//    [30] =>     N[1]    265.1   20.76   0.445   230.0   263.0   311.0   20001   40000
//    [31] =>     N[2]    214.3   12.13   0.3299  196.0   212.0   243.0   20001   40000
//    [32] =>     b[1]    0.04732 0.00695 1.52E-4 0.03352 0.0473  0.06104 20001   40000
//    [33] =>     b[2]    6.179E-4        1.288E-4        3.807E-6        3.791E-4        6.117E-4        8.875E-4      20001   40000
//    [34] =>     b[3]    6.041   0.4519  0.0048  5.187   6.031   6.958   20001   40000
//    [35] =>     d[1]    0.00413 0.002962        5.464E-5        2.703E-4        0.003537        0.01123 2000140000
//    [36] =>     d[2]    0.01127 0.00358 5.766E-5        0.00482 0.01108 0.01882 20001   40000
//    [37] => OpenBUGS> CODA files written
//    [38] => OpenBUGS>
//

		unset($outputmatrix);
		foreach($cmdout as $onerow){
			$rowparts = explode("\t",$onerow);
			if(
				trim($rowparts[1])== "Deploy[1]" ||
				trim($rowparts[1])== "Deploy[2]" ||
				trim($rowparts[1])== "Deploy[3]" ||
				trim($rowparts[1])== "M[1]" ||
				trim($rowparts[1])== "M[2]" ||
				trim($rowparts[1])== "N[1]" ||
				trim($rowparts[1])== "N[2]" ||
				trim($rowparts[1])== "b[1]" ||
				trim($rowparts[1])== "b[2]" ||
				trim($rowparts[1])== "b[3]" ||
				trim($rowparts[1])== "d[1]" ||
				trim($rowparts[1])== "d[2]" ||
				trim($rowparts[2])== "mean" 
								){
				$outputmatrix[] = $rowparts;
				}
		}

//    [0] => Array
//        (
//            [0] => OpenBUGS> 
//            [1] => 
//            [2] => mean
//            [3] => sd
//            [4] => MC_error
//            [5] => val2.5pc
//            [6] => median
//            [7] => val97.5pc
//            [8] => start
//            [9] => sample
//        )
//
//    [1] => Array
//        (
//            [0] => 
//            [1] => Deploy[1]
//            [2] => 1.436
//            [3] => 2.271
//            [4] => 0.04167
//            [5] => -3.683
//            [6] => 1.683
//            [7] => 5.188
//            [8] => 20001
//            [9] => 40000
//        )

		//print "<PRE>";
		//print_r($outputmatrix);
		//print "<PRE>";
		$diim  = $outputmatrix[1][2];
		$dinim = $outputmatrix[2][2];

	$results = "borderline fit";
	if($diim >3 && $dinim>3) $results = "IM and NIM fit";
	if($diim >3 && $dinim<3) $results = "IM fit only";
	if($diim <3 && $dinim>3) $results = "NIM fit only";
	if($diim <3 && $dinim<3) $results = "poor model fit";

	$nowtime = time();
        $q0 = "update sreinfodb set ";
        $q0 .= " istatus='" . "model completed" . "', ";
        $q0 .= " results='" . $results . "', ";
        $q0 .= " modified='" . $nowtime . "', ";
        $q0 .= " t2='" . $nowtime . "', ";
        $q0 .= " diim='" . $diim . "', ";
        $q0 .= " dinim='" . $dinim . "', ";
        $q0 .= " outputmatrix='" . base64_encode(serialize($outputmatrix)) . "' ";
        $q0 .= " where ix='" . $ix . "' limit 1;";
	//print $q0;
        mysql_query($q0);

	$q9 = "SELECT * from sreinfodb where ix='" . $ix . "' LIMIT 1;";
	$dbr9 = mysql_query($q9);
	$row9 = mysql_fetch_object($dbr9);
	$projname = trim($row9->projname);
	$teststartdate = trim($row9->teststartdate);

		$debug=1;
		if($debug==1){
		  print "<table>";
		  foreach($outputmatrix as $onerow){
			print "<tr>";
			foreach($onerow as $oneitem){
			  print "<td>";
			  print $oneitem ;
			  print "</td>";
			}
			print "</tr>";
		  }
		  print "</table>";
		}

?>

