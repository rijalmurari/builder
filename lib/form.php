<?php 

class PL_Form {
	
	static $args = array();

	public static function generate_form($items, $args, $section_prefix = '') {
		extract(self::process_defaults($args), EXTR_SKIP);
		$cache = new PL_Cache('form');
		if ($result = $cache->get($items, $args)) {
			if ($echo_form) {
				echo $result;
				return false;	
			} else {
				return $result;
			}
		}
		
		$form = '';
		$form_group = array();
		foreach ($items as $key => $attributes) {
			if ( isset($attributes['type']) && isset($attributes['group']) ) {
			 	$form_group[$attributes['group']][] = self::item($key, $attributes, $method, false, $section_prefix);
			 } elseif ( !isset($attributes['type']) && is_array($attributes) ) {
				foreach ($attributes as $child_item => $attribute) {
					if ( isset($attribute['group']) ) {
						$form_group[$attribute['group']][] = self::item($child_item, $attribute, $method, $key, $section_prefix);	
					}
				}
			}
		}	
		$section_index = 1;
		$section_count = count( $form_group );
		foreach ($form_group as $group => $elements) {
			if (empty($group)) { $section_id = 'custom'; } else {$section_id = $group; }
			$form = apply_filters( 'pl_form_section_before', $form, $section_index, $section_count );
			$form .= "<section class='form_group' id='".str_replace(" ","_",$section_id)."'>";
			if (!empty($group)) {
				$form .= $title ? "<h3>" . ucwords($group) . "</h3>" : '';
			}
			$form .= implode($elements, '');
			$form .= "</section>";
			$form = apply_filters( 'pl_form_section_after', $form, $section_index, $section_count );
		}
		$form .= '<section class="clear"></section>';
		if ($include_submit) {
			$form .= '<button id="' . $id . '_submit_button" type="submit">Submit</button>';
		}
		if ($wrap_form) {
			$form = '<form name="input" method="' . $method . '" url="' . $url . '" class="complex-search" id="' . $id . '">' . $form . '</form>';
		}
		$cache->save($form);
		if ($echo_form) {
			echo $form;
		} else {
			return $form;
		}
		
	}

	public static function item($item, $attributes, $method, $parent = false, $section_prefix = '') {
		extract(self::prepare_item($item, $attributes, $method, $parent), EXTR_SKIP);
		ob_start();
		if ($type == 'checkbox') {
			?>
				<section id="<?php echo $section_prefix . $id ?>" class="pls_search_form <?php echo $css ?>">
					<input id="<?php echo $id ?>" type="<?php echo $type ?>" name="<?php echo $name ?>" value="true" <?php echo $value ? 'checked' : '' ?>/>
					<label for="<?php echo $id ?>"><?php echo $text ?></label>	
				</section>
			<?php	
		} elseif ($type == 'textarea') {
			$rows = ! empty( $attributes['rows'] ) ? $attributes['rows'] : 2;
			$cols = ! empty( $attributes['cols'] ) ? $attributes['cols'] : 20;
			?>
				<section id="<?php echo $section_prefix . $id ?>" class="pls_search_form <?php echo $css ?>">
					<label for="<?php echo $id ?>"><?php echo $text ?></label>	
					<textarea id="<?php echo $id ?>" name="<?php echo $name ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"><?php echo $value ?></textarea>
				</section>
			<?php
		} elseif ($type == 'select') {
			?>
				<section id="<?php echo $section_prefix . $id ?>" class="pls_search_form <?php echo $css ?>" >
					<label for="<?php echo $id ?>"><?php echo $text ?></label>	
					<select name="<?php echo $name ?>" id="<?php echo $id ?>" <?php echo ($type == 'multiselect' ? 'multiple="multiple"' : '') ?> >
						<?php foreach ($options as $key => $text): ?>
							<option id="<?php echo $key ?>" value="<?php echo $key ?>" <?php echo ($key == $value ? 'selected' : '' ) ?>><?php echo $text ?></option>
						<?php endforeach ?>
					</select>
				</section>
			<?php	
		} elseif ($type == 'multiselect') {
			?>
				<section id="<?php echo $section_prefix . $id ?>" class="pls_search_form <?php echo $css ?>" >
					<label for="<?php echo $id ?>"><?php echo $text ?></label>	
					<select name="<?php echo $name ?>[]" id="<?php echo $id ?>" <?php echo ($type == 'multiselect' ? 'multiple="multiple"' : '') ?> >
						<?php foreach ($options as $key => $text): ?>
							<option id="<?php echo $key ?>" value="<?php echo $key ?>" <?php echo ((is_array($value) && in_array($key, $value) ) ? 'selected' : '' ) ?>><?php echo $text ?></option>
						<?php endforeach ?>
					</select>
				</section>
			<?php	
		} elseif( $type == 'text' ) {
			?>
				<section id="<?php echo $section_prefix . $id ?>" class="pls_search_form <?php echo $css ?>">
					<label for="<?php echo $id ?>"><?php echo $text ?></label>	
					<input id="<?php echo $id ?>" type="<?php echo $type ?>" name="<?php echo $name ?>" value="<?php echo $value ?>" />
				</section>
			<?php
		} elseif ( $type == 'date') {
			?>
				<section id="<?php echo $section_prefix . $id ?>" class="pls_search_form <?php echo $css ?>">
					<label for="<?php echo $id ?>"><?php echo $text ?></label>	
					<input id="<?php echo $id ?>_picker" class="trigger_datepicker" type="text" name="<?php echo $name ?>" <?php echo !empty($value) ? 'value="'.$value.'"' : ''; ?> />
				</section>
			<?php
		} elseif( $type == 'image' ) {
			?>
				<section id="<?php echo $section_prefix . $id ?>" class="pls_search_form <?php echo $css ?>">
					<label for="<?php echo $id ?>"><?php echo $text ?></label>	
					<input id="fileupload" type="file" name="images" name="<?php echo $name ?>" multiple /> 
				</section>
			<?php
		} elseif ( $type == 'bundle' ) {
				$bundle = '';
				foreach (self::prepare_custom_item($options, $method) as $key => $form_items) {
					$bundle .= "<section class='form_group' id='".$key."'>";
					if (self::$args['title']) {
						$bundle .= "<h3>" . ucwords($key) . "</h3>";
					}
					if (is_array($form_items)) {
						$bundle .= implode($form_items, '');	
					} else {
						$bundle .= $form_items;	
					}
					$bundle .= "</section>";
				}
			 echo $bundle;
		} elseif ($type == 'radio') {
			?>
				<section id="<?php echo $section_prefix . $id ?>" class="pls_search_form <?php echo $css ?>" >
					<label for="<?php echo $id ?>"><?php echo $text ?></label>
					<?php foreach( $options as $key => $text ): ?>
					<div class="<?php echo $name . '_radios'; ?>">
						<label for="<?php echo $name . '_' . $key; ?>"><?php echo $text ?></label>
						<input id="<?php echo $name . '_' . $key; ?>" type="radio" value="<?php echo $text; ?>" name="<?php echo $name; ?>" />
					</div>
					<?php endforeach; ?>	
				</section>
			<?php	
		}
		 elseif ( $type == 'custom_data' ) {
			?>
				<section id="<?php echo $id ?>" class="pls_search_form <?php echo $css ?>">
					<label for="">Category Name</label>
					<input type="text" name="custom_attribs[][cat]" />
					<label for="">Label Name</label>
					<input type="text" name="custom_attribs[][type]" />
					<label for="">Information Type</label>
					<input type="text" name="custom_attribs[][name]" />
					<button id="<?php echo $id ?>">Add another</button>
				</section>
			<?php
		}
		return trim(ob_get_clean());
	}

