<?php
/**
 * Plugin Name: BM Posts Top Widget
 * Plugin URI: http://brainymore.com
 * Description: This Widget support to show your posts on top of website.
 * Version: 1.0
 * Author: Brainymore
 * Author URI: http://brainymore.com
 * Demo URI: http://demo.brainymore.com/wordpress-extensions
 * This a responsive widget for posts
 */

add_action( 'widgets_init', 'bw_posts_top_load_widgets' );

add_action( 'wp_enqueue_scripts', 'load_bw_posts_top_script' );
/**
 * Register BW Posts Top widget.
 * 'bw_post_top_Widget' is the widget class used below.
 */
 
function bw_posts_top_load_widgets() {
	register_widget( 'bw_post_top_Widget' );
}

/**
 * Function: load_index_page_script
 * Load script (css, js) for BW Posts Top.
 */
 
function load_bw_posts_top_script(){
	if (!is_admin() && !defined('bw_posts_top')) {      
		define('bw_posts_top', 'ASSETS BW POSTS TOP');
		wp_enqueue_style( 'bm-posts-top-style', plugins_url('/assets/css/styles.css', __FILE__) );	
	}
}

/**
 * BW Posts Top Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, display, and update.  Nice!
 */
class bw_post_top_Widget extends WP_Widget {

