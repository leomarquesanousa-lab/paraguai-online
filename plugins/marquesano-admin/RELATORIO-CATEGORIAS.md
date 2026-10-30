# Categorias pesquisáveis no painel

{"APROVADO":15,"PENDENTE":1}

- APROVADO NEW: Cadastro carrega select múltiplo e componente sem dependências externas
- APROVADO HIERARCHY: Pai, filha e neta identificados pelo caminho completo
- APROVADO SINGLE: Uma categoria persiste via WC_Product
- APROVADO MULTIPLE: Múltiplas categorias e relações pai/filhas persistem
- APROVADO EDIT: Edição carrega todos os IDs associados como selecionados
- APROVADO VALIDATION: Erro não apaga seleções enviadas nem modifica associações salvas
- APROVADO NEW-VALIDATION: Cadastro preserva categoria após validação recusada
- APROVADO REMOVE: Remoção da seleção persiste sem recriar categorias
- APROVADO INVALID-ID: ID inexistente é rejeitado pela validação original
- APROVADO LAYOUT: Formulário mantém imagens, atributos e botão de salvar
- APROVADO CLEAN: Contexto e configurações restaurados
- APROVADO PRODUCTS: Produtos existentes preservados
- APROVADO CATEGORIES: Categorias existentes preservadas e temporárias removidas
- APROVADO USERS: Conta temporária removida
- APROVADO FIXTURES: Produto temporário removido
- PENDENTE VISUAL: Desktop/mobile e interação assistiva no navegador — Inventário CUA sem navegador; teclado/DOM e leitor de tela ainda exigem validação interativa.

Componente sem dependências: select múltiplo nativo categories[], pesquisa local, etiquetas removíveis, hierarquia e fallback sem JavaScript. APIs e validações de persistência não alteradas. Testes de lógica JS: node --test --test-isolation=none plugins/marquesano-admin/tests/category-picker.test.cjs (220 categorias simuladas).

Repetição HTTP: wp eval-file CAMINHO_ABSOLUTO/plugins/marquesano-admin/tests/audit/run-categories.php --user=ADMIN. Recuperação: acrescentar recover.

Testes concluídos: 15 verificações HTTP aprovadas, zero falhas; 4 testes Node aprovados usando 220 categorias simuladas (pesquisa sem acentos/hierarquia, seleção múltipla, remoção, edição e movimentação circular). Os testes Node cobrem a lógica usada pelo componente, não a renderização/interação DOM em navegador.

Arquivos de execução alterados: includes/panel/views-catalog.php, includes/panel/views.php, assets/panel.css e assets/category-picker.js. Testes: tests/audit/run-categories.php, tests/category-picker.test.cjs e ajuste de tests/audit/scenarios-panel.php para o novo markup de seleção.

Validação estática: quatro arquivos PHP sem erros de sintaxe; JavaScript sintaticamente válido; strings e delimitadores CSS balanceados. Nenhuma dependência instalada. Persistência continua usando categories[] e WC_Product::set_category_ids()/save(), sem alteração nas APIs ou na validação de permissões.

Pendente: revisão visual desktop/mobile e testes reais de teclado/leitor de tela. Nenhum navegador conectado nesta sessão. Todo o trabalho desta melhoria ficou dentro de plugins/marquesano-admin.