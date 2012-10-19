<?php

/***************************************************/
/* Messing around with the Theme Customizer API... */
/***************************************************/

add_action ('admin_menu', 'themedemo_admin');
function themedemo_admin() 
{
    // add the Customize link to the admin menu
    add_theme_page( 'Customize', 'Customize', 'edit_theme_options', 'customize.php' );
}

add_action( 'customize_register', 'PL_customize_register', 1 );
function PL_customize_register( $wp_customize ) 
{
	// A simple check to ensure function was called properly...
	if ( !isset($wp_customize) ) { return; }

	define_custom_controls();

	/* NOTE: For now, always load customizer with onboard settings... */
	$onboard = true; // ( isset($_GET['onboard']) && strtolower($_GET['onboard']) == 'true' ) ? true : false;
	PL_Customizer::register_components( $wp_customize, $onboard );

	// Prevent default control from being created
	remove_action( 'customize_register', array(  $wp_customize, 'register_controls' ) );

	// No infobar in theme previews...
	remove_action( 'wp_head', 'placester_info_bar' );

	// Register function to inject script to make postMessage settings work properly
	if ( $wp_customize->is_preview() && ! is_admin() )
	{ add_action( 'wp_footer', 'inject_postMessage_hooks', 21); }
}

function inject_postMessage_hooks() {
  // Gets the theme that the customizer is currently set to display/preview...
  global $wp_customize;
  $theme_opts_key = $wp_customize->get_stylesheet();
  // error_log($theme_opts_key);

  $postMessage_settings = array(
  								 'pls-site-title' => 'header h1 a', 
  								 'pls-site-subtitle' => 'header h2, #slogan', 
  								 'pls-user-email' => 'section.e-mail a, #contact .email a, header .phone a, section.email a, header p.h-email a', 
  								 'pls-user-phone' => 'section.contact-info .phone, header p.h-phone, header div.phone, header section.phone .phone-bg-mid'
  								);

  ?>
    <script type="text/javascript">
    ( function( $ ){
    <?php foreach ($postMessage_settings as $id => $selector): ?>
      wp.customize('<?php echo "{$theme_opts_key}[{$id}]"; ?>', function( value ) {
        value.bind(function(to) {
          //if (to) {	
            $('<?php echo $selector; ?>').text(to);
          //}
        });
      });
    <?php endforeach; ?>  
    } )( jQuery )
    </script>
  <?php
}

add_action( 'customize_controls_print_footer_scripts', 'load_preview_spinner' );
function load_preview_spinner() {
  ?>
    <img id="preview_load_spinner" src="<?php echo plugins_url('/placester/images/preview_load_spin.gif'); ?>" alt="Theme Preview is Loading..." />
  <?php
}

