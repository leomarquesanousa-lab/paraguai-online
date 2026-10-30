<?php
/** Capability-aware access to authoritative native management. @package MarquesanoAdmin */
namespace Marquesano\Admin;
defined( 'ABSPATH' ) || exit;

function management() {
	guard( 'manage_woocommerce' );
	heading( __( 'Gestão da loja', 'marquesano-admin' ) );
	if ( ! woo_available() ) { echo '</div>'; return; }
	echo '<p>' . esc_html__( 'Escolha uma área para abrir a gestão nativa. Alterações são salvas diretamente no WooCommerce, com as permissões e validações oficiais.', 'marquesano-admin' ) . '</p><div class="ms-grid">';
	native_link( __( 'Produtos e estoque', 'marquesano-admin' ), __( 'Criar e editar produtos simples ou variáveis, imagens, preços, promoções, variações e estoque.', 'marquesano-admin' ), 'edit.php?post_type=product', 'edit_products' );
	native_link( __( 'Adicionar produto', 'marquesano-admin' ), __( 'Cadastrar um produto real. Variações são configuradas na edição do produto.', 'marquesano-admin' ), 'post-new.php?post_type=product', 'publish_products' );
	native_link( __( 'Categorias', 'marquesano-admin' ), __( 'Organizar categorias, hierarquia, descrição e imagens.', 'marquesano-admin' ), 'edit-tags.php?taxonomy=product_cat&post_type=product', 'manage_product_terms' );
	native_link( __( 'Atributos', 'marquesano-admin' ), __( 'Cadastrar os atributos utilizados em variações de produtos.', 'marquesano-admin' ), 'edit.php?post_type=product&page=product_attributes', 'manage_product_terms' );
	$order_url = class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ? 'admin.php?page=wc-orders' : 'edit.php?post_type=shop_order';
	native_link( __( 'Pedidos e status', 'marquesano-admin' ), __( 'Consultar pedidos, atualizar status e acompanhar notas no editor oficial.', 'marquesano-admin' ), $order_url, 'edit_shop_orders' );
	native_link( __( 'Clientes', 'marquesano-admin' ), __( 'Consultar dados permitidos no relatório nativo de clientes do WooCommerce.', 'marquesano-admin' ), 'admin.php?page=wc-admin&path=/customers', 'view_woocommerce_reports' );
	native_link( __( 'Contas de usuários', 'marquesano-admin' ), __( 'Administrar usuários conforme as permissões do WordPress.', 'marquesano-admin' ), 'users.php', 'list_users' );
	foreach ( array( 'general' => __( 'Endereço e moeda', 'marquesano-admin' ), 'products' => __( 'Produtos e inventário', 'marquesano-admin' ), 'shipping' => __( 'Frete e zonas', 'marquesano-admin' ), 'checkout' => __( 'Pagamentos', 'marquesano-admin' ), 'account' => __( 'Contas e privacidade', 'marquesano-admin' ), 'email' => __( 'E-mails da loja', 'marquesano-admin' ), 'advanced' => __( 'Páginas e endpoints', 'marquesano-admin' ) ) as $tab => $title ) {
		native_link( $title, __( 'Abrir configurações oficiais. Nenhuma alteração automática é realizada.', 'marquesano-admin' ), 'admin.php?page=wc-settings&tab=' . $tab, 'manage_woocommerce' );
	}
	native_link( __( 'Páginas institucionais', 'marquesano-admin' ), __( 'Editar páginas de contato, sobre, termos e políticas já existentes ou criar as necessárias.', 'marquesano-admin' ), 'edit.php?post_type=page', 'edit_pages' );
	native_link( __( 'Status do sistema', 'marquesano-admin' ), __( 'Verificar compatibilidade, registros e diagnóstico do WooCommerce.', 'marquesano-admin' ), 'admin.php?page=wc-status', 'manage_woocommerce' );
	echo '</div><p>' . esc_html__( 'Limite desta versão: a edição operacional ocorre nas telas nativas; o painel Marquesano não replica editores de produtos, pedidos ou clientes.', 'marquesano-admin' ) . '</p></div>';
}

function appearance() {
	guard( 'edit_theme_options' );
	heading( __( 'Personalização da loja', 'marquesano-admin' ) );
	if ( function_exists( 'marquesano_store_setting' ) ) {
		echo '<p><strong>' . esc_html__( 'Template aplicado à loja:', 'marquesano-admin' ) . ' ' . esc_html( strtoupper( marquesano_store_setting( 'preset' ) ) ) . '</strong></p>';
	}
	if ( 'marquesano-store' !== get_template() ) {
		echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'O tema Marquesano Store não está ativo. As opções da loja dependem dele; nenhuma ativação automática será realizada.', 'marquesano-admin' ) . '</p></div>';
	}
	echo '<p>' . esc_html__( 'A seleção no Personalizador altera a prévia completa. Para aplicar à loja, use o botão de salvar/publicar alterações do Personalizador; isso não desativa o modo Em breve do WooCommerce.', 'marquesano-admin' ) . '</p><div class="ms-grid">';
	native_link( __( 'Templates e conteúdo', 'marquesano-admin' ), __( 'TECH, FASHION, BEAUTY e HOME; cores, fontes locais, banners, seções, textos, contatos e redes sociais.', 'marquesano-admin' ), 'customize.php?autofocus[section]=marquesano_store', 'edit_theme_options' );
	native_link( __( 'Logotipo e favicon', 'marquesano-admin' ), __( 'Escolher imagens reais da Biblioteca de mídia em Identidade do site.', 'marquesano-admin' ), 'customize.php?autofocus[section]=title_tagline', 'edit_theme_options' );
	native_link( __( 'Menus da loja', 'marquesano-admin' ), __( 'Organizar navegação principal e links institucionais do rodapé.', 'marquesano-admin' ), 'nav-menus.php', 'edit_theme_options' );
	echo '</div><section class="ms-card"><h2>' . esc_html__( 'Organizar a home', 'marquesano-admin' ) . '</h2><p>' . esc_html__( 'Use preset no campo Seções para herdar a organização do template. Para personalizar, use hero,categories,offers,products,banner,about na ordem desejada e remova um nome para ocultar a seção. Banners e texto institucional vazios não são exibidos; categorias sem produtos também não.', 'marquesano-admin' ) . '</p><p>' . esc_html__( 'As páginas institucionais usam o editor nativo. Textos comerciais, políticas, imagens e informações de contato devem corresponder ao negócio real.', 'marquesano-admin' ) . '</p></section></div>';
}
