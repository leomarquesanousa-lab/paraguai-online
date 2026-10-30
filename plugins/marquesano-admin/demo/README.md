# Catálogo demonstrativo — execução manual

**Importação local executada por solicitação do usuário: 12 produtos e 12 imagens originais gravados, três produtos por segmento.** A rotina continua manual e não roda durante visitas ou ativação. A reexecução dos quatro segmentos foi testada sem criar duplicações.

Há 12 produtos simples no manifesto: três de cada segmento, com categoria própria, preço, uma promoção por segmento, descrição, atributos e estoque demonstrativo. Não há pedidos fictícios. O comando usa `WC_Product_Simple`, `WC_Product_Attribute` e APIs de termos/opções do WordPress.

No **Site Shell** do LocalWP, dentro da instalação WordPress e com WP-CLI disponível:

```sh
# Apenas mostrar o conteúdo, sem gravação:
wp marquesano demo plan --segment=tech
wp marquesano demo create --segment=fashion

# Gravar deliberadamente um segmento. Substituir USUARIO_ADMIN pelo login real:
wp marquesano demo create --segment=tech --user=USUARIO_ADMIN --yes

# Gerar ilustrações próprias e associá-las aos produtos demonstrativos:
wp marquesano demo create --segment=tech --user=USUARIO_ADMIN --with-images --yes

# Ver o plano de remoção e depois mover à lixeira:
wp marquesano demo remove --segment=tech --user=USUARIO_ADMIN
wp marquesano demo remove --segment=tech --user=USUARIO_ADMIN --yes
```

Segmentos: `tech`, `fashion`, `beauty`, `home`. Importar apenas o segmento que será avaliado, evitando misturar catálogos. O preset não é alterado pelo comando. Trocar pela prévia no Customizer. Registros existentes, incluindo demonstrativos já editados ou na lixeira, são preservados; colisões de SKU/metadados interrompem a operação.

Identificação: prefixo `[DEMONSTRAÇÃO]`, SKUs `MS-DEMO-V1-*`, metadados `_marquesano_demo_batch`, `_marquesano_demo_key`, `_marquesano_demo_segment` e categorias `marquesano-demo-*` marcadas com metadados. O registro `marquesano_demo_manifest_v1` guarda os IDs para remoção mesmo se o SKU mudar. A remoção verifica os marcadores, não apaga definitivamente e mantém as categorias/registros para permitir restaurar produtos pela lixeira nativa. Após exclusão manual definitiva, categorias vazias podem ser removidas pelo administrador.

## Imagens autorizadas

O parâmetro `--with-images` gera 12 ilustrações geométricas próprias com GD, conforme o segmento, e importa as imagens pelo mecanismo nativo de mídia do WordPress. Fontes reutilizáveis ficam em `demo/artwork/`; cópias e miniaturas ficam nos uploads nativos. Metadados de imagem impedem nova importação ao repetir o comando. Nenhum arquivo externo é baixado. Produtos demonstrativos já existentes recebem imagem apenas se ainda não tiverem uma; produtos reais nunca são atualizados. Para associar outras imagens que você tem direito de usar, adicionar IDs existentes por chave:

```sh
wp marquesano demo create --segment=tech --user=USUARIO_ADMIN --yes --image-map='{"tech-01":123,"tech-02":124}'
```

Os números são exemplos: substituir por IDs reais autorizados. Mídia nunca é excluída pela rotina. O tema inclui quatro ilustrações vetoriais originais para heroes, independentes desses cadastros. São arte editorial, não fotografias ou representação exata dos produtos.

## Limites

A execução exige domínio local e usuário com capacidades adequadas. Não muda moeda, impostos, pagamentos, frete, páginas, idioma ou modo Em breve. Valores seguem a moeda já configurada. Entrar no WordPress e visitar a loja autenticado para avaliar, mantendo o modo Em breve.

## Verificação executada

`verify-store.php`, executável manualmente por `wp eval-file wp-content/plugins/marquesano-admin/demo/verify-store.php --user=USUARIO_ADMIN`, conferiu os 12 produtos, imagens, atributos, preços e estoques; vitrines e buscas dos quatro presets; 12 páginas individuais e catálogo autenticados por HTTP. O tema segmenta somente demonstrativos identificados; produtos comuns continuam elegíveis.

Em uma sessão anônima isolada da Store API, passaram adição de duas unidades, subtotal promocional, atualização de quantidade e remoção. Checkout GET respondeu 200 com campos de endereço e sem pedido. Contagens de pedidos/rascunhos, estoques, preset salvo e modo Em breve foram preservados; as sessões temporárias de teste foram encerradas. Pagamentos, frete, conclusão de compra, revisão visual e remoção à lixeira não foram testados.
