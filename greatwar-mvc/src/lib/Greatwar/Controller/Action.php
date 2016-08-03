<?php

abstract class Greatwar_Controller_Action extends Zend_Controller_Action {

  
  public function init() {
    $this->initView();

    Zend_Controller_Action_HelperBroker::addPath('Emory/Controller/Action/Helper',
						 'Emory_Controller_Action_Helper');
    
    // store controller/action names in view
    $params =  $this->_getAllParams();
    if (isset($params['controller'])) $this->view->controller = $params['controller'];
    if (isset($params['action']))  $this->view->action = $params['action'];

    // select print-view layout
    if (isset($params['view']) && $params['view'] == "printable")
      $this->_helper->layout->setLayout("printable");
  }

}

?>
