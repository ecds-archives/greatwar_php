<?php 

include_once("phpDOM/classes/include.php");
import("org.active-link.xml.XML");

class taminoConnection {

  // connection parameters
  var $host;
  var $db;
  var $coll;
  // whether or not to display debugging information
  var $debug;

  // basedir (xsl files should be in xsl directory under this)
  var $basedir;
  
  // these variables used internally
  var $base_url;
  var $xmlContent;
  var $xml;
  var $xsl_result;

  // cursor variables
  var $cursor;
  var $count;
  var $position;
  var $quantity;

  // variables for highlighting search terms
  var $begin_hi;
  var $end_hi;


  

  function taminoConnection($argArray) {
    $this->host = $argArray['host'];
    $this->db = $argArray['db'];
    $this->coll = $argArray['coll'];
    $this->debug = $argArray['debug'];

    // if basedir is passed in, use that; otherwise, use current directory
    $this->basedir = $argArray['basedir'] ?  $argArray['basedir'] : getcwd ();

    $this->base_url = "http://$this->host/tamino/$this->db/$this->coll?";

    // strings for highlighting search terms 
    for ($i = 0; $i < 4; $i++) {
      $this->begin_hi[$i]  = "<span class='term" . ($i + 1) . "'><b>";
    }
    $this->end_hi = "</b></span>";
  }

  // send an xquery to tamino & get xml result
  // returns  tamino error code (0 for success, non-zero for failure)
  function xquery ($query, $position = NULL, $maxdisplay = NULL) {
    $myurl = $this->base_url . "_xquery=" . $this->encode_xquery($query);
    if (isset($position) && isset($maxdisplay)) {
      $myurl .= "&_cursor=open&_position=$position&_quantity=$maxdisplay&_sensitive=vague&_encoding=utf-8";
    }
    if ($this->debug) {
      print "DEBUG: In function taminoConnection::xquery, url is $myurl.<p>";
    }

    
    $this->xmlContent = file_get_contents($myurl);
    if ($this->debug) {
      $copy = $this->xmlContent;
      $copy = str_replace(">", "&gt;", $copy);
      $copy = str_replace("<", "\n&lt;", $copy);
      print "DEBUG: in taminoConnection::xquery, xmlContent is <pre>$copy</pre>"; 
    }

    if ($this->xmlContent) {		// if xquery was successful
      $length = strlen($this->xmlContent);
      if ($length < 500000) {
        // phpDOM can only handle xmlContent within certain size limits
        $this->xml = new XML($this->xmlContent);
        if (!($this->xml)) {        ## call failed
  	print "TaminoConnection::xquery Error: unable to retrieve xml content, or result size is too large.<br>";
        }
        $error = $this->xml->getTagAttribute("ino:returnvalue", 
					   "ino:response/ino:message");
      } else {
        // not really a tamino error.... might have unexpected results
        $this->xml = 0;
        $error = 0;
      }

      if (!($error)) {    // tamino Error code (0 = success)
       $this->getXQueryCursor();
      } else if ($error == "8306") {	    // invalid cursor position (also returned when there are no matches)
        $this->count = $this->position = $this->quantity = 0;
        if ($debug) {
  	print "DEBUG: Tamino error 8306 = invalid cursor position<br>\n";
        }
      } else if ($error) {
         $this->count = $this->position = $this->quantity = 0;
         print "<p>Error: failed to retrieve contents.<br>";
         print "(Tamino error code $error)</p>";
      }

    } else {
      print "<p><b>Error:</b> unable to access database.</p>";
      $error = -1;
    }
    return $error;	// return tamino error code, in case user wants to check it
  }


  // send an x-query (xql) to tamino & get xml result
  // returns  tamino error code (0 for success, non-zero for failure)
  // optionally allows for use of xql-style cursor
  function xql ($query, $position = NULL, $maxdisplay = NULL) {
    if ($this->debug) {
      print "DEBUG: In function taminoConnection::xql, query is $query.<p>";
    }

    if (isset($position) && isset($maxdisplay)) {
      $xql = "_xql($position,$maxdisplay)=";
    } else {
      $xql = "_xql=";
    }

    $myurl = $this->base_url . $xql . $this->encode_xquery($query);
    if ($this->debug) {
      print "DEBUG: In function taminoConnection::xql, url is $myurl.<p>";
    }

    $this->xmlContent = file_get_contents($myurl);
    if ($this->debug) {
      $copy = $this->xmlContent;
      $copy = str_replace(">", "&gt;", $copy);
      $copy = str_replace("<", "\n&lt;", $copy);
      print "DEBUG: in taminoConnection::xql, xmlContent is <pre>$copy</pre>"; 
    }

    $length = strlen($this->xmlContent);
    if ($length < 200000) {
      // phpDOM can only handle xmlContent within certain size limits
      $this->xml = new XML($this->xmlContent);
      if (!($this->xml)) {        ## call failed
	print "TaminoConnection xquery Error: unable to retrieve xml content.<br>";
      }
      $error = $this->xml->getTagAttribute("ino:returnvalue", 
					   "ino:response/ino:message");
    } else {
      // not really a tamino error.... might have unexpected results
      $this->xml = 0;
      $error = 0;
    }
   return $error;
  }

