<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin(); // Verifica se o usuário está logado

// Obtém os itens do carrinho
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image_url 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();

// Calcula o total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - STYLISH</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom Styles for the Cart */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
        .main-container {
            margin-top: 100px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card img {
            border-radius: 8px;
            transition: transform 0.3s ease;
        }
        .card img:hover {
            transform: scale(1.05);
        }
        .card-details {
            flex: 1;
        }
        .card-details h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .card-details p {
            font-size: 1rem;
            color: #4b5563;
        }
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .quantity-btn {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .quantity-btn:hover {
            background-color: #388E3C;
        }
        .remove-item {
            background: none;
            border: none;
            color: #e74c3c;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .remove-item:hover {
            color: #c0392b;
        }
        .total-box {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .total-box .btn {
            background-color: #3498db;
            color: white;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-size: 1.125rem;
            transition: background-color 0.3s ease;
        }
        .total-box .btn:hover {
            background-color: #2980b9;
        }
        .empty-cart {
            text-align: center;
            padding: 50px;
            font-size: 1.5rem;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="main-container max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="md:col-span-2">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="card flex items-center gap-6 mb-6 p-6">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 class="w-32 h-32 object-cover rounded-lg">
                            <div class="card-details flex-1">
                                <h3 class="font-semibold"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-gray-500 text-sm"><?php echo number_format($item['price'], 2, ',', '.'); ?> each</p>
                                <div class="flex items-center gap-4 mt-4">
                                    <button class="quantity-btn" data-action="decrease" data-id="<?php echo $item['id']; ?>">-</button>
                                    <span class="text-lg"><?php echo $item['quantity']; ?></span>
                                    <button class="quantity-btn" data-action="increase" data-id="<?php echo $item['id']; ?>">+</button>
                                </div>
                            </div>
                            <button class="remove-item" data-id="<?php echo $item['id']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Summary -->
                <div class="total-box">
                    <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between font-bold">
                            <span>Total</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>

                    <button class="btn">Proceed to Checkout</button>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Cart functionality
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', async function() {
                const id = this.dataset.id;
                const action = this.dataset.action;

                try {
                    const response = await fetch('api/cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id, action }),
                    });

                    if (response.ok) {
                        location.reload();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });

        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', async function() {
                const id = this.dataset.id;

                try {
                    const response = await fetch('api/cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id, action: 'remove' }),
                    });

                    if (response.ok) {
                        location.reload();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });
    </script>
</body>
</html>
