

// =============================================================================
// Admin Pages
// =============================================================================

add_action( 'admin_init', 'my_admin_init' );

function my_admin_init()
{
    // Style text in the editor like the frontend
    add_editor_style('/css/editor.css');

    // Remove extra scary buttons
    add_filter("mce_buttons", "extended_editor_mce_buttons", 0);
    add_filter("mce_buttons_2", "extended_editor_mce_buttons_2", 0);

    function extended_editor_mce_buttons($buttons) {
    
	return array(
	    "formatselect","separator",
	    "bold", "italic", "underline", "strikethrough", "separator",
	    "bullist", "separator",
	    "charmap", "separator",
	    "link", "unlink", "anchor", "separator",
	    "undo", "redo", "separator",
	    "fullscreen"
	);
    }

    function extended_editor_mce_buttons_2($buttons) {
	// the second toolbar line
	return array();
    }

    // Remove un-neccesary metaboxes for non admin users
    if ( !current_user_can( 'manage_options' ) || true ) {
	$unused_meta_boxes = array(
	    'trackbacksdiv', 'commentstatusdiv', 'commentsdiv', 'authordiv',  'postcustom',
	    'revisionsdiv'
	);
	foreach( $unused_meta_boxes as $box ){
	    remove_meta_box( $box,'post','normal' );
	    remove_meta_box( $box,'page','normal' );
	    remove_meta_box( $box,'carousel','normal' );
	    remove_meta_box( $box,'events','normal' );
	    remove_meta_box( $box,'research','normal' );
	    remove_meta_box( $box,'news','normal' );
	}
    }
    
    // Admin Styles
    add_admin_style( '/css/admin.css' );
    add_admin_script( '/scripts/admin.js' );


} // admin_init

add_action( 'wp_dashboard_setup', 'my_db_metaboxes', 20, 0 );

function my_db_metaboxes()
{
    global $wp_meta_boxes;
 
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_plugins']);
        
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_secondary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
        
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_incoming_links']);
	
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_comments']);
	
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_right_now']);
	
}


/**
 * Render a text input, will use global post_id to find the current content
 * @global int $post_id
 * @param string $name
 * @param string $label 
 */
function render_input( $name, $label, $size = 50, $multiple = false )
{
    global $post_id;
    if( $multiple ){
	$name = $name . '[]';
    }
    echo '<p class="item textinput">';
    echo '<label for="' . $name . '">' . $label . '</label> ';
    echo '<input type="text" name="' . $name . '" id="' . $name . '" value="' . get_post_meta($post_id, $name, true) . '" size="' . $size . '" />';
    echo '</p>';

}

/**
 * Render a textarea, will use global post_id to find the current content
 * @global int $post_id
 * @param string $name
 * @param string $label 
 */
function render_textarea( $name, $label, $limit = 0 )
{
    global $post_id;
    echo '<p class="item textarea">';
    echo '<label for="' . $name . '">' . $label . '</label> ';
    echo '<textarea data-limit="' . $limit . '" class="text" id="' . $name . '" name="' . $name . '">' . get_post_meta($post_id, $name, true) . '</textarea>';
    echo '</p>';

}

/**
 * Render a select box, will use global post_id to find the current value for checked state
 * @global int $post_id
 * @param type $name
 * @param type $label 
 */
function render_cb( $name, $label )
{
    global $post_id;
    $checked = ( get_post_meta($post_id, $name, true) === 'true') ? 'checked="checked"' : '';

    echo '<p class="item">';
    echo '<label for="' . $name . '">' . $label . '</label> ';

    echo '<input type="checkbox" class="checkbox" value="true" name="' . $name . '" id="' . $name . '" ' . $checked . ' />';
    echo '</p>';
}

/**
 * Render a select box, will use global post_id to check current value for selected state
 * @global int $post_id
 * @param string $name
 * @param string $label
 * @param array $data 
 */
function render_select( $name, $label, $data )
{
    global $post_id;
    $current_value = get_post_meta($post_id, $name, true);
    
    echo '<p class="item">';
    echo '<label for="' . $name . '">' . $label . '</label> ';
    echo '<select id="' . $name . '" name="' . $name . '">';
    echo '<option value="0">Please Select</option>';
    foreach( $data as $value => $text ){
	$selected = ( $current_value == $value ) ? ' selected="selected" ' : '';
	echo '<option value="' . $value . '" ' . $selected . ' >' . $text . '</option>';
    }
    echo '</select>';
    echo '</p>';
}




/**
 * Enqueue a css file for admin, must be run on admin_init
 * @param string $css_file
 * @return bool 
 */
function add_admin_style( $css_file )
{
    $url = get_bloginfo('template_url') . $css_file;
    $file = TEMPLATEPATH . $css_file;
    if ( file_exists($file) ) {
        wp_register_style('rr_admin_styles', $url,'1.0');
        wp_enqueue_style( 'rr_admin_styles');
	return true;
    }else{
	return false;
    }
}

