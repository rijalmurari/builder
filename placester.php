<?php
/**
Plugin Name: Real Estate Website Builder
Description: Quickly create a lead generating real estate website for your real property.
Plugin URI: https://placester.com/
Author: Placester.com
Version: 1.1.5
Author URI: https://www.placester.com/
*/

/*  Copyright (c) 2012 Placester, Inc. <frederick@placester.com>
	All rights reserved.

	Placester Promoter is distributed under the GNU General Public License, Version 2,
	June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
	St, Fifth Floor, Boston, MA 02110, USA

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

define('PL_PLUGIN_VERSION','1.0.6');

define( 'PL_PARENT_DIR', plugin_dir_path(__FILE__) );
define( 'PL_PARENT_URL', plugin_dir_url(__FILE__) );

define( 'PL_VIEWS_DIR', trailingslashit(PL_PARENT_DIR) . 'views/' );
define( 'PL_VIEWS_URL', trailingslashit(PL_PARENT_URL) . 'views/' );

define( 'PL_VIEWS_ADMIN_DIR', trailingslashit(PL_VIEWS_DIR) . 'admin/' );
define( 'PL_VIEWS_ADMIN_URL', trailingslashit(PL_VIEWS_URL) . 'admin/' );

define( 'PL_VIEWS_CLIENT_DIR', trailingslashit(PL_VIEWS_DIR) . 'client/' );
define( 'PL_VIEWS_CLIENT_URL', trailingslashit(PL_VIEWS_URL) . 'client/' );

define( 'PL_VIEWS_PART_DIR', trailingslashit(PL_VIEWS_DIR) . 'partials/' );
define( 'PL_VIEWS_PART_URL', trailingslashit(PL_VIEWS_URL) . 'partials/' );

define( 'PL_VIEWS_SHORT_DIR', trailingslashit(PL_VIEWS_DIR) . 'shortcodes/' );
define( 'PL_VIEWS_SHORT_URL', trailingslashit(PL_VIEWS_URL) . 'shortcodes/' );

define( 'PL_JS_DIR', trailingslashit(PL_PARENT_DIR) . 'js/' );
define( 'PL_JS_URL', trailingslashit(PL_PARENT_URL) . 'js/' );

define( 'PL_LIB_DIR', trailingslashit(PL_PARENT_DIR) . 'lib/' );
define( 'PL_LIB_URL', trailingslashit(PL_PARENT_URL) . 'lib/' );

define( 'PL_HLP_DIR', trailingslashit(PL_PARENT_DIR) . 'helpers/' );
define( 'PL_HLP_URL', trailingslashit(PL_PARENT_URL) . 'helpers/' );

define( 'PL_JS_LIB_DIR', trailingslashit(PL_JS_DIR) . 'lib/' );
define( 'PL_JS_LIB_URL', trailingslashit(PL_JS_URL) . 'lib/' );

define( 'PL_JS_PUB_DIR', trailingslashit(PL_JS_DIR) . 'public/' );
define( 'PL_JS_PUB_URL', trailingslashit(PL_JS_URL) . 'public/' );

define( 'PL_CSS_DIR', trailingslashit(PL_PARENT_DIR) . 'css/' );
define( 'PL_CSS_URL', trailingslashit(PL_PARENT_URL) . 'css/' );

define( 'PL_IMG_DIR', trailingslashit(PL_PARENT_DIR) . 'images/' );
define( 'PL_IMG_URL', trailingslashit(PL_PARENT_URL) . 'images/' );

define( 'PL_CSS_ADMIN_DIR', trailingslashit(PL_CSS_DIR) . 'admin/' );
define( 'PL_CSS_ADMIN_URL', trailingslashit(PL_CSS_URL) . 'admin/' );

define( 'PL_CSS_CLIENT_DIR', trailingslashit(PL_CSS_DIR) . 'client/' );
define( 'PL_CSS_CLIENT_URL', trailingslashit(PL_CSS_URL) . 'client/' );

define( 'PL_THIRD_PARTY_DIR', trailingslashit(PL_PARENT_DIR) . 'third-party/' );
define( 'PL_THIRD_PARTY_URL', trailingslashit(PL_PARENT_URL) . 'third-party/' );

define( 'ADMIN_URL', trailingslashit( admin_url() ) );
define( 'ADMIN_MENU_URL', trailingslashit( ADMIN_URL ) . 'admin.php' );

// Demo Account API Key
define( 'DEMO_API_KEY', '7e63514ebfad7608bbe7b4469ab470ecef4dc651099ae06fc1df6807717f0deacd38809e3c314ca09c085125f773a4c7' );

// Theme skin directory
define( 'PL_THEME_SKIN_DIR', trailingslashit(PL_PARENT_DIR) . 'config/customizer/theme-skins/' );

//config
include_once('config/toggle_form_sections.php');
include_once('config/api/custom_attributes.php');
include_once('config/api/listings.php');
include_once('config/api/users.php');
include_once('config/api/people.php');
include_once('config/api/integration.php');
include_once('config/third-party/google-places.php');
include_once('config/api/wordpress.php');
include_once('config/customizer/onboard_settings.php');
include_once('config/customizer/theme_choices.php');

//lib
// include_once('lib/social_networks.php');
include_once('lib/config.php');
include_once('lib/routes.php');
include_once('lib/http.php');
include_once('lib/debug.php');
include_once('lib/form.php');
include_once('lib/validation.php');
include_once('lib/pages.php');
include_once('lib/menus.php');
include_once('lib/posts.php');
include_once('lib/membership.php');
include_once('lib/caching.php');
// include_once('lib/shortcodes.php');
include_once('lib/widgets.php');
//add_action('init', 'dxshortcodes');
//function dxshortcodes() {
include_once('lib/shortcode_wrapper.php');
include_once('lib/component_entities.php');
include_once('lib/shortcodes-new.php');
	
include_once('lib/featured_listings_post_type.php');
include_once('lib/demo_data.php');
include_once('lib/customizer.php');
include_once('lib/customizer_entities.php');

//post types
include_once('lib/post_types/pl_post_type_manager.php');

//models
include_once('models/listing.php');
include_once('models/custom_attribute.php');
include_once('models/options.php');
include_once('models/user.php');
include_once('models/people.php');
include_once('models/themes.php');
include_once('models/integration.php');
include_once('models/google-places.php');
include_once('models/wordpress.php');
include_once('models/walkscore.php');
include_once('models/education-com.php');

//helpers
include_once('helpers/listing.php');
include_once('helpers/option.php');
include_once('helpers/compatibility.php');
include_once('helpers/css.php');
include_once('helpers/js.php');
include_once('helpers/header.php');
include_once('helpers/user.php');
include_once('helpers/pages.php');
include_once('helpers/people.php');
include_once('helpers/logging.php');
include_once('helpers/compliance.php');
include_once('helpers/integrations.php');
include_once('helpers/custom_attributes.php');
include_once('helpers/settings.php');
include_once('helpers/taxonomy.php');
include_once('helpers/google-places.php');
include_once('helpers/wordpress.php');
include_once('helpers/education-com.php');
include_once('helpers/caching.php');
include_once('helpers/membership.php');
include_once('helpers/snippet.php');
include_once('helpers/template.php');
include_once('helpers/customizer.php');

include_once('helpers/bootup.php');


//third-party scripts
include_once('third-party/tax-meta-class/tax-meta-class.php');
include_once('third-party/convex-hull/convex-hull.php');
include_once('third-party/mixpanel/mixpanel.php');


// Register hook to load blueprint from plugin if no theme has yet to do so...
add_action( 'after_setup_theme', 'load_blueprint_from_plugin', 18 );
function load_blueprint_from_plugin() 
{
    if (!class_exists('Placester_Blueprint')) {
        require_once('blueprint/blueprint.php');
        new Placester_Blueprint('2.1', 'plugin');
        add_action('init', 'blueprint_settings');
        
    }
}

function blueprint_settings() {
    remove_theme_support( 'pls-default-css' );
    remove_theme_support( 'pls-default-style' );
    remove_theme_support( 'pls-default-960' );
    remove_theme_support( 'pls-default-normalize' );
    remove_theme_support( 'pls-js' );
    remove_theme_support( 'pls-routing-util-templates' );
}

register_activation_hook( __FILE__, 'placester_activate' );
// register_deactivation_hook( __FILE__, 'placester_deactivate' );

add_action( 'admin_menu', 'placester_admin_menu' );
function placester_admin_menu() {
    // Add separator
    global $menu;
    $menu['3a'] = array( '', 'read', 'separator1', '', 'wp-menu-separator' );

    // Add Placester Menu
    add_menu_page('Placester','Placester','edit_pages','placester',array('PL_Router','my_listings'), plugins_url('/placester/images/icons/logo_16.png'), '3b' /* position between 3 and 4 */ );

    // Avoid submenu to start with menu function
    global $submenu;
    $submenu['placester'] = array();

    add_submenu_page( 'placester', '','Listings', 'edit_pages', 'placester_properties', array('PL_Router','my_listings'));
    add_submenu_page( 'placester', '', 'Add Listing', 'edit_pages', 'placester_property_add', array('PL_Router','add_listings') );    
    
    // If the site using the plugin is on our hosted network, don't show the theme gallery...
    if ( !defined('HOSTED_PLUGIN_KEY') ) {
    	add_submenu_page( 'placester', '', 'Theme Gallery', 'edit_pages', 'placester_theme_gallery', array('PL_Router','theme_gallery') );    	
    }
    
    global $settings_subpages;
    $settings_subpages = array('Settings' => '',
                               'Client Settings' => '_client',
                               'Global Property Filtering' => '_filtering', 
                               'Polygon Controls' => '_polygons', 
                               'Property Pages' => '_property_pages', 
                               'Template Controls' => '_template', 
                               'International Settings' => '_international' );
    foreach ($settings_subpages as $name => $page_url) {
        add_submenu_page( 'placester', '', $name, 'edit_pages', 'placester_settings' . $page_url, array('PL_Router','settings' . $page_url) );    
    }
    add_submenu_page( 'placester', 'Widgets', 'Widgets', 'edit_pages', 'edit.php?post_type=pl_general_widget' );
    
    /* TODO: Re-enable when social functionality is all set... */
    // add_submenu_page( 'placester', 'Social', 'Social', 'edit_pages', 'placester_social', array('PL_Social_Networks_Twitter','add_social_settings_cb') );
    
    // add_submenu_page( 'placester', '', 'Settings', 'edit_pages', 'placester_settings_general', array('PL_Router','settings') );    
    add_submenu_page( 'placester', '', 'Support', 'edit_pages', 'placester_support', array('PL_Router','support') );    
    add_submenu_page( 'placester', '', 'MLS Integration', 'edit_pages', 'placester_integrations', array('PL_Router','integrations') );    


}

function placester_activate () {
    $metrics = new MetricsTracker("9186cdb540264089399036dd672afb10");
    $metrics->track('Activation');
    PL_WordPress_Helper::report_url();
}

add_action( 'wp_head', 'placester_info_bar' );
function placester_info_bar() {
    if ( PL_Option_Helper::get_demo_data_flag() && current_user_can('manage_options') ) {
        PL_Router::load_builder_partial('infobar.php');
    }
}

?>
