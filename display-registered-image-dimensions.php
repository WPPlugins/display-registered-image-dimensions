<?php
/*
Plugin Name: Display Registered Image Dimensions
Description: Display the images defined using add_image_size in function.php and from installed plugins. Values are displayed in the Media Settings screen.
Author: Jason Tucker
Version: 1.8.5
Author URI: http://www.wpmedia.pro
Plugin URI: http://wpmedia.pro/display-registered-image-dimensions-plugin-for-wordpress/
*/

// Create the function to output the contents of our 

function jgt_dashboard_widget_function() {

        $image_sizes = get_intermediate_image_sizes();
        echo "<table class='form-table'><tbody>";
		foreach ($image_sizes as $size_key => $size_value):
			$image_size = get_thumb_image_size( $size_value );
			if ( !empty( $image_size ) ) {
				$width = $image_size['width'];
				$height = $image_size['height'];
				$crop = ( ! empty($image_size['crop']) ? 'cropped' : '' );
				
			}
			
			echo "<tr valign='top'><th scope='row'>".$size_value."</th><td>".$width."x".$height."px ".$crop."</td></tr>";			
		endforeach;
		echo "</tbody></table>";
}

/* ------------------------------------------------------------------------ * 
 * Setting Registration 
 * ------------------------------------------------------------------------ */   
  
/** 
 * Initializes the theme options page by registering the Sections, 
 * Fields, and Settings. 
 * 
 * This function is registered with the 'admin_init' hook. 
 */   
add_action('admin_init', 'image_dimensions_initialize_theme_options');  
function image_dimensions_initialize_theme_options() {  
  
    // First, we register a section. This is necessary since all future options must belong to one.   
    add_settings_section(  
        'general_settings_section',         // ID used to identify this section and with which to register options  
        'Registered Image Dimensions',                  // Title to be displayed on the administration page  
        'image_dimensions_general_options_callback', // Callback used to render the description of the section  
        'media'                           // Page on which to add this section of options  
    );  

    add_settings_field(   
        'show_content',                       
        'Content',                
        'image_dimensions_toggle_content_callback',    
        'general',                            
        'general_settings_section',           
        array(                                
            'Activate this setting to display the content.'  
        )  
    );  
      
 

    register_setting(  
        'media',  
        'show_content'  
    );  
      

      
} 
  

  
/* ------------------------------------------------------------------------ * 
 * Action Links 
 * ------------------------------------------------------------------------ */     
  
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links' );

function my_plugin_action_links( $links ) {
   $links[] = '<a href="'. get_admin_url(null, 'options-media.php') .'">Display Sizes</a>';
   return $links;
}


add_filter( 'plugin_row_meta', 'ts_plugin_meta_links', 10, 2 );
function ts_plugin_meta_links( $links, $file ) {
 
	$plugin = plugin_basename(__FILE__);
 
	// create link
	if ( $file == $plugin ) {
		return array_merge(
			$links,
			array( '<a href="http://www.wpmedia.pro" target="_blank">WPMedia.Pro</a>', '<a href="http://www.wpwatercooler.com" target="_blank">WPwatercooler Weekly Video Podcast</a>' )
		);
	}
	return $links;
 
}
  
/* ------------------------------------------------------------------------ * 
 * Section Callbacks 
 * ------------------------------------------------------------------------ */   

function image_dimensions_general_options_callback() {  
   	jgt_dashboard_widget_function(); 
} 
  

  

function get_thumb_image_size( $name ) {
	global $_wp_additional_image_sizes;

	if($name == 'thumbnail'){
		$return_dimensions['width'] = get_option( 'thumbnail_size_w');
		$return_dimensions['height'] = get_option( 'thumbnail_size_h');
		return $return_dimensions;
	}
	elseif($name == 'medium'){
		$return_dimensions['width'] = get_option( 'medium_size_w');
		$return_dimensions['height'] = get_option( 'medium_size_h');
		return $return_dimensions;
	}
	elseif($name == 'large'){
		$return_dimensions['width'] = get_option( 'large_size_w');
		$return_dimensions['height'] = get_option( 'large_size_h');
		return $return_dimensions;
	}
	elseif ( isset( $_wp_additional_image_sizes[$name] ) ){
		return $_wp_additional_image_sizes[$name];
	}

	return false;
}
