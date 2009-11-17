<?php
class App_Search_Lucene_Index_Pdfs
{
    /**
     * Extract data from a PDF document and add this to the Lucene index.
     *
     * @param string $pdfPath                       The path to the PDF document.
     * @param Zend_Search_Lucene_Proxy $luceneIndex The Lucene index object.
     * @return Zend_Search_Lucene_Proxy
     */
    public static function index($pdfPath, $luceneIndex)
    {
        // Load the PDF document.
        $pdf = Zend_Pdf::load($pdfPath);
        $key = md5($pdfPath);

        /**
         * Set up array to contain the document index data.
         * The Filename will be used to retrive the document if it is found in
         * the search resutls.
         * The Key will be used to uniquely identify the document so we can
         * delete it from the search index.
         */
        $indexValues = array(
            'Filename'     => $pdfPath,
            'Key'          => $key,
            'Title'        => '',
            'Author'       => '',
            'Subject'      => '',
            'Keywords'     => '',
            'Creator'      => '',
            'Producer'     => '',
            'CreationDate' => '',
            'ModDate'      => '',
            'Contents'     => '',
        );

        // Go through each meta data item and add to index array.
        foreach ($pdf->properties as $meta => $metaValue) {
            switch ($meta) {
                case 'Title':
                    $indexValues['Title'] = $pdf->properties['Title'];
                    break;
                case 'Subject':
                    $indexValues['Subject'] = $pdf->properties['Subject'];
                    break;
                case 'Author':
                    $indexValues['Author'] = $pdf->properties['Author'];
                    break;
                case 'Keywords':
                    $indexValues['Keywords'] = $pdf->properties['Keywords'];
                    break;
                case 'CreationDate':
                    $dateCreated = $pdf->properties['CreationDate'];

                    $distance = substr($dateCreated, 16, 2);
                    if (!is_long($distance)) {
                        $distance = null;
                    }
                    // Convert date from the PDF format of D:20090731160351+01'00'
                    $dateCreated = mktime(substr($dateCreated, 10, 2), //hour
                        substr($dateCreated, 12, 2), //minute
                        substr($dateCreated, 14, 2), //second
                        substr($dateCreated,  6, 2), //month
                        substr($dateCreated,  8, 2), //day
                        substr($dateCreated,  2, 4), //year
                        $distance); //distance
                    $indexValues['CreationDate'] = date('Ymd', $dateCreated);
                    break;
                case 'Date':
                    $indexValues['Date'] = $pdf->properties['Date'];
                    break;
            }
        }

        /**
         * Parse the contents of the PDF document and pass the text to the
         * contents item in the $indexValues array.
         */
        $pdfParse                = new App_Search_Helper_PdfParser();
        $indexValues['Contents'] = $pdfParse->pdf2txt($pdf->render());

        // Create the document using the values
        $doc = new App_Search_Lucene_Document($indexValues);
        if ($doc !== false) {
            // If the document creation was sucessful then add it to our index.
            $luceneIndex->addDocument($doc);
        }

        // Return the Lucene index object.
        return $luceneIndex;
    }
}