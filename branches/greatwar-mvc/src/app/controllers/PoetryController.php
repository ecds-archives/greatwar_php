<?php

require_once("models/Tei.php");

class PoetryController extends Zend_Controller_Action {

  public function indexAction() {
    $TeiSet = Tei::getPoetryTitle();
    $this->view->list = $TeiSet->docs;
  }
  public function contentAction() {
    $id = $this->_getParam("id");
    $this->view->booktoc = Tei::getPoetryContent($id);

  }



}

?>