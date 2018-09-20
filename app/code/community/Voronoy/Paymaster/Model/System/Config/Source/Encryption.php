<?php

class Voronoy_Paymaster_Model_System_Config_Source_Encryption
{
    public function toOptionArray()
    {
        $encryptionMethods = array(
            array('value' => 'sha256', 'label' => 'sha256'),
            array('value' => 'md5', 'label' => 'md5'),
            array('value' => 'sha1', 'label' => 'sha1'),
        );

        return $encryptionMethods;
    }
}
