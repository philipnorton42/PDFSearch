<?php
class Zend_View_Helper_BaseUrl
{
    public function baseUrl()
    {
        $fc     = Zend_Controller_Front::getInstance();
        $config = Zend_Registry::get('config');
        return $config->servername . $fc->getBaseUrl();
    }
}
