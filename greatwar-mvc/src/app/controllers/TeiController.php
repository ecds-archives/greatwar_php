<?php

require_once("models/Tei.php");

class TeiController extends Greatwar_Controller_Action {

  public function listAction() {
    $set = Tei::find();
    $this->view->docs = $set->docs;
    
  }

  public function viewAction() {
    $section = $this->_getParam("section");
    $name = $this->_getParam("name");
    
    $this->view->doc = Tei::findByName("$section/$name.xml");
  }

}
?>