<?php

include_once("xmlDbConnection.class.php");

class figDesc {

  var $tamino;
  var $imgpath;

  // components of TEI figure
  var $entity;
  var $title;
  var $description;
  var $ana;
  var $interp;

  // Constructor 
  function figDesc($argArray) {
    // pass host/db/collection settings to tamino object
    $this->tamino = new xmlDbConnection($argArray);

    $this->entity = $argArray['entity'];
    $this->title = $argArray['title'];
    $this->ana = $argArray['ana'];
    $this->imgpath = $argArray['imgpath'];
    $this->description = $argArray['description'];
    $this->interp = array();

    // clean up description input
    $this->cleanInput();
  }


   // generate an xquery for the specified mode, based on current variable settings
  function xquery ($mode) {
    $for = 'for $a in input()/TEI.2/:text/body//figure ';
    $let = 'let $b := input()/TEI.2/:text/back/:div//interpGrp';
    $where = 'where $a/@entity = "' . $this->entity . '"';
    $return = 'return <figure>{$a/@ana}{$a/head}{$a/figDesc}{$a/p} {$b}</figure>';
    //    $replace["modify-figDesc"] = 'do replace $a/figDesc with <figDesc>' . utf8_decode($this->description) . '</figDesc>';
    $replace["modify-figDesc"] = 'do replace $a/figDesc with <figDesc>' . $this->description . '</figDesc>';
    $replace["modify-ana"] = 'do replace $a/@ana with attribute ana {"' . $this->ana . '"}';
 
    switch ($mode):
  case 'getRecord': 
    // xquery to retrieve a record by figure entity
    $query = "$for $let $where $return"; 
    break;
  case 'modify-figDesc':  
    // xquery to modify the description of an existing record 
    $query = "update $for $where $replace[$mode]";
   break;
  case 'modify-ana':  
    // xquery to modify the interp categories of an existing record 
    $query = "update $for $where $replace[$mode]";
   break;
   endswitch;

   return $query;
  }

  // retrieve a record from tamino by entity value
  function taminoGetRecord() {
    $rval = $this->tamino->xquery($this->xquery('getRecord'));
    if (!$rval) {        // xquery succeeded
      $val = $this->tamino->xpath->query("//figure/head");
      if ($val) { $this->title = $val->item(0)->textContent; }
      $val = $this->tamino->xpath->query("//figure/figDesc");
      if ($val) { $this->description = $val->item(0)->textContent; }
      $val = $this->tamino->xpath->query("//figure");
      if ($val) { 
	$this->ana = $val->item(0)->getAttribute("ana"); 
	$this->interp = explode(" ", $this->ana);
      }
    }
  }

  function printDesc () {
    print "<table class='figDesc'><tr><td>";
    if ($this->imgpath) {
      // FIXME: how to link back to view page for postcard?
      print "<img src='" . $this->imgpath . $this->entity . ".jpg'>";
    }
    print "</td><td>";
    print "<b>entity name:</b> " . $this->entity . "<br>\n";
    print "<b>title:</b> " . $this->title . "<br>\n";
    print "<b>ana:</b> " . $this->ana . "<br>\n";
    print "<b>description:</b><br> " . htmlentities($this->description) . "<br>\n";
    print "</td></tr></table>";
  }

  function printform ($action) {
    print "<form action='$action' method='post'>\n";
    print "<input type='hidden' name='title' value='$this->title'>";
    print "<input type='hidden' name='entity' value='$this->entity'>";
    print "<table  class='figDesc'>";
    print "<tr><th colspan='2'>$this->title</th></tr>";
    print "<tr><td valign='middle'>";
    if ($this->imgpath) { print "<img src='" . $this->imgpath . $this->entity . ".jpg'>";  }
    print "</td><td>";
    print "Description:<br>\n";
    print "<textarea cols='75' rows='8' name='desc'>" . utf8_encode($this->description) . "</textarea>\n";
    print "</td></tr></table>";

    if (isset($this->tamino->xml)) {


      //      if ($val) { $this->description = $val->item(0)->textContent; }

      $ig_nlist = $this->tamino->xpath->query("//figure/interpGrp");
      //      $interpGrps = $this->tamino->xml->getBranches("ino:response/xq:result/figure", "interpGrp");
      print "<table class='figDesc'>\n";
      print "<tr><th colspan='" . $ig_nlist->length . "'>Categories</th></tr>\n<tr>";
      // display categories, with the appropriate ones selected
      for ($x = 0; $x < $ig_nlist->length; $x++) {	// loop through nodelist of interpGrps
        //convert interpGrp node to simple xml object
        $ig = simplexml_import_dom($ig_nlist->item($x));
	$cat = $ig["type"];
        print "<td><h4>$cat</h4>\n";
        $cat = str_replace("\w", "-", $cat);	// replace whitespace with - for form input name
	foreach ($ig->interp as $i) {
	  $id = $i["id"];
	  $value = $i["value"];
          if (preg_match("/$id/", $this->ana)) { $status = "checked"; }
          else { $status = ""; }
          print "<input type='checkbox' name='$cat" . '[]' . "' value='$id'$status> $value<br>\n";
	}
        print "</td>\n";
      }
    }
    print "</tr></table>\n";
    print "<input type='reset'>";
    print "<input type='submit' value='Submit'>";
    print "</form>\n";
  }


  // update a record in tamino
  function taminoModify () {
    // modify figure description
    $rval = $this->tamino->xquery($this->xquery('modify-figDesc'));
    if ($rval) {       // tamino error
       print "<p>Failed to update figure description.</p>";
    } else {
       print "<p>Successfully updated figure description.</p>";
    }
    // update interp categories
    $rval = $this->tamino->xquery($this->xquery('modify-ana'));
    if ($rval) {       // tamino error
      print "<p>Failed to update figure categories.</p>";
    } else {
      print "<p>Successfully updated figure categories.</p>";
    }


  }

  // clean up description input
  function cleanInput () {
    $this->description = utf8_decode($this->description);
    // quotes are getting commented by a backslash in submission process; undo this
    $this->description = preg_replace ('/\\\"/', '"', $this->description);
    $this->description = preg_replace ("/\\\'/", "'", $this->description);

  }


}
