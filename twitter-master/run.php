<?php
session_start();
require_once("twitteroauth/twitteroauth.php"); //Path to twitteroauth library
require_once('../app/Mage.php');
$stream_limit = Mage::getStoreConfig('tab1/general/stream_limit');
if(empty($stream_limit))$stream_limit= 50;
$notweets = $stream_limit;
$twitteruser = Mage::getStoreConfig('tab1/socialwall_twitter/socialwall_id');
$consumerkey = Mage::getStoreConfig('tab1/socialwall_twitter/socialwall_ck');
$consumersecret = Mage::getStoreConfig('tab1/socialwall_twitter/socialwall_cs');
$accesstoken = Mage::getStoreConfig('tab1/socialwall_twitter/socialwall_at');
$accesstokensecret = Mage::getStoreConfig('tab1/socialwall_twitter/socialwall_ats');
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $connection;
}
$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);
echo json_encode($tweets);
?>