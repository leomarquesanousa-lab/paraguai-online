<?php
/** Decorative crossfade only. The original hero CSS and configuration stay authoritative. */
defined('ABSPATH') || exit;
function paraguai_tech_hero_reference_id($settings=null){
 $settings=is_array($settings)?$settings:get_option('marquesano_store_settings',array());$products=marquesano_store_tech_products();$fallback=$products[0]??null;foreach($products as $p){if($p->is_on_sale()){$fallback=$p;break;}}
 $product=!empty($settings['tech_hero_product'])&&function_exists('wc_get_product')?wc_get_product(absint($settings['tech_hero_product'])):$fallback;
 if(!$product||'publish'!==$product->get_status()||!$product->is_visible()||('marquesano-visual-v1'===$product->get_meta('_marquesano_demo_batch')&&'tech'!==$product->get_meta('_marquesano_demo_segment'))){$product=$fallback;}
 foreach(array(marquesano_store_sanitize_setting($settings['tech_hero_image']??0,'tech_hero_image'),marquesano_store_sanitize_setting($settings['hero_image']??0,'hero_image'),$product?$product->get_image_id():0) as $id){if($id&&marquesano_store_tech_image(absint($id),'full')){return absint($id);}}return 0;
}
function paraguai_tech_hero_validate_images($raw,$primary){
 if(!is_scalar($raw)){throw new RuntimeException('A seleção de imagens adicionais é inválida.');}$parts=array_filter(array_map('trim',explode(',',(string)$raw)),'strlen');if(count($parts)>20){throw new RuntimeException('Use até 20 imagens adicionais.');}if(!$parts){return '';}
 $reference=wp_get_attachment_metadata($primary);if(!$primary||empty($reference['width'])||empty($reference['height'])){throw new RuntimeException('Configure uma imagem original válida para o hero primeiro.');}
 $ratio=$reference['width']/$reference['height'];$ids=array();foreach($parts as $part){if(!ctype_digit($part)||!absint($part)){throw new RuntimeException('Selecione imagens válidas da biblioteca.');}$id=absint($part);if($id===$primary||in_array($id,$ids,true)){continue;}
  $meta=wp_get_attachment_metadata($id);if(!wp_attachment_is_image($id)||'trash'===get_post_status($id)||!in_array(get_post_mime_type($id),array('image/jpeg','image/png','image/webp'),true)||empty($meta['width'])||empty($meta['height'])){throw new RuntimeException('Use somente anexos JPEG, PNG ou WebP válidos.');}
  if(abs(($meta['width']/$meta['height'])/$ratio-1)>.05){throw new RuntimeException('A imagem '.$id.' deve manter a proporção da original ('.$reference['width'].' × '.$reference['height'].'), com tolerância de 5%.');}
  if($meta['width']<min(1280,$reference['width'])){throw new RuntimeException('A imagem '.$id.' precisa de pelo menos '.min(1280,$reference['width']).' pixels de largura para evitar perda de qualidade.');}$ids[]=$id;
 }return implode(',',$ids);
}
function paraguai_tech_hero_slides($primary){
 $first=$primary?wp_get_attachment_image_url($primary,'full'):'';if(!$first){return array();}$urls=array($first);$raw=marquesano_store_setting('tech_hero_images');
 foreach(array_filter(explode(',',(string)$raw)) as $id){try{$valid=paraguai_tech_hero_validate_images($id,$primary);}catch(RuntimeException $e){continue;}if(!$valid){continue;}$url=wp_get_attachment_image_url((int)$valid,'full');if($url&&!in_array($url,$urls,true)){$urls[]=$url;}}return $urls;
}
function paraguai_tech_hero_image_guidance(){
 $id=paraguai_tech_hero_reference_id();$m=wp_get_attachment_metadata($id);if(empty($m['width'])||empty($m['height'])){return 'Configure a imagem original antes de escolher imagens adicionais.';}
 return 'A original permanece sempre em primeiro lugar. Recomendado: mesma proporção de '.$m['width'].' × '.$m['height'].' px ('.number_format_i18n($m['width']/$m['height'],2).':1), tolerância de 5%, largura mínima '.min(1280,$m['width']).' px. Hero: largura 100%, altura mínima desktop 440 px (aprox. 3,27:1 em viewport de 1440 px), tablet 420 px e mobile 580 px; a altura real depende do conteúdo. O cover e o recorte existentes não serão alterados. Ordene pelas setas; remova apenas a seleção, sem excluir mídia. Cada imagem fica 5 s e a transição dura 1,2 s.';
}
add_action('wp_enqueue_scripts',static function(){if('tech'!==marquesano_store_setting('preset')||!marquesano_store_setting('tech_hero_images')){return;}wp_enqueue_style('paraguai-hero-crossfade',get_template_directory_uri().'/assets/css/hero-crossfade.css',array('marquesano-tech-signature'),(string)filemtime(get_template_directory().'/assets/css/hero-crossfade.css'));wp_enqueue_script('paraguai-hero-crossfade',get_template_directory_uri().'/assets/hero-crossfade.js',array(),(string)filemtime(get_template_directory().'/assets/hero-crossfade.js'),true);},45);