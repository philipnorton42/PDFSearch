<?php
class Zend_View_Helper_FilesDirectory
{
    public function filesDirectory()
    {
        $config = Zend_Registry::get('config');
        return $config->filesDirectory;
    }
}
