# Guia de imagens — Marquesano Store 1.0

Auditoria em 26/10/2026. Leitura do tema, dos campos de mídia do plugin próprio e do HTML/URLs locais públicos. Nenhum cadastro, configuração ou imagem foi alterado. Não houve medição em navegador renderizado.

**Inventário: 23 variantes de exibição, alimentadas por 12 fontes/campos de mídia.** A contagem distingue enquadramentos por preset; não conta cada produto, categoria ou imagem de galeria como novo slot. Há ainda contextos dinâmicos delegados ao conteúdo e ao WooCommerce, descritos separadamente. Nem todos aparecem simultaneamente: dependem das seções habilitadas, dos produtos e dos campos preenchidos.

## Como interpretar as medidas

- Medidas visíveis são **pixels CSS estimados**, não resolução do arquivo. Base: desktop de 1024–1920 px; tablet de 761–1023 px; celular de 360–760 px. Para TECH, a composição muda em **767 px**: tablet 768–1023 e celular até 767. Telas menores são tratadas nas observações.
- `style.css` limita os contêineres gerais a 1440 px e aplica laterais `clamp(20px,4vw,64px)`, considerando raiz de 16 px. A área útil costuma ficar entre 92% do viewport e aproximadamente 1312–1325 px. Exceções do hero e do protagonista constam abaixo.
- CSS efetivo TECH: `style.css` → `assets/css/tech-premium.css` → `assets/css/tech-signature.css`. Regras antigas de hero presentes nos dois primeiros arquivos não descrevem o componente Signature atual.
- Breakpoints gerais: **1000, 760 e 420 px**; regra adicional acima de 1440 px. TECH também usa **1250, 1100, 1024, 768, 767, 359 e 1600 px**, conforme o componente. Na faixa 761–767 px, os heroes genéricos ainda estão em duas colunas, enquanto o Signature TECH já empilha.
- `cover` preenche e recorta; `contain` preserva o objeto e pode deixar respiro. Máscaras arredondadas podem ocultar cantos mesmo com `contain`. Não há controle de ponto focal no painel.
- As áreas seguras sugeridas são orientações de composição, não garantias calculadas pelo código. Texto comercial deve permanecer nos campos HTML, fora do bitmap.
- Códigos **R01–R12** remetem à tabela consolidada de arquivos em B. São masters para produção; o WordPress pode entregar derivados menores via `srcset`. Não é necessário enviar todas as resoluções de entrega manualmente.

## A. Espaços existentes

### A1. Localização, campo e espaço visível

Campos de aparência pertencem a `marquesano_store_settings`, salvo logo e favicon. D/T/M significam desktop/tablet/celular, nos intervalos acima. `≈` indica estimativa.

