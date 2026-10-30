# Marquesano Admin 1.0

Painel operacional frontend implementado em `/painel`, com login em `/painel/login`, sem substituir o painel técnico existente. Módulos, permissões, limitações e testes em [PAINEL.md](PAINEL.md); continuidade em [PROGRESSO-PAINEL.md](PROGRESSO-PAINEL.md). Nenhuma conta de lojista é criada ou atribuída automaticamente.

Reformulação visual: a rotina opcional de catálogo demonstrativo está documentada em [`demo/README.md`](demo/README.md). Não roda automaticamente. O tema detalha os quatro layouts em `themes/marquesano-store/VISUAL.md`.

Painel local sobre WordPress/WooCommerce, sem tabelas próprias, instalação de dependências, cron próprio ou alterações automáticas em dados/configurações.

## Módulos

- `marquesano-admin.php`: cabeçalho, bootstrap e declaração HPOS.
- `includes/class-plugin.php` e `includes/modules.php`: inicialização em `plugins_loaded`.
- `includes/admin.php`: menus, assets e verificações de capacidades.
- `includes/dashboard.php`: contagens por status, cinco pedidos recentes e diagnóstico de páginas.
- `includes/management.php`: acessos autorizados à gestão e personalização nativas.
- `assets/admin.css`: estilos isolados no painel Marquesano.

## Permissões e armazenamento

O painel exige `manage_woocommerce`. Dados de pedidos só são consultados com `edit_shop_orders`, por `wc_get_orders`/`wc_orders_count`; links de edição usam o objeto de pedido e detectam HPOS. A declaração de compatibilidade não habilita HPOS nem migra dados.

Links de produtos, categorias, pedidos, relatórios, usuários, páginas e tema exigem as respectivas capacidades nativas. A edição continua sujeita à autorização, nonces e validação das telas oficiais. Não há formulários próprios de mutação. A personalização usa os nonces e o salvamento do Personalizador, com sanitização dos campos no tema e escapamento na saída.

## Limitações explícitas

- Os editores de produtos, variações, estoque, pedidos, clientes e configurações são os nativos, acessíveis pelo painel. Não foram recriados.
- Clientes usam o relatório de clientes do WooCommerce; sua disponibilidade depende dos recursos e permissões da instalação. Administradores também possuem acesso a Usuários.
- O dashboard mostra contagens de pedidos por status em todos os períodos e totais dos pedidos recentes, sem apresentar esses totais como receita consolidada.
- O diagnóstico verifica publicação das páginas e presença dos blocos/shortcodes esperados. Conteúdos montados por outros construtores podem exigir revisão manual.
- Nenhuma página, produto, conta, pedido, gateway ou configuração comercial é criado ou habilitado automaticamente.
- Testes autenticados do painel, salvamento no Personalizador, HPOS ligado/desligado, produtos simples/variáveis e fluxo de compra completo exigem verificação adicional no navegador com dados reais autorizados.

## Continuidade

Base entregue: storefront modular, quatro presets, configuração visual persistente, painel com dados reais e gestão nativa. Apenas os dois diretórios próprios foram alterados. PHP 8.2.29 do LocalWP validou os 22 arquivos PHP; JSON válido e home HTTP 200 sem erro fatal visível. Sem navegador conectado na sessão para inspeção visual. Sem commit, push, deploy ou dados de demonstração.

Verificações HTTP adicionais: catálogo, conta, carrinho e página comum retornaram 200; checkout com carrinho vazio redirecionou para o carrinho; painel anônimo redirecionou para login. Filtros de preço vazios foram removidos por redirecionamento. Busca e filtro de preço retornaram 200 sem fatal visível, mas o catálogo público apresentou **Em breve**, portanto seu conteúdo e seus resultados não foram validados. O modo Em breve existente foi preservado. Para continuar, entrar no WordPress e abrir a loja autenticado, sem publicar nem desligar essa proteção automaticamente.
