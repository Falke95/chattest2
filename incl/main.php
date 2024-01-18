<?php

require_once('version.php');

function abc123($n,$s){
$n=preg_replace('/[^\p{L}\p{N} -]/u',$s,$n);
$n=preg_replace('/([\s])\1+/',' ',$n);
return trim($n);}


// ------------------

function abcmixed($n){
global $settings;
$a=0; $tn=str_replace('-','',$n); $tn=str_replace(' ','',$tn); $tn=preg_replace('/[0-9]/','',$tn);
$lg=explode(',',$settings['utf8_set']);
foreach($lg as $value){
$rg='/^(?:\p{'.$value.'}+)$/u';
if(preg_match($rg,$tn)){ return false; }
} return true; }

// ------------------

function escape_emo($x){
$x=str_replace(["'",'"','<','>','&'],'',$x);
if(strlen($x)<2){return ':::';}
$x=str_split($x);
for($i=0;$i<count($x);$i++){
preg_match('/[^\p{L}\p{N}]/u',$x[$i],$m);
if(isset($m[0])){$x[$i]='\\\\'.$x[$i];}}
return implode('',$x);}

// ------------------

function av2letter($n){
if(!preg_match('/^[a-z]/i',$n)){return 'img/001.svg';}
return 'img/00'.strtolower(substr($n,0,1)).'.svg'; }

// ------------------

function redirect($u){ header('location:'.$u); die(); }

// ------------------

function makeclr($x){        
$r_rgb=hexdec(substr($x,0,2));
$g_rgb=hexdec(substr($x,2,2));
$b_rgb=hexdec(substr($x,4,2));
$yiq=(($r_rgb*299)+($g_rgb * 587)+($b_rgb * 114))/1000;
if($yiq>=128){$y='000';}else{$y='fff';}
return $y;}

// ------------------

function length($x){
if($x===NULL){return 0;}
if(function_exists('mb_strlen')){
$y=mb_strlen(trim($x));}
else{$y=strlen(trim($x));}
return $y;}

// ------------------

function newcookie($nm,$vl,$ex,$pt){
global $cookieparam;
$dt=gmdate('D, d M Y H:i:s T',$ex);
header("Set-Cookie: $nm=$vl; Expires=$dt; Path=$pt; $cookieparam");
}

// ------------------

function process_error($s){
global $error_log;
if(is_writeable($error_log)){
$s="\r\n".date('Y-m-d H:i:s').' '.$s;
$fd=fopen($error_log,"a");
$fout=fwrite($fd,$s);fclose($fd);}
die('SQL error... Please check your error log file for details...');}

// ------------------

function rand_str($l){
$l=(int)$l; if($l<5){$l=5;}
$str='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$len=strlen($str); $randstr='';

if(function_exists('random_int')){
for($i=0;$i<$l;$i++){$randstr.=$str[random_int(0,$len-1)];}
return $randstr;}

if(function_exists('password_hash')){
$randstr=substr(password_hash(microtime(),PASSWORD_DEFAULT),10,60);
$randstr=preg_replace('/[^\da-z]/i','',$randstr.$randstr);
$randstr=substr($randstr,0,$l); return $randstr;}

$randstr=substr(substr(md5(microtime()),0,15).substr(base64_encode(sha1(time())),0,15).substr(sha1(microtime()),0,15).substr(base64_encode(md5(time())),0,15),0,$l);
return $randstr;}

// ------------------

function get_settings(){

global $dbss;
$settings=array(); 

$query='SELECT * FROM '.$dbss['prfx'].'_settings';
$result=neutral_query($query);

while($row=neutral_fetch_array($result)){$settings[$row['id']]=$row['value'];}

return $settings;}

// ------------------

