<html>
  <head>
    <link rel="stylesheet" type="text/css" href="../wwi.css">
    <title>The Great War : Links : Approve Links</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <base href="http://reagan.library.emory.edu/rebecca/wwiweb/">
  </head>
<body>

<?php
// run everything as if one directory up
chdir("..");
include_once ("lib/alinkCollection.class.php");
include_once("lib/mybreadcrumb.php");

include("header.html");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";



$args = array('host' => "vip.library.emory.edu",
	      'db' => "WW1",
	      'coll' => 'links',
	      'limit_subject' => $subject[0],
	      'sort' => $sort);

$linkset = new aLinkCollection($args);

print '<div class="content">'; 
$linkset->printApprovalForm("links/do_approve.php");
print "</div>";

print '<div class="sidebar">';
include("searchbox.html");
print "</div>";

include("footer.html");


?>

</body>
</html>

