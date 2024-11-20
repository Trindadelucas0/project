<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Decodifica os dados da requisição
$data = json_decode(file_get_contents('php://input'), true);

// Verifica se os dados foram recebidos corretamente
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

// Executa ações no carrinho com base na requisição
switch ($data['action']) {
    case 'add':
        // Adiciona o produto ao carrinho ou aumenta a quantidade se já estiver no carrinho
        $stmt = $pdo->prepare("
            INSERT INTO cart (user_id, product_id, quantity)
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE quantity = quantity + 1
        ");
        $stmt->execute([$_SESSION['user_id'], $data['product_id']]);
        break;

    case 'increase':
        // Aumenta a quantidade do produto no carrinho
        $stmt = $pdo->prepare("
            UPDATE cart 
            SET quantity = quantity + 1 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$data['id'], $_SESSION['user_id']]);
        break;

    case 'decrease':
        // Diminui a quantidade do produto no carrinho, com um limite de 1
        $stmt = $pdo->prepare("
            UPDATE cart 
            SET quantity = GREATEST(1, quantity - 1)
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$data['id'], $_SESSION['user_id']]);
        break;

    case 'remove':
        // Remove o produto do carrinho
        $stmt = $pdo->prepare("
            DELETE FROM cart 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$data['id'], $_SESSION['user_id']]);
        break;

    default:
        // Retorna um erro caso a ação não seja válida
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit();
}

// Resposta de sucesso
echo json_encode(['success' => true]);
