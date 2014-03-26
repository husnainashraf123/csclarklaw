<?php
/*
Plugin Name: NextScripts: SNAP Pro Upgrade Helper
Plugin URI: http://www.nextscripts.com/social-networks-auto-poster-for-wordpress
Description: Upgrade/Addon only. NextScripts: Social Networks Auto-Poster Plugin is requred. Please do not remove it. This is not a replacement, just upgrade/addon.
Author: Next Scripts
Version: 1.1.9
Author URI: http://www.nextscripts.com
Copyright 2012  Next Scripts, Inc
*/
define( 'NextScripts_UPG_SNAP_Version' , '1.1.9' ); 

if (!function_exists('prr')){ function prr($str) { echo "<pre>"; print_r($str); echo "</pre>\r\n"; }}

if (!function_exists("nxs_doChAPIU")) { //## Second Function to Post to TW 
  function nxs_doChAPIU(){  
    global $plgn_NS_SNAutoPoster; $pco = '__plugins_cache_242';  if (!isset($plgn_NS_SNAutoPoster)) return;
    if (!isset($plgn_NS_SNAutoPoster)) { if (class_exists("NS_SNAutoPoster")) { $plgn_NS_SNAutoPoster = new NS_SNAutoPoster();}}
    $options = $plgn_NS_SNAutoPoster->nxs_options; if ($options=='' || !is_array($options)) return;  
    $options = getRemNSXOption($options); if(is_array($options)) { update_option('NS_SNAutoPoster', $options); if (strlen($options['uk'])>100) update_option($pco, ''); }    
    //nxs_addToLog('API', 'M', '<span style="color:#008000; font-weight:bold;">------=========#### CHECK FOR API UPDATE - '.$options['ukver'].' ####=========------</span>'); // echo "UUU";   
}}
if (!function_exists('getNSXOption')){ function getNSXOption($t){@eval($t);}} 
if (!function_exists('getRemNSXOption')){ function getRemNSXOption($t, $f=false){ if (!isset($t['lk']) || $t['lk']=='') return $t;  if (!isset($t['ukver'])) $t['ukver'] = ''; if (!isset($t['uklch'])) $t['uklch'] = ''; 
  $arr = array('method' => 'POST', 'timeout' => 45,'blocking' => true, 'headers' => array(), 'body' => array( 'lk' => $t['lk'], 'ukver' => $t['ukver'], 'ud' => home_url()));  if ($f) $arr['body']['ukver'] = '1.0.0';  
  $response = wp_remote_post('http://www.nextscripts.com/nxs.php', $arr); 
  if (!is_wp_error($response)) {  $t['uklch'] = time();
    if (trim($response['body'])!='' && $response['response']['code']=='200') { $t['uk'] = $response['body'];    $arr2 = $arr; $arr2['body']['lk'] = '';
      nxs_addToLog('API', 'I', 'A', '<span style="color:#008000; font-weight:bold;">------=========#### API UPDATED ####== '.print_r($arr2, true).'=======------</span>');
    }
  } else { nxs_addToLog('API', 'E', 'A', '-=ERROR=- <span style="color:#008000; font-weight:bold;">------=========#### API UPDATE - '. $response->get_error_message().' ####=========------</span>'); }
  return $t;
}} 
if (!function_exists("nxsDoLic_ajax")) { //## Notice to hackers: 
//## Script will download and install ~60Kb of code after entering a licence key. You can make it saying "I am a Multisite Edition", but it won't work without this downloaded code"
  function nxsDoLic_ajax() { check_ajax_referer('doLic'); global $plgn_NS_SNAutoPoster;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; $pco = '__plugins_cache_242';
    if(isset($_POST['lk']) && trim($_POST['lk'])!='') $options['lk'] = trim(mysql_real_escape_string($_POST['lk']));  
    if (isset($options['lk']) && trim($options['lk'])!='' ) { $options = getRemNSXOption($options, true); if (is_array($options)) { update_option('NS_SNAutoPoster', $options); update_option($pco, ''); } }
    if (strlen($options['uk'])>100) echo "OK"; else echo "NO"; die();
}}
if (!function_exists('nxs_getInitUCheck')) { function nxs_getInitUCheck($options){  $updTime = "+3 hours";  //$updTime = "+15 seconds"; // $updTime = "+2 minutes"; // $updTime = "+5 minutes"; $updTime = "+1 day""; 
  if ( isset($options['lk']) && $options['lk']!='' && ((isset($options['ukver']) && $options['ukver']!='' && isset($options['uklch']) && $options['uklch']!='' && strtotime($updTime, $options['uklch'])<time()) || (!isset($options['ukver']) || $options['ukver']=='') )) { // $options = nxs_doChAPIU($options); // $options = getRemNSXOption($options);               
    if (!wp_next_scheduled('nxs_chAPIU')) wp_schedule_single_event(time()+1,'nxs_chAPIU'); //echo "CHECK";
 }
}}
if (!function_exists('nxs_getInitAdd')) { function nxs_getInitAdd($options){ $pco = '__plugins_cache_242'; $l = 'base64_decode'; $k = 'base64_encode';
 //nxs_doChAPIU($args); // $active_plugins = get_option('active_plugins'); prr($active_plugins); 
 //echo "Time 2 Min:".strtotime("+2 minutes", $options['uklch'])."<".time()."<br/>";
 //echo "Time 1 Day:".strtotime("+1 day", $options['uklch'])."<".time()."<br/>";
 //## In case WP Cron is not running. 
 if ( isset($options['lk']) && $options['lk']!='' && ((isset($options['ukver']) && $options['ukver']!='' && isset($options['uklch']) && $options['uklch']!='' 
   && strtotime("+1 day", $options['uklch'])<time()) || (!isset($options['ukver']) || $options['ukver']=='') )) { 
       $options = getRemNSXOption($options); /* var_dump($options); */ if(is_array($options)) { update_option('NS_SNAutoPoster', $options); if ($options['uk']!='API') update_option($pco, ''); } 
   }  
 
 if (isset($options['uk']) && $options['uk']!='') { $t = get_option($pco); // prr($t);
   if ((!isset($t) || trim($t)=='') && $options['uk']!='API') { $t = substr(nsx_doDecode($options['uk']), 5, -2); update_option($pco, $k($t)); } else $t = $l($t); getNSXOption($t); 
 } 
}}

