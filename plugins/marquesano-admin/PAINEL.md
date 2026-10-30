# Marquesano Store — painel operacional

## Acesso e instalação local

Abrir `/painel` ou `/painel/login`. A rota é atendida pelo plugin, sem criar página no banco, alterar links permanentes ou substituir o tema. Login, senha, cookies e recuperação usam WordPress; formulários próprios possuem nonces. Conteúdo é privado, sem cache e sem indexação.

O plugin registra o papel **Lojista Marquesano** (`marquesano_lojista`) na inicialização, apenas se ainda não existir. Não cria usuários nem atribui esse papel a qualquer conta. Um administrador técnico deverá autorizar explicitamente a conta de cada lojista antes de atribuí-lo. Administradores existentes acessam todos os módulos sem mudar de papel.

O papel tem `read` e capacidades `marquesano_*` específicas de painel, produtos, categorias, pedidos, clientes, aparência, configurações e envio de imagens. Não recebe `manage_options`, `manage_woocommerce`, `edit_users`, `edit_theme_options`, instalação de plugins ou HTML sem filtragem. O acesso ao wp-admin é redirecionado para o painel apenas para lojistas, com exceções para AJAX, cron e admin-post. REST, webhooks, endpoints de conta e demais rotas WooCommerce não são interceptados.

## Rotas e módulos

| Rota | Uso |
| --- | --- |
| `/painel` | Pedidos pagos nos últimos 30 dias, valores líquidos de reembolso por moeda e contagens por status. Cache privado por permissão, até 60 segundos, invalidado ao salvar pedidos. |
| `/painel/login` | Login próprio, proteção por nonce e limite de tentativas por identidade/IP. |
| `/painel/recuperar`, `/painel/redefinir` | Recuperação por e-mail e redefinição nativa, com limite de solicitações. |
| `/painel/produtos` | Busca, filtros de tipo/categoria/estoque/status e paginação. |
| `/painel/produto/novo`, `/painel/produto/ID` | Produto simples ou variável, descrição, preços, SKU, categoria, imagem principal, galeria, estoque e medidas. |
| `/painel/categorias` | Cadastro, edição, descrição e hierarquia. |
| `/painel/estoque` | Consulta e atualização de estoques individuais, inclusive variações. |
| `/painel/pedidos`, `/painel/pedido/ID` | Consulta via API WooCommerce, itens, endereços, totais, notas internas, histórico e transições permitidas. Compatível com HPOS sem SQL de pedidos. |
| `/painel/clientes`, `/painel/cliente/ID` | Consulta de clientes registrados e informações operacionais. Clientes convidados são consultados pelos pedidos; não são criadas contas artificiais. |
| `/painel/aparencia` | Presets, conteúdo, banners, cores, fontes, contato, redes, logo e favicon, reutilizando `marquesano_store_settings`, `custom_logo`, `site_icon` e `blogname`. |
| `/painel/configuracoes` | Endereço, país/estado, moeda, casas decimais e limites de estoque nas opções nativas WooCommerce. |

Todas as gravações usam POST, nonce, verificação de capacidade no servidor e APIs WordPress/WooCommerce. Não há acesso público aos dados operacionais. Produtos existentes não são alterados automaticamente.

## Produtos variáveis

Atributos próprios: uma linha por atributo, como `*Cor: Preto | Branco`. O asterisco indica atributo de variação. Na combinação, usar `Cor=Preto`; para mais atributos, `Cor=Preto;Tamanho=M`. Nomes globais existentes usam `pa_nome`, e o valor da combinação usa o slug nativo do termo.

O formulário valida combinações completas e únicas, SKUs únicos, parentesco das variações, preços e estoque. Não gera uma matriz de combinações nem apaga variações omitidas. Cada variação pode ser desativada sem excluir. Atualizações verificam versão e estoque para detectar edições desatualizadas. O WooCommerce sincroniza preços/estoques do produto variável.

## Imagens

Upload próprio, sem abrir wp-admin, e seleção de até 60 imagens recentes da biblioteca. JPEG/PNG/WebP, até 5 MB (ou limite menor do servidor), máximo 8000 px por lado e 20 milhões de pixels. Tipo real e extensão são verificados; SVG e arquivos executáveis são recusados. A galeria aceita até 20 imagens. Imagens enviadas permanecem na mídia nativa mesmo se o formulário não for salvo.

## Limites explícitos desta versão

- Gateways, credenciais, frete, tributos, e-mails transacionais, menus/páginas institucionais e políticas exigem configuração pelo administrador técnico. Não há links de wp-admin para lojistas nem habilitação automática de pagamentos.
- Reembolsos/estornos e edição financeira de pedidos exigem o responsável técnico. Transições que marcam pagamento exigem confirmação e chamam `payment_complete()`. Atualizações de status podem enviar e-mails e movimentar estoque pelas regras nativas.
- Métrica de pedidos pagos é baseada nos registros/status do WooCommerce, não em conciliação bancária. Moedas diferentes não são somadas como se fossem iguais.
- Produtos agrupados/externos, conversão entre tipos, atributos globais novos, combinações com curingas e mais de 50 variações por edição exigem gestão técnica. Produtos simples e variáveis dentro dos limites têm formulários próprios.
- Não há editor de dados pessoais/usuários. Clientes são consulta somente leitura; dados administrativos não são expostos.
- Restauração de lixeira e exclusão definitiva cabem ao administrador técnico. A interface própria só oferece envio reversível à lixeira, com confirmação.

## Validação e liberação

`tests/verify-panel.php` é um teste manual local: políticas de autorização, CSRF, produtos/variações de teste em rascunho, categoria isolada, estoque, galeria, multipart de imagem e renderização autenticada. Cria somente registros marcados de teste, arquiva-os na lixeira, encerra a sessão temporária e verifica preservação dos produtos/usuários/pedidos/configurações existentes.

Executados nesta entrega: 11 telas autenticadas HTTP 200, operações isoladas acima, edição posterior de preço/estoque por variação (`tests/verify-panel-variations.php`) e interface de lojista em memória sem links técnicos (`tests/verify-panel-access.php`). PHP: 24 arquivos aprovados; JavaScript passou em `node --check`; CSS com blocos balanceados. Login interativo, e-mails, alterações de pedidos reais e salvamento de configurações não foram exercitados. Nenhuma conta de cliente foi criada. Fixtures e IDs estão no arquivo de progresso.

Antes de liberar clientes: testar login/logout e recuperação por e-mail numa conta de homologação autorizada; bloqueio real de wp-admin e acesso REST/AJAX com esse papel; upload e navegação pelo navegador; responsividade e teclado; publicação/variações; transições de pedidos com e-mails/estoque em homologação; salvamento de aparência e configurações. Revisar backups, HTTPS e políticas de privacidade para a instalação final. Nenhuma validação visual é presumida sem navegador.
