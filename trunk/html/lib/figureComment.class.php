<?php

include_once("taminoConnection.class.php");

class figureComment {

  var $tamino;
  var $imgpath;

  // relevant figure components 
  var $entity;
  var $title;
  var $comment;
  var $name;
  var $date;


  // Constructor 
  function figureComment($argArray) {
    // pass host/db/collection settings to tamino object
    $this->tamino = new taminoConnection($argArray);
    $this->imgpath = $argArray['imgpath'];
    
    $this->entity = $argArray['entity'];
    $this->title = $argArray['title'];
    $this->comment = $argArray['comment'];
    $this->name = $argArray['name'];
    $this->date = $argArray['date'];

    /* If date is not set, initialize date value to today.  
       Format is: 2004-04-09 4:13:00 PM */
    if (!($this->date)) {
      $this->date = date("Y-m-d g:i:s A");
    }

    $this->cleanInput();
  }


   // generate an xquery for the specified mode, based on current variable settings
  function xquery ($mode) {
    $declare = 'declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction"
		declare namespace xf="http://www.w3.org/2002/08/xquery-functions"';
    $for = 'for $a in input()/TEI.2/:text/body//figure ';
    $where = 'where $a/@entity = "' . $this->entity . '"';
    $return = 'return <figure>{$a/head}</figure>';
    $insert = 'do insert ' . $this->XMLstring() . ' preceding $a/figDesc';
    $user['for'] = 'for $a in input()/TEI.2/:text/body/div1';
    $user['let'] = 'let $docname   := tf:getDocname(xf:root($a))';
    $user['where'] = 'where $docname = "postcard-comments.xml"';
    $user['insert'] = 'do insert ' . $this->XMLstring() .  'following $a/p[position() = last()]';
 
    switch ($mode):
  case 'getRecord': 
    // xquery to retrieve a record by figure entity
    $query = "$for $where $return"; 
    break;
  case 'add':
    // xquery to add a new comment to a figure
    $query = "update $for $where $insert";
    break;
  case 'submit':
    //xquery to submit a user (non-authorized) comment to holding area
    $query = "$declare update $let $where $insert";
   endswitch;

   /*
This xquery works for submitting a user comment to the holding-area file!
(FIXME: maybe get rid of the div1 in the skeleton file? is it necessary?)

declare namespace tf="http://namespaces.softwareag.com/tamino/TaminoFunction"
declare namespace xf="http://www.w3.org/2002/08/xquery-functions"
update for $a in input()/TEI.2/:text/body/div1
let $docname   := tf:getDocname(xf:root($a))
where $docname = 'postcard-comments.xml'
do insert <p id='rlsktest'><date>10-01-2004</date><name>rlsk</name>testing xquery for user submission</p> following $a/p[position() = last()]
   */

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
    print "<table class='comment'>";
    print "<tr><th>entity name:</th><td>" . $this->entity . "</td></tr>\n";
    print "<tr><th>title:</th><td> " . $this->title . "</td></tr>\n";
    print "<tr><th>comments:</th><td> " . htmlentities($this->comment) . "</td></tr>\n";
    print "<tr><th>name:</th><td> " . $this->name . "</td></tr>\n";
    print "<tr><th>date:</th><td> " . $this->date . "</td></tr>\n";
    print "</table>";
    print "</table>";
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
    print "Comments:<br>\n";
    print "<textarea cols='80' rows='15' name='comment'>" . $this->comment . "</textarea>\n";
    print "<tr><td>Name:</td>\n";
    print "<td><input type='text' name='name' size='80' value='$this->name'></td></tr>\n";
    print "<tr><td>Date:</td>\n";
    print "<td><input type='text' name='date' size='80' value='$this->date'></td></tr>\n";
    print "</table>";
    print "<input type='reset'>";
    print "<input type='submit' name='preview' value='Preview'>";
    print "<input type='submit' name='submit' value='Submit'>";
    print "</form>\n";
  }


  // add a comment to a figure in tamino
  function taminoAdd () {
    $rval = $this->tamino->xquery($this->xquery('add'));
    if ($rval) {       // tamino error
      print "<p>Failed to add comment to figure.</p>";
    } else {
      print "<p>Successfully added comment to figure.</p>";
    }
  }

  // clean up description input
  function cleanInput () {
    // quotes are getting commented by a backslash in submission process; undo this
    $this->comment = preg_replace ('/\\\"/', '"', $this->comment);	// replace \" with "
    $this->comment = preg_replace ("/\\\'/", "'", $this->comment);      // replace \' with '

  }

  function XMLstring () {
    $myid = $this->name . "-" . $this->date;
    $myid = preg_replace ('/ /', '_', $myid);	// replace space with underscore
    $myid = preg_replace ('/:/', '', $myid);	// remove colons
    $xmlstring = "<p id='$myid' n='comment'>
      <name>$this->name</name>
      <date>$this->date</date>
      $this->comment
      </p>";
    return $xmlstring;
  }


}
