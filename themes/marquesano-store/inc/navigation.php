<?php
/** TECH navigation presentation; native menu persisted by the own plugin. */
defined('ABSPATH') || exit;
function paraguai_tech_navigation_nodes($nodes,$level=0){
 if($level>20){return;}echo '<ul class="'.($level?'po-category-list':'po-main-list').'">';
 foreach($nodes as $node){echo '<li>';if(!empty($node['children'])){$id=wp_unique_id('po-menu-panel-');echo '<details class="'.($level?'po-nav-branch':'po-nav-dropdown').'"><summary aria-controls="'.esc_attr($id).'">'.esc_html($node['label']).'<span aria-hidden="true" class="po-nav-chevron"></span></summary><div id="'.esc_attr($id).'" class="'.($level?'po-branch-content':'po-menu-panel').'"><a class="po-view-category" href="'.esc_url($node['url']).'">'.esc_html($level?'Ver categoria':'Ver todos os produtos').'</a>';paraguai_tech_navigation_nodes($node['children'],$level+1);echo '</div></details>';}else{echo '<a href="'.esc_url($node['url']).'">'.esc_html($node['label']).'</a>';}echo '</li>';}
 echo '</ul>';
}
function paraguai_tech_navigation(){
 if(class_exists('Marquesano\\Admin\\Panel\\Navigation')){paraguai_tech_navigation_nodes(\Marquesano\Admin\Panel\Navigation::tree());}
 elseif(has_nav_menu('primary')){wp_nav_menu(array('theme_location'=>'primary','container'=>false,'depth'=>4));}
 else{paraguai_tech_navigation_nodes(array(array('label'=>'Início','url'=>home_url('/'),'children'=>array()),array('label'=>'Produtos','url'=>marquesano_store_shop_url(),'children'=>array())));}
}
add_action('wp_enqueue_scripts',static function(){
 if('tech'!==marquesano_store_setting('preset')){return;}
 wp_enqueue_style('paraguai-tech-navigation',get_template_directory_uri().'/assets/css/tech-navigation.css',array('marquesano-tech-signature'),(string)filemtime(get_template_directory().'/assets/css/tech-navigation.css'));
 wp_enqueue_script('paraguai-tech-navigation',get_template_directory_uri().'/assets/tech-navigation.js',array(),(string)filemtime(get_template_directory().'/assets/tech-navigation.js'),true);
},40);