<html>
  <head>
    <link rel="stylesheet" type="text/css" href="wwi.css">
    <title>The Great War : Postcards : Thumbnails</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  </head>
<body>

<?php
include_once("lib/taminoConnection.class.php");
$args = array('host' => "vip.library.emory.edu",
	      'db' => "WW1",
	      'debug' => false,
	      'coll' => 'postcards');
$tamino = new taminoConnection($args);

$query ='for $a in input()/TEI.2/:text/body/p/figure
return $a';
$xsl_file = "figures.xsl";

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

$tamino->xslTransform($xsl_file);
$tamino->printResult($myterms);

print '</div>';

print '<div class="sidebar">';
include("searchbox.html");
print '</div>';

include("footer.html");

?>

</body>
</html>