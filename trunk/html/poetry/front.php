<?php
include("../config.php");	

print "<html>
  <head> 
    $csslink
    <title>The Great War : Poetry : View</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <base href='$base_url'>
  </head> 
<body> 
"; 

include_once("lib/taminoConnection.class.php");
include_once("lib/mybreadcrumb.php");

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";

$id = $_GET['id'];

$args = array('host' => $tamino_server,
	      'db' => $tamino_db,
	      'coll' => 'poetry',
      	      'basedir' => $basedir,
      	      'debug' => false, 
	     );
$tamino = new taminoConnection($args);
// FIXME: this won't work for foreword...
$query = 'for $a in input()/TEI.2/:text/front/div1
where $a/@id = ' . "'$id'" . '
return $a';

$xsl_file = "poetry.xsl";
$xsl_params = array("mode" => "frontmatter");

$tamino->xquery($query, $pos, $maxdisplay); 

print '<div class="content">';
$tamino->xslTransform($xsl_file, $xsl_params);
$tamino->printResult();
print "</div>";

print '<div class="sidebar">';
include("nav.html");
include("searchbox.php");
print "</div>";

include("footer.html");

?>

</body>
</html>
