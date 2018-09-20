<?php

class Voronoy_Paymaster_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Restore Quote and Replace Current Quote.
     *
     * @param $quote
     */
    public function restoreQuote($quote)
    {
        if ($quote->getId()) {
            $quote->setIsActive(true);
            $quote->setReservedOrderId(null);
            $quote->save();
        }
        Mage::getSingleton('checkout/session')->replaceQuote($quote)->unsLastRealOrderId();
    }

    public function getGatewayUrl()
    {
        $gatewayUrl = Mage::getStoreConfig('payment/paymaster_general/gateway_url');
        //$lang = Mage::getStoreConfig('payment/paymaster_general/lang');
        if ($lang) {
            $gatewayUrl = sprintf('%s%s/', $gatewayUrl);
        }

        return $gatewayUrl;
    }

    public function getOrderState()
    {
        return Mage::getStoreConfig('payment/paymaster_general/order_status');
    }
}
