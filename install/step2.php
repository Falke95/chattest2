<?php 

if(!isset($_POST['dbhost']) || !isset($_POST['dbname']) || !isset($_POST['dbuser']) || !isset($_POST['dbpass'])){die();}

require_once('../version.php');
require_once('lang_english.utf8'); ?>

<!DOCTYPE html>
<html lang="en">

<head><title><?php print $lang['installing'].' ('.$lang['step'].' 2)';?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" href="style.css">
</head>

<body class="x_global x_overal">

<?php
if(is_file('../config.php') && filesize('../config.php')>5){?>
<div class="holder x_accent_bg round4" style="text-align:center;padding:50px">
<?php print $lang['config_set'];?>
</div></body></html>
<?php die();}?>

<?php
if(!is_file('../config.php') || !is_writeable('../config.php')){?>
<div class="holder x_accent_bg round4" style="text-align:center;padding:50px">
<?php print $lang['config_chm'];?>
</div></body></html>
<?php die();}?>

<?php

// setting config.php

$blabws_server_path=$_POST['blabws_server_path'];
$blabws_server_port=$_POST['blabws_server_port']; if($blabws_server_port==''){$rplport=9001;$dbport='';}else{$rplport=$blabws_server_port;$dbport=$blabws_server_port;}
$blabws_server_prto=$_POST['blabws_server_prto'];
$blabws_server_akey=$_POST['blabws_server_akey'];
$blabws_server_logf=$_POST['blabws_server_logf'];
$blabws_server_addr=$_POST['blabws_server_addr'];
$blabws_prpas_token=$_POST['blabws_prpas_token'];
if($blabws_server_port==''){$EXTHOSTED=1;}else{$EXTHOSTED=0;}

if(isset($_POST['dbhost'])){$dbhost=$_POST['dbhost'];}else{$dbhost='localhost';}
if(isset($_POST['dbname'])){$dbname=$_POST['dbname'];}else{$dbname='';}
if(isset($_POST['dbuser'])){$dbuser=$_POST['dbuser'];}else{$dbuser='';}
if(isset($_POST['dbpass'])){$dbpass=$_POST['dbpass'];}else{$dbpass='';}
if(isset($_POST['dbcset'])){$dbcset=$_POST['dbcset'];}else{$dbcset='utf8';}
if(isset($_POST['dbprfx'])){$dbprfx=trim($_POST['dbprfx']);}else{$dbprfx='blabax';}
if(isset($_POST['dbsock'])){$dbsock=trim($_POST['dbsock']);}else{$dbsock='';}

function slash_n_replace($a,$b,$c){
$b=trim(addcslashes($b,"'\\"));
$c=str_replace($a,$b,$c); return $c;}

$config=@file('phpconfig',FILE_IGNORE_NEW_LINES);
$config=implode("\n",$config);
$config=slash_n_replace('DBHOST',$dbhost,$config);
$config=slash_n_replace('DBNAME',$dbname,$config);
$config=slash_n_replace('DBUSER',$dbuser,$config);
$config=slash_n_replace('DBPASS',$dbpass,$config);
$config=slash_n_replace('PREFIX',$dbprfx,$config);
$config=slash_n_replace('DBSOCK',$dbsock,$config);
$config=slash_n_replace('DBCSET',$dbcset,$config);
$config=slash_n_replace('BLABWSPATH',$blabws_server_path,$config);
$config=slash_n_replace('BLABWSPORT',$rplport,$config);
$config=slash_n_replace('BLABWSAKEY',$blabws_server_akey,$config);
$config=slash_n_replace('BLABWSLOGF',$blabws_server_logf,$config);
$config=slash_n_replace('EXTHOSTED',$EXTHOSTED,$config);

$handle=fopen('../config.php','w');fwrite($handle,$config);fclose($handle);

require_once('../config.php');
require_once('../incl/mysqli_functions.php');

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

function process_error($x){
print $x;die();
}

$cookiesalt=rand_str(50);
$randomsalt=rand_str(40);
$cronkeyrnd=rand_str(20);
$aboxseckey=rand_str(20);
$wsecretkey=rand_str(40);
$timestamp=time();


$options=' ENGINE=MYISAM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
if($dbss['cset']=='utf8'){$options=' ENGINE=MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci';}

$qui_icons='🍏,🍎,🍐,🍊,🍋,🍒,💦,🍉,🍇,🍓,🍒,🥭,🥥,🥦,🥑,🥝,🌽,🧄,🧅,🌹,🌴,🍀,🍄,💐,🌵,🌲,🌻,🌼,🌺,🍁,🐹,🐰,🦊,🐻,🐼,🐨,🦁,🐷,🐸,🙊,🐧,🐦,🦅,🦉,🐝,🦋,🐌,🐞,🦑,🦀,🐡,🐠,🐟,🦓,🐪,🦒,🐃,🦌,🐓,🦃,🦚,🦜,🦩,🕊,⭐,🚗,🚎,🚑,🚜,🚠,🚦';
if($dbss['cset']=='utf8'){$qui_icons='★,✿,✽,❊,✤,⚘,♠,❤,♦,♣';}

