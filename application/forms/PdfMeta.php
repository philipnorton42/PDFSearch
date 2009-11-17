<?php

class Form_PdfMeta extends Zend_Form
{

    public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');

        $this->addElement('textarea', 'Title', array(
                          'label'      => 'Title',
                          'required'   => true,
                          'rows'       => '10',
                          'cols'       => '50',
                          'filters'    => array('StringTrim'),
                          'validators' => array(
                                          array('validator' => 'StringLength', 'options' => array(0,500)))
                          ));
        $this->addElement('textarea', 'Author', array(
                          'label'      => 'Author',
                          'required'   => true,
                          'rows'       => '10',
                          'cols'       => '50',
                          'filters'    => array('StringTrim'),
                          'validators' => array(
                                          array('validator' => 'StringLength', 'options' => array(0,500)))
                          ));
        $this->addElement('textarea', 'Subject', array(
                          'label'      => 'Subject',
                          'required'   => true,
                          'rows'       => '10',
                          'cols'       => '50',
                          'filters'    => array('StringTrim'),
                          'validators' => array(
                                          array('validator' => 'StringLength', 'options' => array(0,2000)))
                          ));
        $this->addElement('textarea', 'Keywords', array(
                          'label'      => 'Keywords',
                          'required'   => true,
                          'rows'       => '10',
                          'cols'       => '50',
                          'filters'    => array('StringTrim'),
                          'validators' => array(
                                          array('validator' => 'StringLength', 'options' => array(0,500)))
                          ));

        // add the submit button
        $this->addElement('submit', 'submit', array('label' => 'Save'));
    }
}
