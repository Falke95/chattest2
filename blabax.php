<?php

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings();

$dnmode=2;
if(isset($_GET['dnmode']) && $_GET['dnmode']==='0' && $settings['dnmode']!='0'){newcookie($xcookie_dn_mode[0],'0',time()+$xcookie_dn_mode[1],'/');$_COOKIE[$xcookie_dn_mode[0]]='0';$dnmode=0;$settings['style_delivery']=$mode0css;$settings['tinting_c']='#241f31';$settings['tinting_o']='#fff';}
if(isset($_GET['dnmode']) && $_GET['dnmode']==='1' && $settings['dnmode']!='0'){newcookie($xcookie_dn_mode[0],'1',time()+$xcookie_dn_mode[1],'/');$_COOKIE[$xcookie_dn_mode[0]]='1';$dnmode=1;$settings['style_delivery']=$mode1css;$settings['tinting_c']='#000000';$settings['tinting_o']='#000';}
if(isset($_GET['dnmode']) && $_GET['dnmode']==='2'){newcookie($xcookie_dn_mode[0],'',time()-3600,'/');$_COOKIE[$xcookie_dn_mode[0]]='2';$dnmode=2;}
if(isset($_COOKIE[$xcookie_dn_mode[0]]) && $_COOKIE[$xcookie_dn_mode[0]] === '0' && $settings['dnmode']>0){$dnmode=0;$settings['style_delivery']=$mode0css;$settings['tinting_c']='#241f31';$settings['tinting_o']='#fff';}
if(isset($_COOKIE[$xcookie_dn_mode[0]]) && $_COOKIE[$xcookie_dn_mode[0]] === '1' && $settings['dnmode']>0){$dnmode=1;$settings['style_delivery']=$mode1css;$settings['tinting_c']='#000000';$settings['tinting_o']='#000';}

get_language(); 
if(function_exists('customphp')){customphp();} 

if(isset($_COOKIE[$xcookie_uidhash[0]])){ require_once 'incl/cookieauth.php'; } else{ redirect('account.php');die(); }

if(!isset($xuser['id']) || !isset($xuser['name'])){print '...';die();}
$xuser['bwsuser']=1; if(strlen($xuser['password'])>0){$xuser['bwsuser']=2;}

$uid=$xuser['id'];
$welcome=$settings['welcome_msg'];

// check id & ip ban
if($uid!=1){
$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_ban WHERE (ipaddr='$ipaddr' OR userid=$uid) AND $timestamp < timestamp");
if(neutral_num_rows($res)>0){
$info_timeout=0; $info_line=$lang['info_ban'];
require_once('templates/info.pxtm'); die();}
}

if($uid>1 && $settings['chaton']<1){
$info_timeout=0; $info_line=$settings['chatoff'];
require_once('templates/info.pxtm'); die();}

// ---

$uname=abc123($xuser['name'],'');

$user_visible=1;
if(isset($stealth_users) && is_array($stealth_users) && in_array($uid,$stealth_users)){$user_visible=0;}

$tstamp_token=$timestamp+$settings['token_validity'];

$b64name=base64_encode($uname);
$admin=0;if($uid==1){$admin=1;}
$hash=hash('sha256',$b64name.$uid.'1'.$admin.$user_visible.$tstamp_token.$settings['server_key']);

$stoken=hash('sha256',$uid.$settings['random_salt']);
$stoken=$uid.'z'.$stoken;

$ptoken=0;$applink='';

//user extra
$mymotto=''; $myavatar=av2letter($xuser['name']);
$res=neutral_query('SELECT * FROM '.$dbss['prfx'].'_uxtra WHERE id='.$uid);
if(neutral_num_rows($res)>0){
$uxtra=neutral_fetch_array($res); $mymotto=htmlspecialchars($uxtra['motto']);
if(strlen($uxtra['image'])>0){$myavatar=htmlspecialchars($uxtra['image']);}}

$b64ava=base64_encode($myavatar);
$mtoken=hash('sha256',$uid.$b64name.'1'.$b64ava.$settings['random_salt']);
$mtoken=$uid.'|'.$b64name.'|1|'.$b64ava.'|'.$mtoken;

