<?php

include_once("taminoConnection.class.php");
include_once("subjectList.class.php");
include_once("linkRecord.class.php");

class linkCollection {
  var $tamino;
  var $subject;
  var $link;
  var $ids;
  var $sort;         // sort linkRecords by: title|contrib|date 
  var $sort_opts;
  var $pretty_sort_opts;
  var $sortfield;

  var $limit_subject;

  var $dcns;
  var $xquery;
  
  function linkCollection ($argArray) {
    // pass host/db/collection settings to member objects
    $this->tamino = new taminoConnection($argArray);
    $this->subject = new subjectList($argArray);

    $this->sort_opts = array("title", "date","contrib");
    $this->pretty_sort_opts['date'] = "Date Submitted";
    $this->pretty_sort_opts['title'] = "Title";
    $this->pretty_sort_opts['contrib'] = "Contributor";

    $this->sortfield["date"] = "dc:date";
    $this->sortfield["title"] = "dc:title";
    $this->sortfield["contrib"] = "dc:contributor";
    $this->sort = $argArray['sort'];
    $this->limit_subject = $argArray['limit_subject'];

    if ($this->sort == '') { $this->sort = "title"; }  // default

    // Dublin Core namespace
    $this->dcns = "dc='http://purl.org/dc/elements/1.1/'";
    // xquery to retrieve all linkRecord identifiers from tamino
    $this->xquery = "declare namespace $this->dcns" . 
      'for $b in input()/linkCollection/linkRecord/@id';
    if (isset($this->limit_subject) && ($this->limit_subject != '') 
	&& ($this->limit_subject != 'all')) {
      $this->xquery .= " where \$b/../dc:subject = '$this->limit_subject' ";
    }
    $this->xquery .= ' return $b ';
    $this->xquery .= " sort by (../" . $this->sortfield[$this->sort] . ")"; 
    // return \$b sort by (../" . $this->sortfield[$this->sort] . ")";  

    // initialize id list from Tamino  
    $this->taminoGetIds(); 
    // for each id, create and initialize a linkRecord object
    foreach ($this->ids as $i) {
      $linkargs = $argArray;
      $linkargs["id"] = $i;
      $this->link[$i] = new linkRecord($linkargs);
      $this->link[$i]->taminoGetRecord();
    }
  }

  // retrieve all the linkRecord ids
  function taminoGetIds() {
    $rval = $this->tamino->xquery($this->xquery);
    if ($rval) {       // tamino Error
      print "<p>LinkCollection Error: failed to retrieve linkRecord id list.<br>";
      print "(Tamino error code $rval)</p>";
    } else {       
      // convert xml ids into a php array 
      $this->ids = array();
      $this->xml_result = $this->tamino->xml->getBranches("ino:response/xq:result");
      if ($this->xml_result) {
	// Cycle through all of the branches 
	foreach ($this->xml_result as $branch) {
	  if ($att = $branch->getTagAttribute("id", "xq:attribute")) {
	    array_push($this->ids, $att);
	  }
	}    /* end foreach */
      } 
    }

  }     /* end taminoGetIds() */

  // print full details of all linkRecords in a nice table
  function printRecords ($show_edits = 1) {
    print "<table border='1' width='90%'>";
    foreach ($this->ids as $i) {
      print "<tr><td>";
      $this->link[$i]->printHTML($show_edits);
      print "</td>";
      print "<td><p><a href='delete.php?id=$i'>Delete</a></p>";
      print "<p><a href='modify.php?id=$i'>Modify</a></p></td></tr>";
    }
    print "</table>";
  }

  //print summary info for all linkRecords
  function printSummary () {
    foreach ($this->ids as $i) {
      $this->link[$i]->printSummary();
    }
   }


  // print sort options linked to the url passed in
  // FIXME: may need some kind of mode-- user vs. admin ?
  function printSortOptions ($url) {
    print "<p align='center'><b>Sort by:</b> ";
    foreach ($this->sort_opts as $s) {
      if ($s != $this->sort_opts[0]) {
	// print a separator between terms
	print " | ";
      }
      if ($s == $this->sort) {
	print "&nbsp;" . $this->pretty_sort_opts[$s] . "&nbsp;";
      } else {
	print "&nbsp;<a href='$url?sort=$s'>" . 
	  $this->pretty_sort_opts[$s] . "</a>&nbsp;";
      }
    }
    print "</p>";
  }

  // drop-down box to limit links by subject
  // optionally specify the current selection (by default, none)
  function printSubjectOptions ($url, $selected = NULL) {
    print "Limit by Subject:<br>\n";
    print "<form action='$url' method='get'>\n";
    $this->subject->printSelectList($selected, 1, 'no', true);
    print '<input type="submit" value="Go">';
    print "</form>\n";
  }


}