function get_language(){

global $settings,$lang_list,$xcookie_langsel,$langsel,$lang,$js_lang,$lang_abbr,$lang_name;

if(isset($_COOKIE[$xcookie_langsel[0]])){
$langsel=(int)$_COOKIE[$xcookie_langsel[0]];}
else{$langsel=$settings['default_lang'];}

require_once('lang/languages.php');

$lang_abbr =         $lang_list[0][0];
$lang_name =         $lang_list[0][1];
$lang_file = 'lang/'.$lang_list[0][2];

if(isset($lang_list[$langsel])){
$lang_abbr =         $lang_list[$langsel][0];
$lang_name =         $lang_list[$langsel][1];
$lang_file = 'lang/'.$lang_list[$langsel][2];
}
require_once($lang_file);}

// ------------------

$mode0css='body,td,p,div,input,select,textarea{font-size:15px;font-family:sans-serif}.x_global{}.x_blab{}input,select{color:#000}.x_overal{color:#000;background-color:#fff}.x_accent_bg{color:#fff;background-color:#3584e4}.x_accent_fg{color:#3584e4;background-color:transparent}.x_accent_bb{border-bottom:1px solid #3584e4}.x_input_blabws{color:#000;background-color:#f6f5f4}.x_bcolor_x{color:#000;background-color:#f6f5f4}.x_bcolor_y{color:#000;background-color:#f6f5f4}.x_bcolor_z{color:#fff;background-color:#241f31}.x_left_rounded{border-radius:5px 0 0 5px}.x_right_rounded{border-radius:0 5px 5px 0}.x_bottom_rounded{border-radius:0 0 5px 5px}.x_top_rounded{border-radius:5px 5px 0 0}.x_all_rounded{border-radius:5px 5px 5px 5px}.x_circle{border-radius:100%}';
$mode1css='body,td,p,div,input,select,textarea{font-size:15px;font-family:sans-serif}.x_global{}.x_blab{}input,select{color:#fff}.x_overal{color:#fff;background-color:#000}.x_accent_bg{color:#fff;background-color:#3584e4}.x_accent_fg{color:#3584e4;background-color:transparent}.x_accent_bb{border-bottom:1px solid #3584e4}.x_input_blabws{color:#fff;background-color:#181818}.x_bcolor_x{color:#fff;background-color:#181818}.x_bcolor_y{color:#fff;background-color:#181818}.x_bcolor_z{color:#fff;background-color:#000}.x_left_rounded{border-radius:5px 0 0 5px}.x_right_rounded{border-radius: 0 5px 5px 0}.x_bottom_rounded{border-radius: 0 0 5px 5px}.x_top_rounded{border-radius: 5px 5px 0 0}.x_all_rounded{border-radius: 5px 5px 5px 5px}.x_circle{border-radius:100%}';

// fix missing elements in old config.php

if(!isset($xcookie_dn_mode)){$xcookie_dn_mode = array($dbss['prfx'].'_dn_mode',31536000);}
if(!isset($xcookie_langsel)){$xcookie_langsel = array($dbss['prfx'].'_langsel',31536000);}
if(!isset($storage_options)){$storage_options = $dbss['prfx'].'_options';}
if(!isset($storage_xsounds)){$storage_xsounds = $dbss['prfx'].'_xsounds';}
if(!isset($storage_ignored)){$storage_ignored = $dbss['prfx'].'_ignored';}
if(!isset($cookieparam)){$cookieparam='SameSite=Lax;';}
if(!isset($supersalt)){$supersalt=0;}

// ------------------

$timestamp=time();
$random=mt_rand(1,999999); 
if(!isset($ipaddr)){$ipaddr=$_SERVER['REMOTE_ADDR'];}
if(isset($norealips) && $norealips>0){$ipaddr=sha1($ipaddr);}
if(!isset($ping_period) || $ping_period<3 || $ping_period>20){$ping_period=5;}
if(!isset($ping_multpl) || $ping_multpl<60 || $ping_multpl>120){$ping_multpl=65;}
if(!isset($msgs2keep) || $msgs2keep<10 || $msgs2keep>500){$msgs2keep=100;}

if(isset($_COOKIE['mobileapp'])){ $cookieparam='SameSite=None; Secure;';}

require_once 'incl/mysqli_functions.php';

if(!headers_sent()){
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Content-type: text/html; charset=UTF-8");}

?>