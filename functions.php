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

function getprojectinfo($userid,$licensekey,$host){
	//print "curl init starting ";
        $ch = curl_init();
        //print_r($ch);
        curl_setopt($ch, CURLOPT_URL, "http://" . $host . "/GetProjectInfo.htm" );
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, "user_license_key=" . $licensekey );

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //print " curl opts set";
        // $output contains the output string
        $response = curl_exec($ch);
        //print "curl done";
        //print $response;
	$phpdata = parsexml2php($response);
	print_r($phpdata);
	return($phpdata);
}


function parsexml2php($data){
	print $data;
	$cleandata = get_string($data,"<response>","</response>");
	$item_count= get_string($cleandata,"<item_count>","</item_count>");
	$items= get_string($cleandata,"<items>","</items>");
	print "item_count=$item_count";

	$projectlist = explode("<project>",$items);
	print_r($projectlist);
	unset($allrecords);

	foreach($projectlist as $oneproject){
//          $xmlfragment = $oneproject;
//	  unset($record);
//          $record['project_key']  = get_string($xmlfragment,"<project_key>","</project_key>");
//          $record['results']      = get_string($xmlfragment,"<results>","</results>");
//          $record['status']       = get_string($xmlfragment,"<status>","</status>");
//          $record['modified']       = get_string($xmlfragment,"<modified>","</modified>");
//          $record['filename']       = get_string($xmlfragment,"<filename>","</filename>");
//          $record['teststartdate']       = get_string($xmlfragment,"<teststartdate>","</teststartdate>");
//          $record['projectname']       = get_string($xmlfragment,"<projectname>","</projectname>");
//          $record['datapoints']       = get_string($xmlfragment,"<datapoints>","</datapoints>");
//          $record['maxeffort']       = get_string($xmlfragment,"<maxeffort>","</maxeffort>");
//          $record['diim']       = get_string($xmlfragment,"<diim>","</diim>");
//          $record['dinim']       = get_string($xmlfragment,"<dinim>","</dinim>");
//          $record['im_effort_left']       = get_string($xmlfragment,"<im_effort_left>","</im_effort_left>");
//          $record['nim_effort_left']       = get_string($xmlfragment,"<nim_effort_left>","</nim_effort_left>");
//          $record['imdefectsleft']       = get_string($xmlfragment,"<imdefectsleft>","</imdefectsleft>");
//          $record['nimdefectsleft']       = get_string($xmlfragment,"<nimdefectsleft>","</nimdefectsleft>");
//
          if($record['project_key !="" ){
           $allrecords[] = $record;
           }
	}//foreach

	print_r($allrecords);
	return($allrecords);
}

function get_string($h, $s, $e){
        $sp = strpos($h, $s, 0 ) + strlen($s);
        $ep = strpos($h, $e, $sp);
        return substr($h, $sp, $ep - $sp);
}

?>
