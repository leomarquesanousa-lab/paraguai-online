# TECH Signature

## Refinamento comercial compacto

O hero anterior crescia com a largura: sua imagem era quadrada e havia metadados e espaçamentos amplos na coluna de texto. A composição agora usa imagem contida de 260–304px no desktop, legenda com nome/preço e tipografia menor, dimensionando o conteúdo padrão para aproximadamente 390–440px. Não há teto de altura que esconda textos personalizados longos. No mobile, a composição se empilha e a imagem mantém altura automática e `contain`.

Header com base de 68px, navegação na mesma faixa em desktop e busca/ações preservadas. Categorias, dupla editorial, protagonista, vitrines e footer tiveram seus espaçamentos compactados. O grid comercial e suas proteções contra larguras percentuais nativas foram mantidos; imagens de cards continuam quadradas. Não houve alteração de dados, controles ou outros presets.

Validação desta etapa: PHP do hero, estrutura dos dois CSSs, limites do grid, regras de enquadramento do hero e respostas HTTP locais de páginas/imagens. Dimensões visuais finais e interação em dispositivos dependem de navegador conectado; não são afirmadas como verificadas por esses testes de código.

Direção editorial original: grafite, off-white e cinza mineral; azul apenas em detalhes funcionais. Sem layouts, logos, slogans ou identidade copiados de marcas de referência.

## Mudança estrutural

1. Header claro e preciso, busca nativa visível e carrinho real.
2. Hero com composição em duas superfícies, imagem vinculada a um produto, preço verdadeiro e link para seus detalhes.
3. Navegação compacta de categorias e produtos existentes.
4. Dupla editorial assimétrica, imagens grandes e textos fora da imagem.
5. Vitrine nativa, grid responsivo de cinco/quatro colunas no desktop, três no tablet e duas no celular; uma coluna abaixo de 360px.
6. Novo protagonista em painel dividido, com imagem grande, preço e até quatro atributos visíveis do WooCommerce.
7. Segunda vitrine com introdução editorial e cards horizontais de promoções reais; omitida se não houver ofertas.
8. Footer próprio com coleção, conta, pedidos, carrinho, menu institucional e contato/redes apenas quando configurados.

Não foi criado motor comercial, tabela, produto, preço, estoque, pedido, sessão de teste ou configuração no banco. Cards e segunda vitrine usam templates/ações nativos. FASHION, BEAUTY e HOME não carregam o CSS Signature nem as seções exclusivas.

## Configuração

Os controles existentes continuam válidos. Em **Marquesano • TECH Signature**, escolher o ID do produto do hero/protagonista, imagens, títulos, textos editoriais e superfícies grafite/mineral. Os dois destaques mantêm seus controles de produto, título, descrição, imagem e URL. Cores, fonte, logo, textos e demais configurações continuam no Customizer.

A ordem padrão TECH passa a `hero,categories,offers,products,spotlight,discovery,banner,about`. Uma ordem personalizada já salva é preservada: incluir `spotlight` e `discovery` nela, ou usar `preset`, para mostrar as novas seções. Nenhuma migração/salvamento automático é executado.

## Imagens disponíveis e assets necessários

Foram encontradas 15 mídias locais: 12 ilustrações de produtos demonstrativos, dois ícones e um placeholder WooCommerce. Não há fotografias editoriais de estúdio. A loja usa as ilustrações realmente anexadas aos produtos, mantendo sua identificação de demonstração. O hero não utiliza mais a arte genérica anterior nem o placeholder. Imagens configuradas sem arquivo local válido são omitidas, com composição limpa.

Para substituir a apresentação demonstrativa por fotografia final, fornecer imagens autorizadas, sem texto incorporado:

- Hero: produto escolhido em estúdio, 1600 × 1600 px, fundo mineral/off-white, espaço de respiro de 10–15%.
- Editorial principal: produto/contexto em 1600 × 1280 px, proporção 5:4.
- Editorial secundário: produto em 1200 × 1500 px, proporção 4:5.
- Protagonista: fotografia detalhada em 1600 × 1600 px; atributos devem continuar vindo do cadastro real.
- Catálogo: imagem quadrada de cada produto, pelo menos 1000 × 1000 px, objeto completo e fundo neutro.

Não foram baixadas imagens nem apresentados produtos/marcas inexistentes. Com apenas três produtos TECH, a sequência reutiliza contextos de destaque e uma oferta real, sem duplicar cadastros ou inventar uma coleção maior.

## Arquivos e verificação

Novos: `assets/css/tech-signature.css`, `template-parts/tech/{spotlight,discovery,footer}.php` e este guia. Modificados: `functions.php`, `front-page.php`, `footer.php`, `inc/{tech,settings,presentation}.php`, hero TECH, dupla editorial e testes de renderização/CSS.

Testes manuais reutilizáveis, somente leitura: `tools/verify-tech-premium.php` por WP-CLI e `tools/verify-tech-grid.cjs` por Node. Conferem presets, isolamento dos estilos, arquivos de mídia, imagens anexadas, produtos reais, atributos, compra nativa, estrutura CSS e faixas do grid. HTTP local verifica home, imagens/assets e rotas acessíveis. A avaliação visual em navegador/dispositivos permanece pendente quando não houver navegador conectado. Não executar os testes antigos que salvam presets nesta etapa sem autorização específica.
