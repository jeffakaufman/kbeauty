<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sitesecurity/sitesecure')};");

$installer->endSetup();
