<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {


    protected function _initAutoload() {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
          'namespace' => '',
          'basePath' => APPLICATION_PATH)
        );

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace(array('App_'));
    }
    
    protected function _initConfigLoad() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $config);
    }
}