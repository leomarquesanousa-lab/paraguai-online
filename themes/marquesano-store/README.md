# Marquesano Store 1.0 — tema

Reformulação visual atual: consulte [VISUAL.md](VISUAL.md) para os quatro layouts comerciais, arquivos, avaliação e limitações. O estado vazio agora usa composição editorial e a seção opcional `offers` exibe somente promoções reais.

Tema clássico com componentes compartilhados e quatro presets. Não copia templates internos do WooCommerce: catálogo, produto, galeria, variações, carrinho, checkout e conta usam as implementações nativas. Não cria páginas, produtos, pedidos, menus ou configurações na ativação.

## Estrutura

- `functions.php`: suporte ao tema, menus, assets e composição dos módulos.
- `inc/settings.php`: presets, campos, leitura e sanitização.
- `inc/customizer.php`: controles persistentes pelo Personalizador.
- `inc/woocommerce.php`: wrappers, filtros, estoque e integração pelos hooks.
- `template-parts/home/`: hero, categorias, produtos, banner e institucional.
- `template-parts/catalog-filters.php`: busca, categoria e intervalo de preço.
- `front-page.php`: composição da home e preservação do conteúdo da página inicial estática.
- `page.php`: páginas comuns e conteúdo nativo de carrinho, checkout e conta.
- `header.php`, `footer.php`, `index.php`, `404.php`: estrutura e estados gerais.
- `style.css`, `theme.json`: estilos responsivos e configuração do editor.

## Personalização

Em **Marquesano → Personalização → Templates e conteúdo**, selecionar TECH, FASHION, BEAUTY ou HOME. Cada preset muda paleta, fonte local, header, hero, cards, botões, espaçamentos e a ordem inicial das seções. Uma ordem de seções salva pelo usuário prevalece sobre o preset. As cores personalizadas também prevalecem; limpar os campos para voltar à paleta do preset.

As opções ficam no único registro `marquesano_store_settings` via APIs WordPress. Logo usa o suporte nativo `custom-logo`; favicon usa `site_icon`. O editor pode pré-visualizar e salvar manualmente. Não há fontes remotas, dependências de build ou motor comercial paralelo.

Usar `hero,categories,products,banner,about` no campo Seções para definir ordem/visibilidade. Categorias vazias são omitidas; produtos sem resultados mostram o aviso WooCommerce. Banners e institucional só aparecem com conteúdo. A home não inventa benefícios, produtos ou imagens.

Menus: atribuir **Navegação principal** e **Links institucionais** em Aparência → Menus. Políticas e páginas comerciais devem ser preenchidas com conteúdo real. O título SEO vem de `title-tag`, canonical singular do WordPress e dados estruturados de produto do WooCommerce; não há geração de metadados comerciais fictícios.

## Verificação manual

A instalação atual apresenta o modo **Em breve** do WooCommerce aos visitantes anônimos. Validar o catálogo autenticado no WordPress. Não desativar o modo nem publicar a loja como parte desses testes.

1. Abrir a home e o catálogo; testar busca, categoria, preço, ordenação e paginação com produtos reais.
2. Abrir um produto simples e outro variável; testar galeria, seleção de variação, disponibilidade e adição ao carrinho.
3. Verificar quantidades e remoção no carrinho; abrir o checkout nativo. Finalizar pedidos somente em um procedimento de teste autorizado.
4. Testar login, dados da conta e histórico com uma conta apropriada.
5. Pré-visualizar os quatro presets, logo, favicon, banners e menus em desktop e mobile; conferir foco de teclado, contraste das cores escolhidas e textos alternativos.

Pagamentos, frete, tributos, e-mails, políticas e publicação exigem configuração e validação próprias. Não há ativação de gateway nem alegação de teste de compra ponta a ponta.