| ID | Preset e página/seção | Componente ou campo | D: largura × altura visível | T: largura × altura visível | M: largura × altura visível |
| --- | --- | --- | --- | --- | --- |
| I01 | Todos; header de todas as páginas | `custom_logo` (theme mod); `header.php`, `tech/header.php` | Geral: até 220 × 64; TECH: até 190 × 48 | Mesmos limites, reduzindo conforme espaço | Geral: até 170 × 64; TECH mantém limite específico 190 × 48. Tamanho efetivo depende da proporção e do header |
| I02 | Todos; aba, favoritos e atalhos do navegador | `site_icon` (opção WordPress; saída de `wp_head`) | Ícone tipicamente 16–32; derivados conforme dispositivo | Igual; ícone de atalho pode ser maior | Atalho Apple costuma usar 180 × 180; não é banner da página |
| I03 | TECH; hero | `heroes/tech.php`; `tech_hero_image` → `hero_image` → imagem do produto selecionado | **Imagem** ≈442–650 × 260–304; hero completo tem mínimo 390 de altura, podendo crescer com texto | ≈314–442 × 260 | Imagem ≈280–420 × 210–315; largura limitada a 420, proporção 4:3. Hero completo tem altura livre |
| I04 | FASHION; hero editorial | `heroes/fashion.php` + `heroes/media.php`; `hero_image` | ≈515–735 × 660–820 | ≈375–515 × 540–660; altura muda também em 1000 | Largura do viewport; altura 420 até 420 px e 540 entre 421–760 |
| I05 | BEAUTY; hero em arco | `heroes/beauty.php`; `hero_image` | Área da foto ≈385–590 × 570, descontado padding do palco | ≈285–405 × 470; acima de 1000, altura 570 | ≈256–436 × 450; palco limitado a 500 incluindo 64 de padding horizontal |
| I06 | HOME; hero de ambiente | `heroes/home.php`; `hero_image` | Foto ocupa fundo do hero, ≈1024–1440 de largura e altura **mínima** 720. Acima de 1440, largura 1376 | ≈761–1023 × pelo menos 720; texto pode ampliar | Foto ≈360–760 × 470; painel de texto começa após 350 de padding superior e sobrepõe parte inferior |
| I07 | TECH; destaque editorial maior | `tech/promotions.php`; `tech_promo_1_image` → imagem do produto | ≈535–760 × 301–428; coluna 7/12 após gap | ≈398–535 × 224–301 | ≈316–706 × 178–397; seção empilhada |
| I08 | TECH; destaque editorial menor | Mesmo componente; `tech_promo_2_image` → imagem do produto | ≈382–542 × 287–407; coluna 5/12, deslocada 32 para baixo | ≈284–382 × 213–287 | ≈297–664 × 223–498; deslocamento lateral de 6% |
| I09 | TECH; produto protagonista | `tech/spotlight.php`; `tech_spotlight_image` → imagem do produto | Imagem ≈563–800 × 260–360; coluna de mídia 60%, com padding | ≈421–563 × 260 | ≈320–727 × 240–545, proporção 4:3; texto empilhado abaixo |
| I10 | Todos; banner secundário opcional da home | `home/banner.php`; `banner_image` | Geral ≈432–640 de largura; FASHION ≈488–725; HOME ≈424–632. Altura proporcional ao arquivo, limitada a 520 | ≈300–490 de largura, altura proporcional até 520 | ≈316–720 de largura, altura proporcional até 520. Empilha apenas em ≤760 |
| I11 | TECH; trilho de categorias e atalhos de produtos | `tech/categories.php`; termo `thumbnail_id` ou imagem principal do produto | Imagem **44 × 44** | 44 × 44 | 44 × 44; trilho horizontal rolável, não grade de banners |
| I12 | FASHION; categorias da home | `home/categories.php`; termo `thumbnail_id` | ≈293–420 × 391–560; 3 colunas | ≈212–293 × 283–391; 3 colunas | ≈150–345 × 200–460; 2 colunas |
| I13 | BEAUTY; categorias da home | Mesmo componente; termo `thumbnail_id` | ≈298–317 × 373–396; grade limitada a 1000 | ≈217–298 × 271–373 | ≈150–345 × 188–431; 2 colunas |
| I14 | HOME; primeira categoria em destaque | Mesmo componente; primeiro termo `thumbnail_id` | ≈617–875 × 229–324; ocupa 2 de 3 colunas | ≈456–617 × 169–229 | ≈320–700 × 119–259; ocupa as 2 colunas |
| I15 | HOME; demais categorias | Mesmo componente; outros termos `thumbnail_id` | ≈293–420 × 220–315 | ≈212–293 × 159–220 | ≈150–345 × 113–259 |
| I16 | TECH; vitrine principal, catálogo, busca, relacionados e outras listas nativas de produtos | `home/products.php` / loop WooCommerce; imagem principal `image_id` | ≈223–315 ao quadrado. 4 colunas em 1024–1439; 5 a partir de 1440 | ≈228–304 ao quadrado; 3 colunas | ≈150–350 ao quadrado; 2 colunas. Abaixo de 360, 1 coluna, conforme largura útil |
| I17 | FASHION; vitrines e listas nativas | Loop WooCommerce; imagem principal | ≈287–415 × 383–553; 3 colunas | ≈207–287 × 276–383; 3 colunas | ≈150–345 × 200–460; 2 colunas |
| I18 | BEAUTY; vitrines e listas nativas | Loop WooCommerce; imagem principal | Foto ≈172–268 ao quadrado, já descontado padding do card; 4 colunas | ≈172–246 ao quadrado; 3 colunas até 1000, 4 acima disso | ≈130–325 ao quadrado, descontado padding; 2 colunas |
| I19 | HOME; vitrines e listas nativas | Loop WooCommerce; imagem principal | ≈211–307 ao quadrado; 4 colunas | ≈211–286 ao quadrado; 3 colunas até 1000, 4 acima | ≈150–345 ao quadrado; 2 colunas |
| I20 | TECH; vitrine secundária em linhas | `tech/discovery.php`; imagens dos produtos em promoção | **140 × 140** | 140 × 140 | **110 × 110**; abaixo de 360, passa à largura disponível, em composição vertical |
| I21 | Todos; página de produto, imagem principal/zoom | Galeria nativa WooCommerce; produto `image_id`; suporte declarado em `functions.php` | Estimativa de planejamento: ≈400–660 de largura, altura intrínseca | ≈300–470 de largura enquanto houver duas colunas nativas | Em ≤760, tema força galeria a 100%: ≈320–700 de largura, altura intrínseca |
| I22 | Todos; fotos adicionais, miniaturas e lightbox do produto | Galeria WooCommerce; `gallery_ids` no painel; até 20 imagens adicionais | Foto ativa: mesma área de I21; miniaturas e lightbox dependem do WooCommerce | Idem, sem dimensão fixa própria | Foto ativa: I21; lightbox condicionado à tela; miniaturas nativas |
| I23 | Todos; foto por variação | `variations[n][image_id]`; galeria nativa de produto variável | Substitui I21; não é um banner adicional | Mesma área de I21 | Mesma área de I21 |

