<?php
include("../config.php");	

print "<html>
  <head>
    $csslink
    <title>The Great War : Poetry : Contents</title>
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
	      'coll' => 'poetry',
       	      'basedir' => $basedir,
      	      'debug' => false,
	      );
$tamino = new taminoConnection($args);
$query = 'declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction"
declare namespace xf="http://www.w3.org/2002/08/xquery-functions"
for $a in input()/TEI.2
let $docname := tf:getDocname(xf:root($a))
let $titlestmt := $a/teiHeader/fileDesc/titleStmt
let $bibl := $a/teiHeader/fileDesc/sourceDesc/bibl
where $docname = ' . "'$id'" . '
return <div>
<teiHeader>
<fileDesc> {$titlestmt}
<sourceDesc>
  <bibl>{$bibl}</bibl>
</sourceDesc>
</fileDesc>
</teiHeader>
{ for $div1 in $a/:text//div1 
   return <div1> {$div1/@id} {$div1/@n} {$div1/@type}
{$div1/head} {$div1/p[1]}
    {for $div2 in $div1/div2 
      return <div2> {$div2/@id} {$div2/@n} {$div2/@type} 
           {$div2/byline}
        </div2> }
   </div1> }
</div>';

$xsl_file = "poetry.xsl";
$xsl_params = array("mode" => "contents");

$rval = $tamino->xquery($query, $pos, $maxdisplay); 
if ($rval) {       // tamino Error code (0 = success) 
  print "<p>Error: failed to retrieve contents.<br>";
  print "(Tamino error code $rval)</p>";
  exit();
}

print '<div class="content">'; 
$tamino->xslTransform($xsl_file, $xsl_params);
$tamino->printResult();
print "</div>";

print '<div class="sidebar">';
include("nav.html");
include("searchbox.html");
print "</div>";

include("footer.html");

?>

</body>
</html>
