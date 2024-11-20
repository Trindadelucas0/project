<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado e se é um administrador
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Lidar com a submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category = $_POST['category'];
            $stock = $_POST['stock'];
            $image_url = $_POST['image_url'];

            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, category, stock, image_url) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $price, $category, $stock, $image_url]);
        }
    }
}

// Obter todos os produtos
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Produtos - STYLISH Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Aqui você pode adicionar o CSS personalizado ou atualizar o que já existe */
        .admin-container {
            display: flex;
        }
        .sidebar {
            background: #2c3e50;
            color: white;
            width: 250px;
            padding: 20px;
            height: 100vh;
        }
        .sidebar-header h2 {
            font-size: 22px;
            margin-bottom: 20px;
        }
        .sidebar-nav a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: #34495e;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .content-header h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        .btn-primary, .btn-secondary {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .table-responsive {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #3498db;
            color: white;
        }
        .product-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            color: #3498db;
        }
        .btn-icon:hover {
            color: #2980b9;
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
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1>Gerenciamento de Produtos</h1>
                <button class="btn-primary" onclick="openAddProductModal()">
                    <i class="fas fa-plus"></i> Adicionar Novo Produto
                </button>
            </header>

            <!-- Tabela de Produtos -->
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     class="product-thumbnail">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td>R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td>
                                <button class="btn-icon" onclick="editProduct(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal de Adição de Produto -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Adicionar Novo Produto</h2>
            <form id="addProductForm" method="POST" action="products.php">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="name">Nome do Produto</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Categoria</label>
                    <select id="category" name="category" required>
                        <option value="">Selecione a Categoria</option>
                        <option value="men">Masculino</option>
                        <option value="women">Feminino</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Preço</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="stock">Estoque</label>
                    <input type="number" id="stock" name="stock" required>
                </div>

                <div class="form-group">
                    <label for="image_url">URL da Imagem</label>
                    <input type="url" id="image_url" name="image_url" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Adicionar Produto</button>
                    <button type="button" class="btn-secondary" onclick="closeAddProductModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