   // convert a readable xquery into a clean url for tamino
   function encode_xquery ($string) {
     // get rid of multiple white spaces
     $string = preg_replace("/\s+/", " ", $string);
     // convert spaces to their hex equivalent
     $string = str_replace(" ", "%20", $string);
     // convert ampersand & # within xquery (e.g., for unicode entities) to hex
     $string = str_replace("&", "%26", $string);
     $string = str_replace("#", "%23", $string);

     return $string;
   }

   // retrieve the XQL cursor & get the total count
   function getCursor () {
     // NOTE: this is an xql style cursor, not xquery
     if ($this->xml) {
       $this->cursor = $this->xml->getBranches("ino:response", "ino:cursor");
       if ($this->cursor) {
	 $this->count = $this->cursor[0]->getTagAttribute("ino:count", "ino:cursor");
       } else {
	 // no matches (or, possibly-- unable to retrieve cursor)
	 $this->count = 0;
       }
     } else {
       print "Error! taminoConnection xml variable uninitialized.<br>";
     }
   }

   // retrieve the XQuery style cursor & get the total count
   function getXQueryCursor () {
     if ($this->xml) {
       //$this->cursor = $this->xml->getBranches("ino:response", "ino:cursor");
       $this->cursor = $this->xml->getBranches("ino:response/ino:cursor", "ino:current");
       if ($this->cursor) {
	 $this->position = $this->cursor[0]->getTagAttribute("ino:position");
 	 $this->quantity = $this->cursor[0]->getTagAttribute("ino:quantity");
       } else {
	 // no matches (or, possibly-- unable to retrieve cursor)
	 $this->position = 0;
 	 $this->quantity = 0;
       }

       $result = $this->xml->getBranches("ino:response", "xq:result");
       if ($result) {
	 //$this->count = $this->findNode("total", $result[0]);
		  $this->count = $this->findNode("total");
       }
       
     } else {
       print "Error! taminoConnection xml variable uninitialized.<br>";
     }
   }

   // get content of an xml node by name when the path is unknown
   // FIXME: should this really be a taminoConnection class function?
   function findNode ($name, $node = NULL) {
     if ($node == NULL){	// by default, search xq:result
       $branch = $this->xml->getBranches("ino:response", "xq:result");
       $node = $branch[0];
     }
     $result = $node->getTagContent($name);
     if ($result) {	// found it
       return $result;
     } else {
       $branches = $node->getBranches();
       for ($i = 0; isset($branches[$i]); $i++) {
	 // recurse on each branch 
	 $result = $this->findNode($name, $branches[$i]);
	 if ($result) { return $result; }
       }
       // if we get through all the branches without returning, then return 0
       return 0;    // not found in this node
     }
   }
   

   // transform the tamino XML with a specified stylesheet
   function xslTransform ($xsl_file, $xsl_params = NULL) {
     if ($this->xmlContent) {	// xquery succeeded, there is xml to process
       // create xslt handler
       $xh = xslt_create();
       // specify file base so that xsl includes will work
       // Note: last / on end of fileBase is important!
       $fileBase = "file://$this->basedir/xsl/";
       //  print "file base is $fileBase<br>";
       xslt_set_base($xh, $fileBase);

       $args = array('/_xml' => $this->xmlContent);
       $this->xsl_result = xslt_process($xh, 'arg:/_xml', $xsl_file, NULL, $args, $xsl_params);
     
       if ($this->xsl_result) {
         // Successful transformation
       } else {
         print "Transformation failed.<br>";
         print "Error: " . xslt_error($xh) . " (error code " . xslt_errno($xh) . ")<br>";
       }
       xslt_free($xh);
     } else {	// xquery failed, no xml to process
       if ($debug) { print "<p><b>Warning:</b> XML content unavailable to transform.</p>"; }
     }
   }

   function printResult ($term = NULL) {
     if (isset($term[0])) {
       $this->highlight($term);
     }
     print $this->xsl_result;

   }

   // Highlight the search strings within the xsl transformed result.
   // Takes an array of terms to highlight.
   function highlight ($term) {
     // note: need to fix regexps: * -> \w* (any word character)
      // FIXME: how best to deal with wild cards?

     // only do highlighting if the term is defined
     for ($i = 0; (isset($term[$i]) && ($term[$i] != '')); $i++) {
       // replace tamino wildcard (*) with regexp -- 1 or more word characters 
       $_term = str_replace("*", "\w+", $term[$i]);
     // Note: regexp is constructed to avoid matching/highlighting the terms in a url or img tag
       // FIXME: breaking words at end of tag (</h4>, </li>... )
       $this->xsl_result = preg_replace("/([^=|']\b)($_term)(\b[^\.])/i",
	      "$1" . $this->begin_hi[$i] . "$2$this->end_hi$3", $this->xsl_result);
     }
   }

   // print out search terms, with highlighting matching that in the text
   function highlightInfo ($term) {
     if (isset($term[0])) {
       print "<p align='center'>The following search terms have been highlighted: ";
       for ($i = 0; isset($term[$i]); $i++) {
	 print "&nbsp; " . $this->begin_hi[$i] . "$term[$i]$this->end_hi &nbsp;";
       }
       print "</p>";
     }
   }

}