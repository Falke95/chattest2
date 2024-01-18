<?php

if(@filesize('config.php')<5){header('location: install/index.php');die();}

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings();

// lang from login
if(isset($_GET['lang'])){
$lang=(int)$_GET['lang'];
newcookie($xcookie_langsel[0],$lang,time()+$xcookie_langsel[1],'/');
redirect('account.php');die();}

// lang from chat
if(isset($_GET['plng']) && isset($_GET['room'])){
$room=(int)$_GET['room'];
$lang=(int)$_GET['plng']; newcookie($xcookie_langsel[0],$lang,time()+$xcookie_langsel[1],'/');
redirect('blabax.php?room='.$room);die();}

if(isset($_GET['mobileapp'])){
$mpp=(int)$_GET['mobileapp']; $cookieparam='SameSite=None; Secure;';
newcookie('mobileapp',$mpp,time()+$xcookie_langsel[1],'/');}

// room selection
if(isset($_GET['room'])){$room=(int)$_GET['room'];if($room>0){newcookie($dbss['prfx'].'_room',$room,time()+3600,'/');}}

// ---

$nexturl='blabax.php'; $is_loggedin=1;
if(!isset($_COOKIE[$xcookie_uidhash[0]])){$nexturl='account.php'; $is_loggedin=0;}

if(!isset($_GET['nosplash']) && isset($settings['splash']) && strlen($settings['splash'])>9){
$splash=str_replace('%ISLOGGEDIN%',$is_loggedin,$settings['splash']);
die($splash);}

redirect($nexturl);

?>