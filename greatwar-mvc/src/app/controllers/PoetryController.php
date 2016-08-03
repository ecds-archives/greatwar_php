<?php

require_once("models/Tei.php");

class PoetryController extends Zend_Controller_Action {

  public function indexAction() {
    $this->view->assign("title", "Poetry");
    $TeiSet = Tei::getPoetryTitle();
    $this->view->list = $TeiSet->docs;
  }
  public function contentAction() {
    $this->view->assign("title", "Poetry - Contents");
    $id = $this->_getParam("id");
    $this->view->booktoc = Tei::getPoetryContent($id);

  }

  public function poemAction() {
    $id = $this->_getParam("id");
    $this->view->poem = Tei::getPoem($id);
  }



}

?>