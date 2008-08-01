<?php
/** Zend_Controller_Action */
/* Require models */

class IndexController extends Zend_Controller_Action {

  protected $_flashMessenger = null;

   public function indexAction() {	
     $this->view->assign("title", "Poetry and Postcards");

     // will use views/layouts/frontpage.phtml instead of site.phtml
     $this->_helper->layout->setLayout("frontpage");
   }

  public function aboutAction() {
    $this->view->assign("title", "About this Site");
  }

  public function creditsAction() {
    $this->view->assign("title", "Credits");
  }

   
	public function listAction() {
	}

	public function createAction() {
	}

	public function editAction() {
	}

	public function saveAction() {
	}

	public function viewAction() {
	}

	public function deleteAction() {
	}
}
?>