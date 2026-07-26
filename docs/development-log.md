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