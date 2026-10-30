<?php
/** Editorial identities and shared presentation helpers. @package MarquesanoStore */
defined( 'ABSPATH' ) || exit;

function marquesano_store_identity( $preset = null ) {
	$key = null === $preset ? marquesano_store_setting( 'preset' ) : $preset;
	$copy = array(
		'tech' => array( 'Tecnologia para o seu dia', 'Os melhores produtos em um só lugar.', 'Explore nossa seleção, compare os detalhes e encontre a tecnologia que combina com você.', 'Ver produtos', 'Produtos em destaque', 'Explore por categoria', 'Sua próxima conexão começa aqui.', 'Novas conexões estão a caminho.' ),
		'fashion' => array( 'Um olhar para o essencial', 'Vista a sua própria perspectiva.', 'Formas, texturas e combinações para expressar quem você é. O cotidiano, visto de um jeito novo.', 'Descobrir a coleção', 'Peças para o seu repertório', 'Encontre sua expressão', 'O estilo está no seu olhar.', 'Uma nova composição está por vir.' ),
		'beauty' => array( 'Essência & cuidado', 'O seu ritual de sentir bem.', 'Perfumes, texturas e pequenos gestos. Descubra novas formas de transformar o cuidado em um momento só seu.', 'Encontrar meu ritual', 'Descobertas para os sentidos', 'Um cuidado para cada momento', 'Pequenos gestos. Novas sensações.', 'Um novo ritual está florescendo.' ),
		'home' => array( 'Objetos que fazem parte da vida', 'A casa encontra o seu jeito.', 'Texturas que acolhem. Formas que convidam a ficar. Descubra peças para viver cada espaço com mais intenção.', 'Inspirar minha casa', 'Detalhes que acolhem', 'Um novo olhar para cada espaço', 'Mais tempo para habitar.', 'Estamos preparando novos espaços.' ),
	);
	$values = $copy[ isset( $copy[ $key ] ) ? $key : 'tech' ];
	// A single translation filter keeps preset copy replaceable by language packs.
	$values = array_map( static function ( $text ) { return translate( $text, 'marquesano-store' ); }, $values );
	return array_combine( array( 'eyebrow', 'hero_title', 'hero_text', 'hero_button', 'products_title', 'categories_title', 'caption', 'empty_title' ), $values );
}

function marquesano_store_empty_collection() {
	get_template_part( 'template-parts/empty-collection', null, array( 'title' => marquesano_store_identity()['empty_title'] ) );
}

function marquesano_store_on_primary( $hex ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) { $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2]; }
	$luminance = 0;
	foreach ( array( 0.2126, 0.7152, 0.0722 ) as $index => $weight ) {
		$channel = hexdec( substr( $hex, $index * 2, 2 ) ) / 255;
		$luminance += $weight * ( $channel <= 0.04045 ? $channel / 12.92 : pow( ( $channel + 0.055 ) / 1.055, 2.4 ) );
	}
	return $luminance > 0.179 ? '#000000' : '#ffffff';
}
