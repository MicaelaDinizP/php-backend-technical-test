# DESAFIO BACKEND

## Sobre o teste
O objetivo do teste é avaliar suas habilidades como programador **backend PHP**.

Você receberá uma aplicação que necessita de ajustes descritos pelo cliente e correções de bugs apontadas, deve resolvê-los com a maior qualidade e organização de código possível. Serão avaliados: domínio da linguagem, resolução de problemas, performance, segurança e organização.

Existe uma seção ao fim do _README_ chamada **"Suas Respostas, Dúvidas e Observações"** reservada para documentação do processo de desenvolvimento, mudanças na API e lógica também devem ser documentadas.

## Configuração do Ambiente

### Requisitos
- _PHP >= 8.0_ e [extensões](https://www.php.net/manual/pt_BR/extensions.php) (**não esquecer de instalar as seguintes extensões: _pdo_, _pdo_sqlite_ e _sqlite3_**);
- [_SQLite_](https://www.sqlite.org/index.html);
- [_Composer_](https://getcomposer.org/).

### Instalação
- Instalar dependências pelo _composer_ com `composer install` na raiz do projeto;
- Servir a pasta _public_ do projeto através de algum servidor.
  (_Sugestão [PHP Built in Server](https://www.php.net/manual/en/features.commandline.webserver.)_. Exemplo para servir a pasta public: `php -S localhost:8000 -t public`)

## Sobre a entrega
>[!CAUTION]
> A entrega deve ser realizada em um repositório **_PRIVADO_** do **GitHub**;
> 
> Você deve adicionar os usuários [`pedrosobucki`](https://github.com/pedrosobucki) e [`aloefflerj`](https://github.com/aloefflerj) como colaboradores do repositório com permissão de leitura para que seu teste possa ser avaliado.

- A primeira etapa é realizar um commit inicial com o código sem nenhuma modificação;
- As modificações devem estar separadas por commits coerentes com as funcionalidades e mudanças realizadas ao longo do processo, não um único commit com todas as modificaçẽs;
- As soluções elaboradas e implementadas por você devem ser apresentadas na seção **Suas Respostas, Dúvidas e Observações** ao fim do _README_.

## Sobre o Projeto
- O cliente XPTO Ltda. contratou seu serviço para realizar alguns ajustes em seu sistema de cadastro de produtos;
- O sistema permite o cadastro, edição e remoção de _produtos_ e _categorias de produtos_ para uma _empresa_;
- Para que sejam possíveis os cadastros, alterações e remoções é necessário um usuário administrador;
- O sistema possui categorias padrão que pertencem a todas as empresas, bem como categorias personalizadas dedicadas a uma dada empresa. As categorias padrão são: (`clothing`, `phone`, `computer` e `house`) e **devem** aparecer para todas as _empresas_;
- O sistema tem um relatório de dados dedicado ao cliente.

## Sobre a API
As rotas estão divididas em:
  - _CRUD_ de _categorias_;
  - _CRUD_ de _produtos_;
  - Rota de busca de um _relatório_ que retorna um _html_.

Deve ser utilizado o [Postman](https://www.postman.com/) para desenvolvimento e documentação, o arquivo para importação das rotas se encontra em `docs/postman-api.json`.

> [!WARNING]
> É importante que se adicione o _header_ `admin_user_id` com o id do usuário desejado ao acessar as rotas para simular o uso de um usuário no sistema.

A documentação da API se encontra na pasta `docs/api-docs.pdf`
  - A documentação assume que a url base é `localhost:8000` mas você pode usar qualquer outra url ao configurar o servidor;
  - O _header_ `admin_user_id` na documentação está indicado com valor `1` mas pode ser usado o id de qualquer outro usuário caso deseje (_pesquisando no banco de dados é possível ver os outros id's de usuários_).

## Sobre o Banco de Dados
- O banco de dados é um _sqlite_ simples e já vem com dados preenchidos por padrão no projeto;
- O banco tem um arquivo de backup em `db/db-backup.sqlite3` com o estado inicial do projeto caso precise ser "resetado".

### Migrations
- Funcionalidades que exijam modificações no banco de dados (seja nos dados ou estrutura) **devem estar contidas em _migrations_**, não enviadas diretamente com o banco;
- **Seu arquivo de banco** `db.sqlite3` **não será utilizado para avaliação** do teste, por isso é importante persistir mudanças necessárias em migrations;
- A biblioteca utilizada para migrations foi o [**_Phinx_**](https://book.cakephp.org/phinx/0/en/index.html);
- As migrations criadas **devem poder ser revertidas** (método `down()`);
- Para interagir com as migrations, você pode usar os seguintes comandos:
- - Criar nova migration: `composer create-migration`
- - Rodar migrations: `composer migrate`
- - Reverter migration: `composer rollback`

# Demandas
Abaixo, as solicitações do cliente:

## Alterações
Modificações requisitadas pelo cliente em funcionalidades já existentes

### Categorias
- [x] A categoria está vindo errada na listagem de produtos para alguns casos (_exemplo: produto `blue trouser` está vindo na categoria `phone`_);
- [x] Alguns produtos estão vindo com a categoria `null` ao serem pesquisados individualmente (_exemplo: produto `iphone 8`_);
- [x] Cadastrei o produto `king size bed` em mais de uma categoria, mas ele aparece **apenas** na categoria `furniture` na busca individual do produto.

### Filtros e Ordenamento
Para a listagem de produtos:
- [x] Gostaria de poder filtrar os produtos ativos ou inativos;
- [x] Gostaria de poder filtrar os produtos por categoria;
- [x] Gostaria de poder ordenar os produtos por data de cadastro.

### Relatório
- [x] O relatório não está mostrando a coluna de logs corretamente, se possível, gostaria de trazer no seguinte formato:
  (Nome do usuário, Tipo de alteração e Data),
  (Nome do usuário, Tipo de alteração e Data),
  (Nome do usuário, Tipo de alteração e Data)
  Exemplo:
  (John Doe, Criação, 01/12/2023 12:50:30),
  (Jane Doe, Atualização, 11/12/2023 13:51:40),
  (Joe Doe, Remoção, 21/12/2023 14:52:50)

### Logs
- [x] Gostaria de saber qual usuário mudou o preço do produto `iphone 8` por último.

### Correção de bug
- [x] Ao rodar os teste unitários com `composer test` são apontados erros. Eles precisam ser resolvidos, com documentação sobre a causa e a solução.

## Features
Novas funcionalidades requisitadas pelo cliente

> [!WARNING]
> Preste atenção, funcionalidades que exijam mudanças no banco de dados devem conter tais modificações em uma ou mais **migrations**.

### Traduções
- [ ] Quero disponibilizar meu sistema para fora do país, crie uma funcionalidade de cadastro de traduções para as categorias que segue o seguinte contrato:
```
POST "$base_url/categories/:id"

{
  "translations": [
    {
      "lang_code": "en",
      "label": "home"
    },
    {
      "lang_code": "pt",
      "label": "casa"
    }
  ]
}
```
- [x] Não deve ser possível cadastrar traduções repetidas, se uma única tradução repetida foi enviada, nenhuma deve persistir;
- [x] Ao buscar por produtos/categorias, o parâmetro adicional opcional "_`lang`_" pode ser passado para determinar a linguagem em que a categoria deve ser retornada;
- [x] Caso não haja categoria correspondente ou não seja especificado por parâmetro, retornar em inglês;
- [x] Inclua a rota e as modificações na coleção do Postman no repositório.

### Estoque
Além das informações já disponíveis do produto, desejo acrescentar também uma contagem de estoque para cada, a qual deve seguir algumas regras:
- [x] Posso cadastrar a quantidade do estoque assim que cadastro um produto, mas se não for informado assumo que o estoque é _0_;
- [x] Posso atualizar o estoque de um produto;
- [x] Ao buscar um produto, posso filtrar por uma quantidade mínima em estoque.

### Comentários
Quero que os usuários do sistema possam discutir sobre os produtos em uma área de comentários.

Para isso, novas rotas devem ser criadas:
- [x] Criar um novo comentário no produto
- [x] Responder um comentário já realizado (todo comentário pode ser diretamente respondido)
- [x] Remover um comentário feito por mim
- [x] Curtir um comentário
- [x] Listar todos os comentários de um produto em um objeto com hierarquia de comentários

##
**Seu trabalho é atender às demandas solicitadas pelo cliente.**

Caso julgue necessário, podem ser adicionadas ou modificadas as rotas da api. Caso altere, por favor, explique o porquê e indique as alterações nesse `README`.

Sinta-se a vontade para refatorar o que achar pertinente, considerando questões como arquitetura, padrões de código, padrões restful, _segurança_ e quaisquer outras boas práticas. Levaremos em conta essas mudanças.

## Docker
Você deve servir a aplicação por meio de um ambiente docker

Para efetuar a criação do ambiente docker, partimos de algumas premissas:
- O ambiente tem o **objetivo de ser utilizado por outros devs**, então deve funcionar sem necessidade de alteração de arquivos para inserção de dados específicos de máquina (quando baixo o repositório quero subir o container sem que fazer edições no ambiente docker);
- Levando o ponto anterior em conta, é inteligente não deixar a criação do container para o final;
- A funcionalidade de **PHP** deve rodar na porta **8000** do host.

### Itens obrigatórios
- [x] Criar um ambiente docker que sobe a aplicação **PHP** na porta **8000**;
- [x] Possibilitar que as **_migrations_ possam ser executadas/criadas por docker** (especificar comando);
- [x] Possibilitar que os **_testes unitários_ sejam executados por docker** (especificar comando).

### Desafios
- [ ] Substituir o banco serverless **SQLite** por um banco como **MySQL**/**PostgreSQL**/outro e servir por container;
- [ ] Escrever **novos testes unitários** para funcionalidades faltantes;
- [ ] Implementar um **Linter** e disponibilizar por docker (especificar comando);
- [ ] Implementar **análise estática** e disponibilizar por docker (especificar comando);
- [ ] Escrever um script "_`check_deploy.sh`_" que faz todas as validações implementadas como uma pipeline e determina se o código está pronto para produção.

## Suas Respostas, Dúvidas e Observações

## Como executar o projeto

### 1. Clone o repositório

```bash
git clone <url-do-repositório>
cd teste-backend
```

### 2. Suba os containers

```bash
docker compose up -d --build
```

### 3. Acesse o container da aplicação

```bash
docker exec -it teste-backend-app bash
```

### 4. Execute as migrations

```bash
composer migrate
```

A aplicação estará disponível conforme as portas definidas no arquivo `docker-compose.yml`.

## Comandos úteis no container

### Criar uma migration

```bash
composer create-migration NomeDaMigration
```

### Executar as migrations

```bash
composer migrate
```

### Executar os testes unitários

```bash
composer test
```

## Sobre o desenvolvimento

O desenvolvimento foi conduzido utilizando um fluxo de trabalho baseado em **Issues**, **branches** e **Pull Requests**, buscando manter cada demanda isolada e rastreável durante todo o processo.

Cada tarefa foi implementada em sua própria branch, documentada por meio de uma issue e concluída com um Pull Request contendo uma descrição detalhada das alterações realizadas. Os commits seguiram o padrão de **Commits Semânticos (Conventional Commits)**, tornando o histórico do repositório mais organizado e facilitando a compreensão da evolução do projeto.

Além dos requisitos propostos pelo desafio, foram identificadas oportunidades de melhoria na aplicação. Algumas delas foram implementadas durante o desenvolvimento, enquanto outras foram registradas como issues para futuras evoluções, em razão da priorização das entregas previstas no escopo do teste técnico.

## Sobre o Development Log

Todo o processo de desenvolvimento foi registrado em um **Development Log**, contendo a documentação cronológica das atividades realizadas.

Nesse documento estão registrados, para cada tarefa:

- Data da implementação;
- Análise do problema ou requisito;
- Planejamento da solução;
- Decisões técnicas adotadas;
- Implementação realizada;
- Validação da solução;
- Observações relevantes.

Como o Development Log se tornou bastante extenso, este README apresenta uma visão consolidada das implementações, enquanto o documento completo permanece disponível como documentação complementar.

O Development Log segue uma estrutura padronizada para todas as atividades, facilitando a navegação e a localização das informações.

```text
Data
Objetivo
Análise
Planejamento
Implementação
Validação
Observações
```

Também é possível localizar facilmente cada implementação por meio do histórico de commits, das branches correspondentes e das Issues e Pull Requests associados a cada demanda.

## Correções realizadas

Além da implementação das novas funcionalidades solicitadas, foram corrigidas inconsistências funcionais identificadas na aplicação original. Todas as correções foram realizadas preservando a arquitetura existente do projeto, buscando minimizar impactos sobre os demais módulos e mantendo compatibilidade com os comportamentos já esperados pela API.

### Correção da associação entre produtos e categorias

Durante a validação da listagem de produtos foi identificado que alguns registros eram associados a categorias incorretas. A investigação demonstrou que o relacionamento entre as tabelas `product_category` e `category` utilizava incorretamente o identificador da tabela intermediária (`product_category.id`) em vez da chave estrangeira (`product_category.cat_id`).

A consulta responsável pela listagem foi corrigida para utilizar o relacionamento adequado, garantindo que cada produto seja associado corretamente às suas categorias.

Após a alteração foram realizadas validações comparando os resultados retornados pela API com os registros existentes no banco de dados, confirmando a correção da inconsistência sem alterar o comportamento dos demais endpoints.

---

### Correção do retorno de categorias padrão

Durante a análise das consultas individuais de produtos foi identificado que categorias compartilhadas entre empresas eram retornadas como `null` em determinados cenários.

A investigação demonstrou que as consultas responsáveis pela recuperação das categorias consideravam apenas categorias pertencentes à empresa do usuário administrador, desconsiderando categorias globais definidas pelo sistema.

Os serviços responsáveis pela recuperação de categorias foram ajustados para considerar tanto categorias específicas da empresa quanto categorias padrão compartilhadas (`company_id IS NULL`), garantindo conformidade com a regra de negócio descrita na documentação do projeto.

Essa alteração também corrigiu a listagem de categorias, que passou a retornar corretamente categorias globais e categorias personalizadas de cada empresa.

---

### Suporte a múltiplas categorias por produto

Foi identificado que produtos associados a mais de uma categoria retornavam apenas um único registro na consulta individual.

A implementação foi revisada para representar corretamente o relacionamento muitos-para-muitos existente entre produtos e categorias.

A entidade `Product` passou a armazenar uma coleção de categorias, enquanto o fluxo de recuperação foi ajustado para utilizar `fetchAll()`, permitindo que todas as categorias relacionadas ao produto sejam retornadas pela API.

Os testes unitários da entidade também foram atualizados para refletir esse novo comportamento.

---

### Correção da exibição dos logs no relatório

O relatório de produtos apresentava a coluna de logs incorretamente, exibindo apenas o texto `Array` devido à tentativa de conversão automática de estruturas complexas para string durante a geração da tabela HTML.

O fluxo responsável pela geração do relatório foi ajustado para formatar previamente os registros de auditoria, passando a apresentar cada alteração no formato:

- Nome do usuário
- Tipo da alteração
- Data da operação

Também foi realizada a padronização da formatação das datas apresentadas no relatório.

---

### Correção dos testes unitários

Durante a execução da suíte de testes foi identificada uma inconsistência na hidratação da entidade `Product`, onde o atributo `price` era convertido para inteiro durante a criação do objeto, ocasionando perda das casas decimais.

A implementação foi corrigida para utilizar o tipo `float`, preservando corretamente os valores monetários.

Após a alteração toda a suíte de testes foi executada novamente, confirmando que a correção eliminou a falha sem introduzir regressões no comportamento da entidade.

## Funcionalidades implementadas

Além das correções solicitadas pelo cliente, foram desenvolvidas novas funcionalidades que ampliam as capacidades da aplicação. As implementações seguiram a arquitetura existente do projeto, priorizando a reutilização da camada de serviços, a consistência das regras de negócio e a rastreabilidade das alterações por meio de migrations, documentação e atualização da coleção do Postman.

### Filtros e ordenação de produtos

A listagem de produtos foi expandida para permitir consultas mais flexíveis por meio de parâmetros opcionais.

Foram implementados filtros para listagem de produtos ativos ou inativos, filtragem por categoria e ordenação pela data de cadastro, preservando o comportamento original do endpoint quando nenhum parâmetro é informado.

Os filtros são aplicados diretamente na consulta responsável pela recuperação dos produtos, evitando processamento adicional na aplicação e mantendo a eficiência da operação.

Parâmetros adicionados:

- `active=1` ou `active=0` para filtrar produtos ativos ou inativos;
- `category=<id>` para filtrar produtos pertencentes a uma categoria específica;
- `sort=asc` ou `sort=desc` para ordenar os resultados pela data de cadastro.

A implementação também permite a combinação simultânea dos filtros e da ordenação.

---

### Controle de estoque

Foi implementado um sistema de controle de estoque integrado ao cadastro de produtos.

A estrutura do banco de dados foi expandida por meio de uma migration, adicionando o campo `stock` à tabela de produtos com valor padrão igual a `0`, conforme especificado nos requisitos.

A funcionalidade permite:

- informar a quantidade em estoque durante o cadastro;
- assumir automaticamente estoque zero quando o campo não é informado;
- atualizar o estoque de produtos existentes;
- consultar o estoque juntamente com os demais dados do produto;
- filtrar produtos por uma quantidade mínima disponível em estoque.

A entidade `Product`, os serviços responsáveis pelo cadastro e atualização, os testes unitários e a coleção do Postman foram atualizados para refletir essa nova funcionalidade.

---

### Tradução de categorias

Foi implementado suporte à internacionalização das categorias, permitindo que produtos e categorias sejam apresentados em diferentes idiomas.

Para isso foi criada uma nova tabela responsável pelo armazenamento das traduções, desacoplando os idiomas da estrutura principal da entidade de categoria e permitindo a inclusão de novos idiomas sem necessidade de alterações estruturais futuras.

A funcionalidade permite:

- cadastrar múltiplas traduções para uma categoria;
- impedir traduções duplicadas para um mesmo idioma;
- realizar rollback completo da operação caso qualquer tradução enviada seja inválida;
- consultar categorias e produtos utilizando o parâmetro opcional `lang`;
- utilizar automaticamente o idioma inglês como fallback quando nenhuma tradução correspondente estiver disponível.

Também foi criado um novo endpoint responsável pelo cadastro das traduções e a coleção do Postman foi atualizada para documentar sua utilização.

---

### Sistema de comentários

Foi desenvolvida uma área de comentários para produtos, permitindo que administradores discutam informações relacionadas aos itens cadastrados.

A funcionalidade contempla:

- criação de comentários;
- respostas a comentários existentes;
- exclusão lógica realizada apenas pelo autor;
- registro de curtidas;
- listagem hierárquica dos comentários.

Para suportar essa funcionalidade foram criadas novas migrations responsáveis pelas tabelas de comentários e curtidas, além da entidade `Comment`, do `CommentService` e do `CommentController`.

Também foram implementadas validações específicas para garantir a existência do produto, impedir respostas para comentários inexistentes, restringir remoções ao autor e evitar curtidas duplicadas.

Durante a recuperação dos comentários, a estrutura é organizada em memória na forma de árvore, permitindo representar corretamente o relacionamento entre comentários e respostas.

Como parte dessa implementação também foi introduzido um padrão de retorno para a camada de serviços por meio da classe `ServiceResult`, simplificando o tratamento de erros, mensagens e códigos HTTP pelos controllers.

---

### Auditoria de alterações de preço

Foi implementado um mecanismo específico para rastrear alterações de preço dos produtos.

Embora a aplicação já registrasse operações de criação, atualização e remoção, os registros existentes não identificavam quais atributos haviam sido modificados durante uma atualização.

Para atender ao requisito, a estrutura de auditoria foi evoluída através de uma migration adicionando informações sobre o campo alterado e seu valor anterior.

Durante atualizações de produtos, a aplicação compara o valor persistido com o novo valor informado e registra alterações apenas quando ocorre modificação no preço.

Também foi criado um novo endpoint:

`GET /products/{id}/last-price-change`

Esse endpoint permite identificar o último usuário responsável por alterar o preço de um produto, atendendo ao requisito solicitado pelo cliente e preparando a estrutura de auditoria para futuras necessidades de rastreamento de outros atributos.

## Segurança e refatorações

Além das demandas funcionais propostas no desafio, foram realizadas melhorias na arquitetura e na camada de persistência com o objetivo de aumentar a segurança, a confiabilidade e a manutenibilidade da aplicação.

Essas alterações foram implementadas preservando o comportamento esperado da API e concentrando as mudanças principalmente na camada de serviços, reduzindo impactos sobre os demais componentes do sistema.

### Eliminação de vulnerabilidades de SQL Injection

Durante a análise da camada de persistência foi identificado que diversas consultas SQL eram construídas por meio da interpolação direta de variáveis.

Embora esse padrão estivesse funcionalmente correto para os cenários existentes, ele permitia a inserção direta de valores externos nas instruções SQL, expondo a aplicação a vulnerabilidades de SQL Injection e dificultando a manutenção das consultas.

Para eliminar esse risco, todas as consultas identificadas foram refatoradas para utilizar **Prepared Statements** do PDO com parâmetros nomeados.

A alteração contemplou operações de:

- consulta;
- inserção;
- atualização;
- remoção de registros.

Além do ganho em segurança, essa abordagem padronizou a construção das consultas SQL em toda a camada de persistência, tornando o código mais legível e consistente.

---

### Controle transacional

Durante a revisão das operações de escrita também foi identificado que algumas funcionalidades executavam múltiplas instruções SQL de forma sequencial, sem qualquer mecanismo para garantir a atomicidade da operação.

Nesses cenários, uma falha intermediária poderia resultar em dados parcialmente persistidos, comprometendo a consistência da base.

Para resolver esse problema foram implementadas transações utilizando os recursos nativos do PDO.

As operações passaram a utilizar:

- `beginTransaction()`;
- `commit()`;
- `rollBack()`.

Dessa forma, todas as alterações realizadas durante uma operação composta somente são persistidas quando todas as etapas são concluídas com sucesso.

Caso qualquer erro ocorra durante o processamento, toda a operação é revertida automaticamente, preservando a integridade dos dados.

---

### Padronização da camada de persistência

Durante as refatorações também foi realizada uma padronização da forma como os parâmetros são enviados para as consultas SQL.

Todos os serviços passaram a utilizar o mesmo padrão de execução por meio de parâmetros nomeados e chamadas ao método `execute()`, reduzindo duplicações e facilitando futuras manutenções.

Também foram adicionadas conversões explícitas de tipos antes da execução das consultas, tornando o tratamento dos dados mais previsível e reduzindo inconsistências durante a persistência.

---

### Evolução da arquitetura dos serviços

Algumas funcionalidades implementadas durante o desafio introduziram regras de negócio significativamente mais complexas que aquelas existentes na aplicação original.

Para evitar o aumento da responsabilidade dos controllers e reduzir duplicações de código, novas abstrações foram introduzidas durante o desenvolvimento.

Entre elas destacam-se:

- `ServiceResult`, responsável por padronizar o retorno da camada de serviços, encapsulando dados, mensagens, erros de validação e códigos HTTP;
- `ValidationResult`, utilizado para representar o resultado das validações de negócio;
- `CommentValidator`, criado para centralizar todas as regras de validação relacionadas ao módulo de comentários.

Essa abordagem tornou a camada de serviços mais organizada e facilitou a implementação de novas regras de negócio sem aumentar o acoplamento entre controllers e services.

---

### Organização do desenvolvimento

Durante todo o desenvolvimento foi adotada uma estratégia baseada em issues, branches e Pull Requests independentes para cada demanda implementada.

Essa organização permitiu manter um histórico de alterações mais claro, facilitando a revisão do código e garantindo rastreabilidade entre requisitos, implementação e validação.

Também foram criadas migrations para todas as alterações estruturais realizadas no banco de dados, evitando dependência do arquivo físico do banco durante a avaliação do projeto e garantindo que todas as modificações possam ser reproduzidas em outros ambientes de forma consistente.

Além disso, a documentação da API e a coleção do Postman foram atualizadas sempre que novas rotas ou parâmetros foram adicionados, mantendo a documentação alinhada com a implementação da aplicação.

## Testes e validação

Ao longo do desenvolvimento, todas as alterações foram validadas incrementalmente, buscando garantir que as correções e novas funcionalidades não introduzissem regressões no comportamento da aplicação.

Sempre que possível, as validações foram realizadas reproduzindo inicialmente o problema reportado, implementando a correção e, em seguida, repetindo os mesmos cenários para confirmar que o comportamento esperado havia sido restabelecido.

Além da validação funcional por meio da API, a suíte de testes unitários existente foi utilizada como mecanismo de verificação durante o desenvolvimento.

### Testes unitários

Logo no início do projeto foi executada a suíte de testes disponibilizada pelo desafio utilizando o comando:

```bash
composer test
```

Durante essa execução foi identificada uma falha relacionada à hidratação da entidade `Product`, ocasionada pela conversão incorreta do atributo `price` para inteiro, resultando na perda das casas decimais.

Após a correção desse comportamento, a suíte de testes foi executada novamente, confirmando a resolução da inconsistência.

Conforme novas funcionalidades foram implementadas, os testes existentes também foram atualizados sempre que alterações estruturais impactavam o comportamento esperado das entidades, como ocorreu durante a implementação do suporte a múltiplas categorias e do controle de estoque dos produtos.

Também foram adicionados testes unitários para a nova entidade `Comment`, validando seu comportamento básico e garantindo consistência com o restante da aplicação.

---

### Validação das funcionalidades

Cada demanda implementada foi validada individualmente por meio da API, contemplando cenários de sucesso, erros de validação e situações limite.

Entre os cenários verificados destacam-se:

#### Produtos

- associação correta entre produtos e categorias;
- suporte a múltiplas categorias por produto;
- cadastro e atualização de estoque;
- filtragem por estoque mínimo;
- filtros por categoria e status;
- ordenação por data de cadastro;
- auditoria de alterações de preço.

#### Categorias

- recuperação de categorias globais e específicas da empresa;
- consulta utilizando diferentes idiomas;
- fallback automático para inglês;
- prevenção de traduções duplicadas;
- rollback completo em operações inválidas.

#### Comentários

- criação de comentários;
- respostas hierárquicas;
- exclusão lógica pelo autor;
- registro de curtidas;
- prevenção de curtidas duplicadas;
- recuperação da árvore completa de comentários.

#### Relatórios

- geração correta do relatório HTML;
- formatação dos registros de auditoria;
- exibição do usuário responsável pelas alterações;
- padronização da apresentação das datas.

---

### Validação das migrations

Todas as alterações estruturais do banco de dados foram implementadas utilizando migrations.

Durante o desenvolvimento, as migrations foram executadas e revertidas repetidas vezes para garantir que os métodos `up()` e `down()` funcionassem corretamente e que a estrutura do banco pudesse ser reproduzida em diferentes ambientes sem depender do arquivo físico do banco de dados.

---

### Validação da documentação

Sempre que novas rotas, parâmetros ou funcionalidades foram adicionados à aplicação, a coleção do Postman foi atualizada para refletir essas alterações.

Esse processo garantiu que a documentação permanecesse consistente com a implementação final da API, facilitando tanto a validação quanto a utilização dos novos recursos desenvolvidos.

## Banco de dados e migrations

Todas as alterações estruturais realizadas durante o desenvolvimento foram implementadas por meio de migrations, conforme solicitado pelo desafio técnico.

Essa abordagem garante que a estrutura do banco de dados possa ser reproduzida em qualquer ambiente apenas executando as migrations do projeto, sem necessidade de distribuir uma versão modificada do banco de dados.

Cada migration foi desenvolvida com suporte às operações de aplicação (`up`) e reversão (`down`), permitindo que as alterações sejam desfeitas de forma segura durante o desenvolvimento e os testes.

As migrations implementadas contemplam tanto as novas funcionalidades quanto as adaptações necessárias para suportar os requisitos propostos.

### Controle de estoque

Foi criada uma migration responsável por adicionar o campo `stock` à tabela de produtos.

A alteração definiu o valor padrão como `0`, garantindo compatibilidade com os registros já existentes e permitindo que novos produtos sejam cadastrados mesmo quando a quantidade em estoque não é informada.

---

### Traduções de categorias

Para suportar a internacionalização das categorias foi criada uma nova tabela destinada ao armazenamento das traduções.

Essa estrutura separa os textos traduzidos da entidade principal de categoria, permitindo que novos idiomas sejam adicionados sem necessidade de alterações estruturais na tabela original.

Também foram definidos os relacionamentos e restrições necessários para impedir inconsistências entre categorias e suas respectivas traduções.

---

### Sistema de comentários

A implementação da funcionalidade de comentários exigiu a criação de novas estruturas no banco de dados.

Foram adicionadas tabelas responsáveis por armazenar:

- comentários;
- relacionamento entre comentários e produtos;
- hierarquia entre comentários e respostas;
- registros de curtidas.

As tabelas foram modeladas para preservar a integridade referencial e permitir futuras expansões da funcionalidade sem necessidade de alterações significativas na estrutura existente.

---

### Auditoria de alterações de preço

A estrutura de auditoria existente foi evoluída para permitir o registro detalhado das alterações de preço realizadas nos produtos.

Para isso foi criada uma migration responsável por adicionar novas colunas destinadas ao armazenamento do campo alterado e do valor anterior, possibilitando identificar posteriormente quais atributos sofreram modificações durante uma atualização.

Essa alteração tornou possível implementar o endpoint responsável por informar o último usuário que alterou o preço de um determinado produto.

---

### Organização das alterações

Cada migration foi desenvolvida de forma independente, representando uma única alteração estrutural no banco de dados.

Essa estratégia facilita a manutenção do histórico de evolução da aplicação, simplifica revisões de código e reduz o risco de conflitos entre diferentes modificações estruturais.

Além disso, a separação das alterações em migrations individuais torna o processo de implantação mais previsível e facilita futuras evoluções da aplicação.

## Documentação da API

Durante o desenvolvimento, a documentação da API foi mantida em sincronia com a implementação das funcionalidades. Sempre que novos endpoints, parâmetros ou comportamentos foram adicionados, a coleção do Postman foi atualizada para refletir essas alterações.

Essa abordagem garante que o processo de validação da aplicação possa ser reproduzido facilmente, permitindo testar tanto os fluxos originais quanto as funcionalidades desenvolvidas durante o desafio.

### Coleção do Postman

A coleção disponibilizada no projeto foi atualizada para incluir todas as novas rotas implementadas, bem como os novos parâmetros adicionados aos endpoints já existentes.

Também foram revisados os exemplos de requisição para facilitar a validação manual da API.

Entre as alterações realizadas estão:

- inclusão das rotas relacionadas ao sistema de comentários;
- inclusão do endpoint de cadastro de traduções de categorias;
- inclusão do endpoint para consulta da última alteração de preço;
- atualização das operações de criação e edição de produtos para contemplar o controle de estoque;
- documentação dos novos parâmetros de filtragem e ordenação da listagem de produtos.

---

### Endpoints implementados

Além dos endpoints originalmente disponibilizados pelo projeto, foram adicionadas novas rotas para suportar os requisitos do desafio.

#### Produtos

- Consulta da última alteração de preço de um produto.
- Suporte aos filtros por categoria, status e estoque mínimo.
- Suporte à ordenação por data de cadastro.

#### Categorias

- Cadastro de traduções.
- Consulta utilizando o parâmetro opcional `lang`.

#### Comentários

- Criação de comentários.
- Resposta a comentários existentes.
- Exclusão lógica de comentários.
- Registro de curtidas.
- Recuperação da árvore de comentários de um produto.

---

### Compatibilidade

Durante a implementação foi preservada a compatibilidade com os endpoints originalmente existentes.

As novas funcionalidades foram adicionadas de forma incremental, evitando alterações incompatíveis na interface pública da API e mantendo o funcionamento das operações já disponíveis antes do início do desenvolvimento.

Sempre que possível, novos comportamentos foram introduzidos por meio de parâmetros opcionais ou novos endpoints, reduzindo impactos sobre clientes já existentes.

---

### Processo de validação

A coleção do Postman foi utilizada durante todo o desenvolvimento como ferramenta de validação das funcionalidades implementadas.

Após cada alteração relevante foram realizados testes manuais para verificar:

- funcionamento dos fluxos de sucesso;
- tratamento de erros de validação;
- respostas para cenários de exceção;
- consistência dos códigos HTTP retornados;
- compatibilidade entre as novas funcionalidades e os endpoints existentes.

Dessa forma, a documentação permaneceu alinhada à implementação final da aplicação, permitindo que qualquer avaliador reproduza facilmente os testes realizados durante o desenvolvimento.

## Organização do desenvolvimento

Durante a execução do desafio foi adotada uma estratégia de desenvolvimento incremental, buscando manter cada demanda isolada desde sua análise até sua entrega.

Sempre que possível, cada requisito foi tratado como uma unidade independente de trabalho, contemplando sua própria issue, branch, implementação, validação e Pull Request. Essa abordagem permitiu manter um histórico organizado das alterações e facilitou a identificação do objetivo de cada modificação realizada no projeto.

### Gerenciamento das tarefas

As atividades foram organizadas utilizando Issues do GitHub, permitindo documentar o problema identificado, a solução proposta e o andamento de cada implementação.

Cada issue originou uma branch específica, seguindo um fluxo de desenvolvimento semelhante ao utilizado em ambientes profissionais, no qual cada funcionalidade ou correção possui seu próprio ciclo de vida.

Após a conclusão e validação de cada demanda, foi aberto um Pull Request contendo uma descrição detalhada das alterações realizadas, impactos da implementação e observações relevantes para revisão.

---

### Estratégia de branches

As branches foram organizadas de forma a isolar cada funcionalidade implementada.

Essa estratégia reduziu o risco de conflitos entre alterações distintas e permitiu que cada entrega fosse desenvolvida, revisada e validada individualmente antes de sua integração ao projeto principal.

Durante o desenvolvimento também foram realizados ajustes na organização das branches para manter o histórico do repositório mais limpo e facilitar a leitura do grafo de commits.

---

### Organização dos commits

Os commits foram realizados procurando representar etapas lógicas do desenvolvimento.

Sempre que possível, cada commit corresponde a uma alteração específica, como:

- correção de uma inconsistência;
- implementação de uma funcionalidade;
- criação de migrations;
- atualização dos testes;
- atualização da documentação.

Essa organização facilita a navegação pelo histórico do projeto e torna mais simples compreender a evolução da implementação.

---

### Desenvolvimento orientado por validação

Antes da implementação de cada demanda foi realizada uma análise do comportamento existente da aplicação para compreender a origem do problema ou identificar o melhor ponto de integração da nova funcionalidade.

Após cada alteração foram executadas validações utilizando a API, a suíte de testes e a coleção do Postman, garantindo que o comportamento esperado fosse preservado e reduzindo o risco de regressões.

Esse processo foi repetido durante todo o desenvolvimento, permitindo que cada entrega fosse concluída com sua respectiva validação antes do início da próxima atividade.

---

### Documentação do processo

Além da documentação presente neste README, todo o processo de desenvolvimento foi registrado em um **Development Log**, contendo a evolução cronológica das atividades realizadas.

Nesse documento estão descritos:

- análises realizadas antes de cada implementação;
- decisões técnicas adotadas;
- dificuldades encontradas;
- alternativas consideradas;
- justificativas para as soluções escolhidas;
- etapas de validação executadas durante o desenvolvimento.

O Development Log complementa este README ao registrar o processo de construção da solução, enquanto este documento apresenta uma visão consolidada das funcionalidades implementadas e das decisões técnicas adotadas.

## Suas Respostas, Dúvidas e Observações

Durante o desenvolvimento procurei seguir uma abordagem incremental, tratando cada requisito como uma entrega independente. Antes de iniciar qualquer implementação, realizei uma análise do comportamento existente da aplicação para compreender a origem do problema ou identificar a melhor forma de integrar a nova funcionalidade à arquitetura já existente.

Todas as alterações estruturais no banco de dados foram implementadas exclusivamente por meio de migrations, preservando a rastreabilidade das mudanças e permitindo que a estrutura da aplicação possa ser reproduzida em qualquer ambiente sem depender de uma versão específica do banco de dados.

Sempre que uma funcionalidade exigiu alterações na API, a documentação e a coleção do Postman foram atualizadas para manter consistência entre implementação e documentação.

Além das demandas explicitamente descritas no desafio, algumas melhorias técnicas foram realizadas por contribuírem diretamente para a qualidade do projeto, entre elas:

- eliminação de vulnerabilidades de SQL Injection por meio da utilização de Prepared Statements;
- implementação de transações para operações compostas de escrita;
- padronização da camada de persistência;
- organização da camada de serviços para reduzir duplicações e responsabilidades dos controllers;
- atualização dos testes unitários impactados pelas alterações;
- criação de documentação complementar para registrar as decisões de implementação.

Durante o desenvolvimento também procurei manter uma organização semelhante à utilizada em projetos reais, utilizando Issues, branches e Pull Requests independentes para cada demanda implementada. Essa estratégia facilitou o acompanhamento da evolução do projeto e manteve um histórico de alterações mais claro e rastreável.

### Principais desafios encontrados

Os desafios mais relevantes encontrados durante o desenvolvimento não estavam relacionados apenas à implementação das novas funcionalidades, mas também à compreensão do comportamento da aplicação existente.

Algumas inconsistências exigiram uma investigação mais aprofundada antes que qualquer alteração pudesse ser realizada, como os problemas de relacionamento entre produtos e categorias, o tratamento das categorias globais e a estrutura utilizada para recuperação das informações de auditoria.

Outro ponto de atenção foi garantir que as novas funcionalidades fossem integradas sem alterar o comportamento já esperado da API. Sempre que possível, novos recursos foram adicionados por meio de parâmetros opcionais ou novos endpoints, preservando a compatibilidade com a interface existente.

### Decisões de implementação

Durante a implementação foram adotadas algumas decisões visando facilitar futuras evoluções da aplicação.

Entre elas destacam-se:

- utilização de uma tabela específica para armazenar traduções de categorias, evitando alterações estruturais para inclusão de novos idiomas;
- implementação do sistema de comentários utilizando uma estrutura hierárquica, permitindo múltiplos níveis de resposta;
- evolução da estrutura de auditoria para registrar informações específicas sobre alterações de preço;
- centralização das regras de validação e padronização dos retornos da camada de serviços.

Essas decisões buscaram manter o código mais modular, reduzir acoplamentos e facilitar a manutenção futura da aplicação.

### Documentação complementar

Este README apresenta uma visão consolidada das funcionalidades implementadas, das correções realizadas e das principais decisões técnicas adotadas durante o desenvolvimento.

Como complemento, foi elaborado um **Development Log** contendo o registro cronológico de todo o processo de desenvolvimento, incluindo análises realizadas, alternativas consideradas, justificativas para as soluções escolhidas e etapas de validação executadas ao longo do projeto.

Esse documento foi mantido separado para preservar a objetividade deste README e, ao mesmo tempo, disponibilizar um histórico detalhado do processo de construção da solução.