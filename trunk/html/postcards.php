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

$query ="for $a in input()/TEI.2/:text/body/p/figure
return $a";
$xsl_file = "xsl/figures.xsl";

include("header.html");

print '<div class="content">';

$rval = $tamino->xquery($query);
if ($rval) {       // tamino Error code (0 = success)
  print "<p>Error: failed to retrieve contents.<br>";
  print "(Tamino error code $rval)</p>";
  exit();
}

$tamino->xslTransform($xsl_file);
$tamino->printResult($myterms);


include("footer.html");

?>

</body>
</html>