$iceservers='{iceServers:[{urls:"turn:openrelay.metered.ca:80",username:"openrelayproject",credential:"openrelayproject",}, {urls:"turn:openrelay.metered.ca:443",username:"openrelayproject",credential:"openrelayproject",},],}';

// db install goes here

$install=array();
neutral_dbconnect();

$install[]='CREATE TABLE '.$dbss['prfx'].'_online(
id integer NOT NULL,
name varchar(64) NOT NULL,
ugroup smallint NOT NULL,
ipaddr varchar(50) NOT NULL,
timestamp int(11) NOT NULL,
status smallint NOT NULL,
avatar varchar(256) NOT NULL,
pinfo varchar(256) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_ban(
id integer NOT NULL auto_increment PRIMARY KEY,
userid integer NOT NULL,
name varchar(64) NOT NULL,
ipaddr varchar(64) NOT NULL,
initstamp integer NOT NULL,
timestamp integer NOT NULL,
ulevel smallint NOT NULL,
ban smallint NOT NULL,
aname varchar(64) NOT NULL,
breason varchar(256) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_social(
id varchar(128) NOT NULL,
userid integer NOT NULL,
social char(2) NOT NULL,
sA varchar(256) NOT NULL,
sB varchar(256) NOT NULL,
sC varchar(512) NOT NULL,
sD varchar(512) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_scache(
id varchar(16) NOT NULL,
value mediumtext NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_bflog(
id integer NOT NULL,
ipaddr varchar(64) NOT NULL,
token integer NOT NULL,
timestamp integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_iplog(
id integer NOT NULL,
name varchar(64) NOT NULL,
ipaddr varchar(64) NOT NULL,
timestamp integer NOT NULL,
country varchar(64) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_ipc(
ipslice varchar(16) NOT NULL,
country_code char(2) NOT NULL,
country_name varchar(64) NOT NULL,
timestamp integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_grules(
id integer NOT NULL auto_increment PRIMARY KEY,
description varchar(256) NOT NULL,
scenario text NOT NULL,
ugroup integer NOT NULL,
zorder integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_rooms(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
description varchar(128) NOT NULL,
color char(6) NOT NULL,
zorder integer NOT NULL,
hidden smallint NOT NULL,
groupids varchar(256) NOT NULL)'.$options;

$install[]='INSERT INTO '.$dbss['prfx']."_rooms VALUES(1,'Chatraum','Willkommen im Chat','3B7AB5',0,0,'')";

$install[]='CREATE TABLE '.$dbss['prfx'].'_fmedia(
id integer NOT NULL auto_increment PRIMARY KEY,
filename varchar(64) NOT NULL,
file2hdd varchar(255) NOT NULL,
filetype integer NOT NULL,
sourcetxt text NOT NULL,
timestamp integer NOT NULL,
userid integer NOT NULL,
username varchar(64) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_paintings(
id integer NOT NULL auto_increment PRIMARY KEY,
description varchar(64) NOT NULL,
srx text NOT NULL,
sry text NOT NULL,
src text NOT NULL,
bgc char(6) NOT NULL,
timestamp integer NOT NULL,
userid integer NOT NULL,
username varchar(64) NOT NULL,
bgid integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_settings(
id varchar(32) NOT NULL,
value text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_stimoji(
id integer NOT NULL auto_increment PRIMARY KEY,
filename varchar(64) NOT NULL,
keytags text NOT NULL,
FULLTEXT(keytags))'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_style(
id integer NOT NULL,
value text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_messages(
id integer NOT NULL auto_increment PRIMARY KEY,
roomid integer NOT NULL,
userid integer NOT NULL,
usergroup integer NOT NULL,
username varchar(64) NOT NULL,
touserid integer NOT NULL,
tousername varchar(64) NOT NULL,
line text NOT NULL,
color smallint NOT NULL,
attach smallint NOT NULL,
timestamp integer NOT NULL,
mhash char(12) NOT NULL)'.$options;

$install[]='INSERT INTO '.$dbss['prfx']."_messages VALUES (NULL,0,0,0,'system',0,'','Installed successfully...',0,0,$timestamp,'1')";

$install[]='CREATE TABLE '.$dbss['prfx'].'_offmsg(
id integer NOT NULL auto_increment PRIMARY KEY,
userid integer NOT NULL,
username varchar(64) NOT NULL,
ugroup integer NOT NULL,
touserid integer NOT NULL,
offmessage text NOT NULL,
timestamp integer NOT NULL,
msgread smallint(6) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_users(
id integer NOT NULL auto_increment PRIMARY KEY,
ugroup integer NOT NULL,
name varchar(64) NOT NULL,
password char(64) NOT NULL,
email varchar(128) NOT NULL,
phone varchar(20) NOT NULL,
salt char(20) NOT NULL,
ipaddr varchar(64) NOT NULL,
question varchar(256) NOT NULL,
answer char(64) NOT NULL,
timestamp integer NOT NULL,
vcode varchar(32) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_uxtra(
id integer NOT NULL,
image varchar(128) NOT NULL,
motto varchar(128) NOT NULL,
age smallint NOT NULL,
location varchar(128) NOT NULL,
gender varchar(128) NOT NULL,
education varchar(128) NOT NULL,
occupation varchar(128) NOT NULL,
interests varchar(128) NOT NULL,
cover varchar(128) NOT NULL,
about text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_ufake(
id integer NOT NULL,
status integer NOT NULL,
hour_begin smallint NOT NULL,
hour_end smallint NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_vip(
id integer NOT NULL auto_increment PRIMARY KEY,
vsign varchar(6) NOT NULL,
name varchar(64) NOT NULL,
salt char(64) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_vipusr(
userid integer NOT NULL,
username varchar(64) NOT NULL,
vip integer NOT NULL,
timestamp integer NOT NULL,
expirestamp integer NOT NULL,
token char(10) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_vproducts(
id integer NOT NULL auto_increment PRIMARY KEY,
vipid integer NOT NULL,
name varchar(64) NOT NULL,
vdesc text NOT NULL,
vlength integer NOT NULL,
stripepriceid varchar(64) NOT NULL,
infoprice varchar(32) NOT NULL,zorder integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_vorders(
id integer NOT NULL auto_increment PRIMARY KEY,
token char(40) NOT NULL,
productid integer NOT NULL,
pintent varchar(40) NOT NULL,
pstatus integer NOT NULL,
viptoken char(10) NOT NULL,
vipcode varchar(200) NOT NULL,
bname varchar(200) NOT NULL,
bmail varchar(200) NOT NULL,
ipaddr varchar(64) NOT NULL,
timestamp integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_groups(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
welcome text NOT NULL,
link integer NOT NULL,
vlnk integer NOT NULL,
color char(6) NOT NULL,
pa smallint NOT NULL,
pb smallint NOT NULL,
pc smallint NOT NULL,
pd smallint NOT NULL,
pe smallint NOT NULL,
pf smallint NOT NULL,
pg smallint NOT NULL,
ph smallint NOT NULL,
pi smallint NOT NULL,
pj smallint NOT NULL,
pk smallint NOT NULL,
pl smallint NOT NULL,
pm smallint NOT NULL,
pn smallint NOT NULL,
po smallint NOT NULL,
pp smallint NOT NULL,
pq smallint NOT NULL,
pr smallint NOT NULL,
ps smallint NOT NULL,
pt smallint NOT NULL,
pu smallint NOT NULL,
pv smallint NOT NULL,
pw smallint NOT NULL,
px smallint NOT NULL,
py smallint NOT NULL,
pz smallint NOT NULL)'.$options;

$install[]='INSERT INTO '.$dbss['prfx']."_groups VALUES(NULL,'DEFAULT','',0,0,'F44336',1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,0,1,1,1,0,1,0)";
$install[]='INSERT INTO '.$dbss['prfx']."_groups VALUES(1,'Moderator','',0,0,'F44336',1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,0,1,1,1,0,1,0)";
$install[]='INSERT INTO '.$dbss['prfx']."_groups VALUES(2,'Administrator','',0,0,'F44336',1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,0,1,1,1,0,1,0)";

$install[]='CREATE TABLE '.$dbss['prfx'].'_jbox(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
enabled integer NOT NULL,
roomid integer NOT NULL,
ugroup integer NOT NULL,
infinite integer NOT NULL,
shuffle integer NOT NULL,
gap integer NOT NULL,
delay integer NOT NULL,
cookielength integer NOT NULL,
hremember integer NOT NULL,
elements mediumtext NOT NULL,
template text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_rbox(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
enabled integer NOT NULL,
pm integer NOT NULL,
roomid integer NOT NULL,
ugroup integer NOT NULL,
keywords text NOT NULL,
answers mediumtext NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_bbox(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
fgc char(6) NOT NULL,
bgc char(6) NOT NULL,
blingkey char(20) NOT NULL,
enabled smallint NOT NULL,
inlist smallint NOT NULL,
blingsuper smallint NOT NULL,
timesec integer NOT NULL,
bgcolor varchar(32) NOT NULL,
sound varchar(256) NOT NULL,
blingcode text NOT NULL,
blingcss text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_gbox(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(64) NOT NULL,
enabled integer NOT NULL,
roomid integer NOT NULL,
ugroup integer NOT NULL,
pagesize integer NOT NULL,
glocale char(2) NOT NULL,
topic varchar(256) NOT NULL,
keywords text NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_gifs(
id integer NOT NULL auto_increment PRIMARY KEY,
idgbox integer NOT NULL,
seen integer NOT NULL,
ggif varchar(256) NOT NULL,
gmp4 varchar(256) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_pcache(
ipaddr varchar(64) NOT NULL,
proxy smallint NOT NULL,
timestamp integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_polls(
id integer NOT NULL,
vote integer NOT NULL,
userid integer NOT NULL,
ipaddr varchar(64) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_geoloc(
id integer NOT NULL,
name varchar(64) NOT NULL,
geolat float NOT NULL,
geolon float NOT NULL,
timestamp integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_qcats(
id integer NOT NULL auto_increment PRIMARY KEY,
value varchar(128) NOT NULL,
enbl integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_qdata(
id integer NOT NULL auto_increment PRIMARY KEY,
catid integer NOT NULL,question text NOT NULL,
aa varchar(256) NOT NULL,
ab varchar(256) NOT NULL,
ac varchar(256) NOT NULL,
ad varchar(256) NOT NULL,
correct char(1) NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_qres(
id integer NOT NULL,
userid integer NOT NULL,
username varchar(64) NOT NULL,
res integer NOT NULL,
timestamp integer NOT NULL)'.$options;

$install[]='CREATE TABLE '.$dbss['prfx'].'_gpt(
id integer NOT NULL auto_increment PRIMARY KEY,
name varchar(256) NOT NULL,
enabled integer NOT NULL,
uid integer NOT NULL,
gid integer NOT NULL,
keywords text NOT NULL,
rplmsg text NOT NULL,
sysmsg text NOT NULL)'.$options;

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('default_lang','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('default_ampm','2')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('colors','F44336|E91E63|9C27B0|673AB7|3F51B5|2196F3|03A9F4|00BCD4|009688|4CAF50|8BC34A|CDDC39|FFEB3B|FFC107|FF9800|FF5722|795548|607D8B|E53935|D81B60|8E24AA|5E35B1|3949AB|1E88E5|039BE5|00ACC1|00897B|43A047|7CB342|C0CA33|FDD835|FFB300|FB8C00|F4511E|6D4C41|546E7A')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('default_sound','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('html_title','Our Chat!')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('allow_guest','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('cookie_salt','$cookiesalt')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('random_salt','$randomsalt')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('allow_reg','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('userperhour','5')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('wrongperhour','5')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('dimonblur','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('notes','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ban_period','86400')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('mute_period','300')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('show_thumbs','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('keepiplg','7776000')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_display','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_default','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_icon','svg_star')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_title','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ctab_content','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('avatar_msize','102400')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('file_msize','512000')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('uploads_user','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('paintings_user','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('style_template','body,td,p,div,input,select,textarea{font-size:[1]px;font-family:[2]}\r\n.x_global{[3]}\r\n.x_blab{[17]}\r\ninput,select{color:#[4]}\r\n.x_overal{color:#[4];background-color:#[5]}\r\n.x_accent_bg{color:#[0];background-color:#[6]}\r\n.x_accent_fg{color:#[6];background-color:transparent}\r\n.x_accent_bb{border-bottom:1px solid #[6]}\r\n.x_input_blabws{color:#[7];background-color:#[8]}\r\n.x_bcolor_x{color:#[9];background-color:#[10]}\r\n.x_bcolor_y{color:#[11];background-color:#[12]}\r\n.x_bcolor_z{color:#[13];background-color:#[14]}\r\n.x_left_rounded{border-radius:[15]px 0 0 [15]px}\r\n.x_right_rounded{border-radius: 0 [15]px [15]px 0}\r\n.x_bottom_rounded{border-radius: 0 0 [15]px [15]px}\r\n.x_top_rounded{border-radius: [15]px [15]px 0 0}\r\n.x_all_rounded{border-radius: [15]px [15]px [15]px [15]px}\r\n.x_circle{border-radius:[16]%}')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('style_delivery','body,td,p,div,input,select,textarea{font-size:14px;font-family:sans-serif}\r\n.x_global{}\r\n.x_blab{}\r\ninput,select{color:#FFFFFF}\r\n.x_overal{color:#FFFFFF;background-color:#222222}\r\n.x_accent_bg{color:#000;background-color:#F44336}\r\n.x_accent_fg{color:#F44336;background-color:transparent}\r\n.x_accent_bb{border-bottom:1px solid #F44336}\r\n.x_input_blabws{color:#000000;background-color:#FFFFFF}\r\n.x_bcolor_x{color:#FFFFFF;background-color:#111111}\r\n.x_bcolor_y{color:#FFFFFF;background-color:#111111}\r\n.x_bcolor_z{color:#FFFFFF;background-color:#000000}\r\n.x_left_rounded{border-radius:5px 0 0 5px}\r\n.x_right_rounded{border-radius: 0 5px 5px 0}\r\n.x_bottom_rounded{border-radius: 0 0 5px 5px}\r\n.x_top_rounded{border-radius: 5px 5px 0 0}\r\n.x_all_rounded{border-radius: 5px 5px 5px 5px}\r\n.x_circle{border-radius:100%}')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('webkit_css','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('post_interval','2')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('acp_css','0d.css')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('token_validity','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('acp_offset','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_url','$blabws_server_addr')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_key','$blabws_server_akey')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_port','$dbport')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_wss','$blabws_server_prto')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('server_pps','$blabws_prpas_token')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_bbcms','blabws')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_cookie','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_prefix','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_nolog','../')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_logout','../')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('intg_pflink','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('msg_style','.msg{margin-bottom:20px;min-width:200px;display:flex;clear:both} .avt{float:left;width:50px;height:50px;margin-right:10px}')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('msg_template','<div class=\"msg\"><img class=\"avt x_circle\" src=\"{AVATAR}\" alt=\"\"><div style=\"text-align:left\"><span class=\"chat_area_user g{GROUP}\">{NAME}</span> <span class=\"chat_area_time\">{TIME}</span><br><span class=\"tt{COLOR}\" style=\"word-break:break-word\">{TEXT}</span></div></div>')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('announce','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('version','$version')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('forcereload','Alexandria')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('drag2scroll','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('whee2scroll','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('group_g','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('group_r','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('group_f','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('gifs_key','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('gifs_num','15')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('stimoji_fts','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('stimoji_num','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('stimoji_dir','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('gifs_rnd','summer, mountain, beach')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('svgtstamp','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('badge_bgc','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('badge_txt','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('showroombg','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgf','serif')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgt','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgc','123456')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgs','90')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('roombgl','8')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('upd_cache','$timestamp')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('logio_msg','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('multi_links','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('msg2db','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ban_order','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('welcome_msg','<b>Welcome to our chat!</b><div style=\"margin:10px\"><div class=\"help_allp help_esck\">The ESC key is your friend. Use it!</div><div class=\"help_allp help_cycl\">Cycle through rooms with Ctrl+Shift+L/R Arrows.</div><div class=\"help_allp help_ctrl\">Change rooms with Ctrl+Shift+1 Ctrl+Shift+2 ...</div><div class=\"help_allp help_drag\">Drag-to-scroll or scroll with the arrow keys.</div><div class=\"help_allp help_dblc\">A double-click swaps scroll &amp; select.</div><div class=\"help_allp help_swip\">A swipe from the left edge opens the menu.</div></div>')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('mottos','Acta Non Verba|Audentes Fortuna Iuvat|Alea Iacta Est|Ars Longa, Vita Brevis|Ave, Morituri Te Salutant|Credo Quia Absurdum|Dulce Bellum Inexpertis|Dum Excusare Credis, Accusas|Fabas Indulcet Fames|Fortis Fortuna Adiuvat|In Vino Veritas|Non Ducor Duco|Oderint Dum Metuat|Quis Custodiet Ipsos Custodes?|Semper Ad Meliora|Semper Inops Quicumque Cupit|Si Vis Amari, Ama|Si Vis Pacem, Para Bellum|Sic Transit Gloria Mundi|Transit Umbra, Lux Permanet|Una Hirundo Non Facit Ver|Veni, Vidi, Vici|Vestis Virum Reddit|Vir Sapit Qui Pauca Loquitur|Vires Acquirit Eundo|Vitam Regit Fortuna, Non Sapientia')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_on','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_sz','1000000')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_la','30')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_lv','10')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_ba','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_bv','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_rs','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vvm_us','5')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('rmb_unsent','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('fb_appid','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('fb_token','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('fb_r_url','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('fb_t_frm','index.php')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_o','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_g','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_m','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_p','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_d','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_u','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_q','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('crn_k','$cronkeyrnd')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('badwords','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('badrepls','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('blingon','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('blingint','20')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pholders','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('defaultsnip','Hello!')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('rqhints','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('multiavatar','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('stopwords','/me,/whois,/topic,/kick')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('profile_templt','<div class=\"x_bcolor_z x_top_rounded\" style=\"padding:10px;text-align:left;color:#fff;border-bottom:8px solid rgba(0,0,0,.2);background-image:url(%COVER%);background-size:cover\"> <div style=\"padding:10px;background-color:rgba(0,0,0,.5)\"><img src=\"%IMAGE%\" alt=\"\" style=\"float:left;width:120px;height:120px;margin-right:10px\"> <b style=\"font-size:120%\">%NAME%</b><br><small>%GROUP%<br>%AGE% %GENDER% %LOCATION%</small><br style=\"clear:both\"></div></div> <div class=\"x_bcolor_y x_bottom_rounded\" style=\"font-size:90%;padding:15px;text-align:left;height:160px;overflow:auto\">%EDUCATION% %OCCUPATION% %INTERESTS%<br>%ABOUT%</div>')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('profile_covers','backgrounds/cover01.jpg\r\nbackgrounds/cover02.jpg\r\nbackgrounds/cover03.jpg\r\nbackgrounds/cover04.jpg\r\nbackgrounds/cover05.jpg\r\nbackgrounds/cover06.jpg\r\nbackgrounds/cover07.jpg\r\nbackgrounds/cover08.jpg\r\nbackgrounds/cover09.jpg\r\nbackgrounds/cover10.jpg\r\nbackgrounds/cover11.jpg\r\nbackgrounds/cover12.jpg\r\nbackgrounds/cover13.jpg\r\nbackgrounds/cover14.jpg\r\nbackgrounds/cover15.jpg\r\nbackgrounds/cover16.jpg\r\nbackgrounds/cover17.jpg\r\nbackgrounds/cover18.jpg\r\nbackgrounds/cover19.jpg\r\nbackgrounds/cover20.jpg\r\nbackgrounds/cover21.jpg\r\nbackgrounds/cover22.jpg\r\nbackgrounds/cover23.jpg\r\nbackgrounds/cover24.jpg\r\nbackgrounds/cover25.jpg\r\nbackgrounds/cover26.jpg\r\nbackgrounds/cover27.jpg\r\nbackgrounds/cover28.jpg\r\nbackgrounds/cover29.jpg\r\nbackgrounds/cover30.jpg\r\nbackgrounds/cover31.jpg\r\nbackgrounds/cover32.jpg\r\nbackgrounds/cover33.jpg\r\nbackgrounds/cover34.jpg\r\nbackgrounds/cover35.jpg\r\nbackgrounds/cover36.jpg\r\nbackgrounds/cover37.jpg\r\nbackgrounds/cover38.jpg\r\nbackgrounds/cover39.jpg\r\nbackgrounds/cover40.jpg\r\nbackgrounds/cover41.jpg\r\nbackgrounds/cover42.jpg\r\nbackgrounds/cover43.jpg\r\nbackgrounds/cover44.jpg\r\n')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('utf8_set','Arabic,Armenian,Bengali,Bopomofo,Braille,Buhid,Canadian_Aboriginal,Cherokee,Cyrillic,Devanagari,Ethiopic,Georgian,Greek,Gujarati,Gurmukhi,Han,Hangul,Hanunoo,Hebrew,Hiragana,Inherited,Kannada,Katakana,Khmer,Lao,Latin,Limbu,Malayalam,Mongolian,Myanmar,Ogham,Oriya,Runic,Sinhala,Syriac,Tagalog,Tagbanwa,TaiLe,Tamil,Telugu,Thaana,Thai,Tibetan,Yi')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('utf8_run','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('utf8_msg','Please choose another name! Numeric-only names and and names containing letters of different alphabets are not allowed.')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('avsize','250')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('chaton','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('chatoff','Our chat is closed now...')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('rbox_sender','8000001:1:GodFather')";

$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('gbox_sender','8000003:1:GIFMaster')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('ptop','')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('pmlog_stop','86400')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('tns_length','200')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('tns_lowprv','0')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('meta_ref','same-origin')";

$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('abox_key','$aboxseckey')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('abox_sender','8000004:1:AuntHedwig')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('abox_count','0')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('abox_dtt','0')";

$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('geo_period','3600')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('geo_timeout','5')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('geo_center','35,33')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('geo_zoom_i','3')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('geo_zoom_m','13')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('geo_error','5')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('geo_grey','0')";
$install[]='INSERT INTO '.$dbss['prfx']."_settings VALUES('geo_max','50')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ip2c','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ip2hash','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('iplocate_key','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_on','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_api_src','pg_iphub')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_api_key','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_timeout','5')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_tcache','86400')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_wlist','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pg_failmsg','Please turn off your VPN and refresh.')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('tips_login','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('tips_reg','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('tips_pass','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('acpreadonly','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('genderlist','Male,Female')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('genderedit','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('force_av','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('offmsg_del','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('helpdesk_usr','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('helpdesk_desc','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('landing_header','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('landing_footer','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('unl_timeout','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('seo_page','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES ('stripe_apikey', '')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES ('webhook_secret', '$wsecretkey')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES ('shuffle_items', '1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES ('vipshop_info', 'Welcome to VIPSHOP! You can purchase VIP codes here. Select one and click BUY. A purchased VIP code can be applied only once.')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES ('vipshop_title', 'VIPSHOP')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES ('vipshop_theme', 'default')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES ('success_msg', 'We appreciate your business. Your VIP code will be displayed very soon. There is no need to reload this page.')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES ('cancel_msg', 'We are sorry but we cannot confirm your order.')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES ('missing_msg', 'We are sorry but we cannot find your order. Please contact us.')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vote_seeres','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vote_change','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vote_ipaddr','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vote_colors','000,ba443e,c17d51,cca851,22865e,ad1457,0d47a1,74554d,6a1b9a,086269')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_ofile','/tmp/online')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_cache','120')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_cross','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_onlu','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_onla','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_stat','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_tten','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('w_last','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('customhlrp','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('customhchat','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('globalfont','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('dnmode','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('rsz_emoji','160')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('tinting_c','#333')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('tinting_o','#444')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('customjs','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('bottomjs','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('splash','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('p2p_global','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('stun_svs','$iceservers')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('ask_av','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('pingws','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('p2p_level','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('uf_order','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('min_qstat','1')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('qui_limit','120')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('qui_icons','$qui_icons')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('verify','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('verify_subj','{LANG}')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('verify_mssg','{LANG}')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('preset_subj','{LANG}')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('preset_mssg','{LANG}')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_smtp_host','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_smtp_user','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_smtp_pass','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_smtp_from','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_smtp_auth','true')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_smtp_secu','tls')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_smtp_port','587')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_smtp_mail','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_smtp_more','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_blacklist','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vpm_passreset','0')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vcs_cs_usr','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vcs_cs_key','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vcs_sender','VerifySMS')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vcs_whitelist','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vcs_blacklist','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('clearscreen','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('mailchange','0')";

$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('vip_signs','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_token','')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_model','gpt-3.5-turbo')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_temperature','0.2')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_max_tokens','500')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_trigger','TGF,TheGodFather,GodFather')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_min_length','5')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_max_length','350')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_timeout','15')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_randoms','I am still here. Lurking in background...\nFor some reason I find some people moderately funny to hang out with.\nI usually do my utmost not to be angered or annoyed 😡 🤯 when I really feel angry and annoyed.\nChanging the topic... I know next to nothing about science or politics, but I do know a bit about 🕍 British history 👈. Test me, okay?\nPlease hold on, I just saw my mate ride off to choir practice on the back of that 🚲 bike.')";
$install[]="INSERT INTO ".$dbss['prfx']."_settings VALUES('openai_defmod','Keep it short and add a bit of sarcasm.\nMake your answer short and overly serious so that it sounds funny.')";

$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sticache1','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sticache2','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('avt_cache','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache1','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache2','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache3','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache4','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache5','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('svgcache6','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound1','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound2','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound3','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound4','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound5','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound6','')";
$install[]="INSERT INTO ".$dbss['prfx']."_scache VALUES('sound7','')";

$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(1,'14')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(2,'sans-serif')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(3,'')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(4,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(5,'222222')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(6,'F44336')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(7,'000000')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(8,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(9,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(10,'111111')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(11,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(12,'111111')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(13,'FFFFFF')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(14,'000000')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(15,'5')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(16,'100')";
$install[]="INSERT INTO ".$dbss['prfx']."_style VALUES(17,'')";

$install[]="INSERT INTO ".$dbss['prfx']."_qcats VALUES(1,'History Europe',1)";
$install[]="INSERT INTO ".$dbss['prfx']."_qcats VALUES(2,'Geography',1)";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Who was the first monarch of Great Britain?','Queen Anne','Alfred The Great','William the Conqueror','','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Which sultan conquered Constantinople in 1453?','Suleiman the Magnificent','Osman I','Mehmed II','','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Which king united England and France?','Henry V','Richard I','None','','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Who was the first Holy Roman Emperor?','Julius Caesar','Charlemagne','Augustus','','B')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Who was the longest reigning Roman emperor?','Vespasian','Augustus','Tiberius','','B')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Which was the first country to adopt Christianity as a state religion?','The Roman Empire','The Byzantine Empire','The Kingdom of Armenia','','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'What was the largest city in Europe in the 10th century?','Constantinople','Rome','London','','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Who said -The die is cast-?','Napoleon','Julius Caesar','Hitler','','B')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Where is set Shakespeare\'s Romeo and Juliet?','Verona','Rome','Venice','','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Who\'s the god of war in the Greek mythology?','Apollo','Ares','Hermes','','B')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Which king of France signed the The Edict of Nantes?','Louis XIV','François I','Henry IV','','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Where did the battle of Waterloo take place?','England','Netherlands','France','','B')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Who was known as the Iron Chancellor?','Margaret Thatcher','Hermann Goering','Otto von Bismarck','','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Which legendary general is said to have sworn undying enmity against Rome?','Vercingetorix','Hannibal','Attila','','B')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Which officer was the first field marshal in history to be captured?','Friedrich Paulus','Erwin Rommel','Erich von Manstein','','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Which campaign was led by Philip II of France, Richard I of England and Frederick I.','Albigensian Crusade','Fourth Crusade','Third Crusade','','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'What Merovingian became king in the fifth century A.D.?','Arpad','Clovis','Ethelred','','B')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'In Ancient Rome, who were the Lares?','Household spirits of dead ancestors','The elite corps of the Roman army','The Muses','','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Who was the faithful wife of Odysseus who waited 20 years for his return from the Trojan War?','Penelope','Aspasia','Telemachus','','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,1,'Who started the movement of Protestant Reformation in Europe?','Martin Luther','John Calvin','Erasmus','','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Which of these seas is largest?','Bering','Arabian Sea','Mediterranean','Gulf of Mexico','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'The tallest mountain peak in the US is Mount McKinley. The second tallest is:','Mount Saint Elias','Glacier Peak','Mount Washington','Gannett Peak','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Which EU member is divided into a Greek and a Turkish part?','Greece','Slovenia','Cyprus','Bulgaria','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Which EU member has a significant part of its territory below sea level?','Belgium','The Netherlands','Poland','Estonia','B')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Which of the following countries and states shares its name with its capital city?','New York','Malta','Luxembourg','Oklahoma','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Which one of these is not a Greek Mediterranean island?','Minorca','Lemnos','Crete','Santorini','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Riga is the capital of which Baltic country?','Estonia','Lithuania','Belarus','Latvia','D')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'What famous square is located in Vatican City, the papal enclave within Rome?','Campo dei Fiori','St. Pauls Square','St. Peters Square','Piazza San Marco','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'What is the name of the island shared by Haiti and the Dominican Republic?','Haiti','Hispaniola','Isla Dominica','Carib Island','B')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'What portion of the Earth’s surface do oceans cover?','More than two thirds','One third','Half of it','One fourth','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Montgomery is the capital city of this US state, and its largest city is Birmingham.','Arizona','Alaska','Arkansas','Alabama','D')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'The Himalayan range, part of which is Mount Everest, stretches across five different countries in Asia. Which of the following countries is not among them?','Israel','Bhutan','Pakistan','India','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'What river that flows through Spain and Portugal is the longest river on the Iberian Peninsula?','Guadiana','Duero','Guadalquivir','Tagus','D')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Maryland derives its name from its Catholic founders, who named the state after which historic person?','Mary, Queen of Scots','Mary Magdalene','The Virgin Mary','Queen Henrietta Maria of France','D')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Which country, Slovakia or Slovenia, used to be part of Yugoslavia?','Slovenia','Both','Slovakia','None of them','A')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'What sea borders Egypt to the east?','Black Sea','Yellow Sea','Mediterranean Sea','Red Sea','D')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'The Ottoman Empire used to rule over North Africa, the Middle East and south-eastern Europe. Which modern day country is the successor of the empire?','Israel','Saudi Arabia','Turkey','Egypt','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'The capital city of what country is called Quito?','Bolivia','Macedonia','Ecuador','Yemen','C')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'What is the name of the capital city of the U.S. state of Wyoming.','Boise','Des Moines','Salt Lake City','Cheyenne','D')";
$install[]="INSERT INTO ".$dbss['prfx']."_qdata VALUES(NULL,2,'Which of these European countries shares a land border with Sweden?','Switzerland','Denmark','Lithuania','Norway','D')";

for($i=0;$i<count($install);$i++){neutral_query($install[$i]);}

// end db install
?>

<div class="holder">
<h2><?php print $lang['step'];?> 2</h2>
<hr>

<div><?php print $lang['step3_desc'];?></div>
<br><hr>

<form action="done.php" method="post" autocomplete="off">

<div class="left">
<?php print $lang['user'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="user" value="" maxlength="16" onfocus="input_style_back(this)">
</div><br><hr>

<div class="left">
<?php print $lang['mail'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="mail" value="" maxlength="64" onfocus="input_style_back(this)">
</div><br><hr>

<div class="left">
<?php print $lang['pass'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="pass" value="" maxlength="32" onfocus="input_style_back(this)">
</div><br><hr>

<div class="left">
<?php print $lang['ques'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="ques" value="" maxlength="128" onfocus="input_style_back(this)">
</div><br><hr>

<div class="left">
<?php print $lang['answ'];?>
</div>
<div class="right">
<input type="text" class="x_accent_bb s250" name="answ" value="" maxlength="128" onfocus="input_style_back(this)">
</div><br><hr>

<input type="button" class="round4 x_bcolor_bg" style="width:100%;font-weight:bold;height:50px" value="<?php print $lang['next'];?>" onclick="check_form()">
</form>
</div>

<script>
function check_form(){
f=document.forms[0];s='x_accent_bg s250';
a=f.user.value; f.user.value=a.replace(/[^a-z0-9]/gi,'');
if(f.user.value.trim().length<3){f.user.className=s;return false}
if(f.mail.value.trim().length<7){f.mail.className=s;return false}
if(f.mail.value.indexOf('@')==-1){f.mail.className=s;return false}
if(f.mail.value.indexOf('.')==-1){f.mail.className=s;return false}
if(f.mail.value.indexOf(' ')!=-1){f.mail.className=s;return false}
if(f.pass.value.trim().length<3){f.pass.className=s;return false}
if(f.ques.value.trim().length<1){f.ques.className=s;return false}
if(f.answ.value.trim().length<1){f.answ.className=s;return false}
document.forms[0].submit()}

function input_style_back(x){x.className='x_accent_bb s250'}

document.forms[0].reset()
window.onunload=function(){}
</script>
</body>
</html>