As medidas I21–I23 são deliberadamente condicionais: o tema não define a largura desktop da galeria, a quantidade de miniaturas por linha nem a janela do lightbox. Isso vem do WooCommerce e de suas configurações/estilos. Os intervalos são para planejamento, não uma medição ou uma garantia de 48% de largura. Não existe altura fixa própria para cortar a foto do produto.

### A2. Arquivo, enquadramento e troca por dispositivo

| ID | Master recomendado em B / proporção | Exibição e recorte | Área segura sugerida | Formato / reutilização entre dispositivos |
| --- | --- | --- | --- | --- |
| I01 | R01; logo horizontal, proporção original | `contain`, limites máximos; sem recorte CSS. Suporte de logo usa tamanho indicativo 360 × 120, com largura/altura flexíveis | Marca inteira, com respiro interno de 8–12%; conferir legibilidade em 48 de altura | PNG transparente. Uma versão responsiva; não há campo de logo mobile ou logo alternativo por preset |
| I02 | R02; 1:1 | Derivados do WordPress; cortes/redimensionamentos do ícone, não `cover` do tema | Símbolo no centro, 10–15% de margem; sem texto miúdo | PNG; mesmo master para todos |
| I03 | R03; 4:3 | `contain`, centralizado, sem recorte do objeto; `mix-blend-mode:multiply` altera a aparência do fundo | Objeto central nos 80% internos; texto/CTA ficam na coluna HTML separada | WebP ou PNG com transparência; mesma imagem atende desktop e mobile |
| I04 | R04; 3:4 | `cover`, centro, recorte variável. Painel cobre cerca de 96 da parte inferior da foto no mobile | Rosto/produto nos 55–60% centrais; não colocar detalhes críticos na base ou nos extremos | WebP; uma foto pode atender, mas enquadramento mobile próprio é recomendável para campanhas. Não há seleção mobile implementada |
| I05 | R04; 3:4 | `cover`, máscara de arco e selo sobreposto no canto inferior esquerdo | Frasco/rosto nos 60% centrais; preservar tampa/topo; evitar cantos superiores e base esquerda | WebP; PNG se realmente precisar de alpha. Um master centralizado pode servir a todos |
| I06 | R05; 2:1 desktop / R06, 4:5, mobile de produção futura | `<img>` absoluto, `cover`, `object-position:center 60%`; fundo visual, não `background-image` CSS | Desktop: ambiente/produto centro-direita; painel cobre a esquerda. Mobile: assunto central e longe dos últimos ≈120 da foto | WebP; versão mobile é fortemente recomendada pelo recorte radical. Hoje só um arquivo pode ser escolhido; R06 fica reservado até existir suporte |
| I07 | R07; 16:9 | `contain`, padding interno 16; sem recorte normal; hover amplia 1,018× e pode ocultar bordas mínimas | Produto dentro dos 85% centrais, sem lettering incorporado | WebP/PNG; mesmo arquivo para todos |
| I08 | R08; 4:3 | Igual a I07, com outra proporção de quadro | Produto dentro dos 85% centrais | WebP/PNG; mesmo arquivo para todos |
| I09 | R03; 4:3 | `contain`, sem recorte; `multiply`; legenda separada abaixo | Produto inteiro com 10% de respiro, sem copiar especificações para dentro da foto | WebP/PNG; mesmo arquivo para todos |
| I10 | R08; 4:3 como composição geral | Imagem em coluna separada do texto; `width:100%`, altura natural com `max-height:520` e `object-fit:cover`. Pode recortar se atingir esse limite | Elemento principal nos 75–80% centrais; não embutir título/CTA | WebP; mesmo arquivo costuma servir. Não existe slot de banner mobile |
| I11 | R09; 1:1 | `contain`, 44 × 44; origem solicitada como `thumbnail` | Objeto central, legível em 44; evitar textos | WebP/PNG; mesma imagem da categoria ou do produto, sem peça mobile |
| I12 | R04; 3:4 | `cover`, cantos retos | Assunto nos 75–80% centrais | WebP; mesma imagem para todas as telas |
| I13 | R10; 4:5 | `cover`, máscara em arco | Assunto nos 65–70% centrais; topo e cantos sem detalhes críticos | WebP; mesma imagem para todas as telas |
| I14 | R11; 2,7:1 | `cover`; primeira categoria é determinada pela posição na consulta, não por um campo próprio de imagem | Assunto nos 70–75% centrais, com folga horizontal | WebP; mesma imagem para todas as telas |
| I15 | R08; 4:3 | `cover` | Assunto nos 75–80% centrais | WebP; mesma imagem para todas as telas |
| I16 | R12; 1:1 | `contain`, padding de imagem 4; sem recorte CSS, salvo derivado já recortado pelo WooCommerce; fundo mineral e `multiply` | Produto inteiro, ocupando ≈75–85% do quadro | WebP/PNG; mesmo master alimenta todas as telas e I20 |
| I17 | R04; 3:4 | `cover`, pode recortar arquivo quadrado e derivados do WooCommerce | Peça/modelo nos 75–80% centrais; folga para mãos, cabeça e calçados | WebP; mesmo master, conferir também a derivação de miniaturas |
| I18 | R12; 1:1 | `contain`, mas máscara arredondada pode ocultar cantos; `multiply` | Frasco nos 65–75% centrais; evitar tampa junto aos cantos | WebP/PNG; mesmo master |
| I19 | R12; 1:1 | `contain`, fundo neutro; sem recorte CSS | Produto inteiro nos 75–85% centrais | WebP/PNG; mesmo master |
| I20 | R12 reutilizado; 1:1 | `contain`; reutiliza imagem principal do produto, sem campo editorial próprio | Mesma composição de I16 | Mesmo arquivo do catálogo; não produzir banner para este espaço |
| I21 | R12, ou R04 para moda; proporção consistente por produto | Imagem de proporção intrínseca, derivado `woocommerce_single`/galeria e original para zoom conforme WooCommerce; recorte do derivado depende da configuração nativa | Produto inteiro, 8–12% de respiro; manter fotografias da mesma série coerentes | WebP; PNG para alpha. Uma série serve para todos; não há campo mobile |
| I22 | Mesmos masters de I21, por ângulo | Foto ativa/lightbox preservam a composição conforme galeria nativa; miniaturas podem usar derivados recortados | Centro seguro nas miniaturas; detalhes adicionais podem ocupar a foto ampliada | WebP; não enviar miniaturas separadamente |
| I23 | Mesmo master de I21 para cada variação que precise de foto distinta | Mesmo comportamento da galeria nativa | Enquadramento, fundo e escala iguais entre cores/modelos | WebP/PNG; mesmo arquivo por variação em todos os dispositivos |

