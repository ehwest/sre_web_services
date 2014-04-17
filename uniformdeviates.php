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

class uniform
{
	//return a random deviate betwween 0.0 and 1.0
	//Taken from description of "ran1" in 209-211 of Numerical Recipes in C
	//Suggested by Donald E. Kunth, Seminumerical Algorithms, 2nd ed. The Art of Computer Programming
	//sections 3.2, 3.3  1981

	private  $r = array();
	private $idum;
	private $iff;

	private $ix1;
	private $ix2;
	private $ix3;

	function __construct(){

		$M1 = 259200;
		$IA1 = 7141;
		$IC1 = 54773;
		$RM1 = 1 / $M1;

		$M2 = 134456;
		$IA2 = 8121;
		$IC2 = 24811;
		$RM2 = 1.0 / $M2;

		$M3 = 243000;
		$IA3 = 4561;
		$IC3 = 51349;

		$this->idum = -1.0 * time(); //reinitialize every call

		$this->iff = 1;
		$this->ix1 = ($IC1 - $this->idum) % $M1;
		$this->ix1 = (($IA1*$this->ix1)+$IC1) % $M1;

		$this->ix2 = $this->ix1 % $M2;
		$this->ix1 = (($IA1 * $this->ix1)+ $IC1) % $M1;
		$this->ix3 = $this->ix1 % $M3;
	
		for($j=1; $j<=97; $j++){
			//print "j=$j\n";
			$this->ix1 = ( ($IA1*$this->ix1) + $IC1) % $M1;
			$this->ix2 = ( ($IA2*$this->ix2) + $IC2) % $M2;
			$this->r[$j] = ($this->ix1 + ($this->ix2 * $RM2))*$RM1;
		}
		$this->idum = 1;
	}

function pullone($min=0,$max=1){

	$M1 = 259200;
	$IA1 = 7141;
	$IC1 = 54773;
	$RM1 = 1 / $M1;

	$M2 = 134456;
	$IA2 = 8121;
	$IC2 = 24811;
	$RM2 = 1.0 / $M2;

	$M3 = 243000;
	$IA3 = 4561;
	$IC3 = 51349;

	$this->ix1 = (($IA1*$this->ix1)+$IC1) % $M1;
	$this->ix2 = (($IA2*$this->ix2)+$IC2) % $M2;
	$this->ix3 = (($IA3*$this->ix3)+$IC3) % $M3;
	
	$j = 1 + ((97*$this->ix3) / $M3);
	$temp = $this->r[$j];
	$this->r[$j] = ($this->ix1 + ($this->ix2 * $RM2) ) * $RM1;
	
	$temp = $min + ($max-$min)*$temp;
	return ($temp);

}//pullone
}//class
?>