if($user_visible>0){
$country='-'; if(isset($_SERVER['GEOIP_COUNTRY_NAME'])){$country=neutral_escape($_SERVER['GEOIP_COUNTRY_NAME'],64,'str');}
$roundhour=gmdate('i',$timestamp)*60+gmdate('s',$timestamp);$roundhour=$timestamp-$roundhour;

$res=neutral_query('SELECT * FROM '.$dbss['prfx']."_iplog WHERE id=$uid AND ipaddr='$ipaddr' AND timestamp>$roundhour");
if(neutral_num_rows($res)<1){neutral_query('INSERT INTO '.$dbss['prfx']."_iplog VALUES($uid,'$uname','$ipaddr',$timestamp,'$country')");}
}

// prepare list of emoticons to replace
require_once('emocodes.php');

$emos2js='';  $emos2dv='';

foreach ($emoticons as $emo) {
$emo=explode(' ',$emo);

if(isset($emo[2])){
$emos2js.="emos['".escape_emo($emo[0])."']='".$emo[1]."';\r\n";
if($emo[2]==1){$emos2dv.='<span class="'.$emo[1].' chat_list_emoticon" onclick="shoop(this,1,100);emo2input(\''.$emo[0].'\')"></span>';}
}}

//prepare language options
$lang2select=array();
foreach ($lang_list as $key => $value) {
$sel='';if($langsel==$key){$sel=' selected="selected"';}
$lang2select[]='<option title="'.$value[0].'" value="'.$key.'"'.$sel.'>'.$value[1].'</option>';
} sort($lang2select); $lang2select=implode("\n",$lang2select);

// get stickers from cache or set empty array if no cache
$mcach=neutral_query('SELECT * FROM '.$dbss['prfx']."_scache WHERE id='sticache1' OR id='sticache2'");
while($row=neutral_fetch_array($mcach)){$msti[$row['id']]=$row['value'];}
if(strlen($msti['sticache1'])<1){$sticker_sets=array();} else{$sticker_sets=unserialize(base64_decode($msti['sticache1']));}
if(strlen($msti['sticache2'])<1){$stick2js='stickers=new Array();';} else{$stick2js=base64_decode($msti['sticache2']);}

// prepare badwordsfilter
$badwordsarray=''; $repwordsarray='';
if(strlen($settings['badwords'])>0 && strlen($settings['badrepls'])>0){ 
$badwordsarray=base64_encode($settings['badwords']);
$repwordsarray=base64_encode($settings['badrepls']);
}

// prepare language entries to be used with JS functions
$lang2js='';
foreach ($js_lang as $key => $value) {
$lang2js.="lang['".$key."']='".$value."';\r\n";
}

// prepare colors and set color on load
$colors=''; $css_colors=''; $i=0;
if($dnmode>1){
$db_col=explode('|',$settings['colors']);
foreach ($db_col as $value) {$i+=1;
if(preg_match("/^[a-fA-F0-9]+$/", $value) == 1 && $value!='123456') {
$css_colors.='.tt'.$i.'{color:#'.$value.'}'."\r\n";
$colors.='<span onclick="swap_color('.$i.',\'#'.$value.'\',1)" class="x_circle panel_color" style="background-color:#'.$value.'"></span>'."\r\n";
}}}

// prepare pm log
if(!isset($settings['pmlog_stop'])){$pmlogstop=86400;}else{$pmlogstop=(int)$settings['pmlog_stop'];}

if($pmlogstop>0){

$pmhist=$timestamp-$pmlogstop; $pmlog='';
$res=neutral_query('SELECT userid,username,a.image AS avatar,MAX(timestamp) AS timestamp FROM (SELECT userid,username,timestamp FROM '.$dbss['prfx']."_messages WHERE roomid=0 AND timestamp>$pmhist AND touserid=$uid) AS t LEFT JOIN ".$dbss['prfx'].'_uxtra a ON userid=a.id GROUP BY userid,username,avatar ORDER BY timestamp DESC LIMIT 20 OFFSET 0');

while($row=neutral_fetch_array($res)){

$pmid=$row['userid'];
$pmname=htmlspecialchars($row['username']);
$pmdate=gmdate('Y-m-d',$row['timestamp']);
if(isset($row['avatar']) && strlen($row['avatar'])>0){$pmavtr=htmlspecialchars($row['avatar']);}else{$pmavtr=av2letter($pmname);}

$pmlog.='<div id="pmd'.$pmid.'" class="x_accent_bb pointer panel_pmitem" onclick="if(!show2user('.$pmid.',this)){setTimeout(\'manage_esc()\',100)}">';
$pmlog.='<img class="panel_pmavtr x_circle" src="'.$pmavtr.'" alt="">';
$pmlog.='<div style="font-weight:bold;text-align:left" class="shorten">'.$pmname.'</div><small>'.$pmdate.'</small><br style="clear:both"></div>';

}}else{$pmlog='';}

