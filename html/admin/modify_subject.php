<html>
  <head>
<!--    <link rel="stylesheet" type="text/css" href="../wwi.css"> -->
    <title>The Great War : Links : Admin : Manage Subjects</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--    <base href="http://reagan.library.emory.edu/rebecca/wwiweb/"> -->
  </head>
<body>

<?php
// run everything as if two directories up
include("../../config.php");
include_once ("lib/subjectList.class.php");
include_once("lib/mybreadcrumb.php");

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";

$myargs = array('host' => "vip.library.emory.edu",
		'db' => "WW1",
		'coll' => 'links');
$subject_list = new subjectList($myargs);

print '<div class="content">
<h3>Modifying Subjects</h3>';

print "<hr>";


$subject = $_GET["subj"];
if (count($subject) > 1) {
  // only if there are multiple subjects (i.e., when removing several)
  foreach ($subject as $s) {
    $s = htmlentities($s);
 }
}
$mode = $_GET["mode"];  // add or del

switch ($mode):
  case 'add':  
  // Only add the subject if it is not already in the list
    if ( $subject_list->isSubject($subject)) {
      print "<p>Error: Subject <b>$subject</b> is already in the list.  Not adding.</p>";
    } else {
      $subject_list->taminoAdd($subject); 
    }
    break;
  case 'del':  
   // it is possible to have multiple subjects selected for deletion
    foreach ($subject as $s) {
      $subject_list->taminoDelete($s); 
    }
    break;
endswitch;

// update subject list from Tamino
$subject_list->taminoGetSubjects();
print "<hr><h3>Newly updated subject heading list</h3>\n";
$subject_list->printHTMLList();


print "</div>";

print '<div class="sidebar">';
include("nav.html");
include("searchbox.php");
print "</div>";

include("footer.html");


?>

</body>
</html>

