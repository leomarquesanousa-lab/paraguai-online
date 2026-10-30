# Correção do controlador crossfade — Paraguai Online

## Diagnóstico
A configuração lida pela API WordPress contém original 66 e adicionais 217,218,219. O HTML público contém quatro URLs distintas, nessa ordem, e uma inclusão do script. Não foram encontradas duplicações desses slides. A imagem original continua como fundo estático do hero, por compatibilidade com a versão sem JavaScript.

O controlador anterior alternava z-index/posição das duas camadas ao finalizar um timer, zerava a opacidade com transition:none e restaurava a transição em requestAnimationFrame sem confirmar o reset no estilo computado. Isso permite agrupamento dessas mudanças pelo navegador e animação residual da camada antiga por cima da nova. O prazo de 1200 ms era validado apenas por timers simulados, sem comprovar os estados efetivamente pintados. Além disso, visibilitychange reinicializava a sequência na imagem original e faltava proteção contra execução repetida do script. Não houve reprodução visual em navegador nesta execução; o defeito de sincronização foi identificado no código.

## Correção
Base permanentemente opaca com z-index 0 e camada de entrada com z-index 1. Somente a camada de entrada anima. Antes de iniciar outro fade, opacity:0 e transition:none são efetivados por leitura do estilo computado. Ao concluir, a camada de entrada é mantida opaca enquanto a base recebe a nova imagem; só então é zerada, sem transição. Não ocorre troca de empilhamento. O fundo fixo nunca fica descoberto pela remoção das camadas entre slides.

Há proteção de instância por hero, um único timer para hold/fade, token de cancelamento para cargas assíncronas e pré-carregamento/decodificação antes do fade. Cada imagem permanece visível por 5000 ms após a conclusão do fade de 1200 ms. Ciclo modular sem acrescentar cópia da original. Pausa de aba preserva o índice atual; movimento reduzido e imagem única ficam estáticos. Falhas de mídia não promovem uma imagem que não foi exibida.

## Verificação
12 cenários aprovados em VM Node com DOM/timers simulados, incluindo 27 transições (três ciclos para 2, 3 e 4 imagens), fechamento do ciclo, timers únicos, execução duplicada, falhas, pausa, movimento reduzido e resize. Sintaxe JavaScript aprovada. Evidências: tools/hero-crossfade-js-results.json; suíte: tools/test-hero-crossfade.js.

Não houve escrita no banco, na galeria, nas mídias ou nos estilos/layout do hero. Tempos e enquadramentos preservados. Nenhum navegador conectado à ferramenta de automação: não foram homologadas pintura, duração medida ou aparência desktop/mobile. Necessário observar pelo menos três ciclos reais no navegador antes de homologar visualmente.

Arquivo de produção alterado: assets/hero-crossfade.js. Arquivos de teste/documentação: tools/test-hero-crossfade.js, tools/hero-crossfade-js-results.json, tools/hero-diagnostic.php e este relatório. Sem commit, push ou deploy.
