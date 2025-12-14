<?php
require_once 'config.php';

// Ambil data global market
$globalData = getCryptoData("/global");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Tracker - Pantau Harga Cryptocurrency</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
                    <a href="portofolio.php" class="nav-link">Portfolio Simulator</a>
                    <a href="about.php" class="nav-link">Tentang</a>
                </div>
                <div class="theme-toggle">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content animate__animated animate__fadeIn">
                <h1 class="hero-title">Pantau <span class="highlight">Cryptocurrency</span> Real-Time</h1>
                <p class="hero-subtitle">Website tracker cryptocurrency tanpa login & tanpa API key pribadi. Data langsung dari CoinGecko API.</p>
                
                <!-- Market Stats -->
                <?php if ($globalData): ?>
                <div class="market-stats">
                    <div class="stat">
                        <span class="stat-label">Kripto Aktif</span>
                        <span class="stat-value"><?php echo number_format($globalData['data']['active_cryptocurrencies']); ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">Market Cap</span>
                        <span class="stat-value">$<?php echo number_format($globalData['data']['total_market_cap']['usd'] / 1000000000, 2); ?>B</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">Volume 24h</span>
                        <span class="stat-value">$<?php echo number_format($globalData['data']['total_volume']['usd'] / 1000000000, 2); ?>B</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">Dominasi BTC</span>
                        <span class="stat-value"><?php echo number_format($globalData['data']['market_cap_percentage']['btc'], 1); ?>%</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <!-- Features Section -->
        <section class="section">
            <h2 class="section-title">Fitur Utama Crypto Tracker</h2>
            <p class="section-subtitle">Akses semua informasi cryptocurrency yang Anda butuhkan dalam satu platform</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Market Real-Time</h3>
                    <p>Pantau harga 10 cryptocurrency teratas dengan data yang diperbarui setiap 5 menit.</p>
                    <a href="market.php" class="feature-link">Lihat Market <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3>Portfolio Simulator</h3>
                    <p>Simulasikan investasi cryptocurrency dan hitung potensi keuntungan Anda.</p>
                    <a href="portofolio.php" class="feature-link">Coba Simulator <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3>Informasi Lengkap</h3>
                    <p>Pelajari tentang teknologi di balik website ini dan API yang digunakan.</p>
                    <a href="about.php" class="feature-link">Pelajari Lebih <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </section>
        
        <!-- Preview Section -->
        <section class="section">
            <h2 class="section-title">Data Cryptocurrency Teratas</h2>
            <p class="section-subtitle">Preview 5 cryptocurrency teratas berdasarkan market cap</p>
            
            <?php
            // Ambil data 5 crypto teratas untuk preview
            $previewData = getCryptoData("/coins/markets?vs_currency=usd&ids=bitcoin,ethereum,tether,binancecoin,solana&order=market_cap_desc&per_page=5&page=1&sparkline=false");
            ?>
            
            <div class="preview-table">
                <table class="crypto-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kripto</th>
                            <th>Harga</th>
                            <th>24j</th>
                            <th>Market Cap</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($previewData): ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($previewData as $crypto): ?>
                            <?php $change24h = $crypto['price_change_percentage_24h']; ?>
                            <tr>
                                <td class="rank"><?php echo $counter; ?></td>
                                <td class="crypto-name">
                                    <img src="<?php echo $crypto['image']; ?>" alt="<?php echo $crypto['name']; ?>" class="crypto-icon">
                                    <div>
                                        <div class="crypto-symbol"><?php echo strtoupper($crypto['symbol']); ?></div>
                                        <div class="crypto-fullname"><?php echo $crypto['name']; ?></div>
                                    </div>
                                </td>
                                <td class="price">$<?php echo number_format($crypto['current_price'], 2); ?></td>
                                <td class="change <?php echo ($change24h >= 0) ? 'positive' : 'negative'; ?>">
                                    <?php echo ($change24h >= 0 ? '+' : '') . number_format($change24h, 2); ?>%
                                </td>
                                <td class="market-cap">$<?php echo number_format($crypto['market_cap'] / 1000000, 2); ?>M</td>
                            </tr>
                            <?php $counter++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="preview-action">
                    <a href="market.php" class="btn-primary">Lihat Semua Crypto <i class="fas fa-arrow-right"></i></a>
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
                    Website tracker cryptocurrency gratis tanpa login. Dibuat untuk demonstrasi penggunaan API publik dengan PHP.
                </p>
                <div class="footer-links">
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="market.php"><i class="fas fa-chart-line"></i> Market</a>
                    <a href="portofolio.php"><i class="fas fa-calculator"></i> Simulator</a>
                    <a href="about.php"><i class="fas fa-info-circle"></i> Tentang</a>
                </div>
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> Crypto Tracker. Data oleh CoinGecko API.</p>
                    <p class="disclaimer">Informasi ini hanya untuk edukasi, bukan anjuran finansial.</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="script.js"></script>
</body>
</html>