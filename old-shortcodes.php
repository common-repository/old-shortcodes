<?php
/*
Plugin Name: Old Shortcodes
Plugin URI: http://www.laptoptips.ca/projects/old-shortcodes/
Description: A simple plugin to hide any unused shortcodes.
Version: 1.0
Author: Andrew Ozz
Author URI: http://www.laptoptips.ca/

Released under the GPL v.2, http://www.gnu.org/copyleft/gpl.html

    Copyright (C) 2008  Andrew Ozz

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2 
    as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License version 2 for more details.
*/

function oshort_replace($atts, $content='') {
	return '';
}

function oshort_init() {
	$old = get_option('oshort_opt');
	
	if ( is_array($old) && ! empty($old) ) {
		foreach ( $old as $val ) {
			if ( 2 > strlen($val) ) continue;
			add_shortcode("$val", 'oshort_replace');
		}
	}
}
add_action( 'plugins_loaded', 'oshort_init' );

function oshort_adminpage() {
    
    load_plugin_textdomain('oshort', 'wp-content/plugins/old-shortcodes/languages');
	$oshort_opt = get_option('oshort_opt');
    $update_opt = false;
	
	if ( ! is_array($oshort_opt) ) {
        $oshort_opt = array();
        $update_opt = 1;
    }

    if ( isset($_POST['oshort_opt']) ) {
        check_admin_referer('oshort_opt-options');
    	$oshort = stripslashes( trim( $_POST['oshort'] ) );

		if ( $_POST['oshort_opt_rem_all'] ) {
            $oshort_opt = array();
            $update_opt = 1;
        }

		if ( $_POST['oshort_opt_add'] && ! in_array( $oshort, $oshort_opt) ) {
			$oshort = preg_replace( '/^\[?(.*?)\]?$/', '$1',  $oshort );
        	$oshort_opt[] = $oshort;
        	if ( 200 < count($oshort_opt) ) $old = array_pop($oshort_opt);
        	$update_opt = 1;
        }

        if ( $_POST['oshort_opt_rem'] && in_array($oshort, $oshort_opt) ) {

            $oshort_opt = array_diff( $oshort_opt, array("$oshort") );
            $update_opt = 1;
        }
    }
    
    if ( $update_opt ) { 
		update_option( 'oshort_opt', $oshort_opt ); ?>
		<div id="message" class="updated fade"><p><strong><?php _e('Settings saved.') ?></strong></p></div>
	<?php } ?>	

    <div class="wrap">
    <h2><?php _e('Old Shortcodes', 'oshort'); ?></h2>
    <p><?php _e('Hide shortcodes left over from old or deactivated plugins.', 'oshort'); ?></p>
    <p><?php _e('Please enter only the shortcode title, without the square brackets [ ].', 'oshort'); ?></p>
    
	<form method="post" name="f1" id="f1" action="">
    <table class="form-table">
		<tr>
        <td style="width:110px;"><?php _e('Enter a shortcode:', 'oshort'); ?></td>
        <td><input type="text" name="oshort" id="oshort" size="25" maxlenght="50" value="" />
            <input type="submit" class="button" name="oshort_opt_add" value="Hide it" onclick="if (form.oshort.value == ''){alert('<?php echo js_escape(__('Please enter a shortcode.')); ?>');return false;}" />
            <input type="submit" class="button" name="oshort_opt_rem" value="Unhide" onclick="if (form.oshort.value == ''){alert('<?php echo js_escape(__('Please enter a shortcode.')); ?>');return false;}" />
        </td></tr>
        
        <tr>
		<td><?php _e('Hidden shortcodes:', 'oshort'); ?></td>
        <td>
<?php if ( is_array($oshort_opt) && ! empty($oshort_opt) ) { 

        foreach( $oshort_opt as $oshort ) { ?> 
            
<span style="background-color:#eee;border: 1px solid #ddd;padding:4px 7px;cursor:pointer;line-height:32px;margin:4px;white-space:nowrap;" onclick="document.forms.f1.oshort.value = '<?php echo $oshort; ?>'"><strong>[<?php echo $oshort; ?>]</strong></span>

<?php   }
    } else {
		echo '(none)';
	} ?>
        </td></tr>
		<tr><td colspan="2">
		<input type="submit" class="button" name="oshort_opt_rem_all" value="Unhide all" onclick="return confirm('<?php echo js_escape(__('Unhiding all old shortcodes...')); ?>');" />
		<?php wp_nonce_field( 'oshort_opt-options' ); ?>
    	<input type="hidden" name="oshort_opt" value="1" />
		</td></tr>
	</table>
    </form>
    </div>
<?            
}

function oshort_menu() {
    
    if ( function_exists('add_management_page') ) 
		add_management_page( __('Old Shortcodes', 'oshort'), __('Old Shortcodes', 'oshort'), 9, __FILE__, 'oshort_adminpage');
}
add_action( 'admin_menu', 'oshort_menu' );

?>