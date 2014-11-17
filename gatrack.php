<?php
/*
Plugin Name: HotChalk - GATrack
Plugin URI: http://www.hotchalk.com/
Version: 07/28/2014
Author: Zander Fields
Description: This plugin allows for direct implementation of the Google Analytics Event Tracking and Analytics Code.


*/

/* Follow the steps below to create a new plugin from this template */

/* In this file:
/* Replace Plugin with Nameofyourplugin
/* Replace HC_GATRACK_ with HC_NAMEOFYOURPLUGIN_ throughout */
/* Replace hc-gatrack with hc-nameofyourplugin throughout */
/* Replace HC_Gatrack with HC_Nameofyourplugin throughout */
/* Replace hcGatrack with hcNameofyourplugin throughout */
/* Replace hc_gatrack with hc_nameofyourplugin throughout */

/* 
	Change the directory name from hc-plugin to hc-nameofyourplugin.
	Change the file name hc-plugin.php to hc-nameofyourplugin.php
	Change the file name hc-plugin.css to hc-nameofyourplugin.css
*/

define ('HC_GATRACK_VERSION', '07/28/2014');
define ('HC_GATRACK_PLUGIN_URL', plugin_dir_url(__FILE__));
define ('HC_GATRACK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define ('HC_GATRACK_SETTINGS_LINK', '<a href="'.home_url().'/wp-admin/admin.php?page=hc-gatrack">Settings</a>');


class HC_Gatrack {
	/* define any localized variables here */
	private $myPrivateVars;
	private $opt; /* points to any options defined and used in the admin */

	function __construct() {
		/* Best practice is to save all your settings in 1 array */
		/*   Get this array once and reference throughout plugin */
		$this->opt = get_option('hcGatrack');
		
		/* You can do things once here when activating / deactivating, such as creating
		     database tables and deleting them. */
		register_activation_hook(__FILE__,array($this,'activate'));
		register_deactivation_hook( __FILE__,array($this,'deactivate'));
		
		/* Enqueue any scripts needed on the front-end */
		add_action('wp_enqueue_scripts', array($this,'frontScriptEnqueue'));
		
		/* Create all the necessary administration menus. */
		/* Also enqueues scripts and styles used only in the admin */
		add_action('admin_menu', array($this,'adminMenu'));
		
		/* adminInit handles all of the administartion settings  */ 
		add_action('admin_init', array($this,'adminInit'));
		
		// if you need anything in the footer, define it here
		//add_action('wp_footer', array($this,'footerScript'));
		
		$ga_plugin = plugin_basename(__FILE__); 
		
		// this code creates the settings link on the plugins page
		add_filter("plugin_action_links_$ga_plugin", array($this,'pluginSettingsLink'));
		
		// create any shortcodes needed
		add_shortcode( 'hc_gatrack', array($this,'shortcode'));
		
		// Add GA Event tracking code
		add_action('wp_footer',array($this,'ga_script_footer'));
		
		// Add GA tracking code
		
		add_action('wp_head',array($this,'ga_script_head'));
    }
	
	// Enqueue any front-end scripts here
	function frontScriptEnqueue() {
		//wp_enqueue_script('swaplogo',HC_PLUGIN_PLUGIN_URL.'js/swaplogo.js',false,null);		
		 //wp_enqueue_style('hc_gatrack', HC_GATRACK_PLUGIN_URL . "css/hc-gatrack.css");
	}

    /* these admin styles are only loaded when the admin settings page is displayed */
	function adminEnqueue() {
		// wp_enqueue_style('hc-plugin-style',HC_PLUGIN_PLUGIN_URL.'css/hc_plugin.css');
	}
	
	// Enqueue any scripts needed in the admin here 
	function adminEnqueueScripts() {
		// wp_enqueue_script('jquery-ui-sortable');
		// wp_enqueue_script('jquery-ui-datepicker');
	}
	
	// code that gets run on plugin activation.
	// create any needed database tables or similar here
	function activate() {
	}

	// code the gets run on plugin de-activation
	// remove any database tables or other settings here
	function deactivate() {
	}
	
	// Setup the admin menu here.  Also enqueues backend styles/scripts
	// images/icon.png is the icon that appears on the admin menu
	function adminMenu() {
		add_menu_page('HotChalk','HotChalk','manage_options','hc_top_menu','',plugin_dir_url(__FILE__).'/images/icon.png', 88.8 ); 
		
		$page = add_submenu_page('hc_top_menu','Gatrack','Gatrack','manage_options','hc-gatrack',array($this,'adminOptionsPage'));
		
		remove_submenu_page('hc_top_menu','hc_top_menu'); // remove extra top level menu item if there
		
		 /* Using registered $page handle to hook stylesheet loading */
		add_action( 'admin_print_styles-' . $page, array($this,'adminEnqueue'));
		add_action( 'admin_print_scripts-' . $page, array($this,'adminEnqueueScripts'));
	}
	
	// settings link on plugins page
	function pluginSettingsLink($links) { 
	  $settings_link = HC_GATRACK_SETTINGS_LINK; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
	
	/* Define the settings for your plugin here */ 
	/* Create as many sections as needed */ 
	function adminInit(){
		register_setting( 'hcGatrack', 'hcGatrack', array($this,'optionsValidate'));
		add_settings_section('hcGatrackSection1', 'Google Analytics Event Tracking and Code Insertion', array($this,'sectionText1'), 'hc-gatrack');
		add_settings_field('hcGatrackSection1', '', array($this,'section1settings'), 'hc-gatrack', 'hcGatrackSection1');
	}
		
	// You can validate input here on saving
	// This gets called when click 'Save Changes' from the admin settings.
	// Process input and then return it
	function optionsValidate($input) {
		return $input;
	}
	
	// Settings section description
	function sectionText1() {
		?>
        <p><strong>The code you paste will be inserted into the header of the page the plugin is implemented on.</strong> This plugin will allow for implementation of the Google Analytics & Event Tracking tool. The GATrack tool allows insertion of the Google Analytics tracking code on the page it is placed, as well as the creation of event tracking for all HTML input and select types.</p>
        <?php
	}
	
	function ga_script_footer(){
		?>
		<!-- GA Event Tracking Code -->
		
		<!-- Input element event tracker -->
	<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('input').bind('blur', function () {
        	var name = $(this).attr('name');
        	var pathname = window.location.pathname;
            ga('send', 'event', 'Clicked Inputs', name, pathname, {'nonInteraction': 1});
        });
		
		/* Select element event tracker */
        $('select').bind('blur', function () {
       		 var name1 = $(this).attr('name');
       		 var pathname1 = window.location.pathname;
             ga('send', 'event', 'Clicked Selects', name1, pathname1, {'nonInteraction': 1});
        });
        $('a').bind('click', function () {
       		 var name2 = $(this).attr('href');
       		 var pathname2 = window.location.pathname;
             ga('send', 'event', 'Clicked Links', name2, pathname2, {'nonInteraction': 1});
        });
		
    });
</script>

<!--End GA Tracking Code -->
<?php
}
	
function ga_script_head(){
	echo $this->opt['GAcode'];
}
	
	
	// Example setting in admin
	
	function section1settings() {
		echo '<div class="section1">';
	   	echo '<label class="default" style="display:inline-block; width:150px;">Paste GA Code here</label><textarea rows="15" cols="75" name="hcGatrack[GAcode]" >';
		echo $this->opt['GAcode'];
		echo '</textarea>';
		echo '</div>';
		submit_button();
	}

	// Example shortcode
	// [hc_plugin parm1="parm1_setting"]
	function shortcode( ) {
		
		ob_start(); ?>


			
			
		<?php return ob_get_clean(); 
	}
	
	// footer scripts		
	function footerScript () {
		?>
		<script type="text/javascript">
		// any needed javascript code here - goes in footer
        </script>
        <?php
	}
	
	/* the Settings page for this plugin */
	function adminOptionsPage() { ?>
		<div id="hc_gatrack">
		<h2>(GATrack) - HotChalk, Inc. v<?php echo HC_GATRACK_VERSION; ?></h2>
		<form method="post" action="options.php">
		<?php settings_fields('hcGatrack'); ?>
		<?php do_settings_sections('hc-gatrack'); ?>
		</form></div>
		<?php
	}
}

$hcPlugin = new HC_Gatrack();
?>