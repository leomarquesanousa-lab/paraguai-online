<?php
/** Original geometric product illustrations, rendered locally with GD. No downloads. */
namespace Marquesano\Admin;
defined( 'ABSPATH' ) || exit;

final class Demo_Artwork {
	public static function media( $row ) {
		$known = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_marquesano_demo_image_key', 'meta_value' => $row['key'] ) );
		if ( $known && wp_attachment_is_image( $known[0] ) ) { return (int) $known[0]; }
		if ( ! function_exists( 'imagecreatetruecolor' ) ) { throw new \RuntimeException( 'GD indisponível para gerar as ilustrações próprias.' ); }
		$directory = __DIR__ . '/artwork';
		if ( ! wp_mkdir_p( $directory ) ) { throw new \RuntimeException( 'Não foi possível criar o diretório de arte.' ); }
		$file = $directory . '/' . sanitize_file_name( $row['key'] ) . '.png';
		self::draw( $row['key'], $file );
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		// Sideload a temporary copy; preserve the reusable source artwork.
		$temp = wp_tempnam( $row['key'] . '.png' );
		if ( ! $temp || ! copy( $file, $temp ) ) { throw new \RuntimeException( 'Falha ao preparar a imagem.' ); }
		$id = media_handle_sideload( array( 'name' => basename( $file ), 'tmp_name' => $temp ), 0, 'Ilustração demonstrativa original — ' . $row['name'] );
		if ( is_wp_error( $id ) ) { if ( file_exists( $temp ) ) { wp_delete_file( $temp ); } throw new \RuntimeException( $id->get_error_message() ); }
		update_post_meta( $id, '_marquesano_demo_image_key', $row['key'] );
		update_post_meta( $id, '_marquesano_demo_batch', Demo_Command::BATCH );
		update_post_meta( $id, '_wp_attachment_image_alt', 'Ilustração demonstrativa: ' . $row['name'] );
		return $id;
	}