### A3. Páginas internas e espaços delegados — sem novo banner próprio

| Contexto | O que realmente existe no código próprio | Medida/resolução e gestão |
| --- | --- | --- |
| Institucionais e conteúdo comum | `page.php`/`index.php` executam `the_content()`. Imagens, galerias e capas só existem se adicionadas ao conteúdo; não há hero institucional fixo | Largura conforme bloco/conteúdo: até a área útil de ≈320–1325 CSS px. `theme.json` informa conteúdo 48rem e largo 75rem, mas isso não impõe sozinho essa largura a todo conteúdo clássico. Master até 1600–2400 de largura para fotos amplas; proporção escolhida no bloco. `cover`/recorte só se o bloco o definir. WebP; SVG não é aceito no upload próprio. Editor nativo, não `/painel/aparencia` |
| Catálogo, busca, categoria, relacionados e vendas adicionais | Imagens de produto reutilizam I16–I19; filtros não têm imagem. Não há banner de topo de catálogo ou de categoria implementado pelo tema | Não produzir um arquivo por rota. Possíveis subcategorias com imagem são uma configuração nativa WooCommerce, fora do layout próprio; reaproveitam `thumbnail_id`. Exibição efetiva depende da configuração não alterada/não auditada no banco |
| Carrinho e checkout | Conteúdo nativo via `page.php`; possíveis miniaturas de itens reutilizam imagem de produto/variação. O tema não cria banners de checkout nem controla esses tamanhos | Dimensões dependem de blocos versus shortcode e do CSS do WooCommerce; não é possível fixá-las pela leitura do tema. Reutilizar masters de I21/I23. Nada para cadastrar em Aparência |
| Conta, histórico/detalhes de pedidos e avaliações | Fluxos delegados ao WooCommerce. Não existe imagem comercial própria de conta/pedidos ou avatar configurável pelo tema | Eventuais miniaturas/avatares/ícones nativos dependem dos componentes ativos; não foram classificados como novos slots de campanha. Não produzir banners para essas rotas |
| Rodapés, seção `about`, estados vazios e 404 | Rodapés usam nome textual, sem segundo logo em imagem. `about` é texto. Estado vazio usa formas CSS; não há arquivo de foto | Nenhuma peça adicional necessária. Suporte a `post-thumbnails` foi declarado, mas `page.php`/`index.php` não renderizam automaticamente imagem destacada |
| Ícones de busca, conta, carrinho e serviços TECH | SVG inline gerado por `inc/tech.php` e componentes; não são campos da biblioteca | Preservar vetores existentes; não produzir PNGs ou supor upload de ícone. Eventuais marcas/ícones de pagamento são responsabilidade dos componentes nativos, sem slot próprio no tema |

