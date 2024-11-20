<?php
$title = "STYLISH - Seu Destino Fashion";
include 'includes/header.php';
require_once 'includes/db.php'; // Certifique-se de incluir a conexão com o banco de dados

// Buscar produtos de ambas as categorias "Men" e "Women" para exibição no início
$products = $pdo->query("SELECT * FROM products WHERE category IN ('men', 'women') LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
  <!-- Seção Hero -->
  <div class="hero">
    <div class="hero-content">
      <h1>Coleção de Verão 2024</h1>
      <p>Descubra nossa mais recente coleção de roupas modernas e confortáveis para todas as ocasiões.</p>
      <button class="btn-primary" onclick="window.location.href = 'shop.php';">Compre Agora</button>
    </div>
  </div>

  <!-- Produtos em Destaque (Masculino e Feminino juntos) -->
  <section class="featured-products">
    <div class="container">
      <h2>Produtos em Destaque</h2>
      <div class="product-grid">
        <?php foreach ($products as $product): ?>
          <div class="product-card">
            <img src="<?= $product['image_url']; ?>" alt="<?= $product['name']; ?>" />
            <h4><?= $product['name']; ?></h4>
            <p>R$ <?= number_format($product['price'], 2, ',', '.'); ?></p>
            <button class="btn-primary" onclick="addToCart(<?= $product['id']; ?>)">Adicionar ao Carrinho</button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Categorias -->
  <section class="categories">
    <div class="container">
      <h2>Compre por Categoria</h2>
      <div class="category-grid">
        <!-- Categoria Masculino -->
        <div class="category-card" data-category="men">
          <div class="category-image">
            <img src="https://images.unsplash.com/photo-1490578474895-699cd4e2cf59?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Moda Masculina">
          </div>
          <div class="category-content">
            <h3>Masculino</h3>
            <button class="btn-secondary" onclick="window.location.href = 'shop.php?category=men';">Compre Agora</button>
          </div>
        </div>

        <!-- Categoria Feminino -->
        <div class="category-card" data-category="women">
          <div class="category-image">
            <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Moda Feminina">
          </div>
          <div class="category-content">
            <h3>Feminino</h3>
            <button class="btn-secondary" onclick="window.location.href = 'shop.php?category=women';">Compre Agora</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Newsletter -->
  <section class="newsletter">
    <div class="container">
      <i class="icon-mail"></i>
      <h2>Assine nossa newsletter</h2>
      <p>Receba as últimas novidades sobre novos produtos e promoções</p>
      <form id="newsletter-form" class="newsletter-form" method="POST" action="subscribe.php">
        <input type="email" name="email" placeholder="Digite seu e-mail" required>
        <button type="submit" class="btn-primary">Assinar</button>
      </form>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>
