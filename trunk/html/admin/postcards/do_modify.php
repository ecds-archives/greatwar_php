<?php
include("../../config.php");	

print "<html>
  <head>
    $csslink
    <title>The Great War : Admin : Postcards : Process Description Change</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'> 
  </head>
<body>
";

// name of the current file (for links that display same content with different options)
$self = "do_modify.php";

include_once ("lib/figDesc.class.php");
include_once("lib/mybreadcrumb.php");

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";

$entity = $_POST["entity"];
$title = $_POST["title"];
$description = $_POST["desc"];
// categories (how not to hard code this?)
// FIXME: these should at least be in one place (config file?)
$interpGrp["nat"] = $_POST["nationality"];
$interpGrp["mil"] = $_POST["military"];
$interpGrp["hf"] = $_POST["homefront"];
$interpGrp["con"] = $_POST["content"];
$interpGrp["img"] = $_POST["image"];
$interpGrp["time"] = $_POST["time-period"];		// FIXME: errors with the space ?

$ana = "";
 foreach ($interpGrp as $ig) {
  for ($i = 0; $ig[$i]; $i++) {
     $ana .= "$ig[$i] ";
  }
}

print '<div class="content"> 
          <h3>Modify Postcard Description</h3>';

$args = array('host' => $tamino_server,
	      'db' => $tamino_db,
	      'coll' => $tamino_coll['postcards'],
	      'entity' => $entity,
      	      'title' => $title,
	      'ana' => $ana,
              'description' => $description,
	      'imgpath' => 'http://chaucer.library.emory.edu/wwi/images/thumbnail/',
	      'debug' => false);
$desc = new figDesc($args);

$desc->printDesc();
$desc->taminoModify();


print "</div>
</body>
</html>";

?>