	private static function draw( $key, $file ) {
		$im = imagecreatetruecolor( 1000, 1000 );
		imageantialias( $im, true );
		$segment = explode( '-', $key )[0];
		$palettes = array( 'tech' => array( '#eef1f8', '#23314d', '#8e9db9', '#cce77f' ), 'fashion' => array( '#eee9e0', '#75614f', '#b6a18b', '#dacdbb' ), 'beauty' => array( '#f5e8df', '#87535a', '#c89b78', '#e8c4ad' ), 'home' => array( '#eae7dc', '#5d6c53', '#ae8b64', '#d0c2a9' ) );
		list( $bg, $dark, $mid, $light ) = $palettes[ $segment ];
		$c = static function ( $hex ) use ( $im ) { return imagecolorallocate( $im, hexdec( substr( $hex, 1, 2 ) ), hexdec( substr( $hex, 3, 2 ) ), hexdec( substr( $hex, 5, 2 ) ) ); };
		$r = static function ( $x, $y, $w, $h, $hex ) use ( $im, $c ) { imagefilledrectangle( $im, $x, $y, $x + $w, $y + $h, $c( $hex ) ); };
		$e = static function ( $x, $y, $w, $h, $hex ) use ( $im, $c ) { imagefilledellipse( $im, $x, $y, $w, $h, $c( $hex ) ); };
		$p = static function ( $points, $hex ) use ( $im, $c ) { imagefilledpolygon( $im, $points, $c( $hex ) ); };
		$r( 0, 0, 1000, 1000, $bg );
		$e( 760, 180, 660, 660, '#ffffff' );
		$e( 505, 797, 520, 55, '#d5cec4' );
		switch ( $key ) {
			case 'tech-01':
				imagesetthickness( $im, 58 ); imagearc( $im, 500, 455, 420, 480, 180, 360, $c( $dark ) );
				imagesetthickness( $im, 24 ); imagearc( $im, 500, 451, 415, 465, 180, 360, $c( $mid ) );
				$r( 263, 425, 50, 190, $mid ); $r( 687, 425, 50, 190, $mid );
				$e( 310, 590, 160, 265, $dark ); $e( 690, 590, 160, 265, $dark );
				$e( 300, 590, 95, 215, '#142139' ); $e( 700, 590, 95, 215, $mid ); $e( 707, 664, 8, 8, $light ); break;
			case 'tech-02':
				$p( array( 185,370,770,340,825,700,220,738 ), $mid ); $p( array( 200,370,755,345,797,662,235,695 ), $dark );
				for ( $y = 0; $y < 5; $y++ ) { for ( $x = 0; $x < 11; $x++ ) { $r( 245 + $x * 45, 400 + $y * 48, 33, 32, $x === 0 ? $light : '#70819e' ); } }
				$r( 370, 640, 240, 24, $mid ); break;
			case 'tech-03':
				$r( 300, 350, 400, 340, $dark ); $e( 500, 350, 400, 100, $mid ); $e( 500, 690, 400, 100, $dark );
				for ( $y = 410; $y < 675; $y += 15 ) { for ( $x = 325; $x < 680; $x += 15 ) { $e( $x, $y, 3, 3, $mid ); } }
				$e( 500, 350, 50, 16, $light ); break;
			case 'fashion-01':
				$p( array( 370,245,270,300,175,460,285,535,345,450,330,780,670,780,655,450,715,535,825,460,730,300,630,245 ), '#e3d6bc' );
				$p( array( 370,245,500,310,430,390 ), $mid ); $p( array( 630,245,500,310,570,390 ), $mid );
				$r( 495, 310, 6, 470, '#b9a992' ); for ( $y = 395; $y < 740; $y += 62 ) { $e( 512, $y, 8, 8, $dark ); } break;
			case 'fashion-02':
				$p( array( 360,240,260,290,150,595,260,642,325,465,305,800,695,800,675,465,740,642,850,595,740,290,640,240 ), $dark );
				$p( array( 360,240,493,390,430,800,305,800,325,465,275,560,300,310 ), '#615041' );
				$p( array( 360,240,493,390,405,365,442,318 ), $mid ); $p( array( 640,240,493,390,590,365,554,318 ), $mid );
				for ( $y = 440; $y < 740; $y += 85 ) { $e( 515, $y, 13, 13, $light ); } break;
			case 'fashion-03':
				imagesetthickness( $im, 30 ); imagearc( $im, 500, 410, 330, 330, 180, 360, $c( $dark ) );
				$p( array( 280,400,720,400,765,745,235,745 ), $dark ); $p( array( 280,400,720,400,670,555,330,555 ), '#8c7460' );
				$r( 470, 523, 60, 45, $light ); $r( 488, 530, 24, 30, $dark ); break;
			case 'beauty-01':
				$r( 340, 370, 320, 355, $mid ); $e( 500, 370, 320, 55, $light ); $r( 365, 390, 270, 315, '#deb891' );
				$r( 410, 235, 180, 135, $dark ); $r( 420, 360, 160, 26, '#ba9b69' );
				$r( 390, 465, 220, 150, '#faf3e8' ); $r( 430, 505, 140, 3, $dark ); $r( 460, 545, 80, 3, $dark ); $r( 375, 402, 8, 270, '#f9ead6' ); break;
			case 'beauty-02':
				$r( 295, 485, 410, 205, '#d7b39e' ); $e( 500, 690, 410, 75, '#c39a86' ); $e( 500, 485, 410, 100, '#ead2bf' );
				$r( 280, 430, 440, 68, $dark ); $e( 500, 430, 440, 95, '#ab7e7b' ); $e( 500, 495, 440, 70, $dark );
				$r( 360, 540, 280, 95, '#f9eee2' ); $r( 420, 573, 160, 3, $dark ); $r( 455, 599, 90, 2, $dark ); break;
			case 'beauty-03':
				$e( 505, 555, 510, 270, '#c28f81' ); $r( 250, 420, 510, 135, '#c28f81' ); $e( 505, 420, 510, 260, '#e1b8a6' );
				$e( 505, 420, 290, 125, '#d4a695' ); $r( 430, 411, 150, 3, $dark );
				$e( 760, 710, 100, 35, '#b87c82' ); $e( 300, 720, 115, 30, '#b87c82' ); break;
			case 'home-01':
				$r( 330, 315, 340, 340, $dark ); $e( 500, 315, 340, 140, $dark );
				$r( 270, 530, 460, 185, '#899375' ); $e( 500, 530, 460, 115, '#a4ad90' );
				$r( 250, 450, 85, 270, $dark ); $r( 665, 450, 85, 270, $dark ); $e( 292, 450, 85, 90, '#7b886c' ); $e( 707, 450, 85, 90, '#7b886c' );
				$r( 300, 710, 22, 110, $mid ); $r( 678, 710, 22, 110, $mid ); break;
			case 'home-02':
				$e( 500, 580, 400, 380, $mid ); $r( 420, 345, 160, 150, $mid ); $e( 500, 345, 160, 45, '#d8ba90' ); $e( 500, 345, 115, 24, '#745b46' );
				$e( 440, 570, 50, 235, '#c5a77e' ); $r( 390, 710, 220, 40, $mid ); break;
			case 'home-03':
				$e( 500, 745, 325, 65, '#80664e' ); $r( 485, 400, 30, 345, $mid );
				$p( array( 365,245,635,245,755,490,245,490 ), '#dbc8a3' ); $e( 500, 245, 270, 55, '#ebdec1' ); $e( 500, 490, 510, 75, '#bfa981' );
				$r( 470, 705, 60, 40, $mid ); break;
		}
		imagesetthickness( $im, 1 );
		imagestring( $im, 3, 55, 55, 'MARQUESANO / DEMONSTRACAO', $c( $dark ) );
		imagestring( $im, 2, 55, 920, strtoupper( $key ) . ' / ILUSTRACAO ORIGINAL', $c( $dark ) );
		if ( ! imagepng( $im, $file, 7 ) ) { throw new \RuntimeException( 'Falha ao renderizar ' . $key ); }
		imagedestroy( $im );
	}
}
