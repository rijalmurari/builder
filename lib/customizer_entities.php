<?php

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
              <textarea class="customize-control-textarea" rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
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
			  <input id="submit_theme" class="bt-disabled" type="button" value="Activate" disabled="disabled">

	          <select id="theme_choices" style="width: 50%">
			    <?php foreach ($PL_CUSTOMIZER_THEMES as $group => $themes): ?>
			      <optgroup label="<?php echo $group; ?>">
			  	    <?php foreach ($themes as $name => $stylesheet): ?>
			  	  	  <option value="<?php echo $stylesheet ?>" <?php selected( $this->manager->get_stylesheet(), $stylesheet ); ?>><?php echo $name; ?></option>
			        <?php endforeach; ?>
			      </optgroup>
			    <?php endforeach; ?>
			  </select>
	        </div><!--theme-switcher-->
	        
	        <div id="theme_info" style="min-height: 300px">
	          <?php $screenshot = $this->manager->theme()->get_screenshot(); ?>
	          <div class="theme-screenshot">
	            <img src="<?php echo esc_url( $screenshot ); ?>" />
	      	  </div>

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
	        </div>

	        <div id="pagination">
	          <a class="prev" href="#">Previous</a>                                       
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
   			?>
   			  <div id='create_listing'>

   			  	<div id="listing_message" class="error" style="display: none">
   			  	  <!-- Inject error message(s) here... -->
   			  	</div>

   			  	<?php $listing_types = PL_Config::PL_API_LISTINGS('create', 'args', 'compound_type'); ?>
   			  	
   			  	<div id="switcher">
		          <h2><?php echo $listing_types['label']; ?></h2>
		          <select id="compound_type" name="compound_type">
				    <?php foreach ( $listing_types['options'] as $val => $text) : ?>
				      <?php if ( $text == 'Not Set' ) { $text = 'Select One'; } ?>
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

	            <div class="disclaimer">
	              * Represents monthly rate for rentals 
	              <br> 
	              * Leave out commas &amp; currency symbols
	            </div>

	            <label style="display: block">Brief Description</label>
	            <textarea id="listing_desc" name="metadata[desc]" maxlength="450"></textarea>
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
	            <input id="blogpost_title" class="fw" type="text" placeholder="My First Real Estate Post">
	            
	            <label>Content</label>
	            <textarea id="blogpost_content" class="post-content"></textarea>
	          
	            <label>Tags</label>
	            <input id="blogpost_tags" class="fw" type="text" placeholder="Separate tags with commas">
	          
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

   class PL_Customize_Color_Scheme_Control extends WP_Customize_Control
   {
   		public $type = 'color_scheme';

   		public function render() {
   			?>
   			  <div id="color_scheme">
   			  	<?php $preset_colors = array('---' => 'none',
   			  								 'Default' => 'default',
   			  								 'Forest Green' => '#569E28',
   			  								 'Navy Blue' => '#000080',
   			  								 'Maroon' => '#AD0707',
   			  								 'Violet' => '#800080',
   			  								 'Golden' => '#CCB400'); 
   			  	?>

   			  	<div id="color_message" class="error" style="display: none">
   			  	  <!-- Inject error message(s) here... -->
   			  	</div>

   			  	<div id="switcher">
		          <h2>Select Palette</h2>
		          <select id="color_select">
				    <?php foreach ($preset_colors as $name => $hex): ?>
				      <!-- <optgroup label="<?php // echo $group; ?>"> -->
				  	    <?php // foreach (): ?>
				  	  	  <option value="<?php echo $hex; ?>"><?php echo $name; ?></option>
				        <?php // endforeach; ?>
				      <!-- </optgroup> -->
				    <?php endforeach; ?>
				  </select>
		        </div>

		        <div>
		          <label>Edit Custom CSS</label>
		          <a id="toggle_css_edit" class="toggle-display" href="#">[+] Show</a>
		    	</div>

		        <div id="css_edit_container" style="display: none">
		          <textarea id="custom_css" class="css-edit-box"></textarea>

		          <div class="button-container">
	                <input id="submit_custom_css" class="bt-norm" type="button" value="Preview">
	              </div>
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

?>