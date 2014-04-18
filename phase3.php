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

        error_reporting(E_PARSE);

        require_once("uniformdeviates.php");

	require_once("setup.php");

        if(trim($ix)=="") $ix = $_REQUEST['ix'];

	//print "ix=$ix";
        require_once 'json.php';

	$q0 = "SELECT * from sreinfodb where ix='" . $ix . "';";
	$dbr0 = mysql_query($q0);
	$row0 = mysql_fetch_object($dbr0);
	//print_r($row0);

	$outputmatrix = unserialize(base64_decode($row0->outputmatrix));
        $tempfolder = "/opt/lampp/temp/";
	$tempfilename = $row0->tempfilename;
	$filenameprefix = $row0->tempfilename;
	$t1 = $row0->t1;

        $json = unserialize(base64_decode($row0->inputparams));
        $metadata = json_decode($json);
	

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
		//print "<PRE>";
		//print_r($metadata);
		//print "</PRE>";
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


                unset($day);
                unset($count);
		$i = 0;
		unset($records);
		unset($day);
		unset($count);
                foreach($timeseries as $onerow){
                                $effort = trim($onerow->effort);
                                $defectcount = trim($onerow->defectcount);
                                if($effort> 0  && $defectcount !=""){
				  unset($record);
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

		$debug=0;
		if($debug==1){
		  print "<br><br>Parsed CSV follows:<br>";
		  print "<br><br>Raw records read<br>";
		  print "<PRE>";
		  print_r($records);
		  print "</PRE>";
		}

		$debug = 0;
		if($debug==1){
		print "<br><br>Loaded raw vector of day numbers.<br>";
		print "<PRE>";
		print_r($day);
		print "</PRE>";

		print "<br><br> Loaded raw vector of defects found on numbered days.<br>";
		print "<PRE>";
		print_r($count);
		print "</PRE>";
		}


                $m = array_sum($count);
                $n = count($day)-1; //recall php arrays started at zero


		$debug = 0;
		if($debug==1){
			print "m=$m; n=$n";
		}
		

                $dat1_filename = $tempfolder . $tempfilename . "-SRE_dat1.txt";
                $dat2_filename = $tempfolder . $tempfilename . "-SRE_dat2.txt";
                $in1_filename =  $tempfolder . $tempfilename .  "-SRE_in1.txt";
                $in2_filename =  $tempfolder . $tempfilename .  "-SRE_in2.txt";
                $mod_filename =  $tempfolder . "SRE_mod.txt";
                $odc_filename =  $tempfolder . $tempfilename .  "-SRE_odc.txt";
                $output_filename =  $tempfolder . $tempfilename .  "-SRE_output.txt";

		$outputstem = $tempfilename;


	//print "<PRE>";
	//print_r( $output);
	//print "</PRE>";
                $debug=0;

                if($debug==1){
                  print "<table>";
		  print "<tr>";
		  print "<td>node</td>";
		  print "<td>mean</td>";
		  print "<td>sd</td>";
		  print "<td>MC error</td>";
		  print "<td>2.5%</td>";
		  print "<td>median</td>";
		  print "<td>97.5%</td>";
		  print "<td>start</td>";
		  print "<td>sample</td>";
		  print "</tr>";
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

	//print "Now loading outputfiles.........";
	//print "Filename of index file=$f";


//LOAD OPENBUGS OUTPUT DATA STRUCTURES

                $indexfilecontents = file_get_contents($tempfolder . $outputstem . "-CODAindex.txt");
                $datafilecontents1  = file_get_contents($tempfolder . $outputstem . "-CODAchain1.txt");
                $datafilecontents2  = file_get_contents($tempfolder . $outputstem . "-CODAchain2.txt");


		$debug=0;
		if($debug==1){
		//print "<PRE>";
		print "Filename of index file: " . $tempfolder . "testdataset" . "-CODAindex.txt" . "\n";
		print "Filename of index file: " . $tempfolder . "$outputstem" . "-CODAindex.txt" . "\n";
		//print $indexfilecontents;
		print "<br>size of datafilecontents1= " . strlen($datafilecontents1);
		print "<br>size of datafilecontents1a= " . strlen($datafilecontents1a);
		print "<br>size of datafilecontents2= " . strlen($datafilecontents2);
		print "<br>size of datafilecontents2a= " . strlen($datafilecontents2a);
		print "</PRE>";

		print "<PRE>";
		print "indexfilecontentsa:  ";
		print $indexfilecontentsa;
		print "indexfilecontents ";
		print $indexfilecontents;

		print "datafilecontents2a ";
		print $datafilecontents2a;
		print "datafilecontents2";
		print $datafilecontents2;
		print "</PRE>";
		}

//First, extract the index file output
		//print "<br><br>";
		unset($indexlines);
		unset($indexrecords);
		$indexlines = explode("\n",$indexfilecontents);
		foreach($indexlines as $oneline){
			unset($indexparts);
			$indexparts = explode("\t",$oneline);
			//print_r($indexparts);
			//print "<br>";
			unset($record);
			$record['variablename'] = trim($indexparts[0]);
			$record['from']         = trim($indexparts[1]);
			$record['to']           = trim($indexparts[2]);
			if(trim($indexparts[0]) !="")
			$indexrecords[trim($indexparts[0])] = $record;
		}

		$debug = 0;
		if($debug==1){
		  print "<br>";
		  print "<PRE>";
		  print_r($indexrecords);
		  print "</PRE>";
		  print "<br><br>";
		}

//Second, extract the datafile contents.
		$row = 0;
		unset($datalines);
		unset($allrecords1);
		$datalines = explode("\n",$datafilecontents1);
		foreach($datalines as $oneline){
			$row +=1;
			unset($indexparts);
			$indexparts = explode("\t",$oneline);
			$index =$indexparts[0];
			$value =$indexparts[1];
			unset($record);
			if($index !=""){
			  $record['index'] = $index;
			  $record['value'] = $value;
			  $allrecords1[$row] = $record;
			}
		}

		$datalines = explode("\n",$datafilecontents2);
		$row = 0;
		unset($allrecords2);
		foreach($datalines as $oneline){
			$row +=1;
			unset($indexparts);
			$indexparts = explode("\t",$oneline);
			$index =$indexparts[0];
			$value =$indexparts[1];
			unset($record);
			if($index !=""){
			  $record['index'] = $index ;
			  $record['value'] = $value;
			  $allrecords2[$row] = $record;
			}
		}

		$debug=0;
		if($debug==1){
		  print count($allrecords1)  . " records in allrecords1<br>";
		  print count($allrecords2)  . " records in allrecords2<br>";
		  print "<PRE>";
		  print_r($allrecords1);
		  print_r($allrecords2);
		  print "</PRE>";
		}

//now corrall the data

		//print "<PRE>";
		unset($onerecord);
		unset($masterecord);
		unset($vnamelist);
		foreach($indexrecords as $key=>$onerecord){
			$from         = trim($onerecord['from']);
			$to           = trim($onerecord['to']);
			$variablename = trim($onerecord['variablename']);
			$vnamelist[] = trim($variablename);
			for($i=$from; $i<=$to; $i++){
				unset($record);
				$record =  $allrecords2[$i];
				$index = trim($record['index']);
				$value = trim($record['value']);
				$masterecord[$variablename][] = $value;//anything not an integer
			}
			//print "Loaded data From=$from and To=$to records for $variablename . \n<br>";
			$extent = $to - $from + 1;
			//print "Statistics for $variablename of  $extent elements.\n<br>";
		}

	

//MAXIMUM LIKELHOOD SELECTIONs
//Histogram data
//print "<PRE>";
//print "number of values in mater record of M1\n";
//print count( $masterecord["M[1]"] );
$M_freq_IM  = array_count_values( $masterecord["M[1]"] );
ksort($M_freq_IM);
//print_r(  $masterecord["M[1]"] );
$M_freq_NIM  = array_count_values( $masterecord["M[2]"] );
ksort($M_freq_NIM);
//print "</PRE>";

//print_r($M_freq_NIM);

$N_freq_IM  = array_count_values( $masterecord["N[1]"] );
ksort($N_freq_IM);
//print "<PRE>";
//print_r($M_freq_IM);
//print "</PRE>";
$N_freq_NIM  = array_count_values( $masterecord["N[2]"] );
ksort($N_freq_NIM);
//print "</PRE>";

//print_r($N_freq_NIM);

//IM MODEL HERE
//First look at MLE
//get the mostlikely value

$MLE_M_IM =  mostlikely($masterecord["M[1]"]) ;
$MLE_N_IM =  round(mostlikely($masterecord["N[1]"])) ;

$MLE_M_IM_indices  = geteqindices( $masterecord["M[1]"], $MLE_M_IM  );
$MLE_N_IM_indices  = geteqindices( $masterecord["N[1]"], $MLE_N_IM  );
//get values of b for those indices

$b_values = getvaluesofindices($masterecord["b[1]"],$MLE_M_IM_indices);
//$b_values = $masterecord["b[1]"];
$b=mostlikely($b_values) ; //most likely from N1 mostlikely
//$b_mean=array_sum($masterecord["b[1]"])/count($masterecord["b[1]"]) ; //meanvalue

$b_mean=array_sum($b_values)/count($b_values) ; //meanvalue
$b = $b_mean;
//$b=mostlikely($masterecord["b[1]"]) ; //most likely overall
//$b=array_sum($b_values)/count($b_values) ; //meanvalue
//$b=medianvalue($b_values);//medianvalue
$MLE_b_MLE_IM  = $b;

$d_values = getvaluesofindices($masterecord["d[1]"],$MLE_M_IM_indices);
//$d_values = $masterecord["d[1]"];
//$d=mostlikelyraw($d_values) ; //most likely from M1 mostlikely
//$d_mean=array_sum($d_values)/count($d_values) ; //meanvalue
//$d_mean=array_sum($masterecord["d[1]"])/count($masterecord["d[1]"]) ; //meanvalue
$d_mean=array_sum($d_values)/count($d_values) ; //meanvalue
$d = $d_mean;
//$d=mostlikely($masterecord["d[1]"]) ; //most likely overall
//$d_mean=array_sum($d_values)/count($d_values) ; //meanvalue
//$d=medianvalue($d_values);//medianvalue
$MLE_d_MLE_IM  = $d;


$debug=0;
if($debug==1){
//forced vlues to compare
print "<PRE>";
print "MLE_M_IM= $MLE_M_IM <br>";
print "MLE_N_IM= $MLE_N_IM <br>";
print "</PRE>";
}

unset($cumsum);
unset($plotrecords_MLE_N_IM);
for($i=1; $i<= $MLE_N_IM; $i++){
	unset($record);
	$record['x'] = $i;
		//rate[i]<-(N_MLE-(i-1))*((i-1)*b^2/(1+b*(i-1))+d)
		$part1 = ($MLE_N_IM-($i-1)); //numerator part 1
		$part2 = ($i-1)*$b*$b; //numerator
		$part3 =  1+($b*($i-1)); //denominator
		$part4 = $part2/$part3;
		$part4 = $part2/($part3+$d);
		$part5 = $part4 + $d;
                $rate_MLE = $part1 * $part5 ;
		//$rate_MLE = $part1 * $part4;
		$xtime_MLE = 1.0/$rate_MLE;
		//if($debug==1) print "rate_MLE=$rate_MLE \n";
		$cumsum += $xtime_MLE;
		//if($debug==1) print "cumsum=$cumsum\n";
	$record['value'] = intval(round($cumsum));
	$plotrecords_MLE_N_IM[$i]= $record;
	if($i == ($MLE_N_IM - 10) ){
		$dropline_MLE_IM['y'] = $i;
		$dropline_MLE_IM['value'] = intval(round($cumsum));
	}
}
$time_to_MLE_N_IM = $cumsum;

$debug=1;
if($debug==1){
//forced vlues to compare
print "<PRE>";
print "IM Model";
print "MLE_M_IM= $MLE_M_IM <br>";
print "MLE_N_IM= $MLE_N_IM <br>";
print "time_to_MLE_N_IM=$time_to_MLE_N_IM<br>";
print "</PRE>";
}

//Now look at 95 percentile.
//Next look at the 95th percentile
$P95_M_IM  = round(mypercentile_of_histogram($M_freq_IM,0.95));
$P95_N_IM  = round(mypercentile_of_histogram($N_freq_IM,0.95));
$selected_indices = indicesaroundmypercent($masterecord["M[1]"],0.93,0.97);


  if(    count($selected_indices)<1  ){
	 $tol95 = 0;
         while(count($selected_indices)<1  ) {
	    $tol95 += 0.1;
            $lb = 0.93 - $tol95;
	    $ub = 0.97 ;
	    $selected_indices = indicesaroundmypercent($masterecord["M[1]"],$lb,$ub);
	 }
   }

$b_values = getvaluesofindices($masterecord["b[1]"],$selected_indices);
//$b_values = $masterecord["b[1]"];
$d_values = getvaluesofindices($masterecord["d[1]"],$selected_indices);
//$d_values = $masterecord["d[1]"];
$b = array_sum($b_values)/(count($b_values));

$d = array_sum($d_values)/(count($d_values));
//$b = array_sum($masterecord["b[1]"])/(count($masterecord["b[1]"]));
//$d = array_sum($masterecord["d[1]"])/(count($masterecord["d[1]"]));
//$b=medianvalue($b_values);//medianvalue

$P95_b_MLE_IM  = $b;
$P95_d_MLE_IM  = $d;


$debug=1;
if($debug==1){
//forced vlues to compare
print "<PRE>";
print  "Computed P95_M_IM=$P95_M_IM;\n";
print  "Computed P95_N_IM=$P95_N_IM;\n";
print "Computed b=$b;\n";
print "Computed d=$d;\n";
print "</PRE>";
}

unset($cumsum);
for($i=1; $i<= $P95_N_IM; $i++){
	unset($record);
	$record['x'] = $i;
                //rate[i]<-(N_MLE-(i-1))*((i-1)*b^2/(1+b*(i-1))+d)
                $part1 = ($P95_N_IM-($i-1)); //numerator part 1
                $part2 = ($i-1)*$b*$b; //numerator
                $part3 =  1+($b*($i-1)); //denominator

                $part4 = $part2/$part3;
                $part5 = $part4 + $d;

                $rate_P95 = $part1 * $part5 ;
		$xtime_P95 = 1.0/$rate_P95;
                   //if($debug==1) print "rate_P95=$rate_P95 \n";
		$cumsum += $xtime_P95;
		   //if($debug==1) print "cumsum=$cumsum\n";

	$record['value'] = intval(round($cumsum));
	$plotrecords_P95_N_IM[$i]= $record;
        if($i == ($P95_N_IM - 10)) {
                $dropline_P95_IM['y'] = $i;
                $dropline_P95_IM['value'] = intval(round($cumsum));
        }

}
//if($debug==1) print "</PRE>";
$time_to_P95_N_IM = $cumsum;


//NIM PLOT

//First we look at MLE
//get the mostlikely value
$MLE_M_NIM = mostlikely($masterecord["M[2]"]) ;

$MLE_N_NIM = round(mostlikely($masterecord["N[2]"])) ;
$MLE_M_NIM_indices  = geteqindices( $masterecord["M[2]"], $MLE_M_NIM  );
$MLE_N_NIM_indices  = geteqindices( $masterecord["N[2]"], $MLE_N_NIM  );
//get values of b for those indices

$b_values = getvaluesofindices($masterecord["b[2]"],$MLE_M_NIM_indices);
//$b_values = $masterecord["b[2]"];
//$b=mostlikelyraw($b_values) ; //most likely from N2 mostlikely
//$b=mostlikely($masterecord["b[2]"]) ; //most likely overall
$b=array_sum($b_values)/count($b_values) ; //meanvalue
//$b_mean=array_sum($masterecord["b[2]"])/count($masterecord["b[2]"]) ; //meanvalue
$b_mean=array_sum($b_values)/count($b_values) ; //meanvalue
//$b=medianvalue($b_values);//medianvalue
$b = $b_mean;

$d_values = getvaluesofindices($masterecord["d[2]"],$MLE_M_NIM_indices);
//$d_values = $masterecord["d[2]"];
//$d_mostlikely=mostlikelyraw($d_values) ; //most likely from M1 mostlikely
//$d_mean=array_sum($d_values)/count($d_values) ; //meanvalue
//$d_mean=array_sum($masterecord["d[2]"])/count($masterecord["d[2]"]) ; //meanvalue
$d_mean=array_sum($d_values)/count($d_values) ; //meanvalue
$d = $d_mean;
//$d=mostlikely($masterecord["d[1]"]) ; //most likely overall
//$d=medianvalue($d_values);//medianvalue
//print "<PRE>";
//print "<br>d_mean=$d_mean; d_mostlikely=$d_mostlikely<br>";
//print_r($d_values);
//print "</PRE>";
$MLE_b_MLE_NIM  = $b;
$MLE_d_MLE_NIM  = $d;


$debug=0;
if($debug==1){
//forced vlues to compare
print "<PRE>";
print "NIM Model";
print  "Computed MLE_M_NIM=$MLE_M_NIM;\n";
print  "Computed MLE_N_NIM=$MLE_N_NIM;\n";
print "Computed b=$b;\n";
print "Computed d=$d;\n";
print "</PRE>";
}

$cumsum = 0;
for($i=1; $i<= $MLE_N_NIM; $i++){
        unset($record);
        $record['x'] = $i;
                //rate[i]<-(N_MLE-(i-1)) * ((i-1)*b+d)
                $part1 = $MLE_N_NIM - ($i-1); //numerator part 1
                $part2 = ($i-1)*$b ;           //numerator part 2
                $part3 = $part2 + $d ;
                $rate_MLE = $part1 * $part3;
                //if($debug==1) print " rate_MLE =$rate_MLE \n";
                $xtime_MLE =  1.0/$rate_MLE ;
                //if($debug==1) print "xtime_MLE =$xtime_MLE \n";
                $cumsum += $xtime_MLE;
                //if($debug==1) print "cumsum=$cumsum\n";

        $record['value'] = intval(round($cumsum));
        $plotrecords_MLE_N_NIM[$i] = $record;
        //print "part1=$part1; part2=$part2;  xtime_MLE=$xtime_MLE;<br>";
        if($i == ($MLE_N_NIM - 10) ){
                $dropline_MLE_NIM['y'] = $i;
                $dropline_MLE_NIM['value'] = intval(round($cumsum));
        }
}
$time_to_MLE_N_NIM = intval(round($cumsum));

$debug=1;
if($debug==1){
//forced vlues to compare
print "<PRE>";
print "time_to_MLE_N_NIM=$time_to_MLE_N_NIM<br>";
print "</PRE>";
}



//Now look at 95th percentil
//get the 95th percentile
$P95_M_NIM = mypercentile_of_histogram($M_freq_NIM,0.95);
$P95_N_NIM  = round(mypercentile_of_histogram($N_freq_NIM,0.95));
$selected_indices = indicesaroundmypercent($masterecord["N[2]"],0.93,0.97);

  if(    count($selected_indices)<1  ){
	$tol95 = 0;
         while(count($selected_indices)<1  ) {
	    $tol95 += 0.1;
            $lb = 0.93 - $tol95;
	    $ub = 1.0 ;
	    $selected_indices = indicesaroundmypercent($masterecord["N[1]"],$lb,$ub);
	}
   }

$b_values = getvaluesofindices($masterecord["b[2]"],$selected_indices);
//$b_values = $masterecord["b[2]"];
$d_values = getvaluesofindices($masterecord["d[2]"],$selected_indices);
//$d_values = $masterecord["d[2]"];
$b = array_sum($b_values)/(count($b_values));
//$b = array_sum($masterecord["b[2]"])/(count($masterecord["b[2]"]));
$d = array_sum($d_values)/(count($d_values));
//$d = array_sum($masterecord["d[2]"])/(count($masterecord["d[2]"]));
//$d=0.00023;
//$b=medianvalue($b_values);//medianvalue
$P95_b_MLE_NIM  = $b;
$P95_d_MLE_NIM  = $d;

$debug=0;
if($debug==1){
//forced vlues to compare
print "<PRE>";
print  "Computed P95_M_NIM=$P95_M_NIM;\n";
print  "Computed P95_N_NIM=$P95_N_NIM;\n";
print  "Target           =39\n\n";
print "Computed b=$b;\n";
print "Target    =0.002413303\n\n";
print "Computed d=$d;\n";
print "Target    =0.002004486\n";
print "</PRE>";
}

unset($cumsum);
for($i=1; $i<= $P95_N_NIM; $i++){
        unset($record);
        $record['x'] = $i;
                //rate[i]<-(N_MLE-(i-1)) * ((i-1)*b+d)
                $part1 = $P95_N_NIM - ($i-1); //numerator part 1
                $part2 = ($i-1)*$b ;           //numerator part 2
                $part3 = $part2 + $d ;
                $rate_P95 = $part1 * $part3;
                //if($debug==1) print " rate_P95 =$rate_P95 \n";
                $xtime_P95 =  1.0/$rate_P95 ;
                //if($debug==1) print "xtime_P95 =$xtime_P95 \n";
                $cumsum += $xtime_P95;
                //if($debug==1) print "cumsum=$cumsum\n";

        $record['value'] = intval(round($cumsum));
        $plotrecords_P95_N_NIM[$i] = $record;
        //print "part1=$part1; part2=$part2;  xtime_P95=$xtime_MLE;<br>";
        if($i == ($P95_N_NIM - 10)){
                $dropline_P95_NIM['y'] = $i;
                $dropline_P95_NIM['value'] = intval(round($cumsum));
        }
}

$time_to_P95_N_NIM = $cumsum;


$results1b_text .= "time_to_MLE_IM= $time_to_MLE_IM; ";
$results2b_text .= "time_to_MLE_NIM= $time_to_MLE_NIM; ";


//calculate deployability results
//mean deployability index
$DI_IM =  number_format(array_sum( $masterecord['Deploy[1]'] )/ count( $masterecord['Deploy[1]'] ),1);
$DI_NIM = number_format(array_sum( $masterecord['Deploy[2]'] ) / count( $masterecord['Deploy[2]'] ),1);

$results1a_text  =  "Key Model Variables:  DI_IM=$DI_IM;  ";
$results2a_text  =  "Key Model Variables:  DI_NIM=$DI_NIM;  ";

$results1a_text .=  "MLE_N_IM="  .  $MLE_N_IM  . ";  ";
$results2a_text .=  "MLE_N_NIM=" .  $MLE_N_NIM  . ";  ";

$results1a_text .=  "MLE_M_IM="  .  $MLE_M_IM  . ";  ";
$results2a_text .=  "MLE_M_NIM=" .  $MLE_M_NIM  . ";  ";

$results1a_text .=  "beta_IM="  .  number_format($MLE_b_MLE_IM,6)  . ";  ";
$results2a_text .=  "beta_NIM=" .  number_format($MLE_b_MLE_NIM,6)  . ";  ";

$results1a_text .=  "delta_IM="  .  number_format($MLE_d_MLE_IM,6)  . ";  ";
$results2a_text .=  "delta_NIM=" .  number_format($MLE_d_MLE_NIM,6)  . ";  ";


$s4  = "Under this IM model, there are 'most likely' " . number_format($MLE_N_IM) . " total defects in the solution.";
$s14  = "Under this NIM model, there are 'most likely' " . number_format($MLE_N_NIM) . " total defects in the solution.";

$tobefound = $MLE_N_IM-$m;
$tobefound_NIM = $MLE_N_NIM-$m;

$tobefoundminus10 = $tobefound - 10;
$tobefoundminus10_NIM = $tobefound_NIM - 10;

$daysleft = $dropline_MLE_IM['value'] - $day[count($day)-1];
$daysleft_NIM = $dropline_MLE_NIM['value'] - $day[count($day)-1];

print "<br>daysleft=$daysleft<br>";
print "daysleft_NIM=$daysleft_NIM<br>";

$s5 = "Since $m defects were already found in " . number_format($day[$n]) . " (days -or- hours), ";
$s15 = "Since $m defects were already found in " . number_format($day[$n]) . " (days -or- hours), ";
$s5 .= " that 'most likely' leaves " . number_format($tobefound) . " defects to find.";
$s15 .= " that 'most likely' leaves " . number_format($tobefound_NIM) . " defects to find.";
$s6 = "Assuming 'readiness' is defined by 10 remaining defects, ";
$s16 = "Assuming 'readiness' is defined by 10 remaining defects, ";
$s7 = "you might plan to stop testing after only " . number_format($daysleft) . " more (days -or- hours) and you would 'most likely' find only " . number_format($tobefoundminus10) . " new defects.";
$s17 = "you might plan to stop testing after only " . number_format($daysleft_NIM) . " more (days -or- hours) and you would 'most likely' find only " . number_format($tobefoundminus10_NIM) . " new defects.";

$s0 = "Interpretative statement: ";

if($DI_IM <3)  {
	 $s1a = "Because DI=". $DI_IM .  " (less than 3) the AT&T SRE IM model IS NOT a good fit.";
	 $s1b = "Too soon to tell.";
	}
if($DI_IM >=3 ) {
	$s1a =  "Because DI=" . $DI_IM . " (greater than 3) the AT&T SRE IM model (for less experienced testers) IS a GOOD fit";
	$s1b =  "if the IM curves shown below visually align. ";
	}

if($DI_NIM <3)  {
	 $s2a = "Because DI=". $DI_NIM . "(less than 3) the AT&T SRE NIM model IS NOT a good fit.";
	 $s2b = "Too soon to tell.";
	}
if($DI_NIM >=3 ) {
	$s2a =  "Because DI=" . $DI_NIM . "(greater than 3) the AT&T SRE NIM model (for more experienced testers) IS a GOOD fit";
	$s2b =  "if the NIM curves shown below visually align";
	}


$M_IM_mean =  number_format(array_sum( $masterecord['M[1]'] )/ count( $masterecord['M[1]'] ),1);
$M_NIM_mean = number_format(array_sum( $masterecord['M[2]'] )/ count( $masterecord['M[2]'] ),1);
//$results1_text .=  "mean(M_IM)=$M_IM_mean; ";
//$results2_text .=  "mean(M_NIM)=$M_NIM_mean; ";


for($i=1;$i<=count(  $masterecord['M[1]']  ); $i++){
	$value = $masterecord['M[1]'][$i];
	$M1_histogram[$value] +=  1;
}
ksort($M1_histogram);
for($i=1;$i<=count(  $masterecord['M[2]']  ); $i++){
	$value = $masterecord['M[2]'][$i];
	$M2_histogram[$value] +=  1;
}
ksort($M2_histogram);
//print "<PRE>";
//print_r($M1_histogram);
//print "</PRE>";

$M_IM_sd   = number_format(standard_deviation($masterecord['M[1]']),1);
$M_NIM_sd  = number_format(standard_deviation($masterecord['M[2]']),1);
//$results1_text .= "sd(M_IM)=$M_IM_sd; ";
//$results2_text .= "sd(NIM)=$M_NIM_sd; ";

$N_IM_95   = mypercentile_of_samples($masterecord['N[1]'],0.95);
$N_NIM_95  = mypercentile_of_samples($masterecord['N[2]'],0.95);
//print "N_IM_95=$N_IM_95;
//print "N_IM_95=$N_IM_95;

$results1b_text =  "N_IM_95=$N_IM_95; ";
$results2b_text =  "N_NIM_95=$N_NIM_95 ";

$results1b_text .= "time_to_MLE_N_IM= "  . intval(round($time_to_MLE_N_IM));
$results2b_text .= "time_to_MLE_N_NIM= " . intval(round($time_to_MLE_N_NIM));




		$debug = 0;
		//print "Looking at $dat2_filename";
		//print "<PRE>";
		$dzcontents = file_get_contents($dat2_filename);
		$dzmatrix = explode("\n",$dzcontents);
		foreach($dzmatrix as $oneline){
			$parts = explode(" ",$oneline);
			$dzday[] = trim($parts[0]);
			$dzvalue[] = trim($parts[1]);
			//print trim($parts[0]) . " " . trim($parts[1]) . "\n";
		}
		//print "</PRE>";

	print "DI_IM=$DI_IM<br>";
	print "DI_NIM=$DI_NIM<br>";
	print "MLE_M_IM=$MLE_M_IM<br>";
	print "MLE_N_IM=$MLE_N_IM<br>";

	$diim = $DI_IM;
	$dinim = $DI_NIM;
        $results = "borderline fit";
        if($diim >3 && $dinim>3) $results = "IM and NIM fit";
        if($diim >3 && $dinim<3) $results = "IM fit only";
        if($diim <3 && $dinim>3) $results = "NIM fit only";
        if($diim <3 && $dinim<3) $results = "poor model fit";


        $nowtime = time();
        $q0 = "update sreinfodb set ";
        $q0 .= " istatus='" . "done" . "', ";
        $q0 .= " results='" . $results . "', ";
        //$q0 .= " modified='" . $nowtime . "', ";
        $q0 .= " t2='" . $nowtime . "', ";
        $q0 .= " diim='" . $diim . "', ";
        $q0 .= " dinim='" . $dinim . "', ";

        $q0 .= " im_effort_left='" . $daysleft . "', ";
        $q0 .= " imdefectsleft='" . $tobefound . "', ";

        $q0 .= " nim_effort_left='" . $daysleft_NIM . "', ";
        $q0 .= " nimdefectsleft='" . $tobefound_NIM . "' ";

        $q0 .= " where ix='" . $ix . "' limit 1;";
        print $q0;
        mysql_query($q0);


function mostlikely($v){
	//print "in mostlikely\n";
	unset($nv);
	foreach($v as $key=>$oneitem){
		$nv[] = $oneitem;
	}
	sort($nv);
	$median = $nv[intval(round(count($nv)/2))];
	//print "median=$median\n";
	if(abs($median)*1000000000 >1) $precision = 11;
	if(abs($median)*100000000 >1) $precision = 10;
	if(abs($median)*10000000 >1) $precision = 9;
	if(abs($median)*1000000 >1) $precision = 8;
	if(abs($median)*100000 >1) $precision = 7;
	if(abs($median)*10000 >1) $precision = 6;
	if(abs($median)*1000 >1) $precision = 5;
	if(abs($median)*100 >1) $precision = 4;
	if(abs($median)*10 >1) $precision = 3;
	if(abs($median)>1) $precision = 2;
	//print "precision=$precision\n";
	unset($tally);
	$maxvalue = 0;
	$maxtally = 0;
	foreach($nv as $key=>$oneitem){
		$tally[number_format($oneitem,$precision)] +=1;
		if($tally[number_format($oneitem,$precision)] >$maxtally) {
			$maxtally = $tally[number_format($oneitem,$precision)] ;
			$maxvalue = number_format($oneitem,$precision);
			}
	}
	//print_r($tally);
	sort($tally);
	$mostest = $maxvalue;
	//print "returning mostest=$mostest\n";
	return($mostest);
}

function mostlikelyraw($v){
	//print "in mostlikelyraw\n";
	unset($nv);
	foreach($v as $key=>$oneitem){
		$nv[] = $oneitem;
	}
	sort($nv);
	unset($tally);
	foreach($nv as $key=>$oneitem){
		$tally[$oneitem] +=1;
		if($tally[$oneitem] >$maxtally) {
			$maxtally = $tally[$oneitem] ;
			$maxvalue = $oneitem;
			}
	}
	//print_r($tally);
	sort($tally);
	$mostest = $maxvalue;
	//print "returning mostest=$mostest\n";
	return($mostest);
}

function medianvalue($v){
	unset($nv);
	foreach($v as $onevalue){
		$nv[] = $onevalue;
	}
	sort($nv);
	$value = $nv[ intval(round(count($nv)/2)) ];
	return($value);
}

function geteqindices($v,$value){
	foreach($v as $key=>$onevalue){
		if(intval(round($onevalue))==intval(round($value))){
		 $indexlist[] = $key;
		}
	}
	return($indexlist);
}

function getvaluesofindices($values,$indices){
	foreach($indices as $oneindex){
		 $valuelist[] =$values[$oneindex]; 
		}
	return($valuelist);
}
function indicesaroundmypercent($data,$lb,$ub){
	//print "<PRE>";
	//print "indicesaroundmypercent\n";
	//print "lb=$lb; ub=$ub\n";
	//print_r($data);
	$valueatlb = mypercentile_of_samples($data,$lb);
	//print "valueatlb=$valueatlb\n";
	$valueatub = mypercentile_of_samples($data,$ub);
	//print "valueatub=$valueatub\n";

	foreach($data as $key=>$value){
		if($value>$valueatlb && $value<=$valueatub){
		$listofkeys[] = $key;
		}
	}
	//print_r($listofkeys);
	//print "</PRE>";

        return($listofkeys);
}

function customhistogram($listofvalues){
	$maxvalue = max($listofvalues);
	$minvalue = min($listofvalues);
	
	$sum = array_sum($listofvalues);
	$mean = $sum/count($listofvalues);
	$range = $maxvalue - $minvalue;
	$buckets = 100;
	$numvalues = count($listofvalues);
	$bucketsize= $range / $buckets;
	$maxtally = 0;
	$mintally = 0;
	$mostlikely = 0;
	$ninetyfivepercentbucket = 0.9 * $buckets;

//initialize buckets
	for($i=0; $i<= $buckets; $i++){
		$record['tally'] = 0;
		$bucketlow  = $minvalue + ($i * $bucketsize);
		$buckethigh = $bucketlow + $bucketsize;
		$bucketmidpoint = ($buckethigh + $bucketlow)/2.0;
		$record['bucketlow'] = $bucketlow;
		$record['buckethigh'] = $buckethigh;
		$record['bucketmidpoint'] = $bucketmidpoint;
		$bucketrecords[$i] = $record;
}

//now do the tallies
	for($i=0; $i<= $buckets; $i++){
		$record = $bucketrecords[$i];
		$bucketlow = $record['bucketlow'];
		$buckethigh = $record['buckethigh'];
		$bucketmidpoint= $record['bucketmidpoint'];
		foreach($listofvalues as $onevalue){
			if($onevalue>= $bucketlow && $onevalue< $buckethigh){
			  $record['tally'] +=1;
			  $bucketrecords[$i]  = $record;
			  if($record['tally']  > $maxtally) {
				$maxtally = $record['tally'];
				$mostlikelyvalue = $bucketmidpoint;
				$mostlikelybucket= $i;
				$mostlikelytally = $record['tally'];	
				}
			  //if($record['tally']  < $mintally) $mintally = $record['tally'];
			}
		}
	}


	//find most likely figures
	$ninetyfivepercentile=0;
	$fiftypercentile =0;
	$meanotfound = 0;
	$cumtallies = 0;
	for($i=0; $i<= $buckets; $i++){
		$record = $bucketrecords[$i];
		$cumtallies += $record['tally'];
		if($cumtallies >= 0.95 * $numvalues && $ninetypercentile==0){
			$ninetyfivepercentile=1;
			$ninetyfivepercentilevalue = $record['bucketmidpoint'];
			$ninetyfivepercentilebucket=$i; 
		}
		if($cumtallies >= 0.5 * $numvalues && $fiftypercentile==0){
			$fiftypercentile=1;
			$fiftypercentilevalue = $record['bucketmidpoint'];
			$fiftypercentilebucket=$i;
		}
		if($record['bucketlow'] >= $mean && $meanotfound ==0){
			$meanotfound = 1;
			$meantally = $record['tally'];
		}
}

	unset($record);
	$record['tallies']  = $bucketrecords;
	$record['mean']  = $mean;
	$record['meantally']  = $meantally;
	$record['buckets']  = $buckets;
	$record['mintally'] = $mintally;
	$record['maxtally'] = $maxtally;
	$record['minvalue'] = $minvalue;
	$record['maxvalue'] = $maxvalue;
	$record['mostlikelyvalue'] = $mostlikelyvalue;
	$record['mostlikelytally'] = $mostlikelytally;
	$record['mostlikelybucket'] = $mostlikelybucket;
	$record['fiftypercentilevalue'] = $fiftypercentilevalue;
	$record['fiftypercentilebucket'] = $fiftypercentilebucket;
	$record['ninetyfivepercentilevalue'] = $ninetyfivepercentilevalue;
	$record['ninetyfivepercentilebucket'] = $ninetyfivepercentilebucket;
	return($record);
}

function standard_deviation($aValues, $bSample = false)
{
    $fMean = array_sum($aValues) / count($aValues);
    $fVariance = 0.0;
    foreach ($aValues as $i)
    {
        $fVariance += pow($i - $fMean, 2);
    }
    $fVariance /= ( $bSample ? count($aValues) - 1 : count($aValues) );
    return (float) sqrt($fVariance);
} 

function mypercentile_of_samples($data,$percentile){ 

	//print "<PRE>";
	//print_r($data);
	//print "</PRE>";

	unset($histogram);
	unset($totalnumber);
	for($i=0; $i< count($data)-1; $i++){
	  $value = $data[$i];
	  $histogram[$value] +=1;
	  $totalnumber +=1;
	}

	ksort($histogram);
	$sumofall = array_sum($histogram);
	$magicnumber = $percentile * $sumofall;
	//print "sumofall=$sumofall";
	//print "magicnumber=$magicnumber";

	$cdf = 0;
	foreach($histogram as $key=>$onevalue){
	  $cdf += $onevalue;
	  if($cdf >= $magicnumber){
	    //print "found magicnumber key=$key; onevalue=" . $onevalue;
	   $magicindex = $key;
	   break;
	  }
  	}

	//print count($data) . " elements in mypercentile.";
	//print  "percentile=$percentile";
	//print "<PRE>";
	//print_r($histogram);
	//print "magicindex for $percentile of above data =$magicindex\n";
	//print "</PRE>";
    	return($magicindex); 
}

function mypercentile_of_histogram($data,$percentile){
	$histogram = $data;
	ksort($histogram);
	$sumofall = array_sum($histogram);
	$magicnumber = $percentile * $sumofall;
	//print "sumofall=$sumofall";
	//print "magicnumber=$magicnumber";

	foreach($histogram as $key=>$onevalue){
	  $cdf += $onevalue;
	  if($cdf >= $magicnumber){
	    //print "found magicnumber key=$key; onevalue=" . $onevalue;
	   $magicindex = $key;
	   break;
	  }
  	}

	//print "<PRE>";
	//print_r( $data);
	//print "sumofall=$sumofall; percentile=$percentile; cdf=$cdf; magicindex=$magicindex;";
	//print "</PRE>";
	return($magicindex);
}

	print_r($metadata);
?>
