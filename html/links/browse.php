<html>
  <head>
<!--    <link rel="stylesheet" type="text/css" href="../wwi.css"> -->
    <title>The Great War : Links : Browse</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--    <base href="http://reagan.library.emory.edu/rebecca/wwiweb/"> -->
  </head>
<body>

<?php
include("../config.php");	

include_once ("lib/alinkCollection.class.php");
include_once("lib/mybreadcrumb.php");

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";

print '<div class="content">'; 
$sort = $_GET["sort"]; // options: title|contrib|date
$subject = $_GET['subj'];


$args = array('host' => "vip.library.emory.edu",
	      'db' => "WW1",
	      'coll' => 'links',
	      'limit_subject' => $subject[0],
	      'sort' => $sort);

$linkset = new aLinkCollection($args);

$linkset->printSortOptions("browse.php");
$linkset->printSubjectOptions("browse.php", $subject);
print "<hr width='50%'>";
$linkset->printSummary();


print "</div>";

print '<div class="sidebar">';
include("nav.html");
include("searchbox.html");
print "</div>";

include("footer.html");


?>

</body>
</html>

