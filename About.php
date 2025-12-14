<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang - Crypto Tracker</title>

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Header & Navigation -->
<header class="header">
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <i class="fas fa-coins"></i>
                <span>Crypto<span class="logo-highlight">Tracker</span></span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="market.php" class="nav-link">Market</a>
                <a href="portfolio.php" class="nav-link">Portfolio Simulator</a>
                <a href="about.php" class="nav-link active">Tentang</a>
            </div>
            <div class="theme-toggle">
                <i class="fas fa-moon" id="theme-icon"></i>
            </div>
        </nav>
    </div>
</header>

<!-- About Section -->
<main class="container">
    <section class="section">
        <h1 class="section-title">Tentang Crypto Tracker</h1>
        <p class="section-subtitle">
            Website sederhana untuk memantau harga cryptocurrency secara real-time
        </p>

        <div class="about-content">
            <div class="about-card">
                <h3><i class="fas fa-bullseye"></i> Tujuan Website</h3>
                <p>
                    Crypto Tracker dibuat untuk membantu pengguna memantau harga cryptocurrency
                    secara real-time tanpa perlu login atau API key pribadi.
                    Website ini cocok untuk pembelajaran dan demonstrasi penggunaan API publik.
                </p>
            </div>

            <div class="about-card">
                <h3><i class="fas fa-database"></i> Sumber Data</h3>
                <p>
                    Semua data harga, market cap, volume, dan statistik global
                    diambil langsung dari <strong>CoinGecko API</strong>,
                    salah satu API cryptocurrency gratis dan terpercaya.
                </p>
            </div>

            <div class="about-card">
                <h3><i class="fas fa-code"></i> Teknologi yang Digunakan</h3>
                <ul class="tech-list">
                    <li>PHP (Backend)</li>
                    <li>HTML5 & CSS3</li>
                    <li>JavaScript (Interaksi & Theme)</li>
                    <li>CoinGecko Public API</li>
                </ul>
            </div>

            <div class="about-card">
                <h3><i class="fas fa-exclamation-triangle"></i> Disclaimer</h3>
                <p>
                    Informasi yang ditampilkan di website ini hanya untuk tujuan edukasi.
                    Bukan merupakan saran atau rekomendasi investasi.
                    Risiko investasi sepenuhnya menjadi tanggung jawab pengguna.
                </p>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <i class="fas fa-coins"></i>
                <span>Crypto<span class="logo-highlight">Tracker</span></span>
            </div>
            <p class="footer-description">
                Website tracker cryptocurrency gratis tanpa login.
                Dibuat untuk demonstrasi penggunaan API publik dengan PHP.
            </p>
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="market.php">Market</a>
                <a href="portfolio.php">Simulator</a>
                <a href="about.php">Tentang</a>
            </div>
            <p class="disclaimer">
                &copy; <?php echo date('Y'); ?> Crypto Tracker — Data oleh CoinGecko API
            </p>
        </div>
    </div>
</footer>

<!-- JS -->
<script src="script.js"></script>

</body>
</html>
