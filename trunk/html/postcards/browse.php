<html>
  <head>
    <link rel="stylesheet" type="text/css" href="../wwi.css">
    <title>The Great War : Postcards : Thumbnails</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <base href="http://reagan.library.emory.edu/rebecca/wwiweb/">
  </head>
<body>

<?php
// run everything as if one directory up
chdir("..");
include_once("lib/taminoConnection.class.php");
$args = array('host' => "vip.library.emory.edu",
	      'db' => "WW1",
	      'debug' => true,
	      'coll' => 'postcards');
$tamino = new taminoConnection($args);

$cat = $_GET["cat"];		// optionally limit postcards by category
$desc = $_GET["desc"];

($desc == "yes") ? $mode = "thumbdesc" : $mode = "thumbnail";
$xsl_params = array("mode" => $mode);

/*
$query ='declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction"
for $a in input()/TEI.2/:text/body/p/figure '; 
if ($cat) { $query .= "where tf:containsText(\$a/@ana, '$cat') "; } 
$query .= 'return $a';
*/

$query ='declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction"';
$query .= '<div> { for $a in input()/TEI.2/:text/body/p/figure ';   
if ($cat) { $query .= "where tf:containsText(\$a/@ana, '$cat') "; }    
$query .= 'return $a } ';
$query .= '{ for $b in input()/TEI.2/:text/back/:div//interpGrp return $b }</div>'; 


// xquery & xsl for category labels 
$cat_query = 'for $a in input()/TEI.2/:text/back/:div//interpGrp 
return $a'; 
$cat_xsl = "interp.xsl"; 

include("header.html"); 


print '<p class="breadcrumbs"> 
<a href="index.html">Home</a> &gt; <a href="postcards/">Postcards</a> 
	  &gt; Browse &gt; Thumbnails  
</p>';  


print '<div class="content">'; 

// need to add an option to use cursor... 
$rval = $tamino->xquery($query); 
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

print '<div class="categories">';
$rval = $tamino->xquery($cat_query);
if ($rval) {       // tamino Error code (0 = success)
  print "<p>Error: failed to retrieve contents.<br>";
  print "(Tamino error code $rval)</p>";
  exit();
}
$tamino->xslTransform($cat_xsl);
$tamino->printResult();

print '</div>';

print '</div>';

include("footer.html");

?>

</body>
</html>
