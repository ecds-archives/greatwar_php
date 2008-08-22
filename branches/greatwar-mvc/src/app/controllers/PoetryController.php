<?php

require_once("models/Tei.php");

class PoetryController extends Zend_Controller_Action {

  public function indexAction() {
    $TeiSet = Tei::getPoetryTitle();
    $this->view->list = $TeiSet->docs;
  }
  public function contentAction() {
    $items = Tei::getPoetryContent($id);
    $this->view->list = $Tei->docs;

  }



}

?>