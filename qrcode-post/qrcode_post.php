<?php 
/**
 * Plugin Name:       QR Code to Post
 * Plugin URI:        https://mharif.com/plugins
 * Description:       qr code for blog post plugins
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            mh Arif
 * Author URI:        https://mharif.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       qrcode-post
 * Domain Path:       /languages/
 */

 function qrcode_post_load_textdomain(){
    load_plugin_textdomain( 'qrcode-post', false, dirname(__FILE__). "/languages" );
 }

function qrcode_display_code($content){

    $current_post_id = get_the_ID();
    $current_post_title = get_the_title($current_post_id);
    $current_post_url = urlencode(get_the_permalink($current_post_id));
    $current_post_type = get_post_type( $current_post_id );
    /**
     * apply filter for qrcode
     */
    $height = get_option('qrcode_height');
    $width = get_option('qrcode_width');
    $height = $height ? $height : 220;
    $width = $width ? $width : 220;
    $dimension = apply_filters( 'qrcode_dimension', "{$width}x{$height}" );
    $image_attribute = apply_filters( 'qrcode_image_attr', null );

    if( 'post' === $current_post_type ){
        $image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?data=%s&size=%s&margin=0',$current_post_url, $dimension); 
        $content .= sprintf( "<div class='qrcode'><img %s src='%s' alt='%s'></div>", $image_attribute, $image_src, $current_post_title );
        return $content;
    }

}
add_filter('the_content', 'qrcode_display_code');

function qrcode_settings_init(){

    add_settings_section('qrcode_section', __('Set QR Code Size', 'qrcode-post'), 'qrcode_section_callback', 'general');

    add_settings_field('qrcode_height', __('QR Code Height', 'qrcode-post'), 'qrcode_height_display', 'general', 'qrcode_section');
    add_settings_field('qrcode_width', __('QR Code Width', 'qrcode-post'), 'qrcode_width_display', 'general', 'qrcode_section');

    register_setting('general', 'qrcode_height', array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'qrcode_width', array('sanitize_callback' => 'esc_attr'));
}
function qrcode_section_callback(){
    echo "<p>".__('Settings for post qr code plugins', 'qrcode-post')."</p>";
}
function qrcode_height_display(){
    $height = get_option('qrcode_height');
    printf("<input type='text' id='%s' name='%s' value='%s' />", 'qrcode_height', 'qrcode_height', $height);
}
function qrcode_width_display(){
    $width = get_option('qrcode_width');
    printf("<input type='text' id='%s' name='%s' value='%s' />", 'qrcode_width', 'qrcode_width', $width);
}
add_action('admin_init', 'qrcode_settings_init');