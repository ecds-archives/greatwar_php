<?php

include_once("taminoConnection.class.php");

class figureComment {

  var $tamino;
  var $imgpath;

  // relevant figure components 
  var $entity;
  var $title;
  var $comment;


  // Constructor 
  function figureComment($argArray) {
    // pass host/db/collection settings to tamino object
    $this->tamino = new taminoConnection($argArray);
    $this->imgpath = $argArray['imgpath'];
    
    $this->entity = $argArray['entity'];
    $this->title = $argArray['title'];
    $this->comment = $argArray['comment'];

    $this->cleanInput();
  }


   // generate an xquery for the specified mode, based on current variable settings
  function xquery ($mode) {
    $for = 'for $a in input()/TEI.2/:text/body//figure ';
    $where = 'where $a/@entity = "' . $this->entity . '"';
    $return = 'return <figure>{$a/head}</figure>';
 
    switch ($mode):
  case 'getRecord': 
    // xquery to retrieve a record by figure entity
    $query = "$for $where $return"; 
    break;
   break;
   endswitch;

   return $query;
  }

  // retrieve a record from tamino by entity value
  function taminoGetRecord() {
    $rval = $this->tamino->xquery($this->xquery('getRecord'));
    if ($rval) {
      print "<p>figDesc Error: failed to retrieve figure details from Tamino.<br>";
      print "(Tamino error code $rval)</p>";
      $this->description = "";		// load failed; set to null string
    } else {            // xquery succeeded
      $val = $this->tamino->xml->getTagContent("ino:response/xq:result/figure/head");
      if ($val) { $this->title = $val; }
    }
  }

  function display () {
    print "<table class='figDesc'><tr><td>";
    if ($this->imgpath) {
      print "<img src='" . $this->imgpath . $this->entity . ".jpg'>";
    }
    print "</td><td>";
    print "<b>entity name:</b> " . $this->entity . "<br>\n";
    print "<b>title:</b> " . $this->title . "<br>\n";
    print "<b>comments:</b><br> " . htmlentities($this->comments) . "<br>\n";
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
    print "<textarea cols='75' rows='8' name='desc'>" . $this->description . "</textarea>\n";
    print "</td></tr></table>";

    $interpGrps = $this->tamino->xml->getBranches("ino:response/xq:result/figure", "interpGrp");
    print "<table class='figDesc'>\n";
    print "<tr><th colspan='" . count($interpGrps) . "'>Categories</th></tr>\n<tr>";
    // display categories, with the appropriate ones selected
    foreach ($interpGrps as $ig) {
      $cat = $ig->getTagAttribute("type");
      print "<td><h4>$cat</h4>\n";
      $cat = str_replace("\w", "-", $cat);	// replace whitespace with - for form input name
      $interp = $ig->getBranches();
      if ($interp) {
        foreach ($interp as $i) {
          $id = $i->getTagAttribute("id");
	  $value = $i->getTagAttribute("value");
	  if (preg_match("/$id/", $this->ana)) { $status = "checked"; }
	  else { $status = ""; }
  	  print "<input type='checkbox' name='$cat" . '[]' . "' value='$id'$status> $value<br>\n";
        }
      }
      print "</td>\n";
    }
    print "</tr></table>\n";
    
    print "<input type='reset'>";
    print "<input type='submit' value='Submit'>";
    print "</form>\n";
  }


  // update a record in tamino
  function taminoModify () {
    // update interp categories
    $rval = $this->tamino->xquery($this->xquery('modify-ana'));
    if ($rval) {       // tamino error
      print "<p>Failed to update figure categories.</p>";
    } else {
      print "<p>Successfully updated figure categories.</p>";
    }
    // modify figure description
    $rval = $this->tamino->xquery($this->xquery('modify-figDesc'));
    if ($rval) {       // tamino error
      print "<p>Failed to update figure description.</p>";
    } else {
      print "<p>Successfully updated figure description.</p>";
    }

  }

  // clean up description input
  function cleanInput () {
    // quotes are getting commented by a backslash in submission process; undo this
    $this->comment = preg_replace ('/\\\"/', '"', $this->comment);
    $this->comment = preg_replace ("/\\\'/", "'", $this->comment);

  }


}
