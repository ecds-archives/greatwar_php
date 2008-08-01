<?php
/** Zend_Controller_Action */
/* Require models */

class AboutController extends Greatwar_Controller_Action {


  public function aboutAction() {
    $this->view->assign("title", "About this Site");
    $this->_helper->layout->setLayout("about");

  }
}
?>