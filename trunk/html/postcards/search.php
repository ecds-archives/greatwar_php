<html>
  <head>
<!--    <link rel="stylesheet" type="text/css" href="../wwi.css"> -->
    <title>The Great War : Postcards : Search Results</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--    <base href="http://reagan.library.emory.edu/rebecca/wwiweb/"> -->
  </head>
<body>

<?php
include("../config.php");	
include_once("lib/taminoConnection.class.php");
include_once("lib/mybreadcrumb.php");
$args = array('host' => "vip.library.emory.edu",
	      'db' => "WW1",
	      'debug' => false,
	      'coll' => 'postcards');
$tamino = new taminoConnection($args);

// search terms
$title = $_GET["title"];
$figdesc = $_GET["figdesc"];
// categories (how not to hard code this?)
$nat = $_GET["nationality"];
$mil = $_GET["military"];
$hf = $_GET["homefront"];
$con = $_GET["content"];
$time = $_GET["time-period"];		// FIXME: errors with the space
$category = array($nat, $mil, $hf, $con);

$desc = "yes";		// for now - default to showing descriptions

($desc == "yes") ? $mode = "thumbdesc" : $mode = "thumbnail";
$xsl_params = array("mode" => $mode);
$cat_params = array("desc" => $desc);

/*
$query ='declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction"
for $a in input()/TEI.2/:text/body/p/figure '; 
if ($cat) { $query .= "where tf:containsText(\$a/@ana, '$cat') "; } 
$query .= 'return $a';
*/

// FIXME: this query won't allow for using the cursor to limit # of figures displayed
/* $query ='declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction"';
$query .= '<div> { for $a in input()/TEI.2/:text/body/p/figure ';   
if ($cat) { $query .= "where tf:containsText(\$a/@ana, '$cat') "; }    
$query .= 'return  $a } ';
$query .= '{ for $b in input()/TEI.2/:text/back/:div//interpGrp return $b }</div>';
*/ 

$firstcond = false;  // false =  this is the first condition; true = print 'and'

$query ='declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction" ';
$query .= 'for $a in input()/TEI.2/:text/body/p/figure ';
$query .= 'let $b := input()/TEI.2/:text/back/:div//interpGrp ';
$query .= 'where ';
if ($title) {
  $query .= " tf:containsText(\$a/head, '$title') ";
  ($desc == "yes") ? $mode = "thumbdesc" : $mode = "thumbnail";
  $firstcond = true;
}
if ($figdesc) {
  if ($firstcond) { $query .= " and "; }
  $query .= " tf:containsText(\$a/figDesc, '$figdesc') ";
  $firstcond = true;
}
foreach ($category as $c) {
  if ($c != "null") {  	// if null, skip selection
    if ($firstcond) { $query .= " and "; }
    $query .= "tf:containsText(\$a/@ana, '$c') ";
    $firstcond = true;
  }
}
$query .= 'return <div> { $a } {$b} </div>';
// FIXME: how to get a count?


$xsl_file = "figures.xsl";

$rval = $tamino->xquery($query);   // , $pos, $maxdisplay); 
if ($rval) {       // tamino Error code (0 = success) 
  print "<p>Error: failed to retrieve contents.<br>";
  print "(Tamino error code $rval)</p>";
  exit();
}
$tamino->getXQueryCursor();


include("header.php");

print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";


/*
print '<p class="breadcrumbs"> 
<a href="index.html">Home</a> &gt; <a href="postcards/">Postcards</a> 
	  &gt; Browse &gt; Thumbnails  
</p>';
*/


// print "<p>Displaying postcards " . $tamino->position . " - " . ($tamino->quantity + $tamino->position - 1) . " of " . $tamino->count . "</p>";

// links to more results
if ($tamino->count > $maxdisplay) {
  $result_links .= 'More postcards:';
  for ($i = 1; $i <= $tamino->count; $i += $maxdisplay) {
    if ($i == $pos) { next; }	// skip current set of results
    else {
      // construct the url, maintaining all parameters
      $url = "browse.php?";
      if ($desc) { $url .= "&desc=$desc"; }
      if ($cat) { $url .= "&cat=$cat"; }
      // now add the key piece: the new position
      $url .= "&position=$i";
      $result_links .= " <a href='$url'>";
      $j = min($tamino->count, ($i + $maxdisplay - 1));
      // special case-- last set only has one result
      if ($i == $j) { $result_links .= "$i"; }
      else { $result_links .= "$i - $j"; }
      $result_links .= "</a> ";
    }
  }
}

print $result_links;


print '<div class="content">'; 

chdir("..");
$tamino->xslTransform($xsl_file, $xsl_params);
chdir("postcards");
//$tamino->xslTransform($xsl_file);
$tamino->printResult();

print '</div>';

print '<div class="sidebar">';
include("nav.html");
include("searchbox.html");
print '</div>';

include("footer.html");

?>

</body>
</html>
