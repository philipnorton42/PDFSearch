<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        
    }

    public function createindexAction()
    {
    // Create index
        $config                = Zend_Registry::get('config');
        $index                 = App_Search_Lucene::create($config->luceneIndex);
        $this->view->indexSize = $index->count();
        $this->view->documents = $index->numDocs();
    }

    public function viewindexAction()
    {
    // Open Index
        $config                = Zend_Registry::get('config');
        $index                 = App_Search_Lucene::open($config->luceneIndex);
        $this->view->indexSize = $index->count();
        $this->view->documents = $index->numDocs();
    }

    public function indexpdfsAction()
    {
        $config = Zend_Registry::get('config');

        $appLucene = App_Search_Lucene::open($config->luceneIndex);
        $globOut   = glob($config->filesDirectory . '*.pdf');
        if (count($globOut) > 0) { // make sure the glob array has something in it
            foreach ($globOut as $filename) {
                $index = App_Search_Lucene_Index_Pdfs::index($filename, $appLucene);
            }
        }
        $appLucene->commit();
        if ($appLucene != null) {
            $this->view->indexSize = $appLucene->count();
            $this->view->documents = $appLucene->numDocs();
        }
    }

    public function searchAction()
    {
        $filters    = array('q' => array('StringTrim', 'StripTags'));
        $validators = array('q' => array('presence' => 'required'));
        $input      = new Zend_Filter_Input($filters, $validators, $_GET);

        if (is_string($this->_request->getParam('q'))) {
            $queryString = $input->getEscaped('q');
            $this->view->queryString = $queryString;

            if ($input->isValid()) {
                $config = Zend_Registry::get('config');
                $index  = App_Search_Lucene::open($config->luceneIndex);

                $query = new Zend_Search_Lucene_Search_Query_Boolean();

                $pathTerm  = new Zend_Search_Lucene_Index_Term($queryString);
                $pathQuery = new Zend_Search_Lucene_Search_Query_Term($pathTerm);
                $query->addSubquery($pathQuery, true);

                $pathTerm  = new Zend_Search_Lucene_Index_Term('20091023', 'CreationDate');
                $pathQuery = new Zend_Search_Lucene_Search_Query_Term($pathTerm);
                $query->addSubquery($pathQuery, true);

                try {
                    $hits = $index->find($query);
                } catch (Zend_Search_Lucene_Exception $ex) {
                    $hits = array();
                }

                $this->view->hits = $hits;
            } else {
                $this->view->messages = $input->getMessages();
            }
        }
    }

    public function optimizeAction()
    {
        // Open existing index
        $config = Zend_Registry::get('config');
        $index = App_Search_Lucene::open($config->luceneIndex);

        // Optimize index.
        $index->optimize();
    }
}