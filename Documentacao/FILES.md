# Documentação do FilesController

Este documento descreve o funcionamento e a utilização do `FilesController`, responsável por servir arquivos armazenados em diretórios não públicos de forma segura.

## Propósito

O `FilesController` tem como objetivo principal permitir o acesso seguro a arquivos que não devem ser expostos diretamente através da pasta `public/` do servidor web. Isso é ideal para arquivos gerados por usuários (uploads), documentos privados ou qualquer outro conteúdo que precise ser acessado via URL, mas com controle de acesso e proteção contra exposição indevida.

## Localização dos Arquivos

O `FilesController` está configurado para servir arquivos exclusivamente do diretório:

*   `writable/uploads/`

Qualquer arquivo que você queira disponibilizar através deste controller deve ser salvo dentro desta pasta (ou em subpastas dela).

## Rota

O `FilesController` intercepta requisições através da seguinte rota, definida em `app/Routes/web.php`:

```php
$routes->get('files/(:any)', 'FilesController::serve/$1');
```

Isso significa que qualquer URL que comece com `/files/` será processada pelo método `serve` do `FilesController`.

## Segurança

O `FilesController` implementa medidas de segurança cruciais para proteger seu sistema:

*   **Prevenção de Path Traversal:** Ele garante que nenhum usuário mal-intencionado possa acessar arquivos fora do diretório `writable/uploads/` usando caminhos como `../` na URL.
*   **Verificação de Existência e Leitura:** O controller verifica se o caminho solicitado corresponde a um arquivo real e se este arquivo pode ser lido pelo servidor antes de tentar entregá-lo.
*   **Controle de Acesso:** Ao centralizar o serviço de arquivos em um controller, você pode facilmente adicionar lógicas de autenticação ou autorização ao método `serve` para restringir o acesso apenas a usuários permitidos, se necessário.

## Como Utilizar

Para acessar um arquivo gerenciado pelo `FilesController`, você deve construir a URL relativa ao diretório `writable/uploads/`.

**Exemplo:**

*   **Caminho do arquivo no sistema:** `D:\Xampp\htdocs\FutebolizeApp\writable\uploads\documentos\relatorio.pdf`
*   **URL para acessar este arquivo:** `http://localhost:8080/files/documentos/relatorio.pdf`

*   **Caminho do arquivo no sistema:** `D:\Xampp\htdocs\FutebolizeApp\writable\uploads\imagens\perfil_usuario.png`
*   **URL para acessar este arquivo:** `http://localhost:8080/files/imagens/perfil_usuario.png`

## Considerações

*   **Apenas Serviço de Arquivos:** Este controller foi projetado *apenas* para servir (ler/download) arquivos. Ele **não** inclui funcionalidades para upload, exclusão ou listagem de arquivos. Essas operações devem ser implementadas em outros controllers, com suas próprias lógicas de segurança.
*   **MIME Types:** O controller tenta determinar automaticamente o `Content-Type` (MIME Type) do arquivo para que o navegador o exiba ou baixe corretamente.
*   **Logs:** Em caso de erro interno ao servir o arquivo, um log pode ser gerado (se descomentado no código), auxiliando na depuração.
