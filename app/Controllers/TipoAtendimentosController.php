<?php

class TipoAtendimentosController
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

    public function listar(): void
    {
        header(self::CONTENT_HEADER);

        $sql = 'SELECT id, nome, status, criado_em
                FROM tipos_atendimentos
                ORDER BY id ASC';

        $stmt = $this->pdo->query($sql);
        $tiposAtendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->retornaResultado($tiposAtendimentos);
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

        $sql = 'SELECT id, nome, descricao, status, criado_em, atualizado_em
                FROM tipos_atendimentos
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $tipoAtendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tipoAtendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Tipo de atendimento não encontrado']);
            return;
        }

        $this->retornaResultado($tipoAtendimento);
    }

    public function criar(): void
    {
        header(self::CONTENT_HEADER);

        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

        if ($nome === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome é obrigatório']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO tipos_atendimentos (nome, descricao, status)
                    VALUES (:nome, :descricao, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'messagem' => 'Tipo de atendimento cadastrado com sucesso',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar tipo de atendimento']);
        }
    }

    public function atualizar(): void
    {
        header(self::CONTENT_HEADER);

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

        if (!$id || $nome === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID e nome são obrigatórios']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'UPDATE tipos_atendimentos
                    SET nome = :nome,
                        descricao = :descricao,
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Tipo de atendimento atualizado com sucesso'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar tipo de atendimento']);
        }
    }

    public function exluir(): void
    {
        header(self::CONTENT_HEADER);

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID Inválido']);
            return;
        }

        try {
            $sql = 'UPDATE tipos_atendimentos SET status = "inativo" WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Tipo de atendimento inativado com sucesso']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inativar tipo de atendimento']);
        }
    }
}
