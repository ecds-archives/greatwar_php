<html>
  <head>
<!--    <link rel="stylesheet" type="text/css" href="../wwi.css"> -->
    <title>The Great War : Links : Admin : Modify Link</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--    <base href="http://reagan.library.emory.edu/rebecca/wwiweb/"> -->
  </head>
<body>

<?php

// run everything as if two directories up
include("../../config.php");
include_once ("lib/alinkCollection.class.php");
include_once("lib/mybreadcrumb.php");

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";

$id = $_GET["id"];

$args = array('host' => "vip.library.emory.edu",
	      'db' => "WW1",
	      'coll' => 'links',
	      'id' => $id);
$link = new aLinkRecord($args);
$link->taminoGetRecord();

print '<div class="content">
<h3>Modify an existing record</h3>';

$link->printHTMLForm("modify");

print '</div>';

print '<div class="sidebar">';
include("nav.html");
include("searchbox.php");
print "</div>";

include("footer.html");

?>

</body>
</html>

