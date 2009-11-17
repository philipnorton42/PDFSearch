<?php

class PdfController extends Zend_Controller_Action {
    public function init() {
    }

    public function indexAction() {
    // action body
    }

    public function listAction() {
        $config = Zend_Registry::get('config');

        $globOut = glob($config->filesDirectory . '*.pdf');
        if(count($globOut)>0) { // make sure the glob array has something in it
            $files = array();
            foreach ($globOut as $filename) {
                $files[] = $filename;
            }
            $this->view->files = $files;
        }else {
            $this->view->message = 'No files found.<br />';
        }
    }

    public function viewmetaAction()
    {
        $pdfPath = urldecode($this->_request->getParam('file'));
        $pdf = Zend_Pdf::load($pdfPath);

        $metaValues = array('Title'        => '',
                            'Author'       => '',
                            'Subject'      => '',
                            'Keywords'     => '',
                            'Creator'      => '',
                            'Producer'     => '',
                            'CreationDate' => '',
                            'ModDate'      => '',
        );

        foreach ($metaValues as $meta => $metaValue) {
            if (isset($pdf->properties[$meta])) {
                $metaValues[$meta] = $pdf->properties[$meta];
            } else {
                $metaValues[$meta] = '';
            }
        }

        $this->view->file = $pdfPath;
        $this->view->metaValues = $metaValues;
    }

    public function editmetaAction()
    {
        // Get the form and send to the view.
        $form = new Form_PdfMeta();
        $this->view->form = $form;

        // Get the file and send the location to the view.
        $pdfPath          = urldecode($this->_request->getParam('file'));
        $file             = substr($pdfPath, strrpos($pdfPath, SLASH)+1);
        $this->view->file = $file;

        // Define what meta data we are looking at.
        $metaValues = array('Title'    => '',
                            'Author'   => '',
                            'Subject'  => '',
                            'Keywords' => '',
        );

        if ($this->_request->isPost()) {
            // Get the current form values.
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                // Form values are valid.

                // Save the contents of the form to the associated meta data fields in the PDF.
                $pdf = Zend_Pdf::load($pdfPath);
                foreach ($metaValues as $meta => $metaValue) {
                    if (isset($formData[$meta])) {
                        $pdf->properties[$meta] = $formData[$meta];
                    } else {
                        $pdf->properties[$meta] = '';
                    }
                }
                $pdf->save($pdfPath);

                // Add to/update index.
                $config = Zend_Registry::get('config');
                $appLucene = App_Search_Lucene::open($config->luceneIndex);
                $index = App_Search_Lucene_Index_Pdfs::index($pdfPath, $appLucene);

                // Redirect the user to the list action of this controller.
                return $this->_helper->redirector('list', 'pdf', '', array())->setCode(301);
            } else {
                // Form values are not valid send the current values to the form.
                $form->populate($formData);
            }
        } else {
            // Make sure the file exists before we start doing anything with it.
            if (file_exists($pdfPath)) {
                // Extract any current meta data values from the PDF document
                $pdf = Zend_Pdf::load($pdfPath);
                foreach ($metaValues as $meta => $metaValue) {
                    if (isset($pdf->properties[$meta])) {
                        $metaValues[$meta] = $pdf->properties[$meta];
                    } else {
                        $metaValues[$meta] = '';
                    }
                }
                // Populate the form with out metadata values.
                $form->populate($metaValues);
            } else {
                // File doesn't exist.
            }
        }
    }
}