	/**
	 * Widget Brainymore Wordpress Setup.
	 */
	function bw_post_top_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'postsTop', 'description' => __('Show your posts on top', 'postsTop') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 350, 'height' => 250, 'id_base' => 'bw-posts-top-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'bw-posts-top-widget', __('BW Posts Top', 'bw-posts-top'), $widget_ops, $control_ops );
	}

	/**
	 * Display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract($args);
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		
		if (!isset($instance['category'])){
			$instance['category'] = 0;
		}
		
		extract($instance);

		$default = array(
			'category__in' => $category,
			'orderby' => $orderby,
			'numberposts' => $numberposts,
			'length' => $length,
            'meta_key'    => '_thumbnail_id'
		);
		
		$list = get_posts($default);

		if ( !array_key_exists('theme', $instance) ){
			$instance['theme'] = 'theme1';
		}
		
		if ( $tpl = $this->getTemplatePath( $instance['theme'] ) ){ 
			$link_img = plugins_url('images/', __FILE__);
			$widget_id = $args['widget_id'];		
			include $tpl;
		}
				
		/* After widget (defined by themes). */
		echo $after_widget;
	}    

	protected function getTemplatePath($tpl='default', $type=''){
		$file = '/'.$tpl.$type.'.php';
		$dir =realpath(dirname(__FILE__)).'/themes';
		
		if ( file_exists( $dir.$file ) ){
			return $dir.$file;
		}
		
		return $tpl=='default' ? false : $this->getTemplatePath('default', $type);
	}
	
	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// strip tag on text field
		$instance['title'] = strip_tags( $new_instance['title'] );

		// int or array
		if ( array_key_exists('category', $new_instance) ){
			if ( is_array($new_instance['category']) ){
				$instance['category'] = array_map( 'intval', $new_instance['category'] );
			} else {
				$instance['category'] = intval($new_instance['category']);
			}
		}

		if ( array_key_exists('orderby', $new_instance) ){
			$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		}

		if ( array_key_exists('order', $new_instance) ){
			$instance['order'] = strip_tags( $new_instance['order'] );
		}

		if ( array_key_exists('numberposts', $new_instance) ){
			$instance['numberposts'] = intval( $new_instance['numberposts'] );
		}

		if ( array_key_exists('length', $new_instance) ){
			$instance['length'] = intval( $new_instance['length'] );
		}
      
        $instance['theme'] = strip_tags( $new_instance['theme'] );
        $instance['small_length'] = strip_tags( $new_instance['small_length'] );
        $instance['image_size'] = strip_tags( $new_instance['image_size'] );
        $instance['image_size_custom'] = $new_instance['image_size_custom'];
        $instance['thumb_size'] = strip_tags( $new_instance['thumb_size'] );
        $instance['thumb_size_custom'] = $new_instance['thumb_size_custom'];
        $instance['show_thumb'] = strip_tags( $new_instance['show_thumb'] );
        $instance['show_readmore'] = strip_tags( $new_instance['show_readmore'] );
        $instance['readmore_label'] = strip_tags( $new_instance['readmore_label'] );
        $instance['show_desc'] = strip_tags( $new_instance['show_desc'] );
        $instance['show_desc_small'] = strip_tags( $new_instance['show_desc_small'] );  
		
		return $instance;
	}

	function category_select( $field_name, $opts = array(), $field_value = null ){
		$default_options = array(
				'multiple' => false,
				'disabled' => false,
				'size' => 5,
				'class' => 'widefat',
				'required' => false,
				'autofocus' => false,
				'form' => false,
		);
		$opts = wp_parse_args($opts, $default_options);
	
		if ( (is_string($opts['multiple']) && strtolower($opts['multiple'])=='multiple') || (is_bool($opts['multiple']) && $opts['multiple']) ){
			$opts['multiple'] = 'multiple';
			if ( !is_numeric($opts['size']) ){
				if ( intval($opts['size']) ){
					$opts['size'] = intval($opts['size']);
				} else {
					$opts['size'] = 5;
				}
			}
		} else {
			// is not multiple
			unset($opts['multiple']);
			unset($opts['size']);
			if (is_array($field_value)){
				$field_value = array_shift($field_value);
			}
			if (array_key_exists('allow_select_all', $opts) && $opts['allow_select_all']){
				unset($opts['allow_select_all']);
				$allow_select_all = '<option value="0">All Categories</option>';
			}
		}
	
		if ( (is_string($opts['disabled']) && strtolower($opts['disabled'])=='disabled') || is_bool($opts['disabled']) && $opts['disabled'] ){
			$opts['disabled'] = 'disabled';
		} else {
			unset($opts['disabled']);
		}
	
		if ( (is_string($opts['required']) && strtolower($opts['required'])=='required') || (is_bool($opts['required']) && $opts['required']) ){
			$opts['required'] = 'required';
		} else {
			unset($opts['required']);
		}
	
		if ( !is_string($opts['form']) ) unset($opts['form']);
	
		if ( !isset($opts['autofocus']) || !$opts['autofocus'] ) unset($opts['autofocus']);
	
		$opts['id'] = $this->get_field_id($field_name);
	
		$opts['name'] = $this->get_field_name($field_name);
		if ( isset($opts['multiple']) ){
			$opts['name'] .= '[]';
		}
		$select_attributes = '';
		foreach ( $opts as $an => $av){
			$select_attributes .= "{$an}=\"{$av}\" ";
		}
		
		$categories = get_categories();
		// if (!$templates) return '';
		$all_category_ids = array();
		foreach ($categories as $cat) $all_category_ids[] = (int)$cat->cat_ID;
		
		$is_valid_field_value = is_numeric($field_value) && in_array($field_value, $all_category_ids);
		if (!$is_valid_field_value && is_array($field_value)){
			$intersect_values = array_intersect($field_value, $all_category_ids);
			$is_valid_field_value = count($intersect_values) > 0;
		}
		if (!$is_valid_field_value){
			$field_value = '0';
		}
	
		$select_html = '<select ' . $select_attributes . '>';
		if (isset($allow_select_all)) $select_html .= $allow_select_all;
		foreach ($categories as $cat){
			$select_html .= '<option value="' . $cat->cat_ID . '"';
			if ($cat->cat_ID == $field_value || (is_array($field_value)&&in_array($cat->cat_ID, $field_value))){ $select_html .= ' selected="selected"';}
			$select_html .=  '>'.$cat->name.'</option>';
		}
		$select_html .= '</select>';
		return $select_html;
	}
	

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults ); 		
		         
		$categoryid 		= isset( $instance['category'] )    ? $instance['category'] : 0;
		$orderby    		= isset( $instance['orderby'] )     ? strip_tags($instance['orderby']) : 'ID';
		$order      		= isset( $instance['order'] )       ? strip_tags($instance['order']) : 'ASC';
		$number     		= isset( $instance['numberposts'] ) ? intval($instance['numberposts']) : 5;
        $length     		= isset( $instance['length'] )      ? intval($instance['length']) : 25;
        $small_length     	= isset( $instance['small_length'] )      ? intval($instance['small_length']) : 15;
		
		$theme = 			isset( $instance['theme'] ) ? strip_tags($instance['theme']) : 'default';
		$image_size = 		isset( $instance['image_size'] ) ? strip_tags($instance['image_size']) : 'large';
		$image_size_custom =isset( $instance['image_size_custom'] ) ? $instance['image_size_custom'] : '';
		$thumb_size = 		isset( $instance['thumb_size'] ) ? strip_tags($instance['thumb_size']) : 'thumbnail';
		$thumb_size_custom =isset( $instance['thumb_size_custom'] ) ? $instance['thumb_size_custom'] : '';
        $show_thumb = 		isset( $instance['show_thumb'] ) ? $instance['show_thumb'] : 1; 
        $show_readmore = 	isset( $instance['show_readmore'] ) ? $instance['show_readmore'] : 1; 
        $readmore_label = 	isset( $instance['readmore_label'] ) ? $instance['readmore_label'] : 'More detail'; 
        $show_desc = 		isset( $instance['show_desc'] ) ? $instance['show_desc'] : 1; 
        $show_desc_small = 	isset( $instance['show_desc_small'] ) ? $instance['show_desc_small'] : 1; 
        
		?>
		
        <p> 
          <div class="bm_header" style="font-weight:bold; background-color:#428BCA; color:#fff; padding:4px;"> [ Source Config ] </div>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category ID', 'brainymore')?></label>
			<br />
			<?php echo $this->category_select('category', array('multiple' => true), $categoryid); ?>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby', 'brainymore')?></label>
			<br />
			<?php $allowed_keys = array('name' => 'Name', 'author' => 'Author', 'date' => 'Date', 'title' => 'Title', 'modified' => 'Modified', 'parent' => 'Parent', 'ID' => 'ID', 'rand' =>'Rand', 'comment_count' => 'Comment Count'); ?>
			<select class="widefat"
				id="<?php echo $this->get_field_id('orderby'); ?>"
				name="<?php echo $this->get_field_name('orderby'); ?>">
				<?php
				$option ='';
				foreach ($allowed_keys as $value => $key) :
					$option .= '<option value="' . $value . '" ';
					if ($value == $orderby){
						$option .= 'selected="selected"';
					}
					$option .=  '>'.$key.'</option>';
				endforeach;
				echo $option;
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'brainymore')?></label>
			<br />
			<select class="widefat"
				id="<?php echo $this->get_field_id('order'); ?>"
				name="<?php echo $this->get_field_name('order'); ?>">
				<option value="DESC" <?php if ($order=='DESC'){?> selected="selected"
				<?php } ?>>
					<?php _e('Descending', 'brainymore')?>
				</option>
				<option value="ASC" <?php if ($order=='ASC'){?> selected="selected"
				<?php } ?>>
					<?php _e('Ascending', 'brainymore')?>
				</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('numberposts'); ?>"><?php _e('Number of Posts', 'brainymore')?></label>
			<br />
			<input class="widefat"
				id="<?php echo $this->get_field_id('numberposts'); ?>"
				name="<?php echo $this->get_field_name('numberposts'); ?>" type="text"
				value="<?php echo esc_attr($number); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('length'); ?>"><?php _e('Excerpt length (in words): ', 'brainymore')?></label>
			<br />
			<input class="widefat"
				id="<?php echo $this->get_field_id('length'); ?>"
				name="<?php echo $this->get_field_name('length'); ?>" type="text"
				value="<?php echo esc_attr($length); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('small_length'); ?>"><?php _e('Excerpt small length (in words): ', 'brainymore')?></label>
			<br />
			<input class="widefat"
				id="<?php echo $this->get_field_id('small_length'); ?>"
				name="<?php echo $this->get_field_name('small_length'); ?>" type="text"
				value="<?php echo esc_attr($small_length); ?>" />
		</p>  
		<p>
        <div class="bm_header" style="font-weight:bold; background-color:#428BCA; color:#fff; padding:4px;"> [ Display Config ] </div>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e("Theme", 'brainymore')?></label>
			<br/>
			
			<select class="widefat"
				id="<?php echo $this->get_field_id('theme'); ?>"
				name="<?php echo $this->get_field_name('theme'); ?>">
				<option value="theme1" <?php if ($theme=='theme1'){?> selected="selected"
				<?php } ?>>
					<?php _e('Theme1', 'brainymore')?>
				</option>
				<option value="theme2" <?php if ($theme=='theme2'){?> selected="selected"
				<?php } ?>>
					<?php _e('Theme2', 'brainymore')?>
				</option>
				<option value="theme3" <?php if ($theme=='theme3'){?> selected="selected"
				<?php } ?>>
					<?php _e('Theme3', 'brainymore')?>
				</option>
				<option value="theme4" <?php if ($theme=='theme4'){?> selected="selected"
				<?php } ?>>
					<?php _e('Theme4', 'brainymore')?>
				</option>
				<option value="theme5" <?php if ($theme=='theme5'){?> selected="selected"
				<?php } ?>>
					<?php _e('Theme5', 'brainymore')?>
				</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e("Image size", 'brainymore')?></label>
			<br/>
			<select class="widefat"
				id="<?php echo $this->get_field_id('image_size'); ?>"
				name="<?php echo $this->get_field_name('image_size'); ?>">
				<option value="large" <?php if ($image_size=='large'){?> selected="selected"
				<?php } ?>>
					<?php _e('Large', 'brainymore')?>
				</option>
				<option value="medium" <?php if ($image_size=='medium'){?> selected="selected"
				<?php } ?>>
					<?php _e('Medium', 'brainymore')?>
				</option>
				<option value="thumbnail" <?php if ($image_size=='thumbnail'){?> selected="selected"
				<?php } ?>>
					<?php _e('Thumbnail', 'brainymore')?>
				</option>
				<option value="full" <?php if ($image_size=='full'){?> selected="selected"
				<?php } ?>>
					<?php _e('Full', 'brainymore')?>
				</option>
			</select>
		</p>
		<p>
            <label for="<?php echo $this->get_field_id('image_size_custom'); ?>"><?php _e("Image size custom. Format:X,Y in this X is width and Y is height, Eg: 100,70", 'brainymore')?></label>
            <br/>
            <input class="widefat"
                id="<?php echo $this->get_field_id('image_size_custom'); ?>"
                name="<?php echo $this->get_field_name('image_size_custom'); ?>" type="text"
                value="<?php echo esc_attr($image_size_custom); ?>" />
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('thumb_size'); ?>"><?php _e("Thumb size", 'brainymore')?></label>
			<br/>
			<select class="widefat"
				id="<?php echo $this->get_field_id('thumb_size'); ?>"
				name="<?php echo $this->get_field_name('thumb_size'); ?>">
				<option value="large" <?php if ($thumb_size=='large'){?> selected="selected"
				<?php } ?>>
					<?php _e('Large', 'brainymore')?>
				</option>
				<option value="medium" <?php if ($thumb_size=='medium'){?> selected="selected"
				<?php } ?>>
					<?php _e('Medium', 'brainymore')?>
				</option>
				<option value="thumbnail" <?php if ($thumb_size=='thumbnail'){?> selected="selected"
				<?php } ?>>
					<?php _e('Thumbnail', 'brainymore')?>
				</option>
				<option value="full" <?php if ($thumb_size=='full'){?> selected="selected"
				<?php } ?>>
					<?php _e('Full', 'brainymore')?>
				</option>
			</select>
		</p>
		<p>
            <label for="<?php echo $this->get_field_id('thumb_size_custom'); ?>"><?php _e("Thumb size custom. Format:X,Y in this X is width and Y is height, Eg: 100,70", 'brainymore')?></label>
            <br/>
            <input class="widefat"
                id="<?php echo $this->get_field_id('thumb_size_custom'); ?>"
                name="<?php echo $this->get_field_name('thumb_size_custom'); ?>" type="text"
                value="<?php echo esc_attr($thumb_size_custom); ?>" />
        </p>		
		<p>
            <label for="<?php echo $this->get_field_id('show_thumb'); ?>"><?php _e("Show Thumbnails", 'brainymore')?></label>
            <br/>
            <select class="widefat"
                id="<?php echo $this->get_field_id('show_thumb'); ?>"
                name="<?php echo $this->get_field_name('show_thumb'); ?>">
                <option value="1" <?php if ($show_thumb=='1'){?> selected="selected"
                <?php } ?>>
                    <?php _e('Yes', 'brainymore')?>
                </option>
                <option value="0" <?php if ($show_thumb=='0'){?> selected="selected"
                <?php } ?>>
                    <?php _e('No', 'brainymore')?>
                </option>
            </select>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('show_readmore'); ?>"><?php _e("Show readmore", 'brainymore')?></label>
            <br/>
            <select class="widefat"
                id="<?php echo $this->get_field_id('show_readmore'); ?>"
                name="<?php echo $this->get_field_name('show_readmore'); ?>">
                <option value="1" <?php if ($show_readmore=='1'){?> selected="selected"
                <?php } ?>>
                    <?php _e('Yes', 'brainymore')?>
                </option>
                <option value="0" <?php if ($show_readmore=='0'){?> selected="selected"
                <?php } ?>>
                    <?php _e('No', 'brainymore')?>
                </option>
            </select>
        </p>				
		<p>
            <label for="<?php echo $this->get_field_id('show_desc'); ?>"><?php _e("Show Desc", 'brainymore')?></label>
            <br/>
            <select class="widefat"
                id="<?php echo $this->get_field_id('show_desc'); ?>"
                name="<?php echo $this->get_field_name('show_desc'); ?>">
                <option value="1" <?php if ($show_desc=='1'){?> selected="selected"
                <?php } ?>>
                    <?php _e('Yes', 'brainymore')?>
                </option>
                <option value="0" <?php if ($show_desc=='0'){?> selected="selected"
                <?php } ?>>
                    <?php _e('No', 'brainymore')?>
                </option>
            </select>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('show_desc_small'); ?>"><?php _e("Show Desc of small posts", 'brainymore')?></label>
            <br/>
            <select class="widefat"
                id="<?php echo $this->get_field_id('show_desc_small'); ?>"
                name="<?php echo $this->get_field_name('show_desc_small'); ?>">
                <option value="1" <?php if ($show_desc_small=='1'){?> selected="selected"
                <?php } ?>>
                    <?php _e('Yes', 'brainymore')?>
                </option>
                <option value="0" <?php if ($show_desc_small=='0'){?> selected="selected"
                <?php } ?>>
                    <?php _e('No', 'brainymore')?>
                </option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('readmore_label'); ?>"><?php _e("Readmore label", 'brainymore')?></label>
            <br/>
            <input class="widefat"
                id="<?php echo $this->get_field_id('readmore_label'); ?>"
                name="<?php echo $this->get_field_name('readmore_label'); ?>" type="text"
                value="<?php echo esc_attr($readmore_label); ?>" />
        </p>             
            
	<?php
	}	
}
?>