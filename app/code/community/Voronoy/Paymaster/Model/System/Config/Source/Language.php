<?php

class Voronoy_Paymaster_Model_System_Config_Source_Language
{
    const LANG_CODE_RUSSIA = 'ru';
    const LANG_CODE_UKRAINE = 'uk';
    const LANG_CODE_ENGLISH = 'en';

    public function toOptionArray()
    {
        $encryptionMethods = array(
            array('value' => self::LANG_CODE_RUSSIA, 'label' => 'Russian/Русский'),
            array('value' => self::LANG_CODE_UKRAINE, 'label' => 'Ukraine/Українська'),
            array('value' => self::LANG_CODE_ENGLISH, 'label' => 'English/English'),
        );

        return $encryptionMethods;
    }
}
