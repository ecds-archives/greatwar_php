<?php
include("../../config.php");	

print "<html>
  <head>
    $csslink
    <title>The Great War : Admin : Postcards : Process Comment</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'> 
  </head>
<body>
";

// name of the current file (for links that display same content with different options)
$self = "add_comment.php";

include_once ("lib/figureComment.class.php");
include_once("lib/mybreadcrumb.php");

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";

$entity = $_POST["entity"];
$title = $_POST["title"];
$comment = $_POST["comment"];
$name = $_POST["name"];
$date = $_POST["date"];
$pre = $_POST["preview"];
$post = $_POST["submit"];

if ($pre) { $mode = "preview"; }
else if ($post) { $mode = "submit"; }

print "<div class='content'>\n";
if ($mode == "preview") {
  print "<h3>Preview Postcard Comment</h3>";
} else if ($mode == "submit") {
  print "<h3>Process Postcard Comment</h3>";
}

$args = array('host' => $tamino_server,
	      'db' => $tamino_db,
	      'coll' => $tamino_coll['postcards'],
	      'entity' => $entity,
      	      'title' => $title,
              'comment' => $comment,
              'name' => $name,
	      'date' => $date,
	      'imgpath' => 'http://chaucer.library.emory.edu/wwi/images/thumbnail/',
	      'debug' => true);
$fc = new figureComment($args);

if ($mode == "preview") {
  print "<p>Preview</p>";
  $fc->display();
  print "<hr>";
  print "<p>Edit</p>";
  $fc->printform("add_comment.php");
} else if ($mode == "submit") {
  $fc->display();
  print "DEBUGGING... xmlstring is " . htmlentities($fc->XMLstring()) . "<br>\n";
  $fc->taminoAdd();
}


print "</div>
</body>
</html>";

?>
