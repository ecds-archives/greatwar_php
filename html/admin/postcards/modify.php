<?php
include("../../config.php");	

print "<html>
  <head>
    $csslink
    <title>The Great War : Admin : Postcards : Modify Description</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'> 
  </head>
<body>
";

// name of the current file (for links that display same content with different options)
$self = "modify.php";

include_once ("lib/figDesc.class.php");
include_once("lib/mybreadcrumb.php");

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";

$entity = $_GET["entity"]; 

print '<div class="content"> 
          <h3>Modify Postcard Description</h3>';

$args = array('host' => $tamino_server,
	      'db' => $tamino_db,
	      'coll' => $tamino_coll['postcards'],
	      'entity' => $entity,
	      'imgpath' => 'http://chaucer.library.emory.edu/wwi/images/thumbnail/',
	      'debug' => false);
$desc = new figDesc($args);

$desc->taminoGetRecord();

$desc->printform("do_modify.php");

print "</div>
</body>
</html>";

?>
