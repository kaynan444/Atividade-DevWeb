# Atividade Avaliativa: Fundamentos de PHP e Integração Web

Estudante: Kaynan Andrey Hermano Siqueira
Disciplina: Desenvolvimento Web

## Conteúdo do repositório

- `parte1-respostas.md`: respostas das 5 questões teóricas (Parte 1).
- `tech-experience/`: projeto prático "Tech Experience 2026" (Parte 2).

## Estrutura do projeto

```
tech-experience/
  index.html     formulário de inscrição (HTML5 semântico)
  style.css      estilo responsivo em tons claros
  script.js      contador de caracteres e validação das oficinas
  processar.php  validação no servidor e resposta segura (PHP 8+)
```

## Como executar

Dentro da pasta `tech-experience/`, rode:

```
php -S localhost:8000
```

Depois abra `http://localhost:8000/index.html` no navegador.

## Testes realizados

- Envio válido: a página de confirmação mostra os dados cadastrados.
- Teste de XSS: ao digitar `<script>alert("XSS")</script>` no nome, o texto aparece literal na resposta.
- Acesso direto a `http://localhost:8000/processar.php` (GET): a aplicação retorna a mensagem de bloqueio com status 405.
- DevTools, aba Network: método POST, status 200 e parâmetros visíveis no Payload.
