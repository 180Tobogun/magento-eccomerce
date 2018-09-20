<?php

class Voronoy_Paymaster_Model_Config extends Varien_Object
{
    /**
     * Path Merchant Id.
     */
    const XML_PATH_MERCHANT_ID = 'payment/paymaster_general/merchant_id';

    /**
     * Path Secret Key.
     */
    const XML_PATH_MERCHANT_SECRET_KEY = 'payment/paymaster_general/secret_key';

    /**
     * Path Order Status.
     */
    const XML_PATH_ORDER_STATUS = 'payment/paymaster_general/order_status';

    /**
     * Path Test Mode.
     */
    const XML_PATH_DEBUG_MODE = 'payment/paymaster_general/debug';

    /**
     * Payment Code.
     *
     * @var string
     */
    protected $_paymentCode;

    /**
     * @param string $paymentCode
     */
    public function setPaymentCode($paymentCode)
    {
        $this->_paymentCode = $paymentCode;
    }

    /**
     * Get Merchant Id.
     *
     * @return string
     */
    public function getMerchantId()
    {
        return Mage::getStoreConfig(self::XML_PATH_MERCHANT_ID);
    }

    /**
     * Get Merchant Id.
     *
     * @return string
     */



    public function getDebug()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEBUG_MODE);
    }

    /**
     * Get Merchant Secret Key.
     *
     * @return string
     */
    public function getMerchantSecretKey()
    {
        return Mage::getStoreConfig(self::XML_PATH_MERCHANT_SECRET_KEY);
    }


    /**
     * Get Order Status after Order Place.
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return Mage::getStoreConfig(self::XML_PATH_ORDER_STATUS);
    }

    public function getTitle()
    {
        $path = sprintf('payment/%s/title', $this->_paymentCode);

        return Mage::getStoreConfig($path);
    }
}
