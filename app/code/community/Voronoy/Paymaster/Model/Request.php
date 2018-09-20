<?php

include_once "functions.php";

class Voronoy_Paymaster_Model_Request extends Varien_Object
{
    const FIELD_NAME_MERCHANT_ID = 'MerchantID';
    const FIELD_NAME_MERCHANT_SECRET_KEY = 'TerminalID';
    const FIELD_NAME_PAYMENT_AMOUNT = 'TotalAmount';
    const FIELD_NAME_PAYMENT_TIME = 'PurchaseTime';
    const FIELD_NAME_PAYMENT_NO = 'OrderID';
    const FIELD_NAME_PAYMENT_CURRENCY = 'Currency';
    const FIELD_NAME_SESSION = 'SD';
    const FIELD_NAME_PAYMENT_DESC = 'PurchaseDesc';
    const FIELD_NAME_SIGNATURE = 'Signature';
    const FIELD_NAME_PAYMENT_SYSTEM = 'ECC';

    /**
     * Order.
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Paymaster Config.
     *
     * @var Voronoy_Paymaster_Model_Config
     */
    protected $_config;

    /**
     * Get Payment Method.
     *
     * @var Voronoy_Paymaster_Model_Method_Abstract
     */
    protected $_paymentMethod;

    /**
     * Prepare Redirect Form Request.
     */
    public function prepareRequest()
    {
        $requestData = [
            'Version' => 1,
            'redirect' => '',
            self::FIELD_NAME_MERCHANT_ID => $this->getConfig()->getMerchantId(),
            self::FIELD_NAME_MERCHANT_SECRET_KEY => $this->getConfig()->getMerchantSecretKey(),
            self::FIELD_NAME_PAYMENT_AMOUNT => sprintf('%0.2f', $this->getOrder()->getBaseTotalDue())*100,
            self::FIELD_NAME_PAYMENT_CURRENCY=> '980',
            'locale' => 'en',
            self::FIELD_NAME_SESSION => 'aa',
            self::FIELD_NAME_PAYMENT_NO => $this->getOrder()->getIncrementId(),
            self::FIELD_NAME_PAYMENT_TIME => date('ymdHis'),
            'PurchaseDesc' => 'test',
          ];
      $sign = $this->createSignature($requestData);

      $requestData = array_merge($requestData, [self::FIELD_NAME_SIGNATURE => $sign]);


        $this->addData($requestData);
    }

    /**
    * @param array $requestData
    *
    * @return string
    */
    private function createSignature($requestData) {

      $basepath = Mage::getBaseDir('media');

      $pemFile = $basepath .'/keys/'.$requestData[self::FIELD_NAME_MERCHANT_ID].'.pem';
      $fp = fopen($pemFile, "r");
      $priv_key = fread($fp, 8192);
      fclose($fp);
      $pkeyid = openssl_get_privatekey($priv_key);
      $data = implode(';', [
         $requestData[self::FIELD_NAME_MERCHANT_ID],
         $requestData[self::FIELD_NAME_MERCHANT_SECRET_KEY],
         $requestData[self::FIELD_NAME_PAYMENT_TIME],
         $requestData[self::FIELD_NAME_PAYMENT_NO],
         $requestData[self::FIELD_NAME_PAYMENT_CURRENCY],
         $requestData[self::FIELD_NAME_PAYMENT_AMOUNT],
         $requestData[self::FIELD_NAME_SESSION],
         '',
      ]);

      openssl_sign( $data , $signature, $pkeyid);
      openssl_free_key($pkeyid);
      $b64sign = base64_encode($signature) ;

      return $b64sign;
    }

    public function getSignOfRequest()
    {
        $hash = sprintf('%s%s%0.2f%s', $this->getConfig()->getMerchantId(), $this->getOrder()->getIncrementId(),
            $this->getOrder()->getBaseTotalDue(), $this->getConfig()->getMerchantSecretKey());

        return strtoupper(hash($this->getConfig()->getEncryptionMethod(), $hash));
    }

    /**
     * @return Voronoy_Paymaster_Model_Config
     */
    public function getConfig()
    {
        if (!$this->_config) {
            $this->_config = Mage::getModel('voronoy_paymaster/config');
            //var_dump($this->_config);

            $this->_config->setPaymentCode($this->getPaymentMethod()->getCode());
            //var_dump(2);
        }

        return $this->_config;
    }

    /**
     * @param Voronoy_Paymaster_Model_Config $config
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * Get Payment Method Object.
     *
     * @return Voronoy_Paymaster_Model_Method_Abstract
     */
    public function getPaymentMethod()
    {
        if (!$this->_paymentMethod) {
            $this->_paymentMethod = $this->getOrder()->getPayment()->getMethodInstance();
        }

        return $this->_paymentMethod;
    }

    /**
     * @param Voronoy_Paymaster_Model_Method_Abstract $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->_paymentMethod = $paymentMethod;
    }

    /**
     * Get Order.
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            if ($this->getData(self::FIELD_NAME_PAYMENT_NO)) {
                $orderIncrementId = $this->getData(self::FIELD_NAME_PAYMENT_NO);
            }
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            if (!$order->getId()) {
                Mage::throwException(sprintf('Invalid Order ID: %s', $orderIncrementId));
            }
            $this->_order = $order;
        }

        return $this->_order;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     */
    public function setOrder($order)
    {
        $this->_order = $order;
    }

    /**
     * Validate Hash String.
     *
     * @param $hash
     *
     * @return bool
     */
    public function validateHash($hash)
    {
        $sign = $this->getConfig()->getMerchantId().$this->getOrder()->getIncrementId()
            .$this->getData('PAYMENT_ID').$this->getData('PAYMENT_DATE')
            .$this->getData('PAYMENT_AMOUNT').$this->getData('PAID_AMOUNT')
            .$this->getData('PAYMENT_SYSTEM').$this->getData('MODE')
            .$this->getConfig()->getMerchantSecretKey();

        $sign = strtoupper(hash($this->getConfig()->getEncryptionMethod(), $sign));
        if ($hash == $sign) {
            return true;
        }

        return false;
    }


}
