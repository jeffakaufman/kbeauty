<?php

class Productiveminds_Core_Model_Email {
	
	const PROMINDS_EMAIL = 'support@productiveminds.com';
	const PROMINDS_NAME = 'Prominds Support';

	public function sendMail($subject, $useremail, $username, $emailBody, $attachments)
	{
		$mail = new Zend_Mail ();
	
		$mail->setBodyHtml($emailBody, "UTF-8");
		$mail->setFrom($useremail, $username);
		$mail->addTo(self::PROMINDS_EMAIL, self::PROMINDS_NAME);
		$mail->setSubject($subject);
	
		for ($i = 0; $i < count($attachments['name']); $i++) {
			$tmpFilePath = $attachments['tmp_name'] [$i];
			if ($tmpFilePath != "") {
				$newFilePath = "./media/downloadable/" . $attachments['name'] [$i];
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$fileName = $attachments['name'] [$i];
					$attachedItem = new Zend_Mime_Part (file_get_contents($newFilePath));
					$attachedItem->disposition = Zend_Mime::DISPOSITION_INLINE;
					$attachedItem->encoding = Zend_Mime::ENCODING_BASE64;
					$attachedItem->filename = $fileName;
					$mail->addAttachment($attachedItem);
				}
			}
		}
	
		try {
			$mail->send();
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productivemindscore')->__('Your ticket was successfully sent - you will be in touch shortly, Many thanks.'));
		} catch (Exception $ex) {
			Mage::getSingleton('core/session')->addError(Mage::helper('productivemindscore')->__($ex->getMessage()));
		}
	}
}