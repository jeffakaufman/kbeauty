<?php

require_once 'app/Mage.php';

umask(0);

/* not Mage::run(); */

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);



if (isset($_GET['profile_id'])) {

  $profileId = intval($_GET['profile_id']);

} else if ($argv[1]) { // First command line parameter

  $profileId = intval($argv[1]);

} else {

  echo "No profile ID specified.";

  die();

}



$profile = Mage::getModel('xtento_productexport/profile')->load($profileId);

$exportModel = Mage::getModel('xtento_productexport/export', array('profile' => $profile));

$filters = Mage::getModel('xtento_productexport/observer_cronjob')->addProfileFilters($profile);

#$filters[] = array('entity_id' => array('from' => 1, 'to' => 1000));

$exportModel->cronExport($filters);



?>
