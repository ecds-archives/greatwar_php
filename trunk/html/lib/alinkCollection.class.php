<?php

include_once("linkCollection.class.php");
include_once("alinkRecord.class.php");

// a linkCollection to use alinkRecord (approval)
class aLinkCollection extends linkCollection {


  function aLinkCollection ($argArray) {
      parent::linkCollection($argArray);	   // call parent constructor first
      // then override linkRecords with alinkRecords
      $this->link = array();	// over-write as empty array
      foreach ($this->ids as $i) {
        $linkargs = $argArray;
        $linkargs["id"] = $i;
        $this->link[$i] = new alinkRecord($linkargs);
        $this->link[$i]->taminoGetRecord();
      }
  }

  //print summary info ONLY if approved=yes
  function printSummary () {
    foreach ($this->ids as $i) {
      if ($this->link[$i]->approved == "yes") {
        $this->link[$i]->printSummary();
      }
    }
   }

  // print out an approval form to approve submitted links
  function printApprovalForm ($url) {
    $count = 0;
    print "<form action='$url'>";
    print "<table>";
    //foreach linkRecord that is not approved, print out a table row with checkbox
    foreach ($this->ids as $i) {
      if ($this->link[$i]->approved == "no") {
	$count++;
	print "<tr><td>";
	print "<input type='checkbox' name='$i' checked='yes'></td>";
	print "<td>";
        $this->link[$i]->printSummary();
	print "</td></tr>";
      }
    }
    if ($count == 0) {
      print "<tr><td></td><td>There are no link records to approve.</td></tr>";
    } 
    print "</table>";
    if ($count != 0) {
      print "<input type='submit' value='Approve Selected Links'></form>";
    }
  }

}
