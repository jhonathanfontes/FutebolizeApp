## Como Utilizar

### Adicionando um Novo Arquivo CSS ou JS

1.  **Adicione o arquivo:** Crie seu novo arquivo `.css` ou `.js` dentro do diretório apropriado em `resources/development/assets/`.
2.  **Nomeie corretamente:** Se a ordem de carregamento for importante, adicione um prefixo numérico ao nome do arquivo (ex: `03-meu-novo-script.js`).
3.  **Pronto!** O `AssetManager` irá detectar e carregar automaticamente o novo arquivo no próximo refresh da página em ambiente de desenvolvimento.

### Atualizando os Arquivos de Produção

Após adicionar ou modificar os assets de desenvolvimento, você precisa rodar o processo de build do seu projeto para gerar os arquivos `app.min.css` e `app.min.js` atualizados no diretório `public/assets/`.

#### Comando de Build: `php spark build:assets`

Este comando Spark personalizado é responsável por concatenar e minificar seus assets de desenvolvimento para uso em produção.

**Propósito:**

*   Combinar todos os arquivos CSS individuais em um único `app.min.css`.
*   Combinar todos os arquivos JavaScript individuais em um único `app.min.js`.
*   Minificar ambos os arquivos para reduzir o tamanho e otimizar o tempo de carregamento.

**Pré-requisitos:**

*   A biblioteca `matthiasmullie/minify` deve estar instalada via Composer (`composer require matthiasmullie/minify`).

**Como Rodar:**

Abra seu terminal na raiz do projeto e execute:

```bash
php spark build:assets
```

**O que ele faz:**

1.  Procura todos os arquivos `.css` em `app/Resources/development/assets/css/`.
2.  Concatena e minifica esses arquivos em `public/assets/css/app.min.css`.
3.  Procura todos os arquivos `.js` em `app/Resources/development/assets/js/`.
4.  Concatena e minifica esses arquivos em `public/assets/js/app.min.js`.

**Verificação:**

Após a execução bem-sucedida do comando, você pode verificar a pasta `public/assets/css/` e `public/assets/js/` para confirmar que `app.min.css` e `app.min.js` foram criados ou atualizados. Você também pode inspecionar o conteúdo desses arquivos.
