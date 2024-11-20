<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado e é um administrador
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Obter estatísticas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$totalUsers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$totalOrders = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - STYLISH</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reset some basic styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            min-height: 100vh;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar */
        .sidebar {
            background-color: #2c3e50;
            color: white;
            width: 250px;
            padding: 30px 15px;
        }

        .sidebar-header h2 {
            font-size: 22px;
            margin-bottom: 20px;
            text-align: center;
            color: #ecf0f1;
        }

        .sidebar-nav a {
            display: block;
            padding: 10px;
            color: #ecf0f1;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .sidebar-nav a:hover {
            background-color: #34495e;
        }

        .sidebar-nav .active {
            background-color: #1abc9c;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 30px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .content-header h1 {
            font-size: 28px;
            color: #333;
        }

        .admin-info {
            font-size: 16px;
            color: #555;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-icon {
            font-size: 40px;
            margin-bottom: 10px;
            color: #1abc9c;
        }

        .stat-info h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-info p {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        .recent-orders {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .recent-orders h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f7fa;
            color: #333;
        }

        td {
            color: #555;
        }

        .status {
            padding: 5px 10px;
            border-radius: 5px;
            text-transform: capitalize;
        }

        .status.pending {
            background-color: #f39c12;
            color: #fff;
        }

        .status.completed {
            background-color: #2ecc71;
            color: #fff;
        }

        .status.cancelled {
            background-color: #e74c3c;
            color: #fff;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .sidebar {
                width: 200px;
            }

            .main-content {
                padding: 20px;
            }

            .content-header h1 {
                font-size: 24px;
            }

            .stat-card {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
                padding: 20px;
            }

            .main-content {
                padding: 10px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>STYLISH Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="products.php">
                    <i class="fas fa-tshirt"></i> Produtos
                </a>
                <a href="orders.php">
                    <i class="fas fa-shopping-cart"></i> Pedidos
                </a>
                <a href="users.php">
                    <i class="fas fa-users"></i> Usuários
                </a>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1>Dashboard</h1>
                <div class="admin-info">
                    <span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
            </header>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total de Produtos</h3>
                        <p><?php echo $totalProducts; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total de Usuários</h3>
                        <p><?php echo $totalUsers; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total de Pedidos</h3>
                        <p><?php echo $totalOrders; ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <section class="recent-orders">
                <h2>Últimos Pedidos</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID do Pedido</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("
                                SELECT o.*, u.name as customer_name 
                                FROM orders o 
                                JOIN users u ON o.user_id = u.id 
                                ORDER BY o.created_at DESC 
                                LIMIT 5
                            ");
                            while ($order = $stmt->fetch()) {
                                echo "<tr>";
                                echo "<td>#" . htmlspecialchars($order['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($order['customer_name']) . "</td>";
                                echo "<td>" . date('d M, Y', strtotime($order['created_at'])) . "</td>";
                                echo "<td><span class='status " . strtolower($order['status']) . "'>" . 
                                     htmlspecialchars($order['status']) . "</span></td>";
                                echo "<td>R$ " . number_format($order['total'], 2, ',', '.') . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
