<?php
require_once 'config.php';

// Simulasi portfolio default
$defaultPortfolio = [
    ['id' => 'bitcoin', 'name' => 'Bitcoin', 'symbol' => 'btc', 'amount' => 0.5],
    ['id' => 'ethereum', 'name' => 'Ethereum', 'symbol' => 'eth', 'amount' => 2],
    ['id' => 'solana', 'name' => 'Solana', 'symbol' => 'sol', 'amount' => 10],
    ['id' => 'cardano', 'name' => 'Cardano', 'symbol' => 'ada', 'amount' => 500],
    ['id' => 'polkadot', 'name' => 'Polkadot', 'symbol' => 'dot', 'amount' => 50]
];

// Ambil data portfolio dari session atau gunakan default
session_start();
if (!isset($_SESSION['portfolio'])) {
    $_SESSION['portfolio'] = $defaultPortfolio;
}

$portfolio = $_SESSION['portfolio'];

// Proses form tambah aset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_asset'])) {
    $coin_id = $_POST['coin_id'];
    $amount = floatval($_POST['amount']);
    
    // Cek apakah koin sudah ada di portfolio
    $found = false;
    foreach ($_SESSION['portfolio'] as &$asset) {
        if ($asset['id'] === $coin_id) {
            $asset['amount'] += $amount;
            $found = true;
            break;
        }
    }
    
    // Jika koin belum ada, tambahkan baru
    if (!$found) {
        // Ambil nama dan simbol koin dari API
        $coinData = getCryptoData("/coins/markets?vs_currency=usd&ids={$coin_id}&order=market_cap_desc&per_page=1&page=1&sparkline=false");
        if ($coinData) {
            $newAsset = [
                'id' => $coin_id,
                'name' => $coinData[0]['name'],
                'symbol' => $coinData[0]['symbol'],
                'amount' => $amount
            ];
            $_SESSION['portfolio'][] = $newAsset;
        }
    }
    
    // Refresh halaman
    header("Location: portofolio.php");
    exit();
}

// Proses hapus aset
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $_SESSION['portfolio'] = array_filter($_SESSION['portfolio'], function($asset) use ($deleteId) {
        return $asset['id'] !== $deleteId;
    });
    
    // Reset indeks array
    $_SESSION['portfolio'] = array_values($_SESSION['portfolio']);
    header("Location: portofolio.php");
    exit();
}

// Ambil data harga untuk semua aset di portfolio
$portfolioValue = 0;
$totalInvestment = 0;
$totalProfit = 0;

if (!empty($portfolio)) {
    $coinIds = array_column($portfolio, 'id');
    $idsString = implode(',', $coinIds);
    
    $priceData = getCryptoData("/coins/markets?vs_currency=usd&ids={$idsString}&order=market_cap_desc&per_page=" . count($portfolio) . "&page=1&sparkline=false");
    
    if ($priceData) {
        // Map data harga untuk akses mudah
        $priceMap = [];
        foreach ($priceData as $coin) {
            $priceMap[$coin['id']] = [
                'current_price' => $coin['current_price'],
                'price_change_percentage_24h' => $coin['price_change_percentage_24h'],
                'image' => $coin['image'],
                'market_cap' => $coin['market_cap']
            ];
        }
        
        // Hitung nilai portfolio
        foreach ($portfolio as &$asset) {
            if (isset($priceMap[$asset['id']])) {
                $currentPrice = $priceMap[$asset['id']]['current_price'];
                $asset['current_price'] = $currentPrice;
                $asset['current_value'] = $asset['amount'] * $currentPrice;
                $asset['price_change_24h'] = $priceMap[$asset['id']]['price_change_percentage_24h'];
                $asset['image'] = $priceMap[$asset['id']]['image'];
                
                // Asumsikan harga beli adalah 90% dari harga saat ini untuk simulasi
                $asset['buy_price'] = $currentPrice * 0.9;
                $asset['investment'] = $asset['amount'] * $asset['buy_price'];
                $asset['profit'] = $asset['current_value'] - $asset['investment'];
                $asset['profit_percentage'] = ($asset['profit'] / $asset['investment']) * 100;
                
                $portfolioValue += $asset['current_value'];
                $totalInvestment += $asset['investment'];
                $totalProfit += $asset['profit'];
            }
        }
    }
}

