<?php

class AtendimentoController
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

        $sql = 'SELECT a.id,
                       a.descricao,
                       a.status,
                       a.data_atendimento,
                       a.horario_atendimento,
                       a.criado_em,
                       p.nome AS pessoa_nome,
                       t.nome AS tipo_atendimento,
                       u.nome AS usuario_nome
                FROM atendimentos a
                INNER JOIN pessoas p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipos_atendimento_id
                INNER JOIN usuarios u ON u.id = a.usuario_id
                ORDER BY a.id ASC';

        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->retornaResultado($atendimentos);
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

        $sql = 'SELECT id, pessoa_id, tipos_atendimento_id, usuario_id,
                       descricao, status, data_atendimento, horario_atendimento,
                       observacao_final, criado_em, atualizado_em
                FROM atendimentos
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado']);
            return;
        }

        $this->retornaResultado($atendimento);
    }

    public function criar(): void
    {
        header(self::CONTENT_HEADER);

        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipos_atendimento_id = filter_input(INPUT_POST, 'tipos_atendimento_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $descricao = trim($_POST['descricao'] ?? '');
        $status = trim($_POST['status'] ?? 'aberto');
        $data_atendimento = trim($_POST['data_atendimento'] ?? '');
        $horario_atendimento = trim($_POST['horario_atendimento'] ?? '');
        $observacao_final = trim($_POST['observacao_final'] ?? '');

        if (!$pessoa_id || !$tipos_atendimento_id || !$usuario_id || $descricao === '' || $data_atendimento === '' || $horario_atendimento === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Pessoa, tipo, usuário, descrição, data e horário são obrigatórios']);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos (
                        pessoa_id, tipos_atendimento_id, usuario_id,
                        descricao, status, data_atendimento,
                        horario_atendimento, observacao_final
                    ) VALUES (
                        :pessoa_id, :tipos_atendimento_id, :usuario_id,
                        :descricao, :status, :data_atendimento,
                        :horario_atendimento, :observacao_final
                    )';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':tipos_atendimento_id', $tipos_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':horario_atendimento', $horario_atendimento);
            $stmt->bindValue(':observacao_final', $observacao_final);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'messagem' => 'Atendimento cadastrado com sucesso',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar atendimento']);
        }
    }

    public function atualizar(): void
    {
        header(self::CONTENT_HEADER);

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipos_atendimento_id = filter_input(INPUT_POST, 'tipos_atendimento_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $descricao = trim($_POST['descricao'] ?? '');
        $status = trim($_POST['status'] ?? 'aberto');
        $data_atendimento = trim($_POST['data_atendimento'] ?? '');
        $horario_atendimento = trim($_POST['horario_atendimento'] ?? '');
        $observacao_final = trim($_POST['observacao_final'] ?? '');

        if (!$id || !$pessoa_id || !$tipos_atendimento_id || !$usuario_id || $descricao === '' || $data_atendimento === '' || $horario_atendimento === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID, pessoa, tipo, usuário, descrição, data e horário são obrigatórios']);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos
                    SET pessoa_id = :pessoa_id,
                        tipos_atendimento_id = :tipos_atendimento_id,
                        usuario_id = :usuario_id,
                        descricao = :descricao,
                        status = :status,
                        data_atendimento = :data_atendimento,
                        horario_atendimento = :horario_atendimento,
                        observacao_final = :observacao_final
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':tipos_atendimento_id', $tipos_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':horario_atendimento', $horario_atendimento);
            $stmt->bindValue(':observacao_final', $observacao_final);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Atendimento atualizado com sucesso'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar atendimento']);
        }
    }

    public function excluir(): void
    {
        header(self::CONTENT_HEADER);

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID Inválido']);
            return;
        }

        try {
            $sql = 'DELETE FROM atendimentos WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Atendimento excluído com sucesso']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir atendimento']);
        }
    }
}
