<?php

require_once('config.php');
require_once('incl/main.php');

neutral_dbconnect(); $settings=get_settings();
if(isset($_COOKIE[$xcookie_dn_mode[0]]) && $_COOKIE[$xcookie_dn_mode[0]] === '0'){$settings['style_delivery']=$mode0css;$settings['tinting_c']='#241f31';$settings['tinting_o']='#fff';}
if(isset($_COOKIE[$xcookie_dn_mode[0]]) && $_COOKIE[$xcookie_dn_mode[0]] === '1'){$settings['style_delivery']=$mode1css;$settings['tinting_c']='#000000';$settings['tinting_o']='#000';}

get_language(); 
if(function_exists('customphp')){customphp();} 

$info_url=''; $info_line='';
if(isset($_GET['q']) && $_GET['q']=='mtw'){$info_line=$lang['multi_conn'];}
if(isset($_GET['q']) && $_GET['q']=='rem'){$info_line=$lang['kicked_out'];}
if(isset($_GET['q']) && $_GET['q']=='ban'){$info_line=$lang['info_ban'];}
if(isset($_GET['q']) && $_GET['q']=='nop'){$info_line=$lang['error_noperms'];}

require_once('templates/info.pxtm'); 

?>