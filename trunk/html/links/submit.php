<html>
  <head>
<!--    <link rel="stylesheet" type="text/css" href="../wwi.css"> -->
    <title>The Great War : Links : Submit a New Link</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--    <base href="http://reagan.library.emory.edu/rebecca/wwiweb/"> -->
  </head>
<body>

<?php
include("../config.php");	
include_once ("lib/alinkRecord.class.php");
include_once("lib/mybreadcrumb.php");

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";

print '<div class="content">'; 

$args = array('host' => "vip.library.emory.edu",
	      'db' => "WW1",
	      'coll' => 'links',
	      'debug' => false);
$link = new aLinkRecord($args);

print "<h2>Submit a new link</h2>";

$link->printHTMLForm("add");

print "</div>";

print '<div class="sidebar">';
include("nav.html");
include("searchbox.html");
print "</div>";

include("footer.html");


?>

</body>
</html>

