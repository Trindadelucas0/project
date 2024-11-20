<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'STYLISH'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <h1>STYLISH</h1>
                </div>
                
                <div class="nav-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <ul class="nav-menu">
                    <li><a href="index.php">Início</a></li>
                    <li><a href="men.php">Masculino</a></li>
                    <li><a href="women.php">Feminino</a></li>
                    <li><a href="sale.php">Promoções</a></li>
                </ul>

                <div class="search-bar">
                    <input type="text" placeholder="Procure por produtos...">
                    <button><i class="fas fa-search"></i></button>
                </div>

                <div class="nav-icons">
                    <a href="account.php"><i class="fas fa-user"></i></a>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                </div>
            </div>
        </nav>
    </header>