// Can't nest class definitions in PHP, so these have to be placed in a global function...
function define_custom_controls() 
{	
	class PL_Customize_TextArea_Control extends WP_Customize_Control 
    {
        public $type = 'textarea';

        public function render_content() {
          ?>
            <label>
              <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
              <textarea rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            </label>
          <?php
        }
    }

   class PL_Customize_Typography_Control extends WP_Customize_Control
   {
   		public $type = 'typography';

   		public function render_content() {
   		  ?>
   		  	<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

   			<!-- Font Size -->
			<select class="of-typography of-typography-size" <?php $this->link('size'); ?> >
			  <?php for ($i = 9; $i < 71; $i++): 
				$size = $i . 'px'; ?>
				<option value="<?php echo esc_attr( $size ); ?>" <?php selected( $this->value('size'), $size ); ?>><?php echo $size; ?></option>
			  <?php endfor; ?>
			</select>
		
			<!-- Font Face -->
			<select class="of-typography of-typography-face" <?php $this->link('face'); ?> >

			<?php $faces = of_recognized_font_faces(); // Global function defined in Blueprint ?>

			  <?php foreach ( $faces as $key => $face ): ?>
			 	<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $this->value('face'), $key ); ?>><?php echo $face; ?></option>
			  <?php endforeach; ?>		
			</select>

			<!-- Font Style -->
			<select class="of-typography of-typography-style" <?php $this->link('style'); ?> >

			<?php $styles = of_recognized_font_styles(); // Global function defined in Blueprint ?>

			  <?php foreach ( $styles as $key => $style ): ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $this->value('style'), $keys ); ?>><?php echo $style; ?></option>
			  <?php endforeach; ?>
			</select>

			<!-- Font Color -->
			<!-- 
			<div id="colorpicker">
			  <div id="<?php //echo esc_attr( $this->id ); ?>_color_picker" class="another_colorpicker">
			    <div style="<?php //echo esc_attr( 'background-color:' . $this->value('color') ); ?>"></div>
			  </div>
			</div>  
 			-->
			<input type="text" class="of-color of-typography of-typography-color" value="<?php echo esc_attr( $this->value('color') ); ?>" id="<?php echo esc_attr( $this->id ); ?>_color" <?php $this->link('color'); ?> />
		  <?php
   		}
   }

   class PL_Customize_Integration_Control extends WP_Customize_Control
   {
   		public $type = 'integration';

   		public function render() {
   			PL_Router::load_builder_partial('integration-form.php', array('no_form' => true));
   			?>
   			  <!-- <div id="customize_integration_submit" style="width: 50px; height: 30px; background: grey;">Submit</div> -->

   			  <div class="row">
		        <input type="button" id="customize_integration_submit" class="bt-norm" value="Submit Request" />
		      </div>
   			<?php

   			// Needed to subscribe a user...
   			PL_Router::load_builder_partial('free-trial.php');
   		}

   		public function render_content() {
   			// Do nothing...
   		}
   }

   class PL_Customize_Load_Theme_Opts_Control extends WP_Customize_Control 
   {
   		public $type = 'load_opt_defaults';

   		public function render() {
   		  ?>
			<!-- Build default dropdown... -->
			<div id="default_opts" class="custom-control">
			  <span class="customize-title-span">Use Default Theme Options </span>
			  <select id="def_theme_opts">
			    <?php foreach (PLS_Options_Manager::$def_theme_opts_list as $name) : ?>
			  	  <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
			    <?php endforeach; ?>
			  </select>
			  <input type="button" id="btn_def_opts" class="top-button button-primary" value="Load" style="margin: 0px" />
			</div>
		  <?php
   		}

   		public function render_content() {
   			// Do Nothing...
   		}
   }

   class PL_Customize_Switch_Theme_Control extends WP_Customize_Control
   {
   		public $type = 'switch_theme';

   		public function render() {
   		  ?>
	        <?php global $PL_CUSTOMIZER_THEMES; ?>

	        <div id="switcher">
	          <h2>Select Theme</h2>
	          <select id="theme_choices">
			    <?php foreach ($PL_CUSTOMIZER_THEMES as $group => $themes): ?>
			      <optgroup label="<?php echo $group; ?>">
			  	    <?php foreach ($themes as $name => $stylesheet): ?>
			  	  	  <option value="<?php echo wp_customize_url($stylesheet); ?>" <?php selected( $this->manager->get_stylesheet(), $stylesheet ); ?>><?php echo $name; ?></option>
			        <?php endforeach; ?>
			      </optgroup>
			    <?php endforeach; ?>
			  </select>
	        </div><!--theme-switcher-->
	        
	        <?php $screenshot = $this->manager->theme()->get_screenshot(); ?>
	        <img class="theme-screenshot" src="<?php echo esc_url( $screenshot ); ?>" />
	      
	        <h2>Theme Description</h2>
	        <p><?php echo $this->manager->theme()->display('Description'); ?></p>
	        <!-- 
	        <h2>Features</h2>
	        <ul id="featureslist">
	          <li>
	          	<div class="featureicon"><a class="ico-responsive" href="#"></a></div>
	          	Responsive Web Design
	          </li>                
	        </ul>
	         -->
	        <div id="pagination">
	          <a class="prev" href="#">Previous</a>
	          <!-- <div id="center">
	            <a class="active" href="#">1</a>
	            <a href="#">2</a>
	            <a href="#">3</a>
	            <span>...</span>
	            <a href="#">9</a>
	            <a href="#">10</a>            
	            <a href="#">11</a>            
	          </div>      -->                                          
	          <a class="next" href="#">Next</a>
	          <div class="clearfix"></div>
	        </div><!--pagination-->
   		  <?php
   		}

   		public function render_content() {
   			// Do Nothing...
   		}
   }

   class PL_Customize_Listing_Control extends WP_Customize_Control
   {
   		public $type = 'listing';

   		public function render() {
   			// ob_start();
   			// pls_dump(PL_Config::PL_API_LISTINGS('create', 'args', 'compound_type'));
   	  //   	error_log(ob_get_clean());

   			?>
   			  <div id='create_listing'>

   			  	<div id="listing_message" class="error" style="display: none">
   			  	  <!-- <h3></h3> -->
   			  	</div>

   			  	<?php $listing_types = PL_Config::PL_API_LISTINGS('create', 'args', 'compound_type'); ?>
   			  	
   			  	<div id="switcher">
		          <h2><?php echo $listing_types['label']; ?></h2>
		          <select id="compound_type" name="compound_type">
				    <?php foreach ( $listing_types['options'] as $val => $text) : ?>
				  	  <option value="<?php echo $val; ?>"><?php echo $text; ?></option>
				    <?php endforeach; ?>
				  </select>
		        </div>

	            <label>Street Address</label>
	            <input id="listing_addr" name="location[address]" class="fw" type="text">
	            
	            <label>City</label>
	            <input id="listing_city" name="location[locality]" class="fw" type="text">       
	            
	            <div class="lg">
	              <label>State</label>
	              <input id="listing_state" name="location[region]" class="sw" type="text">                
	            </div>

	            <div class="lg">
	              <label>Zip Code</label>
	              <input id="listing_zip" name="location[postal]" class="sw" type="text">       
	         	</div>

	         	<div class="lg-cl">
	              <label>Country</label>
	              <select id="listing_country" name="location[country]" class="cl">
	                <?php foreach ( PL_Listing_Helper::supported_countries() as $code => $text ): ?>
	                  <option value="<?php echo $code; ?>" <?php selected( "US", $code ); ?>><?php echo $text; ?></option>
	                <?php endforeach; ?>
	              </select>       
	         	</div>

	         	<div class="lg">
	              <label>Price*</label>
	              <input id="listing_price" name="metadata[price]" class="sw" type="text">
	            </div>

	            <div style="float: left; 
	            			font-size: 13px;
							width: 300px;
							font-style: italic;
							color: gray;
							padding-top: 21px;
							line-height: 18px;">
	              * Represents monthly rate for rentals 
	              <br> 
	              * Leave out commas &amp; currency symbols
	            </div>

	            <label style="display: block">Brief Description</label>
	            <textarea id="listing_desc" name="metadata[desc]"></textarea>
			<!-- 	    
	    		<?php // $amenities = array('Pets Allowed', 'Hot Water', 'Air Conditioning', 'Furnished', 'Balcony', 'Pets Allowed'); ?>
	            <label>Amenities</label>
	            <ul id="checkboxlist">
	              <?php // foreach ($amenities as $amenity) : ?>
	                <li>
	                  <input class="cb" type="checkbox">
	                  <?php // echo $amenity; ?>          
	                </li>               
	              <?php // endforeach; ?>
	            </ul>
	            <br>
 			-->
	            <!-- <label>Upload Images</label> -->
	          
				<div class="button-container">	          
	              <input id="submit_listing" class="bt-norm" type="button" value="Post Listing">
		        <div>
		      </div>
   			<?php
   		}

   		public function render_content() {
   			// Do nothing...
   		}
   }

   class PL_Customize_Blog_Post_Control extends WP_Customize_Control
   {
   		public $type = 'blog_post';

   		public function render() {
   			?>
   			  <div id="create_post">
   			  	<div id="blogpost_message" class="error" style="display: none">
   			  	  <h3>Make sure to fill-in the <u>title</u> and <u>content</u> of your post</h3>
   			  	</div>

	            <label>Title</label>
	            <input id="blogpost_title" class="fw" type="text" value="My First Real Estate Post">
	            
	            <label>Content</label>
	            <textarea id="blogpost_content" class="post-content"></textarea>
	          
	            <!-- <label>Post Excerpt</label><br>
	            <textarea class="fw"></textarea>  -->           
	          
	          	<?php // $categories = array('Real Estate 101', 'Tax Advice', 'Mortgages', 'Market Update', 'Realtor Advice', 'Advertising'); ?>
	        <!-- 
	            <label>Category</label>
	            <ul id="checkboxlist">
	              <?php // foreach ($categories as $category) : ?>
	                <li>
	                  <input class="cb" type="radio">
	                  <?php // echo $category; ?>
	                </li>
	              <?php // endforeach; ?>
	                <li id="addlink"><a href="#">Add a Category</a></li>
	            </ul>
	    
	            <br>
	            <label>Upload Images</label>
	          
	            <!-- Upload Plugin Goes Here ->
	            <br><br><br><br><br> 
	         -->
	          
	          	<div class="button-container">
	              <input id="submit_blogpost" class="bt-norm" type="button" value="Publish">
	            </div>
	          </div>
   			<?php
   		}

   		public function render_content() {
   			// Do nothing...
   		}
   }

   /*
    * Custom Section -- Allows for complete customization of section look-and-feel...
    */
   class PL_Customize_Section extends WP_Customize_Section
   {
   		public $subtitle = '';
   		public $class = '';

   		function __construct( $manager, $id, $args = array() ) {
   			// First, call parent constructor...
   			parent::__construct( $manager, $id, $args );

   			// Now additional functionality...
   			if ( isset($args['subtitle']) ) {
   				$this->subtitle = $args['subtitle'];
   			}

   			if ( isset($args['class']) ) {
   				$this->class = $args['class'];
   			}

   			return $this;
   		}

   		public function render() {
   		  ?>
   		  	<li id="<?php echo esc_attr( $this->id ); ?>" class="<?php echo esc_attr( $this->class ); ?>">
			  <a href="#"></a>
			  <div id="<?php echo esc_attr( $this->id ); ?>_content" class="control-container">
			  	<?php $this->render_controls(); ?>
			  </div>
		    </li>
   		  <?php
   		}

   		public function render_controls() {
   		  ?>
   		    <h1 title="<?php echo esc_attr( $this->description ); ?>"><?php echo esc_html( $this->title ); ?></h1>
   		  	<h3><?php echo esc_html( $this->subtitle ); ?></h3>
		    <ul class="control-list">
		 	  <?php
			    foreach ( $this->controls as $control )
				$control->maybe_render();
			  ?>
		    </ul>
   		  <?php
   		}
   }
}

