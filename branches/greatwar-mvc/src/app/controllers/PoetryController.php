<?php
class PoetryController extends Zend_Controller_Action {

  public function indexAction() {
    //$this->view->assign("title", "Poetry");
    $list = Tei::getPoetryTitle($id);
  }
  public function viewAction() {
    $items = Tei::getPoetryContent();

  }



}

?>