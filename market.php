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

// Ambil trending coins untuk banner
$trendingData = getCryptoData("/search/trending");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --card-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            --hover-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.25);
        }
        
        .dark-mode {
            --glass-bg: rgba(0, 0, 0, 0.2);
            --glass-border: rgba(255, 255, 255, 0.05);
            --card-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
            --hover-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.3);
        }
        
        /* Glassmorphism effects */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--card-shadow);
        }
        
        /* Animated background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.05;
            pointer-events: none;
        }
        
        .crypto-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 50%;
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(100px);
                opacity: 0;
            }
        }
        
        /* Trending Banner */
        .trending-banner {
            margin-bottom: 30px;
            overflow: hidden;
            border-radius: 16px;
            position: relative;
        }
        
        .trending-slider {
            display: flex;
            gap: 20px;
            animation: slide 60s linear infinite;
            padding: 20px;
        }
        
        @keyframes slide {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        .trending-coin {
            min-width: 200px;
            padding: 15px;
            background: var(--glass-bg);
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .trending-coin:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
            border-color: var(--primary-color);
        }
        
        .trending-rank {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            background: var(--card-bg);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Enhanced Market Stats */
        .market-stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .market-stat-card {
            padding: 20px;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .market-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .stat-card-1 {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%);
            border: 1px solid rgba(79, 70, 229, 0.2);
        }
        
        .stat-card-2 {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.1) 100%);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .stat-card-3 {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.1) 100%);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        
        .stat-card-4 {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(248, 113, 113, 0.1) 100%);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .market-stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .stat-icon-1 { background: var(--primary-gradient); color: white; }
        .stat-icon-2 { background: var(--success-gradient); color: white; }
        .stat-icon-3 { background: var(--warning-gradient); color: white; }
        .stat-icon-4 { background: var(--danger-gradient); color: white; }
        
        .market-stat-label {
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .market-stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-color);
            font-family: 'Orbitron', sans-serif;
            margin-bottom: 5px;
        }
        
        /* Enhanced Market Controls */
        .market-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            border-radius: 16px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
        }
        
        .search-box {
            position: relative;
            flex: 1;
            max-width: 400px;
        }
        
        .search-box input {
            width: 100%;
            padding: 15px 25px 15px 50px;
            border-radius: 12px;
            border: 2px solid transparent;
            background: var(--card-bg);
            color: var(--text-color);
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1), 0 4px 20px rgba(79, 70, 229, 0.2);
            transform: translateY(-2px);
        }
        
        .search-box i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 18px;
        }
        
        .sort-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .sort-btn {
            padding: 12px 20px;
            background: var(--card-bg);
            border: 2px solid transparent;
            border-radius: 10px;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .sort-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
        }
        
        .sort-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }
        
        /* Enhanced Table */
        .preview-table {
            border-radius: 16px;
            overflow: hidden;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            box-shadow: var(--card-shadow);
        }
        
        .crypto-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .crypto-table thead {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
        }
        
        .crypto-table th {
            padding: 20px 15px;
            text-align: left;
            font-weight: 700;
            color: var(--text-color);
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--glass-border);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .crypto-table tbody tr {
            transition: all 0.3s ease;
            position: relative;
        }
        
        .crypto-table tbody tr:hover {
            background: rgba(79, 70, 229, 0.05);
            transform: scale(1.005);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.1);
        }
        
        .crypto-table tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.02);
        }
        
        .crypto-table td {
            padding: 18px 15px;
            border-bottom: 1px solid var(--glass-border);
            position: relative;
        }
        
        .crypto-name {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .crypto-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--glass-border);
            padding: 2px;
            background: white;
            transition: all 0.3s ease;
        }
        
        .crypto-table tbody tr:hover .crypto-icon {
            transform: scale(1.1);
            border-color: var(--primary-color);
        }
        
        .crypto-symbol {
            font-weight: 700;
            font-size: 16px;
            color: var(--text-color);
        }
        
        .crypto-fullname {
            font-size: 13px;
            color: var(--text-muted);
        }
        
        .price {
            font-weight: 700;
            font-size: 16px;
            font-family: 'Orbitron', sans-serif;
        }
        
        .change {
            font-weight: 700;
            font-size: 15px;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
        }
        
        .positive {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.1) 100%);
            color: #10b981;
        }
        
        .negative {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(248, 113, 113, 0.1) 100%);
            color: #ef4444;
        }
        
        .market-cap, .volume {
            font-weight: 600;
            font-size: 15px;
        }
        
        /* Enhanced Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
            gap: 10px;
            flex-wrap: wrap;
            padding: 25px;
            background: var(--glass-bg);
            border-radius: 16px;
            margin: 40px 20px 20px;
        }
        
        .page-btn {
            padding: 12px 20px;
            background: var(--card-bg);
            border: 2px solid transparent;
            border-radius: 10px;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            min-width: 45px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .page-btn:hover:not(.disabled) {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
            background: var(--primary-gradient);
            color: white;
        }
        
        .page-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
            transform: translateY(-2px);
        }
        
        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .page-btn.disabled:hover {
            transform: none;
            box-shadow: none;
            border-color: transparent;
            background: var(--card-bg);
            color: var(--text-color);
        }
        
        .page-info {
            margin: 0 20px;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Last Updated */
        .last-updated {
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            padding: 20px;
            border-top: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--glass-bg);
            border-radius: 0 0 16px 16px;
        }
        
        .last-updated i {
            color: var(--primary-color);
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Crypto Filters */
        .crypto-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 10px 20px;
            background: var(--card-bg);
            border: 2px solid var(--glass-border);
            border-radius: 10px;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-btn:hover {
            border-color: var(--primary-color);
            background: rgba(79, 70, 229, 0.05);
        }
        
        .filter-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
        }
        
        /* Performance Chart */
        .performance-chart {
            height: 100px;
            width: 100%;
            margin-top: 10px;
        }
        
        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        
        .crypto-loader {
            width: 80px;
            height: 80px;
            position: relative;
            margin-bottom: 20px;
        }
        
        .crypto-loader div {
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--primary-color);
            animation: crypto-load 1.2s linear infinite;
        }
        
        .crypto-loader div:nth-child(1) {
            top: 8px;
            left: 8px;
            animation-delay: 0s;
        }
        
        .crypto-loader div:nth-child(2) {
            top: 8px;
            left: 32px;
            animation-delay: -0.4s;
        }
        
        .crypto-loader div:nth-child(3) {
            top: 8px;
            left: 56px;
            animation-delay: -0.8s;
        }
        
        .crypto-loader div:nth-child(4) {
            top: 32px;
            left: 8px;
            animation-delay: -0.4s;
        }
        
        .crypto-loader div:nth-child(5) {
            top: 32px;
            left: 32px;
            animation-delay: -0.8s;
        }
        
        .crypto-loader div:nth-child(6) {
            top: 32px;
            left: 56px;
            animation-delay: -1.2s;
        }
        
        .crypto-loader div:nth-child(7) {
            top: 56px;
            left: 8px;
            animation-delay: -0.8s;
        }
        
        .crypto-loader div:nth-child(8) {
            top: 56px;
            left: 32px;
            animation-delay: -1.2s;
        }
        
        .crypto-loader div:nth-child(9) {
            top: 56px;
            left: 56px;
            animation-delay: -1.6s;
        }
        
        @keyframes crypto-load {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(0.5);
            }
        }
        
        /* Crypto Detail Modal */
        .crypto-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .crypto-modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content {
            background: var(--bg-color);
            border-radius: 20px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transform: translateY(50px);
            transition: transform 0.3s ease;
        }
        
        .crypto-modal.active .modal-content {
            transform: translateY(0);
        }
        
        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: var(--primary-color);
            color: white;
            transform: rotate(90deg);
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .market-stats-bar {
                grid-template-columns: repeat(2, 1fr);
            }
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
            
            .market-stats-bar {
                grid-template-columns: 1fr;
            }
            
            .crypto-table {
                font-size: 14px;
            }
            
            .crypto-table th,
            .crypto-table td {
                padding: 15px 10px;
            }
            
            .crypto-table th:nth-child(5),
            .crypto-table td:nth-child(5),
            .crypto-table th:nth-child(7),
            .crypto-table td:nth-child(7) {
                display: none;
            }
            
            .crypto-icon {
                width: 32px;
                height: 32px;
            }
            
            .crypto-fullname {
                font-size: 12px;
            }
        }
        
        @media (max-width: 480px) {
            .sort-btn, .filter-btn {
                padding: 8px 12px;
                font-size: 14px;
            }
            
            .page-btn {
                padding: 8px 12px;
                min-width: 35px;
            }
            
            .crypto-table th:nth-child(4),
            .crypto-table td:nth-child(4) {
                display: none;
            }
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--card-bg);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg" id="animatedBg"></div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="crypto-loader">
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
        </div>
        <h3 style="color: var(--text-color);">Memuat data cryptocurrency...</h3>
    </div>
    
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
            <h1 class="section-title animate__animated animate__fadeInDown">Market Cryptocurrency</h1>
            <p class="section-subtitle">Data harga real-time untuk 1000+ cryptocurrency. Diperbarui otomatis setiap menit.</p>
            
            <!-- Trending Banner -->
            <?php if ($trendingData && isset($trendingData['coins'])): ?>
            <div class="trending-banner glass-card">
                <div class="trending-slider">
                    <?php $trendCount = 1; ?>
                    <?php foreach ($trendingData['coins'] as $trend): ?>
                    <div class="trending-coin" onclick="showCryptoDetail('<?php echo $trend['item']['id']; ?>')">
                        <div class="trending-rank"><?php echo $trendCount++; ?></div>
                        <img src="<?php echo $trend['item']['thumb']; ?>" alt="<?php echo htmlspecialchars($trend['item']['name']); ?>" width="40" height="40">
                        <div>
                            <div style="font-weight: 700;"><?php echo htmlspecialchars($trend['item']['name']); ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);"><?php echo strtoupper($trend['item']['symbol']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php foreach ($trendingData['coins'] as $trend): ?>
                    <div class="trending-coin" onclick="showCryptoDetail('<?php echo $trend['item']['id']; ?>')">
                        <div class="trending-rank"><?php echo $trendCount++; ?></div>
                        <img src="<?php echo $trend['item']['thumb']; ?>" alt="<?php echo htmlspecialchars($trend['item']['name']); ?>" width="40" height="40">
                        <div>
                            <div style="font-weight: 700;"><?php echo htmlspecialchars($trend['item']['name']); ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);"><?php echo strtoupper($trend['item']['symbol']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Market Stats Bar -->
            <?php if ($globalData && isset($globalData['data'])): ?>
            <div class="market-stats-bar">
                <div class="market-stat-card stat-card-1">
                    <div class="market-stat-icon stat-icon-1">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="market-stat-label">Total Market Cap</div>
                    <div class="market-stat-value">$<?php echo isset($globalData['data']['total_market_cap']['usd']) ? number_format($globalData['data']['total_market_cap']['usd'] / 1000000000, 2) : '0.00'; ?>B</div>
                    <?php $market_cap_change = isset($globalData['data']['market_cap_change_percentage_24h_usd']) ? $globalData['data']['market_cap_change_percentage_24h_usd'] : 0; ?>
                    <div class="market-stat-change <?php echo $market_cap_change >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $market_cap_change >= 0 ? '+' : ''; ?><?php echo number_format($market_cap_change, 2); ?>% (24j)
                    </div>
                </div>
                <div class="market-stat-card stat-card-2">
                    <div class="market-stat-icon stat-icon-2">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="market-stat-label">Volume 24h</div>
                    <div class="market-stat-value">$<?php echo isset($globalData['data']['total_volume']['usd']) ? number_format($globalData['data']['total_volume']['usd'] / 1000000000, 2) : '0.00'; ?>B</div>
                </div>
                <div class="market-stat-card stat-card-3">
                    <div class="market-stat-icon stat-icon-3">
                        <i class="fab fa-bitcoin"></i>
                    </div>
                    <div class="market-stat-label">Dominasi BTC</div>
                    <div class="market-stat-value"><?php echo isset($globalData['data']['market_cap_percentage']['btc']) ? number_format($globalData['data']['market_cap_percentage']['btc'], 1) : '0.0'; ?>%</div>
                </div>
                <div class="market-stat-card stat-card-4">
                    <div class="market-stat-icon stat-icon-4">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="market-stat-label">Kripto Aktif</div>
                    <div class="market-stat-value"><?php echo isset($globalData['data']['active_cryptocurrencies']) ? number_format($globalData['data']['active_cryptocurrencies']) : '0'; ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Quick Filters -->
            <div class="crypto-filters">
                <button class="filter-btn active" onclick="filterCrypto('all')">
                    <i class="fas fa-list"></i> Semua
                </button>
                <button class="filter-btn" onclick="filterCrypto('gainers')">
                    <i class="fas fa-arrow-up"></i> Top Gainers
                </button>
                <button class="filter-btn" onclick="filterCrypto('losers')">
                    <i class="fas fa-arrow-down"></i> Top Losers
                </button>
                <button class="filter-btn" onclick="filterCrypto('volume')">
                    <i class="fas fa-fire"></i> High Volume
                </button>
            </div>
            
            <!-- Market Controls -->
            <div class="market-controls glass-card">
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
                    <button class="sort-btn <?php echo $order == 'price_change_percentage_24h_desc' ? 'active' : ''; ?>" onclick="window.location.href='market.php?order=price_change_percentage_24h_desc&page=1'">
                        <i class="fas fa-rocket"></i> Gainers
                    </button>
                </div>
            </div>
            
            <!-- Crypto Table -->
            <div class="preview-table glass-card">
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
                            <th>Chart</th>
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
                            $decimal_places = $current_price < 1 ? 6 : ($current_price < 10 ? 4 : 2);
                            $price_formatted = '$' . number_format($current_price, $decimal_places);
                            
                            // Format market cap dan volume
                            $market_cap = isset($crypto['market_cap']) && $crypto['market_cap'] > 0 ? number_format($crypto['market_cap'] / 1000000, 2) : '0.00';
                            $volume = isset($crypto['total_volume']) && $crypto['total_volume'] > 0 ? number_format($crypto['total_volume'] / 1000000, 2) : '0.00';
                            
                            // Data attributes untuk filtering
                            $data_change_24h = $change24h !== null ? $change24h : 0;
                            $data_volume = isset($crypto['total_volume']) ? $crypto['total_volume'] : 0;
                            ?>
                            <tr class="crypto-row" 
                                data-change-24h="<?php echo $data_change_24h; ?>"
                                data-volume="<?php echo $data_volume; ?>"
                                data-crypto-id="<?php echo isset($crypto['id']) ? $crypto['id'] : ''; ?>"
                                onclick="showCryptoDetail('<?php echo isset($crypto['id']) ? $crypto['id'] : ''; ?>')">
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
                                <td>
                                    <div class="performance-chart" id="chart-<?php echo $rank; ?>" data-change-24h="<?php echo $data_change_24h; ?>"></div>
                                </td>
                            </tr>
                            <?php $rank++; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 60px 20px;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 60px; color: var(--warning-color); margin-bottom: 20px;"></i>
                                    <h3 style="color: var(--text-color); margin-bottom: 15px;">Data tidak tersedia</h3>
                                    <p style="color: var(--text-muted); margin-bottom: 25px;">Gagal mengambil data dari CoinGecko API. Coba refresh halaman.</p>
                                    <button onclick="location.reload()" class="sort-btn" style="background: var(--primary-gradient); color: white;">
                                        <i class="fas fa-sync-alt"></i> Refresh Halaman
                                    </button>
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
                    <i class="fas fa-sync-alt"></i> 
                    <span>Data diperbarui setiap menit. Terakhir: <span id="lastUpdatedTime"><?php echo date('H:i:s'); ?></span> WIB</span>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Crypto Detail Modal -->
    <div class="crypto-modal" id="cryptoModal">
        <div class="modal-content" id="modalContent">
            <!-- Modal content akan diisi oleh JavaScript -->
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content glass-card" style="border-radius: 20px; padding: 40px;">
                <div class="footer-logo">
                    <i class="fas fa-coins"></i>
                    <span>Crypto<span class="logo-highlight">Tracker</span></span>
                </div>
                <p class="footer-description">
                    Platform real-time cryptocurrency tracker dengan data live dan analisis mendalam. 
                    Dibangun dengan teknologi modern untuk pengalaman trading yang lebih baik.
                </p>
                <div class="footer-links">
                    <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="market.php"><i class="fas fa-chart-line"></i> Live Market</a>
                    <a href="portofolio.php"><i class="fas fa-calculator"></i> Portfolio Simulator</a>
                    <a href="about.php"><i class="fas fa-code"></i> API Docs</a>
                </div>
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> Crypto Tracker Pro. Data disediakan oleh CoinGecko API.</p>
                    <p class="disclaimer">
                        <i class="fas fa-exclamation-triangle"></i> Informasi ini hanya untuk tujuan edukasi dan analisis. 
                        Trading cryptocurrency memiliki risiko tinggi.
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.2.1/build/global/luxon.min.js"></script>
    
    <!-- JavaScript -->
    <script src="script.js"></script>
    <script>
        // Animated Background
        function createParticles() {
            const bg = document.getElementById('animatedBg');
            if (!bg) return;
            
            const particleCount = 30;
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'crypto-particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 20 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 20) + 's';
                bg.appendChild(particle);
            }
        }
        
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Create animated background
            createParticles();
            
            // Hide loading overlay
            setTimeout(() => {
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingOverlay) {
                    loadingOverlay.style.opacity = '0';
                    setTimeout(() => {
                        loadingOverlay.style.display = 'none';
                    }, 300);
                }
            }, 1000);
            
            // Create mini charts for each row
            createMiniCharts();
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('#cryptoTable tbody .crypto-row');
                    
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
            
            // Update last updated time every second
            updateLastUpdatedTime();
        });
        
        // Create mini charts for performance visualization
        function createMiniCharts() {
            const charts = document.querySelectorAll('.performance-chart');
            
            charts.forEach(chart => {
                const change24h = parseFloat(chart.getAttribute('data-change-24h'));
                const isPositive = change24h >= 0;
                
                const options = {
                    series: [{
                        name: 'Performance',
                        data: generateRandomData(12, isPositive)
                    }],
                    chart: {
                        type: 'line',
                        height: 100,
                        width: '100%',
                        sparkline: {
                            enabled: true
                        },
                        animations: {
                            enabled: true,
                            speed: 800
                        }
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    colors: [isPositive ? '#10b981' : '#ef4444'],
                    tooltip: {
                        enabled: false
                    },
                    grid: {
                        show: false
                    },
                    xaxis: {
                        labels: {
                            show: false
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        show: false
                    }
                };
                
                const apexChart = new ApexCharts(chart, options);
                apexChart.render();
            });
        }
        
        // Generate random data for mini charts
        function generateRandomData(count, isPositive) {
            const data = [];
            let value = 50;
            
            for (let i = 0; i < count; i++) {
                const change = isPositive ? 
                    (Math.random() * 10 - 2) : 
                    (Math.random() * 10 - 8);
                value += change;
                data.push(Math.max(0, value));
            }
            
            return data;
        }
        
        // Filter crypto based on criteria
        function filterCrypto(filterType) {
            const rows = document.querySelectorAll('#cryptoTable tbody .crypto-row');
            const filterBtns = document.querySelectorAll('.filter-btn');
            
            // Update active filter button
            filterBtns.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            rows.forEach(row => {
                const change24h = parseFloat(row.getAttribute('data-change-24h'));
                const volume = parseFloat(row.getAttribute('data-volume'));
                
                switch(filterType) {
                    case 'gainers':
                        row.style.display = change24h >= 5 ? '' : 'none';
                        break;
                    case 'losers':
                        row.style.display = change24h <= -5 ? '' : 'none';
                        break;
                    case 'volume':
                        row.style.display = volume > 10000000 ? '' : 'none';
                        break;
                    default:
                        row.style.display = '';
                }
            });
        }
        
        // Show crypto detail modal
        async function showCryptoDetail(cryptoId) {
            if (!cryptoId) return;
            
            const modal = document.getElementById('cryptoModal');
            const modalContent = document.getElementById('modalContent');
            
            // Show loading state
            modalContent.innerHTML = `
                <div style="padding: 60px 40px; text-align: center;">
                    <div class="crypto-loader">
                        <div></div><div></div><div></div>
                        <div></div><div></div><div></div>
                        <div></div><div></div><div></div>
                    </div>
                    <h3 style="margin-top: 20px; color: var(--text-color);">Memuat detail cryptocurrency...</h3>
                </div>
                <div class="modal-close" onclick="closeCryptoModal()">
                    <i class="fas fa-times"></i>
                </div>
            `;
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            try {
                // In a real app, you would fetch crypto details from your API
                // This is a mock implementation
                setTimeout(() => {
                    modalContent.innerHTML = `
                        <div class="modal-close" onclick="closeCryptoModal()">
                            <i class="fas fa-times"></i>
                        </div>
                        <div style="padding: 40px;">
                            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                                <img src="https://assets.coingecko.com/coins/images/1/large/bitcoin.png" alt="Bitcoin" width="60" height="60" style="border-radius: 50%;">
                                <div>
                                    <h1 style="color: var(--text-color); margin: 0 0 5px 0;">Bitcoin (BTC)</h1>
                                    <p style="color: var(--text-muted); margin: 0;">Rank #1 • Market Cap: $1.2T</p>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                                <div style="background: var(--card-bg); padding: 20px; border-radius: 12px;">
                                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 5px;">Harga Saat Ini</div>
                                    <div style="font-size: 32px; font-weight: 800; color: var(--text-color); font-family: 'Orbitron', sans-serif;">$67,890.42</div>
                                </div>
                                <div style="background: var(--card-bg); padding: 20px; border-radius: 12px;">
                                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 5px;">Perubahan 24h</div>
                                    <div style="font-size: 32px; font-weight: 800; color: #10b981; font-family: 'Orbitron', sans-serif;">+2.45%</div>
                                </div>
                            </div>
                            
                            <div id="detailChart" style="height: 300px; margin-bottom: 30px;"></div>
                            
                            <div style="background: var(--card-bg); padding: 25px; border-radius: 12px;">
                                <h3 style="color: var(--text-color); margin-top: 0;">Tentang Bitcoin</h3>
                                <p style="color: var(--text-muted); line-height: 1.6;">
                                    Bitcoin adalah cryptocurrency pertama dan terbesar berdasarkan kapitalisasi pasar. 
                                    Diciptakan pada tahun 2009 oleh seseorang atau sekelompok orang dengan nama samaran Satoshi Nakamoto.
                                </p>
                                <div style="display: flex; gap: 30px; margin-top: 20px;">
                                    <div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Market Cap</div>
                                        <div style="font-size: 18px; font-weight: 700; color: var(--text-color);">$1.2T</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Volume 24h</div>
                                        <div style="font-size: 18px; font-weight: 700; color: var(--text-color);">$34.5B</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Supply</div>
                                        <div style="font-size: 18px; font-weight: 700; color: var(--text-color);">19.6M BTC</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Create chart for modal
                    const chartOptions = {
                        series: [{
                            name: 'Harga',
                            data: generatePriceData(30)
                        }],
                        chart: {
                            type: 'area',
                            height: 300,
                            toolbar: {
                                show: true
                            },
                            zoom: {
                                enabled: true
                            }
                        },
                        colors: ['#4f46e5'],
                        stroke: {
                            width: 3,
                            curve: 'smooth'
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.7,
                                opacityTo: 0.1,
                                stops: [0, 90, 100]
                            }
                        },
                        xaxis: {
                            type: 'datetime',
                            labels: {
                                style: {
                                    colors: 'var(--text-muted)'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: 'var(--text-muted)'
                                },
                                formatter: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        grid: {
                            borderColor: 'var(--border-color)'
                        },
                        tooltip: {
                            x: {
                                format: 'dd MMM yyyy'
                            }
                        }
                    };
                    
                    const chart = new ApexCharts(document.querySelector("#detailChart"), chartOptions);
                    chart.render();
                }, 1000);
            } catch (error) {
                console.error('Error loading crypto details:', error);
                modalContent.innerHTML = `
                    <div class="modal-close" onclick="closeCryptoModal()">
                        <i class="fas fa-times"></i>
                    </div>
                    <div style="padding: 40px; text-align: center;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 60px; color: var(--warning-color); margin-bottom: 20px;"></i>
                        <h3 style="color: var(--text-color);">Gagal memuat data</h3>
                        <p style="color: var(--text-muted);">Terjadi kesalahan saat mengambil detail cryptocurrency.</p>
                        <button onclick="showCryptoDetail('${cryptoId}')" class="sort-btn" style="margin-top: 20px;">
                            <i class="fas fa-redo"></i> Coba Lagi
                        </button>
                    </div>
                `;
            }
        }
        
        // Generate price data for detail chart
        function generatePriceData(days) {
            const data = [];
            let price = 65000;
            const now = DateTime.now();
            
            for (let i = days; i >= 0; i--) {
                const date = now.minus({days: i}).toJSDate();
                const change = (Math.random() - 0.5) * 0.1; // Random change between -5% and +5%
                price = price * (1 + change);
                
                data.push({
                    x: date,
                    y: price
                });
            }
            
            return data;
        }
        
        // Close crypto modal
        function closeCryptoModal() {
            const modal = document.getElementById('cryptoModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        // Update last updated time
        function updateLastUpdatedTime() {
            const timeElement = document.getElementById('lastUpdatedTime');
            if (!timeElement) return;
            
            setInterval(() => {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                timeElement.textContent = timeString;
            }, 1000);
        }
        
        // Auto-refresh data every 60 seconds
        let autoRefreshInterval = setInterval(() => {
            // You can implement partial refresh here
            console.log('Auto-refreshing data...');
            // In a real app, you would fetch new data and update the table
        }, 60 * 1000);
        
        // Clean up interval on page unload
        window.addEventListener('beforeunload', () => {
            clearInterval(autoRefreshInterval);
        });
        
        // Theme toggle enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.querySelector('.theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('dark-mode');
                    const themeIcon = document.getElementById('theme-icon');
                    if (themeIcon) {
                        if (document.body.classList.contains('dark-mode')) {
                            themeIcon.classList.remove('fa-moon');
                            themeIcon.classList.add('fa-sun');
                        } else {
                            themeIcon.classList.remove('fa-sun');
                            themeIcon.classList.add('fa-moon');
                        }
                    }
                    
                    // Store theme preference
                    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
                });
            }
            
            // Load saved theme
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                const themeIcon = document.getElementById('theme-icon');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
            }
        });
    </script>
</body>
</html>