class PL_Customizer 
{
	private static $onboard_sections = array('General', 'User Info');

	private static $def_setting_opts = array(
			                          'default'   => '',
			                          'type'      => 'option',
			                          'transport' => 'refresh' 
			                        );

	private static $priority = 0;


	private function get_setting_opts( $args = array() )
	{
		$merged_opts = wp_parse_args($args, self::$def_setting_opts);
		
		return $merged_opts;
	}

	private function get_priority( $onboard = false, $section = '' ) {
	  	$new_priority;

	  	if ( $onboard ) {
	  		global $PL_CUSTOMIZER_ONBOARD_SECTIONS;
	  		$new_priority = ( is_array($PL_CUSTOMIZER_ONBOARD_SECTIONS) && !empty($section) )
	  					  	? $PL_CUSTOMIZER_ONBOARD_SECTIONS[$section] : 10;
	  	}
	  	else {
			// Return the newly incremented value (note the PREFIX operator...)
			$new_priority = ++self::$priority;
	  	}

	  	return $new_priority;
	}

	private function get_control_opts( $id, $opts, $sect_id, $is_custom = false )
	{
		$args = array(
                        'settings' => $id,
                        'label'    => $opts['name'],
                        'section'  => $sect_id
                     );

		if ( !$is_custom ) {
			$args['type'] = $opts['type'];
		}

		return $args;
	}

