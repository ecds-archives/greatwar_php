<?php

include_once("lib/class.breadcrumb.inc.php");

// set up a breadcrumb object to be used by all wwi pages

$breadcrumb = new breadcrumb;
$breadcrumb->homepage = ''; //don't display webserver root as home
$breadcrumb->dirformat='ucfirst'; // Show the directory in this style
$breadcrumb->showfile=TRUE; // shows the file name in the path
$breadcrumb->hideFileExt=TRUE;  // don't show filename extension
// These mappings will change when the site moves to a more permanent home
$breadcrumb->removeDirs= array('rebecca');
$breadcrumb->changeName=array('wwiweb'=>'Home');

?>