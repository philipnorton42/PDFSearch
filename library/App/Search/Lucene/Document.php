<?php

class App_Search_Lucene_Document extends Zend_Search_Lucene_Document
{

    /**
     * Constructor.
     *
     * @param array $values An associative array of values to be used
     *                      in the document.
     */
    public function __construct($values)
    {
        // If the Filename or the Key values are not set then reject the document.
        if (!isset($values['Filename']) && !isset($values['key'])) {
            return false;
        }

        Zend_Search_Lucene_Analysis_Analyzer::setDefault(
            new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive());

        // Add the Filename field to the document as a Keyword field.
        $this->addField(Zend_Search_Lucene_Field::Keyword('Filename', $values['Filename']));
        // Add the Key field to the document as a Keyword.
        $this->addField(Zend_Search_Lucene_Field::Keyword('Key', $values['Key']));

        if (isset($values['Title']) && $values['Title'] != '') {
            // Add the Title field to the document as a Text field.
            $this->addField(Zend_Search_Lucene_Field::Text('Title', $values['Title']));
        }

        if (isset($values['Subject']) && $values['Subject'] != '') {
            // Add the Subject field to the document as a Text field.
            $this->addField(Zend_Search_Lucene_Field::Text('Subject', $values['Subject']));
        }

        if (isset($values['Author']) && $values['Author'] != '') {
            // Add the Author field to the document as a Text field.
            $this->addField(Zend_Search_Lucene_Field::Text('Author', $values['Author']));
        }

        if (isset($values['Keywords']) && $values['Keywords'] != '') {
            // Add the Keywords field to the document as a Text field.
            $this->addField(Zend_Search_Lucene_Field::Text('Keywords', $values['Keywords']));
        }

        if (isset($values['CreationDate']) && $values['CreationDate'] != '') {
            // Add the CreationDate field to the document as a Text field.
            $this->addField(Zend_Search_Lucene_Field::Text('CreationDate', $values['CreationDate']));
        }

        if (isset($values['ModDate']) && $values['ModDate'] != '') {
            // Add the ModDate field to the document as a Text field.
            $this->addField(Zend_Search_Lucene_Field::Text('ModDate', $values['ModDate']));
        }

        if (isset($values['Contents']) && $values['Contents'] != '') {
            // Add the Contents field to the document as an UnStored field.
            $this->addField(Zend_Search_Lucene_Field::UnStored('Contents', $values['Contents']));
        }
    }
}