### A4. Onde trocar cada imagem

| Fontes de mídia (12) | Painel próprio | Personalizador / acesso técnico |
| --- | --- | --- |
| `custom_logo`, `site_icon` | `/painel/aparencia` → Identidade | Aparência → Personalizar → Identidade do site |
| `hero_image`, `banner_image` | `/painel/aparencia` → Templates, conteúdo e contato | Personalizar → Marquesano • Loja |
| `tech_hero_image`, `tech_promo_1_image`, `tech_promo_2_image`, `tech_spotlight_image` | `/painel/aparencia` → Direção de arte TECH | Personalizar → Marquesano • TECH Signature |
| Categoria `thumbnail_id` | **Não disponível** no formulário de `/painel/categorias`, que só edita nome, hierarquia e descrição | WooCommerce/Produtos → Categorias, com conta técnica autorizada. O módulo Marquesano no wp-admin oferece link nativo, mas lojistas têm wp-admin bloqueado |
| Produto `image_id`, coleção `gallery_ids` | `/painel/produtos` → editar/criar produto → Imagens | Editor nativo de produto, com permissão técnica |
| Variação `variations[n][image_id]` | Mesmo editor próprio → Variações → Imagem da variação | Editor nativo de produto variável |

O seletor próprio oferece envio, prévia e remoção da seleção; as imagens são anexos reais. Upload: JPEG, PNG e WebP, até o menor limite entre 5 MB e o servidor; até 8000 px por lado e 20 milhões de pixels. **AVIF e SVG são recusados pelo painel e pela validação de IDs**, inclusive ao salvar Aparência. Não os entregar como formato final para esse fluxo. SVG só é adequado para os vetores locais já existentes em código ou para futura implementação autorizada. Favicon novo deve ser quadrado e ter pelo menos 512 × 512.

## B. Tamanhos consolidados para produção

Recomendação de masters, sem confundir com o quadro CSS. A maioria atende cerca de 2× o tamanho visível máximo relevante; zoom pode justificar o master de produto maior. Evitar upscaling das ilustrações atuais. Pesos são metas, não limites rígidos: preservar detalhe antes de atingir um número arbitrário.

