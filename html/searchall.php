<?php
include("../config.php");	

print "<html>
  <head> 
    $csslink
    <title>The Great War : Search All</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <base href='$base_url'>
  </head> 
<body> 
"; 

include_once("lib/taminoConnection.class.php");
include_once("lib/mybreadcrumb.php");
$tamino = array();
$args = array('host' => "vip.library.emory.edu",
	      'db' => "WW1",
	      'debug' => false,
	      'coll' => 'postcards');
$tamino["postcards"] = new taminoConnection($args);
$args["coll"] = "poetry";
$tamino["poetry"] = new taminoConnection($args);


$dosearch = array("postcards", "poetry");
$kw = $_GET["keyword"];			// term to search for
$dosearch["poetry"] = $_GET["poetry"];		// databases to search
$dosearch["postcards"] = $_GET["postcards"];
$dosearch["links"] = $_GET["links"];

$search = array("postcards", "poetry");
$query = $for = $return = $xsl_file =  $xsl_params = array();
$declare = 'declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction" ';
$for["postcards"] = 'for $a in input()/TEI.2/:text/body/p/figure ';
$for["poetry"] = 'for $a in input()/TEI.2/:text/body/div1/div2[@type="poem"] ';
$where = "where tf:containsText(\$a, '$kw') ";
$return["postcards"] = "return <div> {\$a} <total> {count(" . $for["postcards"] . " $where return \$a)}</total> </div>";
$return["poetry"] = ' return <div><div2> {$a/@type} {$a/@id} {$a/@n} {$a/byline} {$a/../docAuthor} 
{for $l in $a//l where tf:containsText($l, ' . "'$kw'" . ') return $l }
<linecount> { count($a//l) } </linecount> </div2><total> {count(' . $for["poetry"] . " $where return \$a)}</total></div> sort by (@n) ";  

foreach ($search as $s) {
  $query[$s] ="$declare " . $for[$s]  . " $where " . $return[$s];  
}


$xsl_file["postcards"] = "figures.xsl";
$xsl_params["postcards"] = array("mode" => "thumbnail");
$xsl_file["poetry"] = "poetry.xsl";
$xsl_params["poetry"] = array("mode" => "search");


include("header.html");  
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>"; 

print '<div class="content">'; 

// need to add an option to use cursor... ?

foreach ($search as $s) {
  if ($dosearch[$s] == "on") {
    $rval = $tamino[$s]->xquery($query[$s]); 
    if ($rval) {       // tamino Error code (0 = success) 
      print "<p>Error: failed to retrieve contents.<br>";
      print "(Tamino error code $rval)</p>";
      exit();
    }
    $tamino[$s]->getXQueryCursor();
    print "<p>Found " . $tamino[$s]->count . " matches in $s</p>";
  }
}
 
foreach ($search as $s) {
  if ($dosearch[$s] == "on") {
    print "<hr class='floatright'><p>$s</p>";
    $tamino[$s]->xslTransform($xsl_file[$s], $xsl_params[$s]); 
    $tamino[$s]->printResult(array($kw));
  }
} 

 
print '</div>';

print '<div class="sidebar">';
include("searchbox.html");
print '</div>';
include("footer.html");

?>

</body>
</html>
