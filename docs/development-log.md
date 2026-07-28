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

# 26/07/2026

## Tarefa

Corrigir categoria não retornada na consulta individual de produtos.

**Issue:** #7 – Corrigir categoria `null` na busca individual de produtos  
**Branch:** `fix/product-category-null`

## Contexto

Durante a validação da API foi identificado que alguns produtos retornavam a categoria como `null` ao serem consultados individualmente.

O problema foi reproduzido utilizando o produto `iphone 8`, que apresentava categoria corretamente na listagem de produtos (`GET /products`), porém retornava categoria ausente na consulta individual (`GET /products/{id}`).

Esse comportamento indicava uma inconsistência entre os fluxos responsáveis pela recuperação das categorias.

## Análise

A investigação foi iniciada reproduzindo o problema por meio das rotas de consulta de produtos e comparando os dados retornados pela API com os registros existentes no banco de dados.

Inicialmente foram analisados os fluxos responsáveis pelas duas consultas:

- `ProductController::getAll()`;
- `ProductService::getAll()`;
- `ProductController::getOne()`;
- `CategoryService::getProductCategory()`;
- `CategoryService::getOne()`.

Durante a análise, foi identificado que a listagem de produtos recuperava a categoria diretamente por meio de um `JOIN` entre as tabelas `product_category` e `category`, sem aplicar filtros relacionados à empresa da categoria.

Já a consulta individual realizava uma segunda busca utilizando o método `CategoryService::getOne()`, que aplicava o filtro:

