<?php
namespace Marquesano\Admin\Panel;
defined('ABSPATH') || exit;
final class Hero_Images {
 public static function handle($data) {
  need('manage_appearance'); need('upload_media');
  $id=absint($data['source']??0); $file=get_attached_file($id); $mime=get_post_mime_type($id);
  if(!$id || 'attachment'!==get_post_type($id) || 'trash'===get_post_status($id) || !in_array($mime,array('image/jpeg','image/png','image/webp'),true) || !$file || !is_file($file)){throw new \RuntimeException('Selecione uma imagem JPEG, PNG ou WebP local válida.');}
  $uploads=wp_get_upload_dir();$real=realpath($file);$root=realpath($uploads['basedir']);
  if(!$real || !$root || 0!==strpos(wp_normalize_path($real),trailingslashit(wp_normalize_path($root)))){throw new \RuntimeException('Imagem fora da biblioteca local.');}
  $size=wp_getimagesize($file);
  if(!$size || ($size['mime']??'')!==$mime || $size[0]>8000 || $size[1]>8000 || $size[0]*$size[1]>20000000 || filesize($file)>min(5*MB_IN_BYTES,wp_max_upload_size())){throw new \RuntimeException('Use imagem válida de até 5 MB, 8000 px por lado e 20 milhões de pixels.');}
  if(($data['mode']??'')==='info'){return array('id'=>$id,'url'=>wp_get_attachment_url($id),'width'=>$size[0],'height'=>$size[1],'title'=>get_the_title($id));}
  if(($data['mode']??'')!=='create'){throw new \RuntimeException('Operação inválida.');}
  foreach(array('x','y') as $axis){if(!isset($data[$axis]) || !is_numeric($data[$axis]) || $data[$axis]<0 || $data[$axis]>100){throw new \RuntimeException('Enquadramento inválido.');}}
  require_once ABSPATH.'wp-admin/includes/image.php';
  $editor=wp_get_image_editor($file);if(is_wp_error($editor)){throw new \RuntimeException('Não foi possível abrir a imagem.');}
  $ratio=1896/829; $w=$size[0];$h=$size[1];$cw=min($w,$h*$ratio);$ch=$cw/$ratio;
  $result=$editor->crop((int)round(($w-$cw)*$data['x']/100),(int)round(($h-$ch)*$data['y']/100),(int)round($cw),(int)round($ch),1896,829,false);
  if(is_wp_error($result)){throw new \RuntimeException('Falha ao adaptar a imagem.');}
  $editor->set_quality(85);$dir=$uploads['path'];if(!wp_mkdir_p($dir)){throw new \RuntimeException('Pasta de mídia indisponível.');}
  $dest=trailingslashit($dir).wp_unique_filename($dir,'hero-adaptado-'.$id.'.webp');$output=$editor->save($dest,'image/webp');
  if(is_wp_error($output)){throw new \RuntimeException('Falha ao gravar cópia otimizada.');}
  $copy=wp_insert_attachment(array('post_mime_type'=>'image/webp','post_title'=>get_the_title($id).' — Hero 1896×829','post_status'=>'inherit'),$output['path'],0,true);
  if(is_wp_error($copy)){wp_delete_file($output['path']);throw new \RuntimeException('Falha ao cadastrar cópia.');}
  update_post_meta($copy,'_paraguai_hero_source',$id);update_post_meta($copy,'_paraguai_hero_crop',array('x'=>(float)$data['x'],'y'=>(float)$data['y']));
  wp_update_attachment_metadata($copy,wp_generate_attachment_metadata($copy,$output['path']));
  return array('id'=>$copy,'url'=>wp_get_attachment_url($copy),'title'=>get_the_title($copy));
 }
}
