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