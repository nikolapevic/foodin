<?php 

//Allow only admin and this user to change password
class Password_Reset_Removed
{

  function __construct()
  {
    add_filter( 'show_password_fields', array( $this, 'disable' ) );
    add_filter( 'allow_password_reset', array( $this, 'disable' ) );
    add_filter( 'gettext',              array( $this, 'remove' ) );
  }

  function disable()
  {
    if ( is_admin() ) {
      $userdata = wp_get_current_user();
      $user = new WP_User($userdata->ID);
      if ( !empty( $user->roles ) && is_array( $user->roles ) && $user->roles[0] == 'administrator')
        return true;
    }
    return false;
  }

  function remove($text)
  {
    return str_replace( array('Lost your password?', 'Lost your password'), '', trim($text, '?') );
  }
}

$pass_reset_removed = new Password_Reset_Removed();


//Hide capabilities from non admin users
function hide_admin_capabilities()
{
    if (!is_super_admin()) {
		remove_all_actions( 'admin_notices' );

		add_filter( 'ure_show_additional_capabilities_section', '__return_false' );
		remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
		function hide_personal_options(){
			echo "\n" . '<script type="text/javascript">jQuery(document).ready(function($) { $(\'form#your-profile > h3:first\').hide(); $(\'form#your-profile > table:first\').hide(); $(\'form#your-profile\').show(); });</script>' . "\n";
		}
		add_action('admin_head','hide_personal_options');
		remove_menu_page( 'edit.php');
		remove_menu_page( 'edit-comments.php');
		remove_menu_page( 'wpcf7');
		remove_menu_page( 'vc-welcome');
		remove_menu_page( 'tools.php');
		remove_menu_page( 'index.php');
		remove_menu_page( 'users.php');
    }
}
add_action( 'admin_head', 'hide_admin_capabilities', 1 );

//Remove editing metaboxes from non admin users
function remove_metaboxes() {
	if (!is_super_admin()) {
		remove_meta_box('wpseo_meta','product','normal');
	}
}

add_action( 'add_meta_boxes' , 'remove_metaboxes', 50 );
?>
