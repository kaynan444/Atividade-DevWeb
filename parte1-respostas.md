# Parte 1: Exercício Teórico

Estudante: Kaynan Andrey Hermano Siqueira
Disciplina: Desenvolvimento Web
Atividade: Fundamentos de PHP e Integração Web

## Questão 1: Ciclo de requisição-resposta e execução no servidor

**a)** O navegador apenas faz uma requisição HTTP para `http://localhost:8000/index.php`. Quem a recebe é o servidor, que identifica a extensão `.php` e entrega o arquivo ao interpretador PHP. O interpretador executa tudo o que está entre `<?php ... ?>`, e somente o resultado dessa execução segue na resposta HTTP. O código-fonte permanece no servidor e nunca trafega pela rede. Além disso, o navegador não possui interpretador PHP embutido, então não saberia executar o código mesmo que o recebesse.

Ao abrir o arquivo com duplo clique (`file://`), não existe servidor nem requisição HTTP. O navegador apenas lê o arquivo do disco, e como não sabe o que fazer com um `.php`, exibe o texto puro ou sugere o download.

**b)** O interpretador PHP lê o script, executa a lógica (variáveis, condições, laços, leitura de `$_GET` e `$_POST`) e substitui cada bloco PHP pela saída que ele produziu. O conteúdo entregue como resposta é texto puro, normalmente HTML, que o navegador renderiza. Dependendo do `Content-Type` enviado, também pode ser JSON, CSS ou outro formato de texto.

## Questão 2: GET versus POST

**a)** No GET, os dados são anexados à URL na query string (`?busca=php&pagina=2`). Ficam visíveis na barra de endereço, no histórico do navegador e em logs de servidor, e têm limite de tamanho. No POST, os dados viajam no corpo (body) da requisição HTTP, fora da URL.

O GET é indicado para operações de consulta que podem ser repetidas sem efeito colateral (buscas, filtros, paginação), e a URL pode ser favoritada ou compartilhada. O POST é indicado para criar ou alterar dados (cadastros, login, envio de formulários) e para informações que não devem aparecer na URL.

**b)** O POST apenas tira os dados da URL, mas não os protege. O corpo da requisição trafega em texto claro pela rede, então qualquer pessoa no caminho (por exemplo, em um Wi-Fi público) consegue lê-lo, e o próprio usuário o vê no DevTools. O sigilo só é garantido pelo HTTPS, que criptografa todo o tráfego com TLS/SSL, seja GET ou POST. Essa é a tecnologia indispensável para proteger os dados na rede.

## Questão 3: Normalização, validação e escape de saída

**a)** A normalização prepara e padroniza o dado antes de qualquer regra, como remover espaços nas pontas. A validação verifica se o dado cumpre as regras de negócio e os tipos esperados: formato, tamanho e pertencimento a uma lista permitida. O escape de saída converte caracteres especiais em entidades HTML no momento de imprimir o dado na página, para que ele seja tratado como texto e nunca como código.

Uma etapa não substitui a outra. Um dado válido ainda pode conter caracteres perigosos para o HTML, e o escape não impede que dados incorretos sejam aceitos.

**b)** Exemplos de funções nativas do PHP:

- Normalização: `trim()`, `mb_strtolower()`
- Validação: `filter_var($email, FILTER_VALIDATE_EMAIL)`, `mb_strlen()`, `is_array()`, `in_array($valor, $lista, true)`
- Escape de saída: `htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')`

O escape evita o XSS (Cross-Site Scripting). Sem ele, se um usuário digitar `<script>alert(1)</script>` no campo nome e a aplicação imprimir esse valor direto no HTML, o navegador de quem visualizar a página executará o script. Isso permite roubar cookies, redirecionar para páginas falsas ou alterar o conteúdo exibido. Com `htmlspecialchars`, `<` vira `&lt;` e `"` vira `&quot;`, e o texto aparece de forma literal e inofensiva.

## Questão 4: Validação no cliente versus no servidor

**a)** O navegador pertence ao usuário e, por isso, é um ambiente não confiável. Os atributos HTML5 e o JavaScript podem ser contornados com facilidade. Se o `processar.php` grava `$_POST` sem nenhuma checagem, qualquer dado inválido ou malicioso entra direto na aplicação, comprometendo a integridade dos dados e abrindo espaço para ataques como XSS e injeção de SQL.

**b)** Dois cenários em que a validação do cliente é ignorada: o usuário abre o DevTools (inspecionar elemento) e remove `required` e `minlength` ou altera o `type` do campo; ou envia a requisição POST diretamente por cURL, Postman ou um script, sem passar pelo formulário. Também é possível simplesmente desativar o JavaScript no navegador.

O papel da validação no cliente é melhorar a experiência do usuário, avisando o erro imediatamente, sem ida e volta ao servidor. O papel da validação no servidor é garantir a integridade, a consistência e a segurança dos dados. Somente a validação do servidor é confiável, e por isso ela é obrigatória.

## Questão 5: Superglobais e entradas complexas

**a)** Quando um campo não é enviado (um checkbox desmarcado, por exemplo), a chave não existe em `$_POST`, e acessá-la gera o aviso `Undefined array key`. O operador de coalescência nula `??` retorna o valor da esquerda se a chave existir e não for `null`, e o valor da direita caso contrário. Em `$_POST['nome'] ?? ''`, o resultado é uma string vazia, sem aviso.

**b)** No HTML, o sufixo `[]` no atributo `name` (`name="oficinas[]"`) faz o PHP montar todos os valores marcados em um array dentro de `$_POST['oficinas']`. No backend, o procedimento é:

1. Ler com `$_POST['oficinas'] ?? []`.
2. Confirmar que é uma lista com `is_array()` e que não está vazia.
3. Percorrer cada item e validá-lo contra uma lista permitida (whitelist) com `in_array($item, $permitidos, true)`.

O terceiro argumento `true` ativa a comparação estrita de tipo. A whitelist impede que alguém envie, por cURL ou pelo DevTools, uma oficina que não existe.
