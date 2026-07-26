# Diário de Desenvolvimento

## 24/07/2026

### Tarefa

Preparar o ambiente de desenvolvimento utilizando Docker.

**Issue:** #1 – Configurar o ambiente de desenvolvimento com Docker
**Branch:** `chore/setup-docker`

### Contexto
Antes de implementar qualquer alteração funcional, optei por configurar um ambiente de desenvolvimento reproduzível usando o Docker. Essa abordagem garante que a aplicação tenha um comportamento consistente independentemente do sistema operacional do host, além de atender a um dos requisitos do desafio.

### Análise

Antes de criar a configuração do Docker, analisei a estrutura do projeto e seus arquivos de configuração para compreender os requisitos da aplicação.

A análise incluiu:

- Estrutura de diretórios do projeto.
- Dependências do Composer.
- Configuração do Phinx.
- Configuração do PHPUnit.
- Localização do banco de dados SQLite.
- Ponto de entrada da aplicação.

A partir dessa análise, foi possível identificar que:

### Implementação

Foi criado o ambiente de desenvolvimento com Docker, incluindo os seguintes arquivos:

- `Dockerfile`
- `docker-compose.yml`
- `.dockerignore`

O contêiner foi configurado para:

- Utilizar a imagem oficial do PHP 8.2 CLI.
- Instalar o Composer.
- Executar a aplicação utilizando o servidor web embutido do PHP.
- Expor a porta `8000`.

### Validação

As seguintes validações foram realizadas com sucesso:

- A imagem Docker foi construída sem erros.
- O contêiner foi iniciado corretamente.
- A aplicação ficou acessível em `http://localhost:8000`.
- O Composer está disponível dentro do contêiner.
- As dependências do projeto foram instaladas com sucesso.

## 25/07/2026

### Tarefa

Corrigir falha nos testes unitários da entidade `Product` e documentar a causa e a solução.

**Issue:** #2 – Corrigir falha nos testes unitários  
**Branch:** `fix/product-unit-test-failure`

### Contexto

Após a configuração do ambiente de desenvolvimento, foi executada a suíte de testes unitários utilizando o comando:

```bash
composer test
```

Durante a execução da suíte, foi observada uma falha no teste `Product::testHydrateByFetch()`, indicando uma inconsistência no valor retornado pelo atributo `price`.

O teste esperava o valor:

```text
99.99
```

Porém, a aplicação retornava:

```text
99.0
```

### Análise

A investigação foi iniciada reproduzindo a falha por meio da execução da suíte de testes unitários. 

Inicialmente, foi analisado o teste responsável pela falha para verificar o comportamento esperado e confirmar que o valor informado durante a criação do objeto estava correto.

Em seguida, foi realizado o rastreamento do fluxo de criação da entidade `Product`, analisando:

- O método responsável pela hidratação dos dados (`hydrateByFetch`).
- O construtor da entidade `Product`.
- Os pontos da aplicação que utilizam a entidade e o método de hidratação.
- As conversões aplicadas ao atributo `price`.

Também foi verificada a estrutura do banco de dados e a documentação da API para confirmar o tipo esperado do campo e descartar inconsistências na origem dos dados.

Durante essa análise, foram identificadas oportunidades de melhoria relacionadas à validação e à padronização dos tipos utilizados pela entidade. Entretanto, essas alterações não foram realizadas por não fazerem parte do escopo da correção proposta.

### Implementação

A alteração foi realizada no método `hydrateByFetch()` da entidade `Product`.

O atributo `price` estava sendo convertido para `int` durante a hidratação da entidade, fazendo com que as casas decimais fossem descartadas. A conversão foi ajustada para `float`, preservando corretamente os valores decimais recebidos e mantendo a consistência entre os dados de entrada e os armazenados no objeto.

### Validação

Após a correção, a suíte de testes unitários foi executada novamente:

```bash
composer test
```

Resultado:

- Todos os testes unitários foram executados com sucesso.
- O atributo `price` passou a preservar corretamente os valores decimais.
- O comportamento esperado da entidade `Product` foi mantido.

### Considerações

A correção foi aplicada de forma pontual, mantendo o escopo da tarefa e preservando o comportamento esperado dos demais componentes da aplicação.

# 25/07/2026

## Tarefa

Corrigir inconsistência na categoria retornada pela listagem de produtos.

**Issue:** #4 – Corrigir associação incorreta de categorias na listagem de produtos  
**Branch:** `fix/product-category-mapping`

## Contexto

Durante a validação da API foi identificado que alguns produtos eram retornados com categorias incorretas na listagem de produtos.

Como exemplo, o produto `blue trouser` era associado à categoria `phone`, embora estivesse vinculado a outra categoria no banco de dados.

O comportamento ocorria apenas na listagem (`GET /products`), enquanto a consulta individual de produtos não apresentava a mesma inconsistência.

## Análise

A investigação foi iniciada reproduzindo o problema por meio da execução da listagem de produtos e comparando as categorias retornadas pela API com os registros armazenados no banco de dados.

Inicialmente foi analisado o fluxo responsável pela listagem, passando pelos seguintes componentes:

- `ProductController::getAll()`;
- `ProductService::getAll()`;
- Consulta SQL utilizada para recuperar produtos e categorias.

Durante a análise da consulta SQL, foi revisado o relacionamento entre as tabelas `product`, `product_category` e `category`.

Foi observado que a tabela intermediária `product_category` possui os campos `product_id` e `cat_id`, sendo este último responsável por referenciar a categoria do produto.

Entretanto, o `JOIN` utilizado na consulta relacionava a tabela `category` utilizando o campo `product_category.id`, em vez de `product_category.cat_id`.

Como consequência, a associação entre produtos e categorias era realizada utilizando o identificador da tabela intermediária, o que fazia com que alguns produtos fossem associados a categorias incorretas quando os valores dos identificadores não coincidiam.

Durante a investigação também foi observado que o fluxo utilizado pela consulta individual (`GET /products/{id}`) utiliza corretamente o campo `cat_id`, indicando que a inconsistência estava restrita à consulta de listagem.

## Implementação

A consulta SQL do método `ProductService::getAll()` foi ajustada para utilizar o relacionamento correto entre as tabelas.

O trecho:

```sql
INNER JOIN category c
    ON c.id = pc.id
```

foi alterado para:

```sql
INNER JOIN category c
    ON c.id = pc.cat_id
```

A alteração faz com que a categoria seja recuperada a partir da chave estrangeira definida na tabela de relacionamento, respeitando o modelo de dados da aplicação.

## Validação

Após a alteração, a listagem de produtos foi executada novamente e os resultados foram comparados com os registros existentes no banco de dados.

As seguintes validações foram realizadas:

- Os produtos passaram a ser associados às categorias corretas.
- O produto utilizado para reproduzir o problema (`blue trouser`) passou a retornar sua categoria corretamente.
- A consulta individual de produtos permaneceu apresentando o mesmo comportamento esperado.

## Considerações

A correção foi aplicada de forma pontual, restringindo-se ao relacionamento utilizado pela consulta de listagem.

Durante a investigação foi identificada uma possível inconsistência adicional no filtro por empresa utilizado pelo método `ProductService::getAll()`. Como essa observação não está relacionada à causa do problema corrigido e não foi validada como defeito, nenhuma alteração foi realizada neste momento, mantendo o escopo da tarefa.