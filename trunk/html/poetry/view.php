<?php
include("../config.php");	

print "<html>
  <head> 
    $csslink
    <title>The Great War : Poetry : View</title> 
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'> 
    <base href='$base_url'> 
  </head> 
<body> 
";

include_once("lib/taminoConnection.class.php");
include_once("lib/mybreadcrumb.php");

include("header.php");
print "<p class='breadcrumbs'>" . $breadcrumb->show_breadcrumb() . "</p>";

$id = $_GET['id'];

$args = array('host' => $tamino_server,
	      'db' => $tamino_db,
      	      'coll' => $tamino_coll['poetry'],
      	      'basedir' => $basedir,
	      'debug' => false,
	     );
$tamino = new taminoConnection($args);
$query = 'declare namespace xf="http://www.w3.org/2002/08/xquery-functions"
declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction"
for $a in input()/TEI.2/:text/body/div1/div2
let $root := xf:root($a)
let $docname := tf:getDocname($root)
let $titlestmt := $root/TEI.2/teiHeader/fileDesc/titleStmt
let $bibl := $root/TEI.2/teiHeader/fileDesc/sourceDesc/bibl
where $a/@id = ' . "'$id'" . '
return <div id="{$docname}">
<teiHeader>
<fileDesc> {$titlestmt}
<sourceDesc>
  <bibl>{$bibl}</bibl>
</sourceDesc>
</fileDesc>
</teiHeader>
{$a}</div>';

$xsl_file = "poetry.xsl";
$xsl_params = array("mode" => "poem");

$tamino->xquery($query, $pos, $maxdisplay); 

print '<div class="content">'; 
$tamino->xslTransform($xsl_file, $xsl_params);
$tamino->printResult();
print "</div>";

print '<div class="sidebar">';
include("nav.html");
include("searchbox.php");
print "</div>";

include("footer.html");

?>

</body>
</html>
