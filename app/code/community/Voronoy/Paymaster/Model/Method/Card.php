<?php

class Voronoy_Paymaster_Model_Method_Card extends Voronoy_Paymaster_Model_Method_Abstract
{
    protected $_code = 'paymaster_card';
    protected $_paymentSystemId = 21;

    public function getPaymentSystemId()
    {
        return $this->_paymentSystemId;
    }
}
