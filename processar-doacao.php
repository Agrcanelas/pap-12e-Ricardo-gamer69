<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: campanhas.php"); exit;
}

$id_campanha = intval($_POST['id_campanha'] ?? 0);
$montante    = floatval($_POST['montante'] ?? 0);
$mensagem    = trim($_POST['mensagem'] ?? '');
$anonimo     = intval($_POST['anonimo'] ?? 0) === 1 ? 1 : 0;

if ($id_campanha <= 0 || $montante < 1) {
    header("Location: campanha.php?id=$id_campanha#doar"); exit;
}

try {
    // Verificar campanha ativa
    $stmt = $pdo->prepare("SELECT id FROM campanhas WHERE id = :id AND status = 'ativa'");
    $stmt->execute(['id' => $id_campanha]);
    if (!$stmt->fetch()) {
        header("Location: campanha.php?id=$id_campanha&erro=inativa"); exit;
    }

    // Registar doação
    $stmt = $pdo->prepare("INSERT INTO doacoes (id_campanha, id_doador, montante, mensagem, anonimo) VALUES (:campanha, :doador, :montante, :mensagem, :anonimo)");
    $stmt->execute([
        'campanha'  => $id_campanha,
        'doador'    => $_SESSION['user_id'],
        'montante'  => $montante,
        'mensagem'  => $mensagem ?: null,
        'anonimo'   => $anonimo,
    ]);

    // Atualizar valor angariado
    $stmt = $pdo->prepare("UPDATE campanhas SET valor_angariado = valor_angariado + :montante WHERE id = :id");
    $stmt->execute(['montante' => $montante, 'id' => $id_campanha]);

    header("Location: obrigado.php?campanha=$id_campanha&valor=$montante"); exit;
} catch (PDOException $e) {
    header("Location: campanha.php?id=$id_campanha&erro=geral"); exit;
}
?>