// prepare rooms
$rooms=array(); $rooms2js='rooms=new Array();'; $room_coosel=0; $room_getsel=0; $landing_room=1; 

if(isset($_COOKIE[$dbss['prfx'].'_room'])){$room_coosel=(int)$_COOKIE[$dbss['prfx'].'_room'];newcookie($dbss['prfx'].'_room','',time()-3600,'/');}
if(isset($_GET['room'])){$room_getsel=(int)$_GET['room'];}

$res=neutral_query('SELECT * FROM '.$dbss['prfx'].'_rooms ORDER BY zorder,id ASC');
while($row=neutral_fetch_array($res)){
if($row['id']==$room_coosel && $room_getsel<1){$landing_room=$room_coosel;}
if($row['id']==$room_getsel){$landing_room=$room_getsel;}
$rooms[]=array($row['id'],$row['name'],$row['description'],$row['color']);
$rooms2js.="rooms[".$row['id']."]=new Array('".$row['color']."',roomhistbutton,0,'',0);";}

// prepare room backgrounds / css
$svgbg='<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 200 100"><text font-size="'.$settings['roombgs'].'px" fill-opacity="opatxt" font-weight="bold" y="90" x="5" font-family="fnttxt" fill="#txtclr">svgtxt</text></svg>';
$bgrooms='';
foreach($rooms as &$bgt){

$col_repl=$settings['roombgc'];
if($col_repl=='123456'){$col_repl=$bgt[3];}
$tra_repl=$settings['roombgt']/100;

if($settings['showroombg']>0){
$notag=htmlspecialchars(substr(strip_tags($bgt[1]),0,$settings['roombgl']));
$curbg=str_replace('svgtxt',$notag,$svgbg);
$curbg=str_replace('txtclr',$col_repl,$curbg);
$curbg=str_replace('opatxt',$tra_repl,$curbg);
$curbg=str_replace('fnttxt',$settings['roombgf'],$curbg);
$curbg=base64_encode($curbg);
$srm='{background-image: url("data:image/svg+xml;base64,'.$curbg.'")}';}
else{$srm='{background-image:none}';}

$bgrooms.='.rr'.$bgt[0].$srm."\n";
}

// fake users
$ctm=gmdate('H',time()+$settings['acp_offset']*60);
$res=neutral_query('SELECT a.id AS fid, a.status AS fstatus, b.name AS fname, c.image AS favatar FROM '.$dbss['prfx'].'_ufake a INNER JOIN '.$dbss['prfx']."_users b ON a.id=b.id AND $ctm>=a.hour_begin AND $ctm<a.hour_end LEFT JOIN ".$dbss['prfx']."_uxtra c ON a.id=c.id");
$ufake=''; $rcount=0;
if(neutral_num_rows($res)>0){
$ufake=array();
while($row=neutral_fetch_array($res)){
	if(isset($row['favatar']) && strlen($row['favatar'])>0){$favatar=base64_encode($row['favatar']);}else{$favatar=base64_encode(av2letter($row['fname']));}
	$rcount-=1; $ufake[] = "'$rcount':[".$row['fid'].",1,'".$row['fname']."',".$row['fstatus'].",'".$favatar."']";}
$ufake=implode(',',$ufake);
$ufake='fls_online={'.$ufake.'};';
}

// textbox placeholder array
$placeholders=str_replace(["\r",'"'],'',$settings['pholders']);
$placeholders=htmlspecialchars($placeholders);
$placeholders=explode("\n",$placeholders); 
foreach($placeholders as $key => $value){if(strlen(trim($placeholders[$key]))>0){
	$placeholders[$key]="'".base64_encode(trim($placeholders[$key]))."'";}}
$placeholders=implode(',',$placeholders); 
if(strlen($placeholders)>0){$placeholders='placeholders=new Array('.$placeholders.');'."\n";}
else{$placeholders='placeholders=new Array();'."\n";}

if(isset($msg_enter) && isset($sys_xuser) && !in_array($uid,$stealth_users)){
neutral_query('INSERT INTO '.$dbss['prfx']."_messages VALUES(NULL,999999,0,0,'$sys_xuser',0,'','$uname $msg_enter',0,0,$timestamp,'1')");
}

require_once('templates/blabax.pxtm');
?>