```sql
c.company_id = <empresa do administrador>

Ao analisar os dados do banco e a documentação do projeto, foi identificado que algumas categorias possuem `company_id` nulo por representarem categorias padrão do sistema.

A documentação especifica que as categorias padrão (`clothing`, `phone`, `computer` e `house`) devem estar disponíveis para todas as empresas.

Como consequência, categorias globais não eram encontradas pela consulta individual, fazendo com que produtos associados a essas categorias retornassem `category` como `null`.

## Implementação

A alteração foi realizada no serviço responsável pela recuperação das categorias (`CategoryService`).

Os métodos:

- `getAll()`;
- `getOne()`;

foram ajustados para considerar tanto categorias específicas de uma empresa quanto categorias padrão compartilhadas entre todas as empresas.

O filtro:

```sql
c.company_id = <empresa>
```

foi ajustado para contemplar também categorias globais:

```sql
(
    c.company_id = <empresa>
    OR c.company_id IS NULL
)
```

Essa alteração mantém a separação entre categorias personalizadas de empresas e categorias padrão do sistema.

## Validação

Após a alteração, foram realizadas as seguintes validações:

- A listagem de categorias passou a retornar categorias padrão e categorias específicas da empresa.
- O produto utilizado para reproduzir o problema (`iphone 8`) passou a retornar corretamente sua categoria na consulta individual.
- Produtos vinculados a categorias específicas da empresa continuaram funcionando normalmente.
- A consulta de listagem de produtos manteve o comportamento esperado.

## Considerações

Durante a investigação foi identificada uma inconsistência entre a implementação da regra de categorias e a documentação do sistema.

A correção foi aplicada no serviço responsável pela regra de negócio, evitando alterações específicas no fluxo de produtos e garantindo que os diferentes endpoints utilizem o mesmo critério de recuperação de categorias.

Também foi observado que a aplicação possui consultas SQL com interpolação direta de valores, o que pode representar uma oportunidade futura de melhoria relacionada ao uso de parâmetros preparados. Essa alteração não foi realizada por não fazer parte do escopo da correção proposta.

# 26/07/2026

## Tarefa

Corrigir ausência de categorias padrão na listagem de categorias.

**Issue:** #8 – Corrigir ausência de categorias padrão na listagem de categorias  
**Branch:** `fix/category-global-filter`

## Contexto

Durante a investigação da issue #7, relacionada ao retorno de categoria `null` na consulta individual de produtos, foi identificada uma inconsistência adicional no fluxo de recuperação de categorias.

Ao validar a regra de negócio descrita na documentação do projeto, foi observado que as categorias padrão do sistema (`clothing`, `phone`, `computer` e `house`) devem estar disponíveis para todas as empresas.

Entretanto, a listagem de categorias (`GET /categories`) retornava apenas categorias vinculadas diretamente à empresa do usuário administrador, não incluindo categorias padrão com `company_id` nulo.

Esse comportamento fazia com que a resposta da API não representasse corretamente a regra de compartilhamento das categorias padrão entre empresas.

## Análise

A investigação foi iniciada reproduzindo o comportamento da rota de listagem de categorias e comparando os dados retornados pela API com os registros existentes no banco de dados.

Inicialmente foram analisados os seguintes componentes:

- `CategoryController::getAll()`;
- `CategoryService::getAll()`;
- Estrutura da tabela `category`.

Durante a análise, foi identificado que a consulta utilizada pelo método `CategoryService::getAll()` aplicava apenas o filtro:

```sql
c.company_id = <empresa do administrador>
```
Esse filtro retornava somente categorias personalizadas associadas à empresa, ignorando categorias padrão do sistema, identificadas por possuírem `company_id` nulo.

A documentação do projeto define que essas categorias padrão devem ser exibidas para todas as empresas. Portanto, a consulta existente não estava implementando completamente a regra de negócio esperada.

Também foi validado que o filtro por empresa deveria ser mantido para impedir que categorias personalizadas de outras empresas fossem retornadas indevidamente.

## Implementação

A alteração foi realizada no método `getAll()` do `CategoryService`.

O filtro existente:

```sql
c.company_id = <empresa>
```
foi ajustado para considerar também categorias padrão:

```sql
c.company_id = <empresa>
OR c.company_id IS NULL
```
Com essa alteração, a consulta passou a retornar:

- Categorias padrão compartilhadas entre empresas.
- Categorias personalizadas pertencentes à empresa do usuário administrador.

Categorias personalizadas de outras empresas continuam sendo filtradas corretamente.

## Validação

Após a alteração, foram realizadas as seguintes validações:

- A rota de listagem de categorias passou a retornar categorias padrão do sistema.
- As categorias personalizadas da empresa continuaram sendo retornadas corretamente.
- Categorias pertencentes a outras empresas não foram incluídas indevidamente na resposta.
- O comportamento da consulta individual de categorias permaneceu preservado.
- Os testes existentes foram executados para garantir que a alteração não causou regressões.

## Considerações

Essa correção foi identificada durante a investigação da issue #3 e tratada separadamente por representar um comportamento incorreto próprio da listagem de categorias.

A alteração foi aplicada no serviço responsável pela regra de negócio, mantendo a lógica de categorização centralizada e evitando correções específicas nos consumidores da informação.

Durante a análise também foi observado que algumas consultas SQL da aplicação utilizam interpolação direta de valores, representando uma oportunidade futura de melhoria relacionada ao uso de parâmetros preparados. Essa alteração não foi realizada por não fazer parte do escopo da correção proposta.

# 26/07/2026

## Tarefa

Corrigir retorno incompleto de categorias na consulta individual de produtos.

**Issue:** #10 – Corrigir retorno de apenas uma categoria na busca individual de produtos  
**Branch:** `fix/product-multiple-categories`

## Contexto

Durante a validação da API foi identificado que produtos associados a múltiplas categorias retornavam apenas uma categoria na consulta individual (`GET /products/{id}`).

O problema foi reproduzido utilizando o produto `king size bed`, que possuía mais de uma categoria associada na tabela de relacionamento `product_category`, porém a resposta da API apresentava somente a categoria `furniture`.

Esse comportamento indicava que a consulta individual não estava tratando corretamente o relacionamento entre produtos e categorias.

## Análise

A investigação foi iniciada reproduzindo o problema por meio da consulta individual de produtos e comparando os dados retornados pela API com os registros existentes no banco de dados.

Inicialmente foram analisados os componentes envolvidos no fluxo de recuperação de produtos:

- `ProductController::getOne()`;
- `ProductService::getOne()`;
- `CategoryService::getProductCategory()`;
- `CategoryService::getOne()`;
- Modelo `Product`.

Durante a análise, foi identificado que o relacionamento entre produtos e categorias é realizado por meio da tabela intermediária `product_category`, permitindo que um produto possua múltiplas categorias associadas.

Entretanto, no fluxo da consulta individual, o método `CategoryService::getProductCategory()` retornava múltiplos registros, mas a implementação utilizava o método `fetch()` do PDO, que recuperava apenas o primeiro resultado encontrado.

Como consequência, quando um produto possuía mais de uma categoria associada, somente a primeira categoria retornada pela consulta era atribuída ao produto.

## Implementação

A alteração foi realizada para adequar a entidade `Product` ao relacionamento existente entre produtos e categorias.

A propriedade responsável por armazenar a categoria foi alterada para suportar uma coleção de categorias:

```php
public array $categories = [];
```
O método `setCategory` foi substituído por `setCategories` na classe `Product`, e seu comportamento foi ajustado para realizar a atribuição de uma coleção de categorias.

O fluxo de consulta individual foi ajustado para recuperar todas as categorias associadas ao produto utilizando `fetchAll()`.

A lógica anterior, que considerava apenas uma categoria, foi substituída por uma iteração sobre os registros retornados, permitindo que todas as categorias relacionadas ao produto fossem adicionadas ao objeto.

## Validação

Após a alteração, foram realizadas as seguintes validações:

- O produto utilizado para reproduzir o problema (`king size bed`) passou a retornar todas as categorias associadas.
- Produtos com apenas uma categoria continuaram retornando corretamente.
- A suíte de testes unitários foi executada após a atualização dos testes da entidade `Product`.
- O comportamento esperado da consulta individual de produtos foi mantido.

## Considerações

A correção foi aplicada mantendo o escopo da issue, alterando apenas o fluxo responsável pela recuperação das categorias na consulta individual de produtos.

Durante a alteração do modelo `Product`, os testes unitários existentes foram atualizados para refletir o novo comportamento da entidade, substituindo a validação de uma única categoria pela validação de uma coleção de categorias.

# 26/07/2026

## Tarefa

Corrigir a exibição dos logs de alterações no relatório de produtos.

**Issue:** #12 – Corrigir exibição da coluna de logs no relatório  
**Branch:** `fix/report-log-format`

## Contexto

Durante a validação do relatório de produtos foi identificado que a coluna **Logs de Alterações** não era exibida corretamente.

Ao gerar o relatório, a aplicação emitia o aviso `Array to string conversion` e apresentava apenas o texto `Array` na coluna destinada aos logs, impossibilitando a visualização do histórico de alterações de cada produto.

Além disso, o requisito estabelecia que os logs deveriam ser apresentados no formato:

`(Nome do usuário, Tipo de alteração, Data)`

## Análise

A investigação foi iniciada reproduzindo o problema por meio da geração do relatório de produtos.

Inicialmente foram analisados os componentes envolvidos no fluxo de geração do relatório:

- `ReportController::generate()`;
- `ProductService::getAll()`;
- `ProductService::getLog()`;
- Tabelas `product_log` e `admin_user`.

Durante a análise, foi identificado que o método `ProductService::getLog()` retornava uma coleção de registros utilizando `fetchAll()`. Entretanto, esse resultado era adicionado diretamente à estrutura utilizada para gerar a tabela HTML do relatório.

Como consequência, durante a renderização da tabela, o PHP tentava converter um array em string, resultando no aviso `Array to string conversion` e na exibição do texto `Array` na coluna de logs.

Também foi identificado que a consulta responsável pela recuperação dos logs retornava apenas os dados da tabela `product_log`, não recuperando o nome do usuário responsável pela alteração, informação necessária para atender ao formato especificado no requisito.

## Implementação

A consulta responsável pela recuperação dos logs foi ajustada para recuperar também o nome do usuário responsável pela alteração por meio de um `JOIN` com a tabela `admin_user`.

Em seguida, o fluxo de geração do relatório foi alterado para formatar os registros retornados antes de adicioná-los à tabela.

Cada log passou a ser convertido para o formato:

`(Nome do usuário, Tipo de alteração, Data)`

As ações armazenadas no banco de dados (`create`, `update` e `delete`) passaram a ser traduzidas para descrições mais amigáveis (`Criação`, `Atualização` e `Remoção`), e as datas foram formatadas para o padrão `dd/mm/aaaa HH:mm:ss`.

Também foi padronizada a exibição da coluna **Data de Criação**, utilizando o mesmo formato adotado para os registros de log.

## Validação

Após a implementação, foram realizadas as seguintes validações:

- O relatório passou a exibir corretamente o histórico de alterações de cada produto.
- O aviso `Array to string conversion` deixou de ocorrer durante a geração do relatório.
- Os logs passaram a ser apresentados no formato especificado pelo requisito.
- A coluna **Data de Criação** passou a utilizar o formato `dd/mm/aaaa HH:mm:ss`, mantendo consistência com as datas dos logs.

## Considerações

A correção foi aplicada mantendo o escopo da issue, alterando apenas o fluxo responsável pela recuperação e apresentação dos logs de alterações no relatório de produtos.

A solução preservou a estrutura existente do relatório, concentrando as alterações na consulta de recuperação dos logs e na formatação das informações antes da geração da tabela HTML.

# 27/07/2026

## Tarefa

Implementar filtros e ordenação na listagem de produtos.

**Issue:** #15 – Implementar filtros e ordenação na listagem de produtos  
**Branch:** `feature/product-list-filters-and-sorting`

## Contexto

A listagem de produtos não disponibilizava mecanismos para restringir ou organizar os resultados retornados pela API.

A demanda consistia em permitir:

- Filtrar produtos ativos ou inativos;
- Filtrar produtos por categoria;
- Ordenar os produtos pela data de cadastro.

A implementação deveria preservar o comportamento atual da listagem quando nenhum filtro fosse informado, mantendo compatibilidade com as demais funcionalidades da aplicação.

## Análise

Inicialmente foi analisado o fluxo responsável pela recuperação da listagem de produtos.

Foram avaliados os seguintes componentes:

- `ProductController::getAll()`;
- `ProductService::getAll()`;
- Tabelas `product`, `product_category` e `category`.

Durante a análise foi identificado que a consulta já realizava o relacionamento entre produtos e categorias por meio das tabelas `product_category` e `category`, retornando o título da categoria para cada produto.

## Implementação

O endpoint de listagem passou a aceitar parâmetros opcionais para filtragem e ordenação.

Foi implementado o filtro por status do produto, permitindo listar produtos ativos ou inativos por meio do atributo `active`.

Também foi implementado o filtro por categoria utilizando o identificador da categoria (`category_id`), aplicando a restrição diretamente na consulta responsável pelo relacionamento entre produtos e categorias.

Por fim, foi adicionada a ordenação da listagem pela data de cadastro (`created_at`), permitindo controlar a sequência de apresentação dos produtos sem alterar a estrutura de retorno da API.

A implementação foi realizada preservando o comportamento anterior quando nenhum parâmetro de filtro ou ordenação é informado.

Filtros definidos: 
- active=1 para categorias ativas ou active=0 para categorias inativas;
- category=id, informando o id da categoria desejada;
- sort=asc para ordenação crescente ou sort=desc para ordenação decrescente da data de cadastro. 

## Validação

Após a implementação foram realizados testes utilizando diferentes combinações de parâmetros da API.

Foram validados os seguintes cenários:

- Listagem sem filtros.
- Filtragem de produtos ativos.
- Filtragem de produtos inativos.
- Filtragem por categoria.
- Combinação dos filtros de status e categoria.
- Ordenação crescente por data de cadastro.
- Ordenação decrescente por data de cadastro.
- Combinação simultânea de filtros e ordenação.
- Consulta utilizando categoria inexistente.

Os resultados retornados em todos os cenários foram comparados com os dados cadastrados no banco de dados, confirmando que os filtros e a ordenação produziram os resultados esperados.

## Considerações

A solução preservou a estrutura existente da API, concentrando as alterações na construção da consulta responsável pela listagem dos produtos.

# 28/07/2026

## Tarefa

Corrigir vulnerabilidades de SQL Injection na camada de persistência.

**Issue:** #22 – Remover vulnerabilidades de SQL Injection na camada de persistência e acrescentar controle de transação  
**Branch:** `refactor/remove-sql-injection`

## Contexto

Durante a análise da camada de persistência da aplicação foram identificadas consultas SQL construídas por meio da interpolação direta de variáveis, permitindo que valores externos fossem inseridos diretamente nas instruções SQL.

Esse padrão representava um risco de SQL Injection e dificultava a manutenção do código, uma vez que o tratamento dos parâmetros não seguia um padrão consistente entre os serviços.

Além da substituição das consultas vulneráveis, foi identificada a necessidade de garantir a atomicidade das operações compostas de escrita, evitando que alterações parciais fossem persistidas caso alguma etapa da operação falhasse.

## Análise

Inicialmente foi realizado um levantamento de todas as consultas SQL presentes nos serviços da aplicação.

Foram analisados os seguintes componentes:

- `ProductService`;
- `CategoryService`;
- `CompanyService`.

Durante a análise foram identificadas consultas utilizando interpolação direta de parâmetros em operações de consulta, inserção, atualização e remoção de registros.

Também foi identificado que as operações de criação, atualização e exclusão de produtos executavam múltiplas instruções SQL de forma sequencial, sem utilização de transações, permitindo que inconsistências fossem persistidas em caso de falha durante a execução.

## Implementação

Todas as consultas vulneráveis foram refatoradas para utilizar prepared statements do PDO com parâmetros nomeados, eliminando a interpolação direta de valores externos nas instruções SQL.

Foi padronizado o envio de parâmetros utilizando o método `execute($params)`, mantendo consistência com o padrão já adotado no projeto.

Também foi realizada a padronização da tipagem dos parâmetros antes da execução das consultas, utilizando conversões explícitas para os tipos esperados.

Nas operações de inserção, atualização e exclusão de produtos foram implementadas transações utilizando `beginTransaction`, `commit` e `rollBack`, garantindo que todas as alterações sejam persistidas apenas quando todas as operações forem concluídas com sucesso.

Em caso de falha durante qualquer etapa da operação, a transação é revertida automaticamente, preservando a consistência dos dados.

## Validação

Após a implementação foram realizadas validações das operações responsáveis pela persistência dos dados.

Foram validados os seguintes cenários:

- Consulta de produtos.
- Consulta de categorias.
- Consulta de empresas.
- Cadastro de produtos.
- Atualização de produtos.
- Exclusão de produtos.
- Cadastro de categorias.
- Atualização de categorias.
- Exclusão de categorias.
- Registro das operações no histórico de produtos.
- Execução das operações transacionais em cenários de sucesso.
- Rollback das operações em caso de falha durante transações.

As consultas continuaram retornando os mesmos resultados esperados após a substituição por prepared statements, enquanto as operações compostas passaram a garantir a integridade dos dados por meio do controle transacional.

## Considerações

A implementação concentrou as alterações exclusivamente na camada de persistência, preservando o comportamento da aplicação e eliminando os pontos identificados de SQL Injection.

Além do fortalecimento da segurança das consultas SQL, a adoção de transações nas operações compostas aumentou a confiabilidade da persistência dos dados, garantindo consistência mesmo diante de falhas durante a execução.
# 27/07/2026

## Tarefa

Implementar controle de estoque para produtos.

**Issue:** #17 – Implementar controle de estoque de produtos  
**Branch:** `feature/product-stock-control`

## Contexto

O sistema passou a exigir o controle da quantidade disponível em estoque para cada produto.

A nova funcionalidade deveria permitir:

- Informar a quantidade em estoque durante o cadastro do produto;
- Assumir automaticamente o valor `0` quando o estoque não fosse informado;
- Atualizar a quantidade em estoque de produtos já cadastrados;
- Filtrar a listagem de produtos por uma quantidade mínima em estoque.

Como a funcionalidade exigia alteração na estrutura do banco de dados, também foi necessária a criação de uma migration para persistir a nova coluna, conforme o padrão adotado pelo projeto.

## Análise

Inicialmente foi analisado o fluxo completo responsável pelo cadastro, atualização e consulta de produtos.

Foram avaliados os seguintes componentes:

- `ProductController`;
- `ProductService`;
- `Product`;
- Tabela `product`;
- Testes unitários da entidade `Product`;
- Coleção do Postman.

Durante a análise foi identificado que a entidade `Product` não possuía um atributo responsável pelo controle de estoque e que as operações de cadastro e atualização manipulavam apenas os atributos já existentes do produto.

Também foi verificado que o método responsável pela listagem já possuía uma estrutura preparada para receber filtros adicionais, permitindo reutilizar o padrão existente para implementar o filtro de estoque mínimo.

## Implementação

Foi criada uma migration adicionando a coluna `stock` à tabela `product`, definindo o valor padrão `0` para registros em que nenhuma quantidade seja informada.

A entidade `Product` foi atualizada para representar o novo atributo, bem como o método de hidratação (`hydrateByFetch()`), garantindo que o estoque seja corretamente carregado a partir do banco de dados.

As operações de cadastro e atualização de produtos foram modificadas para aceitar o novo atributo `stock`. Durante o cadastro, quando nenhuma quantidade é enviada pela requisição, o sistema passa a assumir automaticamente o valor `0`, conforme especificado nos requisitos.

Também foi implementado um novo filtro na listagem de produtos, permitindo retornar apenas produtos cuja quantidade em estoque seja maior ou igual ao valor informado pelo parâmetro `min_stock`.

Por fim, foram atualizados os testes unitários da entidade `Product`, adequando-os ao novo construtor e adicionando validações referentes ao atributo de estoque.

A coleção do Postman também foi atualizada para incluir exemplos de utilização do novo campo e do filtro implementado.

## Validação

Após a implementação foram realizados testes para validar os principais cenários da funcionalidade.

Foram validados os seguintes casos:

- Cadastro de produto informando estoque.
- Cadastro de produto sem informar estoque.
- Aplicação automática do valor padrão `0`.
- Atualização da quantidade em estoque.
- Consulta individual retornando o estoque.
- Listagem retornando o atributo `stock`.
- Filtragem utilizando o parâmetro `min_stock`.
- Combinação do filtro de estoque com os demais filtros existentes.
- Execução dos testes unitários atualizados.

Os resultados obtidos confirmaram que a funcionalidade atende aos requisitos definidos, preservando o comportamento das operações existentes e mantendo compatibilidade com a estrutura atual da aplicação.

## Considerações

A implementação foi realizada reutilizando a arquitetura já existente do projeto, concentrando as alterações na entidade de produto, na camada de serviço, na estrutura do banco de dados e na documentação da API.

A utilização de uma migration garante que a alteração estrutural possa ser reproduzida em outros ambientes sem depender do arquivo do banco de dados, mantendo o processo de implantação consistente com as diretrizes do projeto.

# 28/07/2026

## Tarefa

Implementar suporte à tradução de categorias.

**Issue:** #18 – Implementar suporte a traduções de categorias
**Branch:** `feature/category-translations`

## Contexto

O sistema passou a exigir suporte à internacionalização das categorias para permitir que a API seja utilizada em diferentes idiomas.

A funcionalidade deveria permitir:

- Cadastrar múltiplas traduções para uma categoria;
- Impedir o cadastro de traduções duplicadas para uma mesma categoria;
- Garantir que, caso uma única tradução inválida seja enviada, nenhuma tradução seja persistida;
- Permitir que produtos e categorias sejam consultados em diferentes idiomas por meio do parâmetro opcional `lang`;
- Utilizar inglês como idioma padrão sempre que nenhuma tradução correspondente estiver disponível;
- Atualizar a coleção do Postman com os novos exemplos de utilização.

Como a funcionalidade exige persistência das traduções, também foi necessária a criação de uma nova estrutura no banco de dados por meio de uma migration.

## Análise

Inicialmente foi analisado o fluxo responsável pela recuperação das categorias e pela associação entre produtos e categorias.

Foram avaliados os seguintes componentes:

- `CategoryController`;
- `CategoryService`;
- `ProductController`;
- Tabelas `category` e `product_category`;
- Estrutura de rotas da API;
- Coleção do Postman.

Durante a análise foi identificado que a entidade de categoria armazenava apenas um único título, impossibilitando a representação de múltiplos idiomas.

Também foi identificado que a busca individual de produtos obtém as categorias por meio do `CategoryService`, permitindo reutilizar essa camada para aplicar a tradução sem alterar a estrutura da entidade `Product`.

## Implementação

Foi criada uma migration adicionando a tabela `category_translation`, responsável por armazenar as traduções associadas às categorias.

Também foi criada uma restrição de unicidade para impedir o cadastro de mais de uma tradução para o mesmo idioma dentro de uma categoria.

Foi implementado um novo endpoint responsável pelo cadastro de traduções, permitindo registrar múltiplos idiomas em uma única requisição.

A operação foi implementada utilizando transação no banco de dados, garantindo que qualquer erro durante o processamento provoque o rollback completo da operação, preservando a consistência dos dados.

As consultas de categorias passaram a aceitar o parâmetro opcional `lang`, retornando a tradução correspondente quando disponível.

Quando nenhuma tradução é encontrada para o idioma solicitado, ou quando o parâmetro não é informado, a API passa a utilizar automaticamente a tradução em inglês como idioma padrão, conforme definido nos requisitos.

A consulta individual de produtos também foi adaptada para utilizar o idioma informado durante a recuperação das categorias associadas ao produto.

Por fim, a coleção do Postman foi atualizada para incluir o novo endpoint e exemplos de utilização da funcionalidade.

## Validação

Após a implementação foram realizados testes contemplando os principais cenários da funcionalidade.

Foram validados os seguintes casos:

- Cadastro de múltiplas traduções para uma categoria.
- Cadastro de traduções para diferentes idiomas.
- Tentativa de cadastro de tradução duplicada.
- Verificação do rollback quando ocorre erro durante a operação.
- Consulta de categorias utilizando diferentes idiomas.
- Consulta de categorias sem informar o parâmetro `lang`.
- Consulta utilizando idioma inexistente, validando o fallback para inglês.
- Consulta individual de produtos retornando categorias traduzidas.
- Atualização da coleção do Postman.

Os resultados confirmaram que a funcionalidade atende aos requisitos definidos, preservando o comportamento existente da aplicação e garantindo a consistência das informações persistidas.

## Considerações

A implementação foi realizada preservando a arquitetura existente do projeto, concentrando as alterações na camada de serviço, nas rotas da API, na estrutura do banco de dados e na documentação.

A utilização de uma tabela dedicada para traduções desacopla o idioma da entidade de categoria, permitindo a inclusão de novos idiomas sem necessidade de alterações estruturais futuras.
