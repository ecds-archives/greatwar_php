<?php
include("../config.php");	

print "<html>
  <head> 
    $csslink
    <title>The Great War : Postcards : Search Results</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <base href='$base_url'>
  </head> 
<body> 
"; 

include_once("lib/taminoConnection.class.php");
include_once("lib/mybreadcrumb.php");

$args = array('host' => $tamino_server,
	      'db' => $tamino_db,
	      'coll' => 'postcards',
	      'basedir' => $basedir,
	      'debug' => false,
	      );
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
$pos = $_GET["position"];
if (isset($pos)) {} else {$pos = 1;}

$desc = "yes";		// for now - default to showing descriptions

($desc == "yes") ? $mode = "thumbdesc" : $mode = "thumbnail";
$xsl_params = array("mode" => $mode);
$cat_params = array("desc" => $desc);

$firstcond = false;  // false =  this is the first condition; true = print 'and'

$declare ='declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction" ';
$for = 'for $a in input()/TEI.2/:text/body/p/figure ';
$let = 'let $b := input()/TEI.2/:text/back/:div//interpGrp ';
$where = 'where ';
if ($title) {
  $where .= " tf:containsText(\$a/head, '$title') ";
  ($desc == "yes") ? $mode = "thumbdesc" : $mode = "thumbnail";
  $firstcond = true;
}
if ($figdesc) {
  if ($firstcond) { $where .= " and "; }
  $where .= " tf:containsText(\$a/figDesc, '$figdesc') ";
  $firstcond = true;
}
foreach ($category as $c) {
  if ($c != "null") {  	// if null, skip selection
    if ($firstcond) { $where .= " and "; }
    $where .= "tf:containsText(\$a/@ana, '$c') ";
    $firstcond = true;
  }
}
$return .= 'return <div> { $a } {$b} ' . "<total>{ count($for $where return \$a) }</total> </div>";
$query = "$declare $for $let $where $return";


$xsl_file = "figures.xsl";


$maxdisplay = 10;
$rval = $tamino->xquery($query, $pos, $maxdisplay); 
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

print "<p>Displaying postcard";
($tamino->quantity > 1) ? print "s " : print " ";
print $tamino->position;
if ($tamino->position != $tamino->quantity) { print " - " . ($tamino->quantity + $tamino->position - 1); }
print " of " . $tamino->count . "</p>"; 

// links to more results
if ($tamino->count > $maxdisplay) {
  $result_links .= 'More postcards:';
  for ($i = 1; $i <= $tamino->count; $i += $maxdisplay) {
    if ($i == $pos) { next; }	// skip current set of results
    else {
      // construct the url, maintaining all parameters
      $url = "postcards/browse.php?";
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
