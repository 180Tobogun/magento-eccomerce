<?php

class Voronoy_Paymaster_Block_Method_Form extends Mage_Payment_Block_Form
{
    /**
     * Form Id.
     */
    const ELEMENT_FORM_ID = 'paymaster_form';

    /**
     * Block Template.
     *
     * @var string
     */
    protected $_template = 'paymaster/redirect.phtml';

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Form.
     *
     * @var Varien_Data_Form
     */
    protected $_form;

    protected function _construct()
    {
        parent::_construct();
        $this->_prepareForm();
    }

    /**
     * Get Form.
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        if (!$this->_form) {
            $this->_form = new Varien_Data_Form();
            $this->_form->setAction(Mage::helper('voronoy_paymaster')->getGatewayUrl())
                ->setId(self::ELEMENT_FORM_ID)
                ->setName('paymaster_form_name')
                ->setMethod('POST')
                ->setUseContainer(true);
        }

        return $this->_form;
    }

    /**
     * Prepare Form.
     */
    protected function _prepareForm()
    {
        $form = $this->getForm();

        $paymentMethod = $this->getOrder()->getPayment()->getMethodInstance();
        $paymentMethod->debugData($this->getFields());

        foreach ($this->getFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        $submitButton = new Varien_Data_Form_Element_Submit(array(
            'value' => $this->__('Click here if you are not redirected within 10 seconds...'),
        ));
        $form->addElement($submitButton);
    }

    /**
     * Get Current Order.
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            $this->_order = $order;
        }

        return $this->_order;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        if (!$this->_fields) {
            $paymentMethod = $this->getOrder()->getPayment()->getMethodInstance();
            $request = $paymentMethod->getRequest();
            $request->prepareRequest();
            $this->_fields = $request->getData();
        }

        return $this->_fields;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;
    }
}
