<?php
include("config.php");	

print "<html>
  <head> 
    $csslink
    <title>The Great War : Keyword Search</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <base href='$base_url'>
  </head> 
<body> 
"; 

include_once("lib/taminoConnection.class.php");
include_once("lib/alinkCollection.class.php");
include_once("lib/mybreadcrumb.php");

$kw = $_GET["keyword"];			        // term to search for
$dosearch["poetry"] = $_GET["poetry"];		// which searches to do
$dosearch["postcards"] = $_GET["postcards"];
$dosearch["links"] = $_GET["links"];

// base settings used by all three searches
$args = array('host' => $tamino_server,
	      'db' => $tamino_db,
	      'debug' => false);
$search = array("postcards", "poetry", "links");
foreach ($search as $s) {
  $args["coll"] = $tamino_coll[$s];	// tamino collection
  if ($s == "links") {		// links needs a different arg & is a different object type
    $args["keyword"] = $kw;
    $db[$s] = new aLinkCollection($args);    
  } else {
    $db[$s] =  new taminoConnection($args);
  }
}

// name of this page, and link back to current instance
$self = "searchall.php";
$selflink = "$self?keyword=$kw&poetry=" . $dosearch['poetry']. "&postcards=" . $dosearch['postcards'] . "&links=" . $dosearch['links'];

// xqueries for postcards & poetry (links works differently)
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
  if ($s == "links") next;
  $query[$s] ="$declare " . $for[$s]  . " $where " . $return[$s];  
}

$xsl_file["postcards"] = "figures.xsl";
$xsl_params["postcards"] = array("mode" => "thumbnail");
$xsl_file["poetry"] = "poetry.xsl";
$xsl_params["poetry"] = array("mode" => "search");

include("header.php");  
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>"; 

print '<div class="content">'; 

print "<p align='center'>Results for keyword <span class='term1'>$kw</span><ul class='horiz'>";

$first = true;
foreach ($search as $s) {
  if ($dosearch[$s] == "on") {
    if ($s != "links" ) { $db[$s]->xquery($query[$s]); }
    $li = "<li class='horiz'";
    if ($first) { $li .= " id='first' "; $first = false; }
    $li .= ">";
    print "$li<a href='$selflink#$s'>" . ucfirst($s) . "</a>: " . $db[$s]->count . "</a> match";
    if ($db[$s]->count != 1) { print "es"; }
    print "</li>";
  }
}
print "</ul></p>";

foreach ($search as $s) {
  if (($dosearch[$s] == "on") && ($db[$s]->count > 0)) {
    print "<hr class='floatright'><p><a name='$s'>" . ucfirst($s) . "</a></p>";
    if ($s == "links") {
      $db[$s]->printSummary();
    } else {
      $db[$s]->xslTransform($xsl_file[$s], $xsl_params[$s]); 
      $db[$s]->printResult(array($kw));
    }
  }
} 

print '</div>';

print '<div class="sidebar">';
include("searchbox.php");
print '</div>';
include("footer.html");

?>

</body>
</html>
