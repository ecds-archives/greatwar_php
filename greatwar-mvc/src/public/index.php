<?

/*
*  
*
*
*/
//Error Reporting
error_reporting(E_ALL|E_STRICT);
ini_set("display_errors", "on");

// ZendFramework
ini_set("include_path", "../lib:../app/:../app/modules/:" . ini_get("include_path"));

require("Zend/Loader.php");
Zend_Loader::loadClass("Zend_Controller_Front");

function __autoload($class) {
  Zend_Loader::loadClass($class);
}

//Load Configuration
$env_config	= new Zend_Config_Xml("../config/environment.xml", "environment");
$config 	= new Zend_Config_Xml("../config/config.xml", $env_config->mode);

//Create eXist DB object
// FIXME: check DB adapter type in config?
$db = new Emory_Db_Adapter_Exist($config->database->params->toArray());
Zend_Registry::set('exist-db', $db);

//set default timezone
date_default_timezone_set($config->timezone);

//Setup Controller
$front = Zend_Controller_Front::getInstance();

// Set the default controller directory:
$front->setControllerDirectory(array("default" => "../app/controllers"));
$front->addModuleDirectory("../app/modules");

// Define Layout
Zend_Layout::startMvc(array(
    "layout" => "site",			// default layout
    "layoutPath" => "../app/views/layouts",	// layout scripts directory
    ));

// add local helper path to view
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
$viewRenderer->initView();
$viewRenderer->view->addHelperPath('Emory/View/Helper', 'Emory_View_Helper');


//set internal error handler
$front->throwExceptions($env_config->display_exception);

$front->dispatch();
?>

