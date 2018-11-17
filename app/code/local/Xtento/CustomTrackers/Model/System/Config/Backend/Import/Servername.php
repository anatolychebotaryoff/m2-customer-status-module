<?php

/**
 * Product:       Xtento_CustomTrackers (1.6.0)
 * ID:            RlVX2Xvr9DxGADvNaSvfd1jQoZge9YZz7qN69i5SEG0=
 * Packaged:      2015-05-05T19:38:19+00:00
 * Last Modified: 2012-01-22T19:54:28+01:00
 * File:          app/code/local/Xtento/CustomTrackers/Model/System/Config/Backend/Import/Servername.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_CustomTrackers_Model_System_Config_Backend_Import_Servername extends Mage_Core_Model_Config_Data
{
    const MODULE_MESSAGE = 'The Custom Trackers extension couldn\'t be enabled. Please make sure you are using a valid license key.';

    public function afterLoad()
    {
        $sName1 = Mage::getModel('customtrackers/system_config_backend_import_server')->getFirstName();
        $sName2 = Mage::getModel('customtrackers/system_config_backend_import_server')->getSecondName();
        if ($sName1 !== $sName2) {
            $this->setValue(sprintf('%s (Main: %s)', $sName1, $sName2));
        } else {
            $this->setValue(sprintf('%s', $sName1));
        }
    }
}