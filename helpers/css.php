<?php 

PL_Css_Helper::init();

class PL_Css_Helper {
	
	function init () {		
		// add_action( 'admin_init', array( __CLASS__, 'admin' ));
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin' ) );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'customizer' ) );
	}

	function admin ($hook) {
		// Inject premium themes logic into the themes admin page when visiting from any site on the hosted env...
		if ($hook == 'themes.php' && defined('HOSTED_PLUGIN_KEY')) {		
			self::register_enqueue_if_not('global-css', trailingslashit(PL_CSS_URL) .  'global.css');
			self::register_enqueue_if_not('jquery-ui', trailingslashit(PL_JS_LIB_URL) .  'jquery-ui/css/smoothness/jquery-ui-1.8.17.custom.css');
			// self::register_enqueue_if_not('jquery-ui-dialog', OPTIONS_FRAMEWORK_DIRECTORY.'css/jquery-ui-1.8.22.custom.css');
		}

		$pages = array('placester_page_placester_properties', 
					   'placester_page_placester_property_add', 
					   'placester_page_placester_settings', 
					   'placester_page_placester_support', 
					   'placester_page_placester_theme_gallery', 
					   'placester_page_placester_integrations',
		 			   'placester_page_placester_settings_polygons', 
		 			   'placester_page_placester_settings_property_pages', 
		 			   'placester_page_placester_settings_international', 
		 			   'placester_page_placester_settings_neighborhood', 
		 			   'placester_page_placester_settings_filtering', 
		 			   'placester_page_placester_settings_template', 
		 			   'placester_page_placester_settings_client');

		if (!in_array($hook, $pages)) { return; }

		//always load these
		self::register_enqueue_if_not('global-css', trailingslashit(PL_CSS_URL) .  'global.css');		
		self::register_enqueue_if_not('jquery-ui', trailingslashit(PL_JS_LIB_URL) .  'jquery-ui/css/smoothness/jquery-ui-1.8.17.custom.css');
		self::register_enqueue_if_not('integrations', trailingslashit(PL_CSS_ADMIN_URL) .  'integration.css');		


		if ($hook == 'placester_page_placester_properties') {
			self::register_enqueue_if_not('my-listings', trailingslashit(PL_CSS_ADMIN_URL) .  'my-listings.css');					
		}

		if ($hook == 'placester_page_placester_property_add') {
			self::register_enqueue_if_not('add-listing', trailingslashit(PL_CSS_ADMIN_URL) .  'add-listing.css');			
		}

		if ($hook == 'placester_page_placester_support') {
			self::register_enqueue_if_not('support', trailingslashit(PL_CSS_ADMIN_URL) .  'support.css');			
		}

		if ($hook == 'placester_page_placester_theme_gallery') {
			self::register_enqueue_if_not('support', trailingslashit(PL_CSS_ADMIN_URL) .  'theme-gallery.css');			
		}

		if ($hook == 'placester_page_placester_settings') {
			self::register_enqueue_if_not('settings-all', trailingslashit(PL_CSS_ADMIN_URL) .  '/settings/all.css');					
			self::register_enqueue_if_not('settings-general', trailingslashit(PL_CSS_ADMIN_URL) .  '/settings/general.css');	
		}

		if ($hook == 'placester_page_placester_settings_polygons') {
			self::register_enqueue_if_not('settings-all', trailingslashit(PL_CSS_ADMIN_URL) .  '/settings/all.css');					
			self::register_enqueue_if_not('settings-polygons', trailingslashit(PL_CSS_ADMIN_URL) .  '/settings/polygon.css');									
			self::register_enqueue_if_not('colorpicker', trailingslashit(PL_JS_URL) .  'lib/colorpicker/css/colorpicker.css');					
		}

		if ($hook == 'placester_page_placester_settings_property_pages') {
			self::register_enqueue_if_not('settings-all', trailingslashit(PL_CSS_ADMIN_URL) .  '/settings/all.css');		
			self::register_enqueue_if_not('settings-pages', trailingslashit(PL_CSS_ADMIN_URL) .  '/settings/pages.css');					
		}
		
		if ($hook == 'placester_page_placester_settings_international') {
			self::register_enqueue_if_not('settings', trailingslashit(PL_CSS_ADMIN_URL) .  'settings.css');					
		}
		
		if ($hook == 'placester_page_placester_settings_neighborhood') {
			self::register_enqueue_if_not('settings', trailingslashit(PL_CSS_ADMIN_URL) .  'settings.css');					
		}
		
		if ($hook == 'placester_page_placester_settings_client') {
			self::register_enqueue_if_not('settings-all', trailingslashit(PL_CSS_ADMIN_URL) .  '/settings/all.css');					
			self::register_enqueue_if_not('settings-filtering', trailingslashit(PL_CSS_ADMIN_URL) .  'settings/client.css');					
		}
		
		if ($hook == 'placester_page_placester_settings_filtering') {
			self::register_enqueue_if_not('settings-all', trailingslashit(PL_CSS_ADMIN_URL) .  '/settings/all.css');					
			self::register_enqueue_if_not('settings-filtering', trailingslashit(PL_CSS_ADMIN_URL) .  'settings/filtering.css');					
		}
	}

	function customizer() {
		self::register_enqueue_if_not('customizer-css', trailingslashit(PL_CSS_URL) . 'customizer.css');
		self::register_enqueue_if_not('onboard-css', trailingslashit(PL_CSS_URL) . 'onboard.css');
		self::register_enqueue_if_not('jquery-ui', trailingslashit(PL_JS_LIB_URL) .  'jquery-ui/css/smoothness/jquery-ui-1.8.17.custom.css');

		if ( PL_Bootup::is_theme_switched() ) {
			self::register_enqueue_if_not('global-css', trailingslashit(PL_CSS_URL) .  'global.css');
	    }
	}

	private function register_enqueue_if_not($name, $path, $dependencies = array()) {
		if (!wp_style_is($name, 'registered')) {
			wp_register_style($name, $path, $dependencies);		
		}

		if ( !wp_style_is($name, 'queue') ) {
			wp_enqueue_style($name);		
		}	
	}

// end of class
}