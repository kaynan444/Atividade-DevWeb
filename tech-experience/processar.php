<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

/** Escapa qualquer texto antes de imprimir no HTML (proteção contra XSS). */
function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

/** Lê um campo de texto do POST, normalizando com trim(). Ignora valores que não sejam string. */
function campo(string $chave): string
{
    $valor = $_POST[$chave] ?? '';
    return is_string($valor) ? trim($valor) : '';
}

function topo(string $titulo): void
{
    echo '<!DOCTYPE html><html lang="pt-BR"><head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($titulo) . '</title>';
    echo '<link rel="stylesheet" href="style.css">';
    echo '</head><body><main class="container">';
}

function rodape(): void
{
    echo '</main></body></html>';
}

// 1. Verificação do método HTTP
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    topo('405 Método não permitido');
    echo '<section class="card result-card result-error">';
    echo '<h2>405 Método não permitido</h2>';
    echo '<p>Esta página só aceita dados enviados pelo formulário (método POST).</p>';
    echo '<a class="btn link-back" href="index.html">Voltar ao formulário</a>';
    echo '</section>';
    rodape();
    exit;
}

// Listas permitidas (whitelists) e rótulos para exibição
$niveis = [
    'iniciante'     => 'Iniciante',
    'intermediario' => 'Intermediário',
    'avancado'      => 'Avançado',
];
$turnos = [
    'manha' => 'Manhã',
    'noite' => 'Noite',
];
$oficinasPermitidas = [
    'html_css'    => 'HTML e CSS',
    'js_dom'      => 'JavaScript e DOM',
    'php_backend' => 'PHP e Back-end',
];

// 2. Leitura e normalização
$nome        = campo('nome');
$email       = campo('email');
$experiencia = campo('experiencia');
$turno       = campo('turno');
$mensagem    = campo('mensagem');
$oficinasBruto = $_POST['oficinas'] ?? [];

// 3. Validação
$erros = [];

if (mb_strlen($nome, 'UTF-8') < 3) {
    $erros[] = 'O nome deve ter pelo menos 3 caracteres.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Informe um e-mail válido.';
}

if ($experiencia === '') {
    $erros[] = 'Selecione o nível de experiência.';
} elseif (!in_array($experiencia, array_keys($niveis), true)) {
    $erros[] = 'O nível de experiência informado não é válido.';
}

if ($turno === '') {
    $erros[] = 'Selecione o turno de preferência.';
} elseif (!in_array($turno, array_keys($turnos), true)) {
    $erros[] = 'O turno informado não é válido.';
}

$oficinas = [];
if (!is_array($oficinasBruto)) {
    $erros[] = 'As oficinas foram enviadas em formato inválido.';
} elseif (count($oficinasBruto) === 0) {
    $erros[] = 'Selecione ao menos uma oficina.';
} else {
    foreach ($oficinasBruto as $item) {
        if (!is_string($item) || !in_array($item, array_keys($oficinasPermitidas), true)) {
            $erros[] = 'Foi enviada uma oficina que não existe na lista permitida.';
            $oficinas = [];
            break;
        }
        $oficinas[] = $item;
    }
    $oficinas = array_values(array_unique($oficinas));
}

if (mb_strlen($mensagem, 'UTF-8') > 200) {
    $erros[] = 'A mensagem deve ter no máximo 200 caracteres.';
}

// 4. Renderização segura
if (count($erros) > 0) {
    topo('Corrija os dados informados');
    echo '<section class="card result-card result-error">';
    echo '<h2>Não foi possível concluir a inscrição</h2>';
    echo '<ul class="error-list">';
    foreach ($erros as $erro) {
        echo '<li>' . e($erro) . '</li>';
    }
    echo '</ul>';
    echo '<a class="btn link-back" href="index.html">Voltar ao formulário</a>';
    echo '</section>';
    rodape();
    exit;
}

topo('Inscrição confirmada');
echo '<section class="card result-card result-ok">';
echo '<h2>Inscrição confirmada</h2>';
echo '<dl class="data-list">';
echo '<dt>Nome</dt><dd>' . e($nome) . '</dd>';
echo '<dt>E-mail</dt><dd>' . e($email) . '</dd>';
echo '<dt>Nível de experiência</dt><dd>' . e($niveis[$experiencia]) . '</dd>';
echo '<dt>Turno</dt><dd>' . e($turnos[$turno]) . '</dd>';
echo '<dt>Oficinas</dt><dd>';
$nomesOficinas = [];
foreach ($oficinas as $codigo) {
    $nomesOficinas[] = e($oficinasPermitidas[$codigo]);
}
echo implode(', ', $nomesOficinas) . '</dd>';
echo '<dt>Mensagem</dt><dd>' . ($mensagem !== '' ? nl2br(e($mensagem)) : 'Não informada') . '</dd>';
echo '</dl>';
echo '<a class="btn link-back" href="index.html">Nova inscrição</a>';
echo '</section>';
rodape();