/**
 * Enqueue a script for admin, must be run on admin_init
 * @param string $js_file
 * @param string $handle
 * @return bool 
 */
function add_admin_script( $js_file, $handle = 'rr_admin_scripts' )
{
    
    $url = get_bloginfo('template_url') . $js_file;
    $file = TEMPLATEPATH . $js_file;
    if ( file_exists($file) ) {
        wp_register_script($handle, $url,'1.0');
        wp_enqueue_script( $handle);
	return true;
    }else{
	return false;
    }
}

/**
 * Render a nonce field to check post data is coming from the correct post_type
 * @param string $context 
 */
function render_nonce_field( $context )
{
    echo '<input class="nonce" type="hidden" name="_nonce" value="' . wp_create_nonce( $context ) . '" />';
}


// =============================================================================
// Handle posted data, and save it against the post
// =============================================================================

add_action('save_post', 'my_save_meta');

/* When the post is saved, saves our custom data */
function my_save_meta( $post_id )
{
    if( !isset($_POST) || !array_key_exists( '_nonce', $_POST ) )
	return false;
    
    
    // find out what set of cutom fields this page is using, use also as nonce context
    $custom_fieldset = page_type( $_POST['ID'], $_POST['post_type'] );
    
    
    if ( !wp_verify_nonce( $_POST['_nonce'], $custom_fieldset ) ) {
	return $post_id;
    }

    // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
    // to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	return $post_id;

    // Check permissions
    if ( isset( $_POST['post_type'] ) ) {
	if ( 'page' == $_POST['post_type'] ) {
	    if ( !current_user_can( 'edit_page', $post_id ) )
		return $post_id;
	} else {
	    if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	}
    }
    

    
    
    

    // OK, we're authenticated: we need to find and save the data
    if( !isset($GLOBALS['accepted_custom_fields'][$custom_fieldset]) ){
	return false;
    }
    
    foreach ( $GLOBALS['accepted_custom_fields'][$custom_fieldset] as $key ) {
	if ( array_key_exists( $key, $_POST ) ) {
	    $meta_value = $_POST[$key];

	    if( is_array( $meta_value ) ){
		$meta_value = serialize( $meta_value );
	    }
	    

	    // Check meta value exists already and update it if it does
	    if ( get_post_meta( $post_id, $key, true ) ) {
		update_post_meta( $post_id, $key, $meta_value );
	    } else {
		if ( !update_post_meta( $post_id, $key, $meta_value ) ) {
		    add_post_meta( $post_id, $key, $meta_value );
		}
	    }
	}else{
	    // field is registered but not sent in post. it might be an uncheck checkbox...
	    delete_post_meta($post_id, $key);
	    
	}
    }
}// save function


/**
 * If a post is a page, check whether it is the homepage, otherwise see if it has a special template. and return that.
 * Otherwise its not a page
 * @param type $post_id
 * @return type 
 */
function page_type( $post_id, $post_type )
{
    if( 'page' == $post_type ) {
	if( is_admin_homepage($post_id) ){
	    // homepage
	    return 'home';
	}else{
	    $template = get_post_meta( $post_id,'_wp_page_template', true );
	    if( $template && 'default' != $template ){
		// special template page
		$page_type = str_replace('.php', '', $template);
		return $page_type;
	    }else{
		// normal page
		return 'page';
	    }
	}
    }else{
	// not a page
	return $post_type;
    }
}

/**
 * Find out if this current admin form is the homepage, as set by WP Options.
 * Good to use in 'add_meta_boxes' bound function
 * @param int $post_id
 * @return bool 
 */
function is_admin_homepage($post_id)
{
    return ( 
    'page' == get_option( 'show_on_front' ) 
    && get_option( 'page_on_front' ) 
    && $post_id == get_option( 'page_on_front' ) 
    );
}


/**
 * Get names of pages/posts in an array, with the id as the key
 * @param array (optional) $query_args
 * @return array 
 */
function get_post_names( $query_args = null )
{
    $args = array( 
	'post_type' => 'page'
    );
    
    if( is_array( $query_args ) ){
	$args = array_merge($args, $query_args);
    }
    
    $data = get_posts( $args );
    $ret = array();
    foreach( $data as $item ){
	$ret[$item->ID] = $item->post_title;
    }
    
    return $ret;
    
}


/**
 * Remove unwanted menu items, hooked to 'admin_menu' action
 */
function remove_menus () {
    global $menu;
    
    $unused = array('menu-posts','menu-comments','menu-links');
	
    foreach( $menu as $key => $item ){
	if( in_array( @$item[5], $unused ) ){
	    unset($menu[$key]);
	}
    }
}
add_action('admin_menu', 'remove_menus');