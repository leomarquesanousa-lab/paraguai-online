<?php
/** One native custom_logo shared by storefront, operational panel and Woo emails. */
defined('ABSPATH') || exit;
function paraguai_online_brand_image($loading='lazy'){
 $id=(int)get_theme_mod('custom_logo');if(!$id||!wp_attachment_is_image($id)){return '';}
 return wp_get_attachment_image($id,'medium_large',false,array('class'=>'po-brand-image','alt'=>'Paraguai Online','loading'=>$loading,'decoding'=>'async','sizes'=>'(max-width:600px) 170px,220px'));
}
add_filter('get_custom_logo_image_attributes',static function($attributes){$attributes['alt']='Paraguai Online';return $attributes;});
add_action('wp_enqueue_scripts',static function(){wp_enqueue_style('paraguai-branding',get_template_directory_uri().'/assets/css/branding.css',array('marquesano-store'),(string)filemtime(get_template_directory().'/assets/css/branding.css'));},45);
// Read-time integration only: the WooCommerce option itself is never overwritten.
$paraguai_email_logo=static function($value){$id=(int)get_theme_mod('custom_logo');return $id&&wp_attachment_is_image($id)?wp_get_attachment_image_url($id,'full'):$value;};
add_filter('option_woocommerce_email_header_image',$paraguai_email_logo);
add_filter('default_option_woocommerce_email_header_image',$paraguai_email_logo);