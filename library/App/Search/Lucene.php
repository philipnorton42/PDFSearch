<?php


class App_Search_Lucene extends Zend_Search_Lucene
{
    /**
     * Create a new index.
     *
     * @param string $directory         The location of the Lucene index.
     * @return Zend_Search_Lucene_Proxy The Lucene index.
     */
    public static function create($directory)
    {
        return new Zend_Search_Lucene_Proxy(new App_Search_Lucene($directory, true));
    }

    /**
     * Open the index. If the index does not exist then one will be created and
     * returned.
     *
     * @param string $directory         The location of the Lucene index.
     * @return Zend_Search_Lucene_Proxy The Lucene index.
     */
    public static function open($directory)
    {
        try {
            // Attempt to open the index.
            return new Zend_Search_Lucene_Proxy(new App_Search_Lucene($directory, false));
        } catch (Exception $e) {
            // Return a newly created index using the create method of this class.
            return self::create($directory);
        }
    }

    /**
     * Add a document to the index.
     *
     * @param Zend_Search_Lucene_Document $document The document to be added.
     * @return Zend_Search_Lucene                   
     */
    public function addDocument(Zend_Search_Lucene_Document $document)
    {
        // Search for documents with the same Key value.
        $term = new Zend_Search_Lucene_Index_Term($document->Key, 'Key');
        $docIds = $this->termDocs($term);

        // Delete any documents found.
        foreach ($docIds as $id) {
            $this->delete($id);
        }

        return parent::addDocument($document);
    }
}