<html>
  <head>
    <link rel="stylesheet" type="text/css" href="../wwi.css">
    <title>The Great War : Search All</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <base href="http://reagan.library.emory.edu/rebecca/wwiweb/">
  </head>
<body>

<?php
include_once("lib/taminoConnection.class.php");
include_once("lib/mybreadcrumb.php");
$args = array('host' => "vip.library.emory.edu",
	      'db' => "WW1",
	      'debug' => false,
	      'coll' => 'postcards');
$tamino = new taminoConnection($args);

$kw = $_GET["keyword"];			// term to search for
$poetry = $_GET["poetry"];		// databases to search
$postcards = $_GET["postcards"];
$links = $_GET["links"];

$query = array();
$query["postcard"] ='declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction"
for $a in input()/TEI.2/:text/body/p/figure ';
$query["postcard"] .= "where tf:containsText(\$a, '$kw') ";
$query["postcard"] .= 'return <div> {$a} <total> {count($a/../figure)}</total> </div>';

$xsl_file = "figures.xsl";
$xsl_params = array("mode" => "thumbnail");

include("header.html");  
print "<p class='breadcrumbs'>".$breadcrumb->show_breadcrumb()."</p>"; 

print '<div class="content">'; 

// need to add an option to use cursor... 
$rval = $tamino->xquery($query["postcard"]); 
if ($rval) {       // tamino Error code (0 = success) 
  print "<p>Error: failed to retrieve contents.<br>";
  print "(Tamino error code $rval)</p>";
  exit();
}

$tamino->xslTransform($xsl_file, $xsl_params); 
//$tamino->xslTransform($xsl_file);
$tamino->printResult();

print '</div>';

print '<div class="sidebar">';
include("searchbox.html");
print '</div>';
include("footer.html");

?>

</body>
</html>
