<?php

if(!isset($_GET['id'])){die();}
$id=(int)$_GET['id']; if($id<1){die();}

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect();

$motto='';
$unused='000';

$res=neutral_query('SELECT * FROM '.$dbss['prfx'].'_uxtra WHERE id='.$id);
$uxtra=neutral_fetch_array($res); 
if(isset($uxtra['motto']) && strlen($uxtra['motto'])>0){$motto=htmlspecialchars(str_replace('|','',$uxtra['motto']));}
else{
$settings=get_settings();
$mottos=explode('|',$settings['mottos']);
$motto=array_rand($mottos); $motto=htmlspecialchars($mottos[$motto]);}

$uxtra=$id.'|'.$motto.'|'.$unused;
print $uxtra;

?>