<?php

require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/FrontendController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TipoAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentoController.php';
require_once __DIR__ . '/app/Middlewares/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

switch ($controller) {
    case 'auth':
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;

            case 'entrar':
                $authController->entrar();
                break;

            case 'dashboard':
                $authController->dashboard();
                break;

            case 'logout':
                $authController->logout();
                break;

            default:
                http_response_code(404);
                echo 'Ação de autenticação não encontrada.';
        }
        break;

    case 'frontend':
        $frontendController = new FrontendController();

        switch ($action) {
            case 'pessoas':
                $frontendController->pessoas();
                break;

            case 'tipos':
                $frontendController->tiposAtendimentos();
                break;

            case 'atendimentos':
                $frontendController->atendimentos();
                break;

            default:
                http_response_code(404);
                echo 'Ação do frontend não encontrada.';
        }
        break;

    case 'usuarios':
        exigirAutenticacao();
        $usuariosController = new UsuariosController();

        switch ($action) {
            case 'listar':
                $usuariosController->listar();
                break;

            case 'buscarPorId':
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
                http_response_code(404);
                echo 'Ação de usuários não encontrada.';
        }
        break;

    case 'pessoas':
        exigirAutenticacao();
        $pessoasController = new PessoasController();

        switch ($action) {
            case 'listar':
                $pessoasController->listar();
                break;

            case 'buscar':
            case 'buscarPorId':
                $pessoasController->buscarPorId();
                break;

            case 'criar':
                $pessoasController->criar();
                break;

            case 'atualizar':
                $pessoasController->atualizar();
                break;

            case 'inativar':
                $pessoasController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Ação de pessoas não encontrada.';
        }
        break;

    case 'tipos':
    case 'tipos_atendimentos':
        exigirAutenticacao();
        $tipoAtendimentosController = new TipoAtendimentosController();

        switch ($action) {
            case 'listar':
                $tipoAtendimentosController->listar();
                break;

            case 'buscar':
            case 'buscarPorId':
                $tipoAtendimentosController->buscarPorId();
                break;

            case 'criar':
                $tipoAtendimentosController->criar();
                break;

            case 'atualizar':
                $tipoAtendimentosController->atualizar();
                break;

            case 'inativar':
            case 'excluir':
                $tipoAtendimentosController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Ação de tipos de atendimento não encontrada.';
        }
        break;

    case 'atendimentos':
        exigirAutenticacao();
        $atendimentoController = new AtendimentoController();

        switch ($action) {
            case 'listar':
                $atendimentoController->listar();
                break;

            case 'buscarPorId':
                $atendimentoController->buscarPorId();
                break;

            case 'criar':
                $atendimentoController->criar();
                break;

            case 'alterarStatus':
                $atendimentoController->alterarStatus();
                break;

            case 'atualizar':
                $atendimentoController->atualizar();
                break;

            case 'excluir':
                $atendimentoController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Ação de atendimentos não encontrada.';
        }
        break;

    default:
        http_response_code(404);
        echo 'Controller não encontrado.';
}
