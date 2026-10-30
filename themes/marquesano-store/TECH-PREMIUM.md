# TECH Premium — referência aprovada

## Correção da vitrine

O shortcode usava `columns-6`, e `woocommerce-layout.css` aplicava `width:13.5%` por item. Essa largura percentual continuava ativa dentro das células do grid do tema: o reset anterior não superava os seletores nativos que incluem o número de colunas. Isso comprimia cards e imagens. As miniaturas locais retornaram HTTP 200 como PNG; não foi necessário alterar mídia, URLs, produtos ou thumbnails.

O TECH agora neutraliza explicitamente largura, float, clear e margens dos itens, com seletor suficiente para shortcode, catálogo e relacionados. O shortcode usa `columns-5`; o grid determina cinco colunas em >=1440px, quatro em >=1024px, três em >=768px e duas abaixo de 768px. Abaixo de 360px, uma coluna preserva a legibilidade.

Imagens têm proporção 1:1, largura fluida e `object-fit:contain`; cards não têm altura fixa. Nomes são limitados visualmente a duas linhas, mantendo o texto integral no HTML. Preço, estoque e compra permanecem no fluxo natural, com botões alinhados por flex. Tipografia, contraste e espaçamentos foram refinados sem expandir os efeitos visuais.

Verificação estrutural de CSS e limites dos breakpoints, sem instalar dependências:

```sh
node wp-content/themes/marquesano-store/tools/verify-tech-grid.cjs
```

O teste de renderização existente também verifica a marcação `columns-5`, arquivos de mídia e thumbnails anexados. Nenhuma validação declara aprovação visual sem navegador.

Implementação local baseada em `references/tech-reference.png`. A referência foi analisada, nunca usada como fundo ou imagem da interface.

## Composição

- Header azul quase preto, marca, navegação real, busca clara, conta e carrinho com contador nativo atualizado pelos fragmentos WooCommerce.
- Hero panorâmico, título forte branco/azul elétrico, CTA real e quatro atalhos configuráveis. Em celular, texto e imagem se empilham; navegação permanece acessível sem botão simulado.
- Faixa horizontal de categorias existentes e atalhos de produtos, dois banners assimétricos de produtos reais, vitrine clara com cards compactos, preços/estoque reais e ação de compra nativa.
- Avaliações, preços antigos e ofertas aparecem apenas quando fornecidos pelo WooCommerce. Favoritos, entrega nacional, garantia e parcelamento da referência não foram inventados.

## Configurar para clientes

Em **Aparência → Personalizar → Marquesano • TECH Premium**:

- Imagem do hero, trecho do título destacado e texto lateral.
- Para cada banner: ID de produto opcional, imagem, título, descrição e link. Valores vazios herdam o produto publicado; os IDs estão na listagem nativa de produtos.
- Textos dos quatro atalhos, no formato `título|descrição`. Campo vazio oculta o atalho. As ações continuam apontando às funções reais da loja.

Título/texto/botão do hero, logotipo, cores e organização das seções continuam nas opções existentes. Os controles novos não gravam valores automaticamente. Manter `offers` na ordem das seções para exibir os dois banners de destaque do TECH. Os demais presets mantêm seus próprios componentes e não carregam o CSS Premium.

## Peças visuais pendentes

O catálogo local tem três produtos TECH com ilustrações demonstrativas. Por isso, são exibidos esses produtos e seus nomes reais; não há repetição para preencher os seis cards da referência nem departamentos fictícios.

Para fidelidade fotográfica, ainda são necessárias imagens autorizadas: composição de eletrônicos do hero (aprox. 1600 × 700), duas artes de campanha (aprox. 1000 × 450 e 700 × 450) e, se desejado, versão clara do logotipo. Até lá, usa-se a arte local de fones no hero e as imagens cadastradas nos banners/cards. Nenhuma foto externa foi baixada ou produto alterado.

## Arquivos

Novos: `inc/tech.php`, `assets/css/tech-premium.css`, `template-parts/tech/{header,service-links,categories,promotions}.php`, `tools/verify-tech-premium.php` e este guia.

Alterados: `functions.php`, `header.php`, `inc/{settings,presentation,customizer}.php`, `template-parts/heroes/tech.php`, `template-parts/home/{categories,offers,products}.php`.

## Validação

Teste de renderização somente em memória, pelo Customizer real, sem chamar `save()`:

```sh
wp eval-file wp-content/themes/marquesano-store/tools/verify-tech-premium.php --user=USUARIO_ADMIN
```

Confere os quatro presets, isolamento do CSS, composição/H1, pesquisa, carrinho, banners de produtos existentes, botões de compra nativos e breakpoints. PHP e JSON também são validados. Testes HTTP consultam apenas páginas/assets locais. Não são executadas as rotinas anteriores que salvam presets, importam produtos ou manipulam sessões/carrinhos.

Revisão visual em navegador e interação em dispositivos continuam necessárias; verificações de HTML/CSS não substituem essa avaliação. O modo Em breve permanece intacto. Sem alterações deliberadas em banco, catálogo, preços, estoque ou configuração.
