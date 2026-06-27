<?php

require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TipoAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentoController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

if ($controller === 'usuarios') {
    $usuariosController = new UsuariosController();

    switch ($action) {
        case 'listar':
            $usuariosController->listar();
            break;

        case 'buscar':
            $usuariosController->buscarPorId();
            break;

        case 'criar':
            $usuariosController->criar();
            break;

        case 'atualizar':
            $usuariosController->atualizar();
            break;

        case 'excluir':
            $usuariosController->excluir();
            break;

        default:
            echo 'Ação de usuários não encontrada.';
            break;
    }
} elseif ($controller === 'pessoas') {
    $pessoasController = new PessoasController();

    switch ($action) {
        case 'listar':
            $pessoasController->listar();
            break;

        case 'buscar':
            $pessoasController->buscarPorId();
            break;

        case 'criar':
            $pessoasController->criar();
            break;

        case 'atualizar':
            $pessoasController->atualizar();
            break;

        case 'excluir':
            $pessoasController->exluir();
            break;

        default:
            echo 'Ação de pessoas não encontrada.';
            break;
    }
} elseif ($controller === 'tipos_atendimentos') {
    $tipoAtendimentosController = new TipoAtendimentosController();

    switch ($action) {
        case 'listar':
            $tipoAtendimentosController->listar();
            break;

        case 'buscar':
            $tipoAtendimentosController->buscarPorId();
            break;

        case 'criar':
            $tipoAtendimentosController->criar();
            break;

        case 'atualizar':
            $tipoAtendimentosController->atualizar();
            break;

        case 'excluir':
            $tipoAtendimentosController->exluir();
            break;

        default:
            echo 'Ação de tipos de atendimento não encontrada.';
            break;
    }
} elseif ($controller === 'atendimentos') {
    $atendimentoController = new AtendimentoController();

    switch ($action) {
        case 'listar':
            $atendimentoController->listar();
            break;

        case 'buscar':
            $atendimentoController->buscarPorId();
            break;

        case 'criar':
            $atendimentoController->criar();
            break;

        case 'atualizar':
            $atendimentoController->atualizar();
            break;

        case 'excluir':
            $atendimentoController->exluir();
            break;

        default:
            echo 'Ação de atendimentos não encontrada.';
            break;
    }
} else {
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto em execução. Exemplos de rotas:</p>';
    echo '<ul>';
    echo '<li>?controller=usuarios&action=listar</li>';
    echo '<li>?controller=pessoas&action=listar</li>';
    echo '<li>?controller=tipos_atendimentos&action=listar</li>';
    echo '<li>?controller=atendimentos&action=listar</li>';
    echo '</ul>';
}
