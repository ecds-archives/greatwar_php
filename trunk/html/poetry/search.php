<?php
include("../config.php");	

print "
<html>
  <head>
    $csslink
    <title>The Great War : Poetry : Search Results</title> 
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <base href='$base_url'> 
  </head> 
<body>
";

include_once("lib/taminoConnection.class.php");
include_once("lib/mybreadcrumb.php");
$args = array('host' => $tamino_server,
	      'db' => $tamino_db,
	      'coll' => 'poetry',
      	      'basedir' => $basedir,
	      'debug' => false,
	     );
$tamino = new taminoConnection($args);

// search terms
$kw = $_GET["keyword"];
$title = $_GET["title"];
$author = $_GET["author"];
$date = $_GET["date"];
$terms = explode(" ", $kw);    // there can be multiple search terms, divided by spaces
// construct the query, based on which terms are set
$declare ='declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction" ';
$for = ' for $a in input()/TEI.2/:text/body/div1/div2 ';
$where = 'where $a/@type="poem" ';
if ($terms[0]) {
  foreach ($terms as $t) {  $where .= " and tf:containsText(\$a, '$t') "; }
}
if ($title) { $where .= " and tf:containsText(\$a/head, '$title') "; }
// note: for now, must look for author field in two different places
if ($author) { $where .= " and (tf:containsText(\$a/byline, '$author') or tf:containsText(\$a/../docAuthor, '$author')) " ; }
if ($date) { $where .= " and \$a/../docDate = '$date' "; }
$return = ' return <div2> {$a/@type} {$a/@id} {$a/@n} {$a/byline} {$a/../docAuthor} ';
// if using keyword search, retrieve matching lines also
if ($terms[0]) {
   $return .= " {for \$l in \$a//l where ";
   foreach ($terms as $t) {
     if ($t != $terms[0]) { $query .= " or "; }
     $return .= " tf:containsText(\$l, '$t') ";
   }
   $return .= " return \$l }  ";
}
  //foreach ($terms as $t) {  $query .= " {for \$l in \$a//l where tf:containsText(\$l, '$t') return \$l }  "; }
//if ($kw) { $query .= " {for \$l in \$a//l where tf:containsText(\$l, '$kw') return \$l } "; }
$return .= ' <linecount> { count($a//l) } </linecount> ' . 
" <total> {count($for $where return \$a)}</total> </div2> sort by (@n) "; 
$query = "$declare $for $where $return";


$xsl_params = array("mode" => "search");
$xsl_file = "poetry.xsl";

$pos = 1;
$maxdisplay = 50;

$rval = $tamino->xquery($query, $pos, $maxdisplay); 
if ($rval) {       // tamino Error code (0 = success) 
  print "<p>Error: failed to retrieve contents.<br>";
  print "(Tamino error code $rval)</p>";
  exit();
}
$tamino->getXQueryCursor();

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";


print '<div class="content">'; 

print "<p>Found " . $tamino->count . " matches</p>";
$tamino->xslTransform($xsl_file, $xsl_params);
$tamino->printResult($terms);
print '</div>';

print '<div class="sidebar">';
include("nav.html");
include("searchbox.html");
print '</div>';

include("footer.html");

?>

</body>
</html>