	public function register_components( $wp_customize, $onboard = false ) 
	{
		$theme_opts_key = $wp_customize->get_stylesheet();
	    $last_section_id = '';

	    global $PL_CUSTOMIZER_ONBOARD_OPTS;
	    $options = ( $onboard ? $PL_CUSTOMIZER_ONBOARD_OPTS : PLS_Style::$styles );

	    foreach ($options as $opt) 
	    {
	    	// Take care of defining some common vars used many of the cases below...
	    	if ( isset($opt['id']) ) {
	    		$setting_id = "{$theme_opts_key}[{$opt['id']}]";
	    		$control_id = "{$opt['id']}_ctrl";
	    	}

	    	$custom_args = array();
	    	if ( isset($opt['transport']) ) {
	    		$custom_args['transport'] = $opt['transport'];
	    	}

	        switch ( $opt['type'] ) 
	        {
	            case 'heading':
	                $args_section = array( 'title' => __($opt['name'],''), 'description' => $opt['name'] ); 
	                $args_section['priority'] = self::get_priority($onboard, $opt['name']);
	                if ( $onboard ) {
	                	$args_section['subtitle'] = $opt['desc'];
	                	$args_section['class'] = $opt['class'];
	                }

	                $id_base = isset($opt['id']) ? $opt['id'] : $opt['name'];
	                $section_id = strtolower( str_replace( ' ', '_', $id_base ) );
	                // $wp_customize->add_section( $section_id, $args_section );
	                $wp_customize->add_section( new PL_Customize_Section( $wp_customize, $section_id, $args_section ) );

	                // Add dummy control to certain sections so that they will appear...
	                if ( $onboard && isset($opt['class']) && $opt['class'] == 'no-pane' ) {
	                	$wp_customize->add_setting( 'dummy_setting', array() );
	                	$wp_customize->add_control( "dummy_ctrl_{$section_id}", array('settings' => 'dummy_setting', 'section' => $section_id, 'type' => 'none') );
	            	}

	                $last_section_id = $section_id;
	                break;

	            // Handle the standard (i.e., 'built-in') controls...
	            case 'text':
	            case 'checkbox':
	            	$wp_customize->add_setting( $setting_id, self::get_setting_opts($custom_args) );
	                
	                $args_control = self::get_control_opts( $setting_id, $opt, $last_section_id );
	                $wp_customize->add_control( $control_id, $args_control);
	                break;

	            case 'textarea':
		            $wp_customize->add_setting( $setting_id, self::get_setting_opts($custom_args) );

	                $args_control = self::get_control_opts( $setting_id, $opt, $last_section_id, true );
	                $wp_customize->add_control( new PL_Customize_TextArea_Control($wp_customize, $control_id, $args_control) );
	                break;

	            case 'typography':
	            	$typo_setting_keys = array('size', 'face', 'style', 'color');
	            	$typo_setting_ids = array();
	            	
	            	foreach ($typo_setting_keys as $key) {
	            		$wp_customize->add_setting( "{$setting_id}[{$key}]", self::get_setting_opts() );
	            		$typo_setting_ids[$key] = "{$setting_id}[{$key}]";
	            	}
	            	
	            	$args_control = self::get_control_opts( $typo_setting_ids, $opt, $last_section_id, true );
	                $wp_customize->add_control( new PL_Customize_Typography_Control($wp_customize, $control_id, $args_control) );
	            	break;

	            case 'upload':
	            	$wp_customize->add_setting( $setting_id, self::get_setting_opts() );

	            	$args_control = self::get_control_opts( $setting_id, $opt, $last_section_id, true );
	                $wp_customize->add_control( new WP_Customize_Upload_Control($wp_customize, $control_id, $args_control) );
	            	break;

	            case 'background':
	            	$setting_id .= '[color]';
	            	$wp_customize->add_setting( $setting_id, self::get_setting_opts() );

	            	$args_control = self::get_control_opts( $setting_id, $opt, $last_section_id, true );
	            	$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, $control_id, $args_control) );
	            	break;	

	            case 'custom':
	            	// Register PL component...
	            	self::register_pl_control( $wp_customize, $opt['name'], $last_section_id, $onboard );
	            	break;

	            default:
	                break;
	        } 
	    }

	}

	public function register_pl_control( $wp_customize, $name, $section_id, $onboard = false ) 
	{
		// Dummy setting must be associated with non-option sections in order for them to appear/function properly...
	    $dummy_setting_id = 'dummy_setting';
	    $wp_customize->add_setting( 'dummy_setting', array() );

		switch ( $name ) 
		{
			case 'theme-select':
				$switch_theme_ctrl_id = 'switch_theme_ctrl';
	    		$switch_theme_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $section_id, 'type' => 'none');
	    		$wp_customize->add_control( new PL_Customize_Switch_Theme_Control($wp_customize, $switch_theme_ctrl_id, $switch_theme_args_ctrl) );
				break;
			
			case 'integration':
				if ( PL_Option_Helper::api_key() ) {
			        $int_ctrl_id = 'integration_ctrl';
			        $int_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $section_id, 'type' => 'none');
			        $wp_customize->add_control( new PL_Customize_Integration_Control($wp_customize, $int_ctrl_id, $int_args_ctrl) );
				}
				break;
			
			case 'demo-data':
				$demo_setting_id = 'pls_demo_data_flag';
			    $wp_customize->add_setting( $demo_setting_id, self::get_setting_opts() );
				
				$demo_ctrl_id = 'demo_data_ctrl';                
			    $demo_args_control = self::get_control_opts( $demo_setting_id, array('name'=>'Use Demo Listing Data', 'type'=>'checkbox'), $section_id );
			    $wp_customize->add_control( $demo_ctrl_id, $demo_args_control);
				break;
			
			case 'theme-opt-defaults':
				if ( class_exists('PLS_Options_Manager') ) {
				    $load_opts_ctrl_id = 'load_opts_ctrl';
				    $load_opts_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $set_section_id, 'type' => 'none');
				    $wp_customize->add_control( new PL_Customize_Load_Theme_Opts_Control($wp_customize, $load_opts_ctrl_id, $load_opts_args_ctrl) );
				}
				break;

			case 'post-listing':
				$listing_ctrl_id = 'listing_ctrl';
				$listing_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $section_id, 'type' => 'none');
				$wp_customize->add_control( new PL_Customize_Listing_Control($wp_customize, $listing_ctrl_id, $listing_args_ctrl) );
				break;

			case 'blog-post':
				$blog_post_ctrl_id = 'blog_post_ctrl';
				$blog_post_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $section_id, 'type' => 'none');
				$wp_customize->add_control( new PL_Customize_Blog_Post_Control($wp_customize, $blog_post_ctrl_id, $blog_post_args_ctrl) );
				break;
				
			default:
				# code...
				break;
		}
	    
	}
	    
}
?>