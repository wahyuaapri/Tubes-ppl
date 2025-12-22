<?php
require_once 'config.php';

// Parameter untuk sorting
$order = isset($_GET['order']) ? $_GET['order'] : 'market_cap_desc';
$per_page = 50; // Tampilkan 50 koin per halaman
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Ambil data crypto dengan pagination
$marketData = getCryptoData("/coins/markets?vs_currency=usd&order=$order&per_page=$per_page&page=$page&sparkline=false&price_change_percentage=1h,24h,7d");

// Hitung total halaman (asumsi 1000 koin tersedia)
$total_pages = 20; // CoinGecko API punya sekitar 1000 koin yang aktif

// Ambil data global untuk statistik
$globalData = getCryptoData("/global");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Crypto - Crypto Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* Additional styles for market page */
        .market-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .search-box {
            position: relative;
            flex: 1;
            max-width: 400px;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border-radius: 10px;
            border: 2px solid var(--border-color);
            background-color: var(--card-bg);
            color: var(--text-color);
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        
        .sort-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .sort-btn {
            padding: 10px 15px;
            background-color: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .sort-btn:hover {
            border-color: var(--primary-color);
            background-color: rgba(79, 70, 229, 0.05);
        }
        
        .sort-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .page-btn {
            padding: 8px 15px;
            background-color: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            min-width: 40px;
            text-align: center;
        }
        
        .page-btn:hover:not(.disabled) {
            border-color: var(--primary-color);
            background-color: rgba(79, 70, 229, 0.05);
        }
        
        .page-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .page-info {
            margin: 0 15px;
            color: var(--text-muted);
            font-size: 14px;
        }
        
        .market-stats-bar {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            border: 2px solid var(--border-color);
        }
        
        .market-stat-item {
            text-align: center;
        }
        
        .market-stat-label {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }
        
        .market-stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-color);
            font-family: 'Orbitron', sans-serif;
        }
        
        .market-stat-change {
            font-size: 14px;
            font-weight: 600;
        }
        
        .market-stat-change.positive {
            color: #10b981;
        }
        
        .market-stat-change.negative {
            color: #ef4444;
        }
        
        .last-updated {
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
        }
        
        @media (max-width: 768px) {
            .market-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: 100%;
            }
            
            .sort-options {
                justify-content: center;
            }
            
            .crypto-table {
                font-size: 14px;
            }
            
            .crypto-table th,
            .crypto-table td {
                padding: 10px 8px;
            }
            
            .crypto-icon {
                width: 24px;
                height: 24px;
            }
            
            .crypto-fullname {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .market-stats-bar {
                grid-template-columns: 1fr;
            }
            
            .crypto-table th:nth-child(4),
            .crypto-table td:nth-child(4),
            .crypto-table th:nth-child(6),
            .crypto-table td:nth-child(6),
            .crypto-table th:nth-child(7),
            .crypto-table td:nth-child(7) {
                display: none;
            }
        }
        
        .no-data {
            color: var(--text-muted);
            font-style: italic;
        }
    </style>
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
                    <a href="market.php" class="nav-link active">Market</a>
                    <a href="portofolio.php" class="nav-link">Portfolio Simulator</a>
                    <a href="about.php" class="nav-link">Tentang</a>
                </div>
                <div class="theme-toggle">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <!-- Page Header -->
        <section class="section">
            <h1 class="section-title">Market Cryptocurrency</h1>
            <p class="section-subtitle">Data harga real-time untuk 1000+ cryptocurrency. Diperbarui otomatis setiap 5 menit.</p>
            
            <!-- Market Stats Bar -->
            <?php if ($globalData && isset($globalData['data'])): ?>
            <div class="market-stats-bar">
                <div class="market-stat-item">
                    <div class="market-stat-label">Total Market Cap</div>
                    <div class="market-stat-value">$<?php echo isset($globalData['data']['total_market_cap']['usd']) ? number_format($globalData['data']['total_market_cap']['usd'] / 1000000000, 2) : '0.00'; ?>B</div>
                </div>
                <div class="market-stat-item">
                    <div class="market-stat-label">Volume 24h</div>
                    <div class="market-stat-value">$<?php echo isset($globalData['data']['total_volume']['usd']) ? number_format($globalData['data']['total_volume']['usd'] / 1000000000, 2) : '0.00'; ?>B</div>
                </div>
                <div class="market-stat-item">
                    <div class="market-stat-label">Dominasi BTC</div>
                    <div class="market-stat-value"><?php echo isset($globalData['data']['market_cap_percentage']['btc']) ? number_format($globalData['data']['market_cap_percentage']['btc'], 1) : '0.0'; ?>%</div>
                </div>
                <div class="market-stat-item">
                    <div class="market-stat-label">Kripto Aktif</div>
                    <div class="market-stat-value"><?php echo isset($globalData['data']['active_cryptocurrencies']) ? number_format($globalData['data']['active_cryptocurrencies']) : '0'; ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Market Controls -->
            <div class="market-controls">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari cryptocurrency (nama atau simbol)...">
                </div>
                
                <div class="sort-options">
                    <button class="sort-btn <?php echo $order == 'market_cap_desc' ? 'active' : ''; ?>" onclick="window.location.href='market.php?order=market_cap_desc&page=1'">
                        <i class="fas fa-crown"></i> Market Cap
                    </button>
                    <button class="sort-btn <?php echo $order == 'volume_desc' ? 'active' : ''; ?>" onclick="window.location.href='market.php?order=volume_desc&page=1'">
                        <i class="fas fa-chart-bar"></i> Volume
                    </button>
                    <button class="sort-btn <?php echo $order == 'id_asc' ? 'active' : ''; ?>" onclick="window.location.href='market.php?order=id_asc&page=1'">
                        <i class="fas fa-sort-alpha-down"></i> Nama A-Z
                    </button>
                </div>
            </div>
            
            <!-- Crypto Table -->
            <div class="preview-table">
                <table class="crypto-table" id="cryptoTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kripto</th>
                            <th>Harga</th>
                            <th>1j</th>
                            <th>24j</th>
                            <th>7h</th>
                            <th>Market Cap</th>
                            <th>Volume (24j)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($marketData): ?>
                            <?php $rank = ($page - 1) * $per_page + 1; ?>
                            <?php foreach ($marketData as $crypto): ?>
                            <?php 
                            // Gunakan null coalescing operator untuk menghindari undefined array key warnings
                            $change1h = $crypto['price_change_percentage_1h_in_currency'] ?? null;
                            $change24h = $crypto['price_change_percentage_24h_in_currency'] ?? null;
                            $change7d = $crypto['price_change_percentage_7d_in_currency'] ?? null;
                            
                            // Format angka jika tersedia
                            $change1h_formatted = $change1h !== null ? ($change1h >= 0 ? '+' : '') . number_format($change1h, 2) . '%' : '<span class="no-data">N/A</span>';
                            $change24h_formatted = $change24h !== null ? ($change24h >= 0 ? '+' : '') . number_format($change24h, 2) . '%' : '<span class="no-data">N/A</span>';
                            $change7d_formatted = $change7d !== null ? ($change7d >= 0 ? '+' : '') . number_format($change7d, 2) . '%' : '<span class="no-data">N/A</span>';
                            
                            // Tentukan class untuk warna
                            $change1h_class = $change1h !== null ? ($change1h >= 0 ? 'positive' : 'negative') : '';
                            $change24h_class = $change24h !== null ? ($change24h >= 0 ? 'positive' : 'negative') : '';
                            $change7d_class = $change7d !== null ? ($change7d >= 0 ? 'positive' : 'negative') : '';
                            
                            // Format harga dengan decimal places yang sesuai
                            $current_price = $crypto['current_price'] ?? 0;
                            $decimal_places = $current_price < 1 ? 6 : 2;
                            $price_formatted = '$' . number_format($current_price, $decimal_places);
                            
                            // Format market cap dan volume
                            $market_cap = isset($crypto['market_cap']) && $crypto['market_cap'] > 0 ? number_format($crypto['market_cap'] / 1000000, 2) : '0.00';
                            $volume = isset($crypto['total_volume']) && $crypto['total_volume'] > 0 ? number_format($crypto['total_volume'] / 1000000, 2) : '0.00';
                            ?>
                            <tr>
                                <td class="rank"><?php echo $rank; ?></td>
                                <td class="crypto-name">
                                    <?php if (isset($crypto['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($crypto['image']); ?>" alt="<?php echo isset($crypto['name']) ? htmlspecialchars($crypto['name']) : 'Unknown'; ?>" class="crypto-icon">
                                    <?php else: ?>
                                        <div class="crypto-icon-placeholder">
                                            <i class="fas fa-coins"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="crypto-symbol"><?php echo isset($crypto['symbol']) ? strtoupper(htmlspecialchars($crypto['symbol'])) : 'N/A'; ?></div>
                                        <div class="crypto-fullname"><?php echo isset($crypto['name']) ? htmlspecialchars($crypto['name']) : 'Unknown Cryptocurrency'; ?></div>
                                    </div>
                                </td>
                                <td class="price"><?php echo $price_formatted; ?></td>
                                <td class="change <?php echo $change1h_class; ?>">
                                    <?php echo $change1h_formatted; ?>
                                </td>
                                <td class="change <?php echo $change24h_class; ?>">
                                    <?php echo $change24h_formatted; ?>
                                </td>
                                <td class="change <?php echo $change7d_class; ?>">
                                    <?php echo $change7d_formatted; ?>
                                </td>
                                <td class="market-cap">$<?php echo $market_cap; ?>M</td>
                                <td class="volume">$<?php echo $volume; ?>M</td>
                            </tr>
                            <?php $rank++; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--text-muted); margin-bottom: 20px;"></i>
                                    <h3>Data tidak tersedia</h3>
                                    <p>Gagal mengambil data dari CoinGecko API. Coba refresh halaman.</p>
                                    <?php if (isset($api_error)): ?>
                                        <p class="error-message">Error: <?php echo htmlspecialchars($api_error); ?></p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="market.php?order=<?php echo htmlspecialchars($order); ?>&page=<?php echo $page - 1; ?>" class="page-btn">
                            <i class="fas fa-chevron-left"></i> Prev
                        </a>
                    <?php else: ?>
                        <span class="page-btn disabled">
                            <i class="fas fa-chevron-left"></i> Prev
                        </span>
                    <?php endif; ?>
                    
                    <?php
                    // Tampilkan max 5 nomor halaman
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    if ($start_page > 1) {
                        echo '<a href="market.php?order=' . htmlspecialchars($order) . '&page=1" class="page-btn">1</a>';
                        if ($start_page > 2) echo '<span class="page-btn disabled">...</span>';
                    }
                    
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        if ($i == $page) {
                            echo '<span class="page-btn active">' . $i . '</span>';
                        } else {
                            echo '<a href="market.php?order=' . htmlspecialchars($order) . '&page=' . $i . '" class="page-btn">' . $i . '</a>';
                        }
                    }
                    
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) echo '<span class="page-btn disabled">...</span>';
                        echo '<a href="market.php?order=' . htmlspecialchars($order) . '&page=' . $total_pages . '" class="page-btn">' . $total_pages . '</a>';
                    }
                    ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="market.php?order=<?php echo htmlspecialchars($order); ?>&page=<?php echo $page + 1; ?>" class="page-btn">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="page-btn disabled">
                            Next <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                    
                    <div class="page-info">
                        Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?>
                    </div>
                </div>
                
                <!-- Last Updated Info -->
                <div class="last-updated">
                    <i class="fas fa-sync-alt"></i> Data diperbarui setiap 5 menit. Terakhir: <?php echo date('H:i:s'); ?> WIB
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
                    <p>&copy; <?php echo date('Y'); ?> Crypto Tracker. Data oleh DeniSetiawan.</p>
                    <p class="disclaimer">Informasi ini hanya untuk edukasi, bukan anjuran finansial.</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="script.js"></script>
    <script>
        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#cryptoTable tbody tr');
                    
                    rows.forEach(row => {
                        const cryptoNameElement = row.querySelector('.crypto-fullname');
                        const cryptoSymbolElement = row.querySelector('.crypto-symbol');
                        
                        if (cryptoNameElement && cryptoSymbolElement) {
                            const cryptoName = cryptoNameElement.textContent.toLowerCase();
                            const cryptoSymbol = cryptoSymbolElement.textContent.toLowerCase();
                            
                            if (cryptoName.includes(searchTerm) || cryptoSymbol.includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });
                });
            }
            
            // Highlight row on hover
            const rows = document.querySelectorAll('#cryptoTable tbody tr');
            
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'var(--hover-color)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
        
        // Auto-refresh every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 5 * 60 * 1000); // 5 minutes
    </script>
</body>
</html>