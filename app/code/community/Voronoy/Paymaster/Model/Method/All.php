<?php

class Voronoy_Paymaster_Model_Method_All extends Voronoy_Paymaster_Model_Method_Abstract
{
    protected $_code = 'paymaster_all';
    protected $_paymentSystemId;

    public function getPaymentSystemId()
    {
        return $this->_paymentSystemId;
    }
}
