<?php

class PessoasController
{
    private PDO $pdo;
    private const CONTENT_HEADER = 'Content-Type: application/json; charset=utf-8';

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function retornaResultado(array $dados): void
    {
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function telefoneValido(string $telefone): bool
    {
        if ($telefone[0] !== '+') {
            return false;
        }
        $tamanho = strlen($telefone);
        if ($tamanho < 2 || $tamanho > 15) {
            return false;
        }
        for ($i = 1; $i < $tamanho; $i++) {
            if ($telefone[$i] < '0' || $telefone[$i] > '9') {
                return false;
            }
        }
        return true;
    }

    public function listar(): void
    {
        header(self::CONTENT_HEADER);

        $sql = 'SELECT id, nome, curso, periodo, status, criado_em
                FROM pessoas
                ORDER BY id ASC';

        $stmt = $this->pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->retornaResultado($usuarios);
    }

    public function buscarPorId(): void
    {
        header(self::CONTENT_HEADER);

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $sql = 'SELECT id, nome, documento, telefone,email,
                       curso, periodo, observacoes, status,
                       criado_em, atualizado_em
                FROM pessoas
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode((['erro' => 'Usuário não encontrado']));
            return;
        }

        $this->retornaResultado($usuario);
    }

    public function criar(): void
    {
        header(self::CONTENT_HEADER);

        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

        if ($nome === '' || $documento === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome, documento e email são obrigatórios']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail inválido']);
            return;
        }

        if ($telefone !== '' && !$this->telefoneValido($telefone)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Telefone inválido. Informe no formato +XXXXXXXXXXXXX']);
            return;
        }

        if ($curso !== '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Curso deve ter no máximo 120 caracteres.']);
            return;
        }

        if ($periodo !== '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Período deve ter no máximo 20 caracteres.']);
            return;
        }

        if ($observacoes !== '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Observações deve ter no máximo 1000 caracteres.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas (
                        nome, documento, telefone, email,
                        curso, periodo, observacoes, status
                    ) VALUES (
                        :nome, :documento, :telefone, :email,
                        :curso, :periodo, :observacoes, :status 
                    )';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':documento', $documento);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':curso', $curso);
            $stmt->bindValue(':periodo', $periodo);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'messagem' => 'Pessoa cadastrado com sucesso',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao Cadastrar Pessoa']);
        }
    }

    public function atualizar(): void
    {
        header(self::CONTENT_HEADER);

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

        if (!$id || $nome === '' || $documento === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID, nome, documento e email são obrigatórios']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail inválido']);
            return;
        }

        if ($telefone !== '' && !$this->telefoneValido($telefone)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Telefone inválido. Informe no formato +XXXXXXXXXXXXX']);
            return;
        }

        if ($curso !== '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Curso deve ter no máximo 120 caracteres.']);
            return;
        }

        if ($periodo !== '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Período deve ter no máximo 20 caracteres.']);
            return;
        }

        if ($observacoes !== '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Observações deve ter no máximo 1000 caracteres.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'UPDATE pessoas 
                    SET nome = :nome,
                        documento = :documento,
                        telefone = :telefone,
                        email = :email,
                        curso = :perfil,
                        periodo = :periodo,
                        observacoes = :observacoes,
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':documento', $documento);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':curso', $curso);
            $stmt->bindValue(':periodo', $periodo);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'messagem' => 'Pessoa cadastrado com sucesso',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao Cadastrar Pessoa']);
        }
    }

    public function exluir(): void {
        header(self::CONTENT_HEADER);

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID Inválido']);
            return;
        }

        try {
            $sql = 'UPDATE pessoas SET status = "inativo" WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Pessoa inativado com sucesso']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inativar usuário']);
        }
    }
}
