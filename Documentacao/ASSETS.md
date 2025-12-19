# Documentação do Pipeline de Assets

Este documento detalha o funcionamento do pipeline de assets do projeto, que foi projetado para otimizar o fluxo de trabalho em desenvolvimento e a performance em produção.

## Objetivo

O pipeline de assets serve a dois propósitos principais:

1.  **Em Desenvolvimento (`ENVIRONMENT=development`):**
    *   Carregar arquivos CSS e JavaScript individualmente.
    *   Facilitar a depuração, permitindo que as ferramentas de desenvolvedor do navegador mostrem exatamente qual arquivo está sendo usado.
    *   Permitir o hot-reloading de arquivos individuais sem a necessidade de um processo de build a cada alteração.

2.  **Em Produção (`ENVIRONMENT=production`):**
    *   Carregar um único arquivo CSS (`app.min.css`) e um único arquivo JavaScript (`app.min.js`).
    *   Reduzir o número de requisições HTTP, melhorando significativamente o tempo de carregamento da página.
    *   Aplicar versionamento automático (`?v=...`) aos arquivos para invalidar o cache do navegador sempre que o conteúdo for atualizado.

---

## Estrutura de Diretórios

A estrutura de assets é dividida entre o ambiente de desenvolvimento e o de produção.

### Desenvolvimento

-   **Localização:** `app/Resources/development/assets/`
-   **Descrição:** Contém os arquivos-fonte de CSS e JavaScript. Estes arquivos são servidos em ambiente de desenvolvimento através de um controller (`ResourcesController`) para maior segurança.

    ```
    app/
    └── Resources/
        └── development/
            └── assets/
                ├── css/
                │   ├── 01-reset.css
                │   └── 02-main.css
                └── js/
                    ├── 01-library.js
                    └── 02-app.js
    ```

-   **Ordem de Carregamento:** A ordem em que os arquivos são carregados é controlada por um prefixo numérico (ex: `01-`, `02-`). Os arquivos são carregados em ordem alfabética.

### Produção

-   **Localização:** `public/assets/`
-   **Descrição:** Contém os arquivos finais, que são o resultado da concatenação e minificação dos arquivos de desenvolvimento. **Estes arquivos são gerados por um processo de build externo e não devem ser editados manualmente.**

    ```
    public/
    └── assets/
        ├── css/
        │   └── app.min.css
        └── js/
            └── app.min.js
    ```

---

## Fluxo de Implementação

O fluxo de carregamento de assets em **desenvolvimento** é gerenciado inteiramente pelo framework CodeIgniter para garantir segurança e consistência.

1.  **`app/Services/AssetManager.php`**:
    -   Quando o ambiente é `development`, o `AssetManager` gera as URLs para os assets de desenvolvimento (ex: `/resources/development/assets/css/01-main.css`).

2.  **`app/Config/Routes.php`** e **`app/Routes/web.php`**:
    -   Uma rota `get('resources/(:any)', 'ResourcesController::serve/$1')` captura todas as requisições para o diretório virtual `/resources`.

3.  **`app/Controllers/ResourcesController.php`**:
    -   Este controller é o responsável por servir os arquivos.
    -   **Ele bloqueia todas as requisições se `ENVIRONMENT` for `production`**, garantindo que ele nunca seja usado em produção.
    -   Ele valida o caminho do arquivo para prevenir ataques de *path traversal* (`../`).
    -   Se o arquivo for válido e seguro, ele é retornado com o `Content-Type` (MIME) correto.

4.  **`app/Controllers/Web/BaseController.php`**:
    -   Este controller orquestra o processo, chamando o `AssetManager` e passando as tags HTML geradas (`<link>`, `<script>`) para a view.

5.  **`app/Templates/layouts/base.html.twig`**:
    -   O template renderiza as tags no HTML final.

Em **produção**, o fluxo é mais simples: o `AssetManager` gera URLs diretas para os arquivos em `public/assets/`, que são servidos diretamente pelo servidor web.