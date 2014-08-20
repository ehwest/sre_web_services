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

	//this part contains local database credentials
        error_reporting(E_PARSE);

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

?>
