<?php
include("../config.php");
include_once("lib/class.breadcrumb.inc.php");

// set up a breadcrumb object to be used by all wwi pages

$breadcrumb = new breadcrumb;
$breadcrumb->homepage = ''; //don't display webserver root as home
$breadcrumb->dirformat='ucfirst'; // Show the directory in this style
$breadcrumb->showfile=TRUE; // shows the file name in the path
$breadcrumb->hideFileExt=TRUE;  // don't show filename extension

$breadcrumb->changeName=array('wwiweb'=>'Home');
$breadcrumb->changeFileName=array('$base_path/searchall.php'=>'Keyword Search',
				  '$base_path/postcards/searchform.php'=>'Search',
  				  '$base_path/postcards/search.php'=>'Search Results',
  				  '$base_path/poetry/searchform.php'=>'Search',
 				  '$base_path/poetry/search.php'=>'Search Results',
				  );
/* TEMPORARY:
  This mapping will change when the site moves to a more permanent home
*/
$breadcrumb->removeDirs= array('rebecca');

?>