<?php

require_once("models/Tei.php");

class PostcardsController extends Zend_Controller_Action {

  public function indexAction() {
    $this->view->assign("title", "Postcards");
   
  }
  /* public function browseAction() {
    $this->view->assign("title", "Postcard - Contents");
    if (isset($this->_getParam("cat")) $cat = $this->_getParam("cat");
    $this->view->cardlist = Tei::getPostcardContent();

  }*/

/*  public function postcardAction() {
    $id = $this->_getParam("id");
    $this->view->card = Tei::getPostcard($id);
    }*/



}

?>