| Código | Aplicação | Arquivo recomendado | Proporção | Formato e peso indicativo |
| --- | --- | --- | --- | --- |
| R01 | Logo horizontal | **720 × 240**; ajustar à proporção real da marca | 3:1 indicativo | PNG transparente; ideal até 100 KB |
| R02 | Favicon/ícone | **512 × 512** | 1:1 | PNG; ideal até 80 KB |
| R03 | TECH hero e protagonista | **1600 × 1200** | 4:3 | WebP; ≈120–300 KB; PNG apenas quando alpha/qualidade exigir |
| R04 | FASHION hero, categorias, produtos; BEAUTY hero | **1200 × 1600** | 3:4 | WebP; ≈150–350 KB. Foto de produto com textura/zoom pode chegar a ≈450 KB |
| R05 | HOME hero desktop | **2400 × 1200** | 2:1 | WebP; ≈200–450 KB |
| R06 | HOME hero mobile, reservado | **1000 × 1250** | 4:5 | WebP; ≈100–250 KB. **Ainda não há campo/seleção responsiva para este arquivo** |
| R07 | TECH editorial maior | **1600 × 900** | 16:9 | WebP; ≈120–280 KB |
| R08 | TECH editorial menor, banner secundário, HOME categorias comuns | **1440 × 1080** | 4:3 | WebP; ≈120–300 KB |
| R09 | Categoria usada só no trilho TECH | **256 × 256** | 1:1 | WebP/PNG; ≈10–35 KB. Se a categoria for usada em outros presets, produzir o master maior daquele enquadramento |
| R10 | BEAUTY categorias | **800 × 1000** | 4:5 | WebP; ≈70–180 KB |
| R11 | HOME primeira categoria panorâmica | **1800 × 667** | ≈2,7:1 | WebP; ≈120–280 KB |
| R12 | Produtos TECH/BEAUTY/HOME e galerias | **1600 × 1600**; 1200 × 1200 pode bastar sem zoom de detalhe | 1:1 | WebP; ≈100–300 KB. PNG quando transparência for necessária; manter master de qualidade para zoom |

Não criar arquivos separados de 44, 110, 140, 150 e 300 px para upload: são áreas de exibição ou derivados do WordPress. O mesmo master deve alimentar miniaturas, vitrines e página de produto. Para clientes com muitos SKUs, usar a menor resolução que cumpra o uso de zoom real.

## C. Peças a produzir para preencher o TECH

| Peça | Quantidade e destino | Produção |
| --- | --- | --- |
| Identidade da loja | 1 logo + 1 favicon, se ainda não aprovados | R01/R02; não inventar marca. O header atual usa nome em texto |
| Produto principal do hero | 1 foto/recorte editorial | R03; objeto real do catálogo, fundo neutro compatível com `multiply`; texto fica no HTML |
| Destaques assimétricos | 2 peças, uma por campo `tech_promo_1_image` e `tech_promo_2_image` | R07/R08; ângulos/composições distintos. Hoje reutilizam imagens de produto |
| Protagonista | 1 foto de maior detalhe | R03; pode reutilizar ensaio do hero se fizer sentido, mas evitar repetir exatamente a mesma composição em toda a home |
| Categorias | Uma imagem por categoria visível; trilho até 6 atalhos no total | R09. Atalhos de produto já reutilizam suas fotos; não precisam de peças próprias |
| Produtos e galerias | Uma principal por SKU; ângulos adicionais conforme informação útil; foto por variação apenas quando houver diferença visual | R12; não inventar estoque, características ou modelos para preencher a arte |
| Banner secundário | 1 peça **opcional** | R08. Só produzir se a seção estiver na composição e houver `banner_title`; não é necessário preencher todo espaço opcional |

`discovery` não requer imagem editorial: reutiliza produtos reais em promoção. Ícones de serviços também já existem como SVG inline.

## D. Peças para FASHION, BEAUTY e HOME

| Preset | Conjunto necessário | Observações |
| --- | --- | --- |
| FASHION | Hero editorial R04; imagem R04 por categoria; foto R04 por produto e ângulos de galeria; banner R08 opcional | Composição mobile alternativa é aconselhável para ensaios com rosto/corpo; campo mobile ainda inexistente. Evitar fotografias quadradas já recortadas para o card vertical |
| BEAUTY | Hero R04; categorias R10; produtos/galerias R12; banner R08 opcional | Preservar tampa, rótulo e silhueta nas máscaras em arco. Cores reais da embalagem precisam ser conferidas com o efeito `multiply` |
| HOME | Ambiente R05; adaptação R06 reservada; primeira categoria R11 e demais R08; produtos/galerias R12; banner R08 opcional | Priorizar fotografia do ambiente com área livre para painel HTML. A primeira categoria não tem seletor dedicado de posição/imagem de destaque |

Cada cliente também precisa de R01/R02. Esses arquivos de identidade são compartilhados entre presets; não há biblioteca automática de logos por segmento.

## E. Convenção de nomes

Usar minúsculas, sem acentos/espaços, hífens, finalidade explícita e versão quando substituir uma campanha. Nome de arquivo não cria vínculo com campo nem determina breakpoint.

```text
marca-logo-horizontal.png
marca-favicon-512.png
tech-hero-principal-v01.webp
tech-editorial-01-desktop.webp
tech-editorial-02-desktop.webp
tech-protagonista-sku-angulo-v01.webp
tech-categoria-audio.webp
fashion-hero-desktop-v01.webp
fashion-hero-mobile-v01.webp
beauty-hero-principal-v01.webp
home-hero-desktop-v01.webp
home-hero-mobile-v01.webp
home-categoria-destaque-salas.webp
fashion-banner-secundario-v01.webp
sku-123-principal.webp
sku-123-detalhe-01.webp
sku-123-variacao-preto.webp
```

Sufixos `desktop/mobile` documentam entregáveis de direção de arte, **não suporte atualmente implementado**. Para TECH editorial, “desktop” também pode ser reaproveitado no mobile. Não cadastrar arquivos mobile aguardando troca automática: hoje não há `<picture>` com fontes por breakpoint nem campos separados. `srcset` muda a resolução da mesma composição, não faz direção de arte responsiva.

## F. Estado atual, ausências e pendências

### Evidência observada nesta auditoria

- Homepage pública: HTTP 200, preset TECH. Seu HTML contém **12 elementos de imagem com 9 URLs `src` distintas**; as 9 responderam HTTP 200 por HEAD. Nenhum caminho quebrado foi encontrado nessa amostra. Não foram enumerados todos os candidatos de `srcset`, anexos privados ou mídias do banco.
- Hero atual: `tech-01.png`; editoriais: `tech-01.png` e `tech-03.png`; protagonista: `tech-02.png`. Vitrine usa derivados `300x300`; trilho usa `150x150`. São imagens demonstrativas, não fotografias comerciais finais.
- Trilho TECH observado: uma categoria e três atalhos de produto, todos com imagem no HTML. A repetição de `tech-01` decorre de reutilização, não de arquivo quebrado.
- **Não há logo em imagem no header observado**: a loja usa o fallback textual. Não é erro de carregamento confirmado. O banner secundário não foi renderizado: o componente é opcional e exige título; a causa exata da ausência não foi inferida sem consultar as opções do banco.
- Os três links públicos de favicon (32, 192 e 180) responderam HTTP 200. O nome do arquivo contém `apple-touch-180`, mas isso **não comprova a resolução do master**; confirmar origem com pelo menos 512 antes de substituir. A existência de um derivado 192 não comprova qualidade do original.
- Em `plugins/marquesano-admin/demo/artwork/` existem **12 PNGs de 1000 × 1000**, três por segmento. O gerador declara ilustrações geométricas originais feitas localmente, sem downloads. São material de demonstração. Não há ensaio fotográfico/editorial final nesses arquivos.
- Em `assets/art/` existem `tech.svg`, `fashion.svg`, `beauty.svg` e `home.svg`, com quadro vetorial 1200 × 1000. FASHION/BEAUTY/HOME usam sua arte como fallback quando `hero_image` não gera HTML de imagem. TECH Signature não usa `tech.svg` como fallback. Os quatro caminhos locais existem.
- `references/tech-reference.png` existe e é referência visual; **não é renderizado como banner**. Não deve ser exportado inteiro como solução para os slots.
- Páginas públicas de produto consultadas retornam HTTP 200, mas o conteúdo comercial é substituído pelo modo **Em breve**. A ausência de galeria nesse HTML não comprova falta de fotos no cadastro. O modo foi preservado; galeria, variações e demais presets não foram ativados ou alterados para esta auditoria.

### Limitações e problemas de configuração encontrados

1. **Sem direção de arte mobile:** não existem campos `*_mobile`, fontes `<picture>` por dispositivo nem ponto focal editável. Prioridade maior para o ambiente HOME e campanhas FASHION. Produzir composições móveis é recomendável, mas aplicá-las exige implementação futura autorizada.
2. **Campos gerais compartilhados:** há um único `hero_image` para FASHION/BEAUTY/HOME e fallback TECH, além de um único `banner_image` para os quatro. Trocar preset não seleciona automaticamente quatro bibliotecas de imagens. As quatro imagens exclusivas TECH são independentes e prevalecem conforme a cadeia de fallback descrita em A1.
3. **Categorias sem imagem no painel próprio:** existe slot no frontend, mas o formulário do lojista não edita `thumbnail_id`. Atualmente depende de administrador técnico no editor nativo; não oferecer link bloqueado de wp-admin como solução ao lojista.
4. **Fallbacks diferentes:** sem imagem válida, os heroes genéricos mostram SVG ilustrativo; categorias genéricas mostram arte CSS numerada; TECH omite mídia inválida. Produtos nativos podem mostrar placeholder do WooCommerce. Isso é tratamento de ausência, não uma fotografia final. Não foi constatado placeholder quebrado no TECH público observado.
5. **Verificação de arquivo desigual:** `marquesano_store_tech_image()` exige arquivo local existente. Um anexo apenas remoto/offload pode ser omitido nesse helper. Heroes genéricos e banner não têm a mesma checagem de arquivo; ID que ainda gera HTML pode apontar para mídia ausente sem acionar o SVG. Não houve mudança nesses comportamentos.
6. **Derivados podem recortar antes do CSS:** categorias usam `large`/`thumbnail`; cards e galeria seguem tamanhos WooCommerce. A configuração real de crop e filtros externos não foi lida no banco. Um master vertical não garante que todos os derivados continuem verticais. Não mudar globalmente o crop WooCommerce para atender FASHION sem avaliar TECH/BEAUTY/HOME.
7. **Resolução entregue não é apenas `src`:** os cards observados têm `300x300`, mas o WordPress pode oferecer candidatos maiores via `srcset`. Não diagnosticar imagem borrada sem verificar a escolha efetiva no navegador. Há `sizes` aproximados fixados em heroes/TECH (`65vw`, `55vw`, `50vw`, `58vw`) que nem sempre coincidem com o quadro final ou com o limite de 420 no mobile; pode haver transferência excessiva. Ajustes seriam trabalho posterior.
8. **Máscaras e blend:** BEAUTY arredonda imagens; TECH e BEAUTY aplicam `multiply` em locais específicos. Fotografias com fundo colorido e logos que exijam fidelidade devem ser conferidos em navegador. Não há controle desses efeitos por imagem no painel.
9. **Orientação antiga do campo TECH:** o rótulo de `tech_hero_image` ainda recomenda “1600 × 700”. O componente Signature atual usa palco com `contain` e quadro mobile 4:3; R03 é orientação de composição mais adequada. O rótulo não foi alterado nesta auditoria.
10. **Escopo de conferência:** não foi alterado nenhum preset para inspecionar dados salvos; não foram consultadas opções/galerias no banco nem acessado wp-admin autenticado. Assim, falta de arquivos editoriais nos diretórios próprios não prova ausência de fotos adequadas na Biblioteca de mídia. Pendentes: inventário autenticado da biblioteca, candidatos `srcset`, configuração nativa de miniaturas e medição visual dos quatro presets em desktop/tablet/mobile.

### Rastreabilidade do levantamento

Geometria: `style.css`, `assets/css/tech-premium.css`, `assets/css/tech-signature.css`. Renderização: `header.php`, `footer.php`, `front-page.php`, `page.php`, `index.php`, `template-parts/home/`, `template-parts/heroes/`, `template-parts/tech/`. Campos/suportes: `functions.php`, `theme.json`, `inc/settings.php`, `inc/customizer.php`, `inc/tech.php`, `inc/woocommerce.php`, `inc/demo-catalog.php`. Painel/mídia: `plugins/marquesano-admin/includes/panel/{views-settings.php,settings.php,views-catalog.php,products.php,support.php}` e links nativos em `includes/management.php`. Acervo demonstrativo: `demo/class-demo-artwork.php`, `demo/README.md` e cabeçalhos/dimensões dos arquivos de `demo/artwork/`.

**Único arquivo criado por esta tarefa: este guia.** Sem edição de código, mídia, produtos, configuração ou banco; sem instalação, build, commit, push ou deploy.