	private function prepare_item($item, $attributes, $method, $parent) {

		// Sets text
		$text = $item;
		if (isset($attributes['label'])) { $text = $attributes['label']; }

		// generates css, want it about the 
		// name to avoid the explode
		$css = $item;
		if (isset($attributes['css'])) { $css = $attributes['css'];}

		// properly set the name if an array
		//to handle property type bullshit
		if (strpos($item, '.')) {
			$exploded = explode('-', $item);
			$item = $exploded[0];
		}
		$name = $item;
		$id = $item;
		if($parent) {
			$name = $parent . '[' . $item . ']';
			$id = $parent . '-' . $item; //finding brackets in ids is tricky for js
		}

		// get options, if there are any.
		if (isset($attributes['bound']) && is_array(($attributes['bound']))) {
			// Deal with params...
			$params = ( isset($attributes['bound']['params']) ? $attributes['bound']['params'] : array() ) ;
			// If "params" is a single element, encapsulate in an array...
			if ( isset($params) && !is_array($params) ) {
				$params = array($params);
			}

			$options = call_user_func_array(array($attributes['bound']['class'], $attributes['bound']['method']), $params);
		} elseif (isset($attributes['options'])) {
			$options = $attributes['options'];
		} else {
			$options = array();
		}

		if ($method == 'GET') {
			if ($parent) {
				$value = isset($_GET[$parent][$item]) ? $_GET[$parent][$item] : null;
			} else {
				$value = isset($_GET[$item]) ? $_GET[$item] : null;	
			}
		} else {
			if ($parent) {
				$value = isset($_POST[$parent][$item]) ? $_POST[$parent][$item] : null;
			} else {
				$value = isset($_POST[$item]) ? $_POST[$item] : null;	
			}
		}

		if (!$value && isset($attributes['bound']) && isset($attributes['bound']['default']) ) {
			if (is_array($attributes['bound']['default'])) {
				$value = call_user_func($attributes['bound']['default']);
			} else {
				$value = $attributes['bound']['default'];	
			}
		} 
		
		// extra check for blank arrays
		$value = ( is_array( $value ) && count( $value ) === 0 ) ? null : $value;

		return array('name' => $name, 'value' => $value, 'text' => $text, 'options' => $options, 'id' => $id, 'type' => $attributes['type'], 'css' => $css);
	}

	private function prepare_custom_item($options, $method) {
		$custom_items = array();
		foreach ($options as $key => $option) {
			$form_types = PL_Config::PL_API_CUST_ATTR('get');
			$form_types = $form_types['args']['attr_type']['options'];
			$attributes = array('label' => $option['name'], 'type' => $form_types[$option['attr_type']]);
			$custom_items[$option['cat']][] = self::item($option['key'], $attributes, $method, 'metadata');
		} 
		return $custom_items;
	}

	private function process_defaults ($args){
		/** Define the default argument array. */
		$defaults = array(
        	'url' => false,
        	'method' => 'GET',
        	'id' => 'pls_search_form',
        	'title' => false,
        	'include_submit' => true,
        	'wrap_form' => true,
        	'echo_form' => true
        );

		/** Merge the arguments with the defaults. */
        $args = wp_parse_args( $args, $defaults );
        self::$args = $args;
        return $args;
	}

// class end
}