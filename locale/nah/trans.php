<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * typically accessed using the T("...") function
 * provided in the ..../shared/templates.php
 */

#****************************************************************************
#* Formats
#****************************************************************************
$trans["headerDateFormat"]	 = "m.d.Y";

#****************************************************************************
#* Common translation text, arranged alphabetically
#****************************************************************************
####### A #######
$trans['Account'] = 'pohualli';
$trans['Active'] = 'aini';
$trans['Add'] ='xijpiui';
####### B #######
####### C #######
$trans['City'] = 'altepetl';
####### D #######
####### E #######
####### F #######
####### G #######
####### H #######
####### I #######
####### J #######
####### K #######
####### L #######
$trans['LastName'] = 'tzonquīzcā';
$trans['Library'] = 'amoxpialoyan';
####### M #######
####### N #######
$trans['No'] = 'Axcanah';
####### O #######
####### P #######
####### Q #######
####### R #######
$trans['results found'] = 'pilachiamoxtzin tlen moahcic';
####### S #######
$trans['Site'] = 'amoxpialoyan';
$trans['Site'] = 'xictemo';
####### T #######
####### U #######
####### V #######
####### W #######
$trans['Welcome to the Library'] = 'Pialli';
####### Y #######
$trans['Yes'] = 'Quema';
####### Z #######

 ## ##################################
 ## adds suport for plugins - fl, 2009
 ## ##################################
		$list = getPlugIns('tran.tran');
		for ($x=0; $x<count($list); $x++) {
			include($list[$x]);
		}
 ## ##################################