// Ambil data crypto untuk dropdown
$topCryptos = getCryptoData("/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=20&page=1&sparkline=false");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Simulator - Crypto Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .portfolio-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-title {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            font-family: 'Orbitron', sans-serif;
            margin-bottom: 5px;
        }
        
        .stat-change {
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .positive {
            color: #10b981;
        }
        
        .negative {
            color: #ef4444;
        }
        
        .portfolio-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .btn-add {
            background: linear-gradient(135deg, var(--primary-color), #4f46e5);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-add:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.3);
        }
        
        .btn-reset {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            background: #dc2626;
        }
        
        .add-asset-form {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-select, .form-input {
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            background: var(--bg-color);
            color: var(--text-primary);
            transition: border-color 0.3s ease;
        }
        
        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .portfolio-table {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .asset-row {
            display: grid;
            grid-template-columns: 3fr 2fr 2fr 2fr 2fr 1fr;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        @media (max-width: 1024px) {
            .asset-row {
                grid-template-columns: 2fr 1fr 1fr;
                gap: 10px;
            }
            
            .mobile-hidden {
                display: none;
            }
        }
        
        .asset-header {
            background: var(--header-bg);
            font-weight: 600;
            color: var(--text-secondary);
            border-bottom: 2px solid var(--border-color);
        }
        
        .asset-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .asset-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        
        .asset-name {
            display: flex;
            flex-direction: column;
        }
        
        .asset-symbol {
            font-weight: 600;
            font-size: 16px;
        }
        
        .asset-fullname {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .asset-amount {
            font-weight: 600;
        }
        
        .btn-delete {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: background 0.3s ease;
        }
        
        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.1);
        }
        
        .empty-portfolio {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .empty-icon {
            font-size: 60px;
            margin-bottom: 20px;
            color: var(--border-color);
        }
        
        .simulation-note {
            background: rgba(79, 70, 229, 0.1);
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 8px;
            margin-top: 40px;
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
                    <a href="market.php" class="nav-link">Market</a>
                    <a href="portofolio.php" class="nav-link active">Portfolio Simulator</a>
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
        <!-- Portfolio Header -->
        <section class="section">
            <h1 class="section-title">Portfolio Simulator</h1>
            <p class="section-subtitle">Simulasikan investasi cryptocurrency Anda dan pantau performa portfolio</p>
            
            <!-- Portfolio Stats -->
            <div class="portfolio-stats animate__animated animate__fadeIn">
                <div class="stat-card">
                    <div class="stat-title">
                        <i class="fas fa-wallet"></i>
                        <span>Nilai Portfolio</span>
                    </div>
                    <div class="stat-value">$<?php echo number_format($portfolioValue, 2); ?></div>
                    <div class="stat-change <?php echo ($totalProfit >= 0) ? 'positive' : 'negative'; ?>">
                        <i class="fas fa-arrow-<?php echo ($totalProfit >= 0) ? 'up' : 'down'; ?>"></i>
                        <span>$<?php echo number_format($totalProfit, 2); ?> (<?php echo number_format(($totalInvestment > 0) ? ($totalProfit / $totalInvestment * 100) : 0, 2); ?>%)</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-title">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Total Investasi</span>
                    </div>
                    <div class="stat-value">$<?php echo number_format($totalInvestment, 2); ?></div>
                    <div class="stat-title">
                        <small>Harga beli diasumsikan 90% dari harga saat ini untuk simulasi</small>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-title">
                        <i class="fas fa-chart-pie"></i>
                        <span>Jumlah Aset</span>
                    </div>
                    <div class="stat-value"><?php echo count($portfolio); ?></div>
                    <div class="stat-title">
                        <small>Jenis cryptocurrency dalam portfolio</small>
                    </div>
                </div>
            </div>
            
            <!-- Portfolio Actions -->
            <div class="portfolio-actions">
                <button class="btn-add" id="showAddForm">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Aset
                </button>
                
                <a href="portofolio.php?reset=1" class="btn-reset" onclick="return confirm('Reset portfolio ke default?')">
                    <i class="fas fa-redo"></i>
                    Reset Portfolio
                </a>
            </div>
            
            <!-- Add Asset Form -->
            <div class="add-asset-form" id="addAssetForm">
                <h3 style="margin-bottom: 20px;">Tambah Aset ke Portfolio</h3>
                <form method="POST" action="portofolio.php">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Pilih Cryptocurrency</label>
                            <select name="coin_id" class="form-select" required>
                                <option value="">-- Pilih Coin --</option>
                                <?php if ($topCryptos): ?>
                                    <?php foreach ($topCryptos as $crypto): ?>
                                        <option value="<?php echo $crypto['id']; ?>">
                                            <?php echo $crypto['name']; ?> (<?php echo strtoupper($crypto['symbol']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="amount" class="form-input" step="0.000001" min="0.000001" placeholder="Contoh: 0.5" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="add_asset" class="btn-add">
                                <i class="fas fa-check"></i>
                                Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        
        <!-- Portfolio Table -->
        <section class="section">
            <h2 class="section-title">Aset Portfolio</h2>
            
            <?php if (!empty($portfolio)): ?>
                <div class="portfolio-table animate__animated animate__fadeInUp">
                    <div class="asset-row asset-header">
                        <div class="asset-info">Aset</div>
                        <div class="mobile-hidden">Jumlah</div>
                        <div class="mobile-hidden">Harga Saat Ini</div>
                        <div>Nilai Saat Ini</div>
                        <div class="mobile-hidden">Profit/Loss</div>
                        <div>Aksi</div>
                    </div>
                    
                    <?php foreach ($portfolio as $asset): ?>
                        <div class="asset-row">
                            <div class="asset-info">
                                <img src="<?php echo $asset['image'] ?? 'https://via.placeholder.com/40'; ?>" alt="<?php echo $asset['name']; ?>" class="asset-icon">
                                <div class="asset-name">
                                    <span class="asset-symbol"><?php echo strtoupper($asset['symbol']); ?></span>
                                    <span class="asset-fullname"><?php echo $asset['name']; ?></span>
                                </div>
                            </div>
                            
                            <div class="asset-amount mobile-hidden">
                                <?php echo number_format($asset['amount'], 6); ?>
                            </div>
                            
                            <div class="mobile-hidden">
                                $<?php echo number_format($asset['current_price'] ?? 0, 2); ?>
                            </div>
                            
                            <div class="asset-value">
                                $<?php echo number_format($asset['current_value'] ?? 0, 2); ?>
                            </div>
                            
                            <div class="mobile-hidden">
                                <div class="<?php echo ($asset['profit'] >= 0) ? 'positive' : 'negative'; ?>">
                                    <i class="fas fa-arrow-<?php echo ($asset['profit'] >= 0) ? 'up' : 'down'; ?>"></i>
                                    $<?php echo number_format($asset['profit'] ?? 0, 2); ?>
                                    (<?php echo number_format($asset['profit_percentage'] ?? 0, 2); ?>%)
                                </div>
                            </div>
                            
                            <div>
                                <a href="portofolio.php?delete=<?php echo $asset['id']; ?>" class="btn-delete" onclick="return confirm('Hapus aset ini dari portfolio?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-portfolio">
                    <div class="empty-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3>Portfolio Kosong</h3>
                    <p>Tambahkan aset cryptocurrency ke portfolio Anda untuk memulai simulasi</p>
                </div>
            <?php endif; ?>
            
            <!-- Simulation Note -->
            <div class="simulation-note">
                <h4><i class="fas fa-info-circle"></i> Catatan Simulasi:</h4>
                <p>
                    • Harga beli diasumsikan 90% dari harga saat ini untuk tujuan simulasi.<br>
                    • Data harga diperbarui setiap 5 menit dari CoinGecko API.<br>
                    • Portfolio disimpan dalam session browser dan akan hilang setelah browser ditutup.<br>
                    • Simulasi ini hanya untuk edukasi, bukan anjuran finansial.
                </p>
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
    <script>
        // Toggle form tambah aset
        document.getElementById('showAddForm').addEventListener('click', function() {
            const form = document.getElementById('addAssetForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
        
        // Theme toggle (sama dengan di halaman lain)
        const themeToggle = document.getElementById('theme-icon');
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
            themeToggle.classList.remove('fa-moon');
            themeToggle.classList.add('fa-sun');
        }
        
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-theme');
            if (document.body.classList.contains('dark-theme')) {
                localStorage.setItem('theme', 'dark');
                themeToggle.classList.remove('fa-moon');
                themeToggle.classList.add('fa-sun');
            } else {
                localStorage.setItem('theme', 'light');
                themeToggle.classList.remove('fa-sun');
                themeToggle.classList.add('fa-moon');
            }
        });
    </script>
</body>
</html>