# Painel Marquesano — progresso

Escopo: `/painel`, autenticação WordPress própria, papel lojista sem poderes de administrador e módulos operacionais sobre dados nativos WooCommerce/HPOS. Nenhuma conta de cliente será criada automaticamente.

- Implementados: acesso e recuperação próprios, papel/capacidades, bloqueio seletivo de wp-admin, navegação, produtos simples/variáveis, categorias, mídia, estoque, pedidos, clientes, aparência e configurações essenciais.
- Validados: sintaxe, rotas públicas/protegidas, política de permissões e CSRF; renderização autenticada de 11 rotas; criação/edição de produto simples, variações, categorias, galeria, estoque, upload multipart e envio à lixeira.
- Validada adicionalmente a edição de preço/estoque por variação, reutilizando somente a fixture isolada.
- Validada renderização do papel lojista em memória sem links wp-admin e sem criar conta. PHP final: 24 arquivos aprovados; JavaScript válido; CSS com blocos balanceados; login e assets HTTP 200. Nenhuma revisão visual de navegador foi declarada.
- Testes isolados arquivados: produtos 42/43, variações 44/45 e imagem 46 na lixeira; categoria 20 vazia e marcada `_marquesano_panel_test`. Nenhum usuário ou pedido foi criado; produtos existentes, aparência e modo Em breve foram conferidos e preservados.
- Pendentes antes de clientes: navegador/mobile/teclado, login/logout/recuperação por e-mail, sessão HTTP real de uma conta de homologação autorizada no papel lojista, transições reais de pedidos/HPOS e salvamento de configurações. Limites e instruções em `PAINEL.md`.
- Testes de gravação usarão somente registros de teste marcados e reversíveis; produtos existentes não serão modificados.
- Não liberar clientes antes dos testes autenticados, revisão de interface e validação manual das configurações comerciais.
