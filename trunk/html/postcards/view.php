<?php
include("../config.php");	

print "<html>
  <head> 
    $csslink
    <title>The Great War : Postcards : Detail</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <base href='$base_url'>
  </head> 
<body> 
"; 

include_once("lib/taminoConnection.class.php");
include_once("lib/mybreadcrumb.php");

$args = array('host' => $tamino_server,
	      'db' => $tamino_db,
	      'coll' => 'postcards',
	      'basedir' => $basedir,
	      'debug' => false,
	      );
$tamino = new taminoConnection($args);

$id = $_GET["id"];
$zoom = $_GET["zoom"];

($zoom == "2") ? $mode = "zoom" : $mode = "full";
$xsl_params = array("mode" => $mode);


$query = '<div> { for $a in input()/TEI.2/:text/body/p/figure
where $a/@entity = "';
$query .= "$id";
$query .= '" return $a }"';
$query .= '{ for $b in input()/TEI.2/:text/back/:div//interpGrp return $b }</div>';
// need to retrieve interpGrps to display categories nicely
$xsl_file = "figures.xsl";

include("header.html");   

print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . " ";
($zoom == "2") ? print "(Double size)" : print "(Full Details)";
print "</p>";

/*
print '<p class="breadcrumbs">  
<a href="index.html">Home</a> &gt; <a href="postcards/">Postcards</a>  
	  &gt; Browse &gt; Detail  
</p>';
*/

print '<div class="content">'; 

// need to add an option to use cursor... 
$tamino->xquery($query); 

$tamino->xslTransform($xsl_file, $xsl_params); 
$tamino->printResult();

print '</div>';

print '<div class="sidebar">';
include("postcards/nav.html");
include("searchbox.php");

print '</div>';

include("footer.html");

?>

</body>
</html>

