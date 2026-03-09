<?php
/**
 * Funções próprias para Admin
 * Ficheiro centralizado com todas as funções de administração
 */

// Verificar se é admin
function verificar_admin() {
    if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo_utilizador'] !== 'admin') {
        header("Location: index.php");
        exit;
    }
}

// Obter lista de utilizadores
function obter_utilizadores($pdo) {
    $stmt = $pdo->query("SELECT id, nome, email, tipo_utilizador, data_criacao FROM utilizadores ORDER BY data_criacao DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter utilizador por ID
function obter_utilizador_por_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Alterar tipo de utilizador
function alterar_tipo_utilizador($pdo, $id_utilizador, $novo_tipo) {
    $tipos_validos = ['utilizador', 'admin'];
    
    if (!in_array($novo_tipo, $tipos_validos)) {
        return false;
    }
    
    $stmt = $pdo->prepare("UPDATE utilizadores SET tipo_utilizador = :tipo WHERE id = :id");
    return $stmt->execute(['tipo' => $novo_tipo, 'id' => $id_utilizador]);
}

// Eliminar utilizador
function eliminar_utilizador($pdo, $id_utilizador) {
    // Eliminar campanhas do utilizador
    $stmt_campanhas = $pdo->prepare("DELETE FROM campanhas WHERE id_criador = :id");
    $stmt_campanhas->execute(['id' => $id_utilizador]);
    
    // Eliminar donativos do utilizador
    $stmt_donativos = $pdo->prepare("DELETE FROM donativos WHERE id_utilizador = :id");
    $stmt_donativos->execute(['id' => $id_utilizador]);
    
    // Eliminar utilizador
    $stmt = $pdo->prepare("DELETE FROM utilizadores WHERE id = :id");
    return $stmt->execute(['id' => $id_utilizador]);
}

// Obter estatísticas gerais
function obter_estatisticas($pdo) {
    $stats = [];
    
    // Total de utilizadores
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM utilizadores");
    $stats['total_utilizadores'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de campanhas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM campanhas");
    $stats['total_campanhas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de donativos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM donativos");
    $stats['total_donativos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de valor doado
    $stmt = $pdo->query("SELECT COALESCE(SUM(valor), 0) as total FROM donativos");
    $stats['valor_total_doado'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Campanhas ativas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM campanhas WHERE status = 'ativa'");
    $stats['campanhas_ativas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Campanhas concluídas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM campanhas WHERE status = 'concluida'");
    $stats['campanhas_concluidas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    return $stats;
}

// Obter todas as campanhas
function obter_todas_campanhas($pdo) {
    $stmt = $pdo->query(
        "SELECT c.*, u.nome as criador FROM campanhas c 
         LEFT JOIN utilizadores u ON c.id_criador = u.id 
         ORDER BY c.data_criacao DESC"
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter campanha por ID
function obter_campanha_por_id($pdo, $id) {
    $stmt = $pdo->prepare(
        "SELECT c.*, u.nome as criador FROM campanhas c 
         LEFT JOIN utilizadores u ON c.id_criador = u.id 
         WHERE c.id = :id"
    );
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Alterar status de campanha
function alterar_status_campanha($pdo, $id_campanha, $novo_status) {
    $status_validos = ['ativa', 'pausada', 'concluida', 'rejeitada'];
    
    if (!in_array($novo_status, $status_validos)) {
        return false;
    }
    
    $stmt = $pdo->prepare("UPDATE campanhas SET status = :status WHERE id = :id");
    return $stmt->execute(['status' => $novo_status, 'id' => $id_campanha]);
}

// Eliminar campanha
function eliminar_campanha($pdo, $id_campanha) {
    // Eliminar donativos da campanha
    $stmt_donativos = $pdo->prepare("DELETE FROM donativos WHERE id_campanha = :id");
    $stmt_donativos->execute(['id' => $id_campanha]);
    
    // Eliminar campanha
    $stmt = $pdo->prepare("DELETE FROM campanhas WHERE id = :id");
    return $stmt->execute(['id' => $id_campanha]);
}

// Obter logs de atividade (se existir tabela de logs)
function registar_acao_admin($pdo, $id_admin, $tipo_acao, $descricao) {
    // Verifica se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'logs_admin'");
    if ($stmt->rowCount() > 0) {
        $stmt_log = $pdo->prepare(
            "INSERT INTO logs_admin (id_admin, tipo_acao, descricao, data_hora) 
             VALUES (:id_admin, :tipo_acao, :descricao, NOW())"
        );
        $stmt_log->execute([
            'id_admin' => $id_admin,
            'tipo_acao' => $tipo_acao,
            'descricao' => $descricao
        ]);
    }
}

// Obter donativos com filtros
function obter_donativos($pdo, $id_campanha = null, $id_utilizador = null) {
    $query = "SELECT d.*, c.titulo as campanha_titulo, u.nome as doador_nome 
              FROM donativos d 
              LEFT JOIN campanhas c ON d.id_campanha = c.id 
              LEFT JOIN utilizadores u ON d.id_utilizador = u.id 
              WHERE 1=1";
    
    $params = [];
    
    if ($id_campanha) {
        $query .= " AND d.id_campanha = :id_campanha";
        $params['id_campanha'] = $id_campanha;
    }
    
    if ($id_utilizador) {
        $query .= " AND d.id_utilizador = :id_utilizador";
        $params['id_utilizador'] = $id_utilizador;
    }
    
    $query .= " ORDER BY d.data_donativo DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Gerar relatório de campanhas
function gerar_relatorio_campanhas($pdo) {
    $stmt = $pdo->query(
        "SELECT c.id, c.titulo, c.descricao, c.meta, c.status, u.nome as criador,
                COUNT(DISTINCT d.id) as total_donativos,
                COALESCE(SUM(d.valor), 0) as valor_arrecadado
         FROM campanhas c
         LEFT JOIN utilizadores u ON c.id_criador = u.id
         LEFT JOIN donativos d ON c.id = d.id_campanha
         GROUP BY c.id, c.titulo, c.descricao, c.meta, c.status, u.nome
         ORDER BY c.data_criacao DESC"
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter campanha mais popular
function obter_campanha_popular($pdo) {
    $stmt = $pdo->query(
        "SELECT c.id, c.titulo, COUNT(d.id) as total_donativos,
                COALESCE(SUM(d.valor), 0) as valor_total
         FROM campanhas c
         LEFT JOIN donativos d ON c.id = d.id_campanha
         GROUP BY c.id, c.titulo
         ORDER BY total_donativos DESC
         LIMIT 1"
    );
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Editar informações de campanha
function editar_campanha($pdo, $id_campanha, $dados) {
    $campos_permitidos = ['titulo', 'descricao', 'meta', 'status', 'categoria'];
    $updates = [];
    $params = ['id' => $id_campanha];
    
    foreach ($dados as $campo => $valor) {
        if (in_array($campo, $campos_permitidos)) {
            $updates[] = "$campo = :$campo";
            $params[$campo] = $valor;
        }
    }
    
    if (empty($updates)) {
        return false;
    }
    
    $query = "UPDATE campanhas SET " . implode(", ", $updates) . " WHERE id = :id";
    $stmt = $pdo->prepare($query);
    return $stmt->execute($params);
}

// Bloquear/Desbloquear utilizador
function bloquear_utilizador($pdo, $id_utilizador, $bloqueado = true) {
    $status = $bloqueado ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE utilizadores SET bloqueado = :bloqueado WHERE id = :id");
    return $stmt->execute(['bloqueado' => $status, 'id' => $id_utilizador]);
}

// Verificar utilizador bloqueado no login
function utilizador_bloqueado($pdo, $email) {
    $stmt = $pdo->prepare("SELECT bloqueado FROM utilizadores WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['bloqueado'] == 1;
}
?>
