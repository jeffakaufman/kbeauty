<?php

/**
 * Product:       Xtento_TrackingImport (2.1.2)
 * ID:            9Hiec+sXo9Z8XvyLsrsgPILEHN0W5+Sn/0xZtemTYL0=
 * Packaged:      2015-11-13T21:34:24+00:00
 * Last Modified: 2013-11-03T16:32:55+01:00
 * File:          app/code/local/Xtento/TrackingImport/Block/Adminhtml/Source/Edit/Tab/Type/Sftp.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_TrackingImport_Block_Adminhtml_Source_Edit_Tab_Type_Sftp extends Xtento_TrackingImport_Block_Adminhtml_Source_Edit_Tab_Type_Ftp
{
    // SFTP Configuration
    public function getFields($form, $type = 'FTP')
    {
        parent::getFields($form, 'SFTP');
    }
}