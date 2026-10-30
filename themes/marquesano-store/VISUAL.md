# Reformulação visual — avaliação local

Correção da troca de identidades: schema e sanitização desacoplados da leitura da opção. Prévia real do Customizer e frontend após salvar foram testados nos quatro presets, com restauração da configuração original. Detalhes e teste reproduzível em [tools/README.md](tools/README.md). Campos editoriais vazios herdam o preset; usar `preset` em Seções para herdar sua organização.

Atualização do catálogo: 12 produtos demonstrativos e 12 ilustrações próprias foram importados por autorização expressa. Cada preset exibe seus três demonstrativos nas vitrines e buscas, sem excluir produtos reais. Detalhes da importação e testes em `plugins/marquesano-admin/demo/README.md`. O trecho histórico abaixo sobre manifesto não executado corresponde à etapa anterior; a importação agora foi realizada e validada.

## Identidades

| Preset | Composição |
| --- | --- |
| TECH | Header em três áreas com busca central, hero escuro, acento cítrico, vitrine de promoções reais, categorias compactas, cards com até dois atributos reais. |
| FASHION | Marca central, navegação editorial, hero em painel e imagem vertical, categorias escalonadas, vitrine em três colunas e tipografia serifada. |
| BEAUTY | Hero assimétrico, imagem em arco e composição delicada, cards arredondados, categorias verticais, tons de ameixa e areia. |
| HOME | Imagem panorâmica de ambiente, painel de texto sobreposto, categorias em mosaico, cards abertos e composição espaçosa em verde/linho. |

Uma única estrutura comercial WooCommerce continua responsável por preços, estoque, variações, carrinho, checkout, conta e pedidos. Não há botão de favoritos, frete grátis, desconto, avaliações ou benefício comercial fictício. A vitrine `offers` é omitida quando não há promoções reais.

## Arquivos principais

- Alterados: `style.css`, `functions.php`, `header.php`, `front-page.php`, `inc/settings.php`, `inc/woocommerce.php`, `template-parts/home/{hero,categories,products}.php`.
- Criados: `inc/presentation.php`, `template-parts/search.php`, `template-parts/empty-collection.php`, `template-parts/home/offers.php`, `template-parts/heroes/{tech,fashion,beauty,home,copy,media}.php`, `assets/art/{tech,fashion,beauty,home}.svg`.
- Plugin: `includes/modules.php` registra a rotina CLI apenas nesse contexto; `demo/` contém manifesto, comando e instruções separados. Nenhuma rotina de importação roda em visitas ou ativação.

## Avaliar

1. Entrar no WordPress e abrir **Marquesano → Personalização → Templates e conteúdo**.
2. Pré-visualizar os quatro presets. Não é necessário gravar a seleção para avaliar o layout. Fotografias escolhidas no campo Imagem do hero substituem a arte editorial.
3. Cores, textos, fonte e ordem de seções já salvos prevalecem. Se uma ordem antiga não incluir `offers`, adicionar esse nome para habilitar a vitrine de promoções. O modo de avaliação não apaga personalizações anteriores.
4. Avaliar desktop, tablet e mobile, foco de teclado, menus, catálogo e produto. Usar o procedimento em `plugins/marquesano-admin/demo/README.md` somente quando decidir gravar produtos demonstrativos.
5. Validar também carrinho, checkout e conta com dados autorizados. Não finalizar pedidos ou habilitar pagamentos reais durante a avaliação visual.

## Imagens e idioma

As quatro artes SVG são originais do projeto, leves, sem scripts, fontes ou recursos externos. Não são fotografias nem identificam mercadorias reais. A mídia existente contém apenas ícones e placeholder; nenhuma foto comercial foi reaproveitada ou baixada. Usar fotografias autorizadas na mídia para avaliar FASHION/BEAUTY com fotografia real.

Todos os textos novos são em português brasileiro. O idioma das telas nativas continua sob a configuração existente do WordPress/WooCommerce. Na inspeção, o pacote PHP pt_BR do WordPress estava presente e o arquivo `languages/plugins/woocommerce-pt_BR.mo` não foi encontrado. Rótulos nativos eventualmente em inglês exigem revisão do idioma/traduções pelo administrador; não foram alterados idioma, núcleo ou arquivos de terceiros.

## Verificações e pendências

PHP e JSON validados; os quatro SVGs passaram na análise XML. Home respondeu 200 com o novo hero TECH e o novo estado vazio. Rotas de catálogo, carrinho e conta responderam; checkout vazio redirecionou ao carrinho. O modo **Em breve** continua interceptando páginas comerciais anônimas. Sem navegador conectado: revisão visual e testes autenticados não foram concluídos. Manifesto demonstrativo preparado, sem execução de criação ou remoção, sem banco modificado por essa rotina. Não há validação de pagamentos, frete ou compra ponta a ponta.