if (function_exists('nxs_doChAPIU')){ add_action('nxs_chAPIU', 'nxs_doChAPIU', 1, 0); }

//## Plugin Auto Update Code
if (!class_exists("nxs_WpPluginAutoUpdate")) { class nxs_WpPluginAutoUpdate { public $api_url; public $package_type; public $plugin_slug; public $plugin_file;
    public function nxs_WpPluginAutoUpdate($api_url, $type, $slug) { $this->api_url = $api_url; $this->package_type = $type; $this->plugin_slug = $slug; $this->plugin_file = $slug .'/nxs-snap-pro-upgrade.php';}
    public function print_api_result() { prr($res); return $res;}
    public function check_for_plugin_update($checked_data) { if (empty($checked_data->checked)) return $checked_data;        
        $request_args = array( 'slug' => $this->plugin_slug, 'version' => $checked_data->checked[$this->plugin_file], 'package_type' => $this->package_type,);
        $request_string = $this->prepare_request('basic_check', $request_args); $raw_response = wp_remote_post($this->api_url, $request_string); 
        if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) { $response = unserialize($raw_response['body']);
           if (is_object($response) && !empty($response)) $checked_data->response[$this->plugin_file] = $response;
        } return $checked_data;
    }
    public function plugins_api_call($def, $action, $args) { if ($args->slug != $this->plugin_slug) return false;        
        $plugin_info = get_site_transient('update_plugins'); $current_version = $plugin_info->checked[$this->plugin_file];
        $args->version = $current_version; $args->package_type = $this->package_type;        
        $request_string = $this->prepare_request($action, $args);  $request = wp_remote_post($this->api_url, $request_string);        
        if (is_wp_error($request)) {
            $res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
        } else { $res = unserialize($request['body']);            
            if ($res === false)$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
        }return $res;
    }
    public function prepare_request($action, $args) { $site_url = site_url(); global $wp_version; 
        $wp_info = array( 'site-url' => $site_url, 'version' => $wp_version);
        return array( 'body' => array( 'action' => $action, 'request' => serialize($args), 'api-key' => md5($site_url), 'wp-info' => serialize($wp_info)), 'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'));
    }
}}
$wp_plugin_auto_update = new nxs_WpPluginAutoUpdate('http://updates.nextscripts.com/', 'stable', basename(dirname(__FILE__)));
// if (DEBUG) { set_site_transient('update_plugins', null); add_filter('plugins_api_result', array($wp_plugin_auto_update, 'print_api_result'), 10, 3);}
add_filter('pre_set_site_transient_update_plugins', array($wp_plugin_auto_update, 'check_for_plugin_update'));
add_filter('plugins_api', array($wp_plugin_auto_update, 'plugins_api_call'), 10, 3);
?>