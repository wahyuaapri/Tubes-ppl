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
    
    <style>
        /* === STYLE TAMBAHAN UNTUK HALAMAN TENTANG === */
        
        /* Hero Section - Modern Gradient Background */
        .about-hero {
            background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
            padding: 100px 0 60px;
            margin-bottom: 60px;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(0, 255, 255, 0.1);
        }
        
        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(0, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(22, 119, 255, 0.05) 0%, transparent 50%);
        }
        
        .hero-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, #00ffff, #1677ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            color: #a0aec0;
            text-align: center;
            max-width: 700px;
            margin: 0 auto 40px;
            line-height: 1.6;
        }
        
        /* Feature Grid dengan Holographic Effect [citation:2] */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 60px 0;
        }
        
        .feature-card {
            background: rgba(26, 26, 46, 0.7);
            border: 1px solid rgba(0, 255, 255, 0.1);
            border-radius: 20px;
            padding: 35px 30px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #00ffff, #1677ff);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: rgba(0, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 255, 255, 0.1),
                        0 0 60px rgba(22, 119, 255, 0.1);
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 25px;
            display: inline-block;
            background: linear-gradient(135deg, #00ffff, #1677ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .feature-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 15px;
        }
        
        .feature-desc {
            color: #a0aec0;
            line-height: 1.7;
            font-size: 0.95rem;
        }
        
        /* Tech Stack Section */
        .tech-stack {
            background: rgba(10, 10, 15, 0.5);
            border-radius: 20px;
            padding: 50px;
            margin: 60px 0;
            border: 1px solid rgba(0, 255, 255, 0.1);
        }
        
        .stack-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            text-align: center;
            margin-bottom: 40px;
            color: #ffffff;
        }
        
        .stack-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stack-item {
            background: rgba(26, 26, 46, 0.7);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 255, 255, 0.05);
        }
        
        .stack-item:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 255, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0, 255, 255, 0.1);
        }
        
        .stack-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            display: block;
        }
        
        .php-icon { color: #787cb5; }
        .html-icon { color: #e34f26; }
        .css-icon { color: #1572b6; }
        .js-icon { color: #f7df1e; }
        .api-icon { color: #00d8ff; }
        
        /* Stats Section */
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin: 60px 0;
        }
        
        .stat-card {
            background: rgba(26, 26, 46, 0.7);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 255, 255, 0.1);
        }
        
        .stat-number {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, #00ffff, #1677ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #a0aec0;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Warning Card - Enhanced [citation:4] */
        .warning-card {
            background: linear-gradient(135deg, rgba(255, 87, 34, 0.1) 0%, rgba(26, 26, 46, 0.7) 100%);
            border: 1px solid rgba(255, 87, 34, 0.3);
            border-radius: 20px;
            padding: 40px;
            margin: 60px 0;
            position: relative;
            overflow: hidden;
        }
        
        .warning-card::before {
            content: '⚠️';
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2rem;
            opacity: 0.2;
        }
        
        .warning-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            color: #ff5722;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .warning-content {
            color: #ffccbc;
            line-height: 1.8;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .tech-stack {
                padding: 30px 20px;
            }
            
            .stack-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .feature-card, .warning-card {
                padding: 25px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .stack-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-section {
                grid-template-columns: 1fr;
            }
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .floating {
            animation: float 5s ease-in-out infinite;
        }
        
        /* Subtle Background Elements */
        .bg-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(0, 255, 255, 0.5);
            border-radius: 50%;
            pointer-events: none;
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
                <a href="portfolio.php" class="nav-link">Portfolio Simulator</a>
                <a href="about.php" class="nav-link active">Tentang</a>
            </div>
            <div class="theme-toggle">
                <i class="fas fa-moon" id="theme-icon"></i>
            </div>
        </nav>
    </div>
</header>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <h1 class="hero-title">Tentang Crypto Tracker</h1>
        <p class="hero-subtitle">
            Platform revolusioner untuk memantau harga cryptocurrency secara real-time dengan akurasi tinggi 
            dan antarmuka yang intuitif. Dibangun untuk trader, investor, dan enthusiast cryptocurrency.
        </p>
        
        <div class="stats-section">
            <div class="stat-card floating">
                <div class="stat-number">99.9%</div>
                <div class="stat-label">Uptime API</div>
            </div>
            <div class="stat-card floating" style="animation-delay: 0.5s;">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Cryptocurrency</div>
            </div>
            <div class="stat-card floating" style="animation-delay: 1s;">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Update Real-time</div>
            </div>
            <div class="stat-card floating" style="animation-delay: 1.5s;">
                <div class="stat-number">0</div>
                <div class="stat-label">Biaya Langganan</div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="container">
    <!-- Features Grid -->
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <h3 class="feature-title">Misi Kami</h3>
            <p class="feature-desc">
                Menyediakan akses demokratis terhadap data cryptocurrency real-time yang akurat dan andal. 
                Kami percaya bahwa informasi pasar yang transparan adalah hak setiap investor.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-database"></i>
            </div>
            <h3 class="feature-title">Sumber Data Premium</h3>
            <p class="feature-desc">
                Mengintegrasikan <strong>CoinGecko API</strong> yang terpercaya untuk menyediakan data harga, 
                market cap, volume trading, dan statistik global dengan akurasi tertinggi.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h3 class="feature-title">Teknologi Modern</h3>
            <p class="feature-desc">
                Dibangun dengan stack teknologi terdepan untuk memastikan kecepatan, keamanan, dan 
                pengalaman pengguna yang optimal di semua perangkat.
            </p>
        </div>
    </div>
    
    <!-- Tech Stack Section -->
    <div class="tech-stack">
        <h2 class="stack-title">Teknologi yang Digunakan</h2>
        <div class="stack-grid">
            <div class="stack-item">
                <i class="fab fa-php stack-icon php-icon"></i>
                <h3>PHP 8.x</h3>
                <p>Backend Processing</p>
            </div>
            <div class="stack-item">
                <i class="fab fa-html5 stack-icon html-icon"></i>
                <h3>HTML5</h3>
                <p>Semantic Markup</p>
            </div>
            <div class="stack-item">
                <i class="fab fa-css3-alt stack-icon css-icon"></i>
                <h3>CSS3</h3>
                <p>Modern Styling</p>
            </div>
            <div class="stack-item">
                <i class="fab fa-js stack-icon js-icon"></i>
                <h3>JavaScript</h3>
                <p>Interaktivitas</p>
            </div>
            <div class="stack-item">
                <i class="fas fa-plug stack-icon api-icon"></i>
                <h3>CoinGecko API</h3>
                <p>Data Real-time</p>
            </div>
        </div>
    </div>
    
    <!-- Key Features -->
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <h3 class="feature-title">Real-time Updates</h3>
            <p class="feature-desc">
                Data harga diperbarui setiap 60 detik secara otomatis. Tidak ada delay dalam informasi 
                pasar yang kritis untuk keputusan trading.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="feature-title">Tanpa Login</h3>
            <p class="feature-desc">
                Akses penuh tanpa registrasi. Privasi Anda terjaga karena tidak perlu membagikan 
                data pribadi atau membuat akun.
            </p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h3 class="feature-title">Responsive Design</h3>
            <p class="feature-desc">
                Optimalkan untuk semua perangkat. Akses dari desktop, tablet, atau smartphone 
                dengan pengalaman yang konsisten.
            </p>
        </div>
    </div>
    
    <!-- Warning Card -->
    <div class="warning-card">
        <h3 class="warning-title">
            <i class="fas fa-exclamation-triangle"></i>
            Penting: Disclaimer & Informasi Legal
        </h3>
        <div class="warning-content">
            <p>
                <strong>Informasi Edukasional:</strong> Semua data dan analisis yang disajikan di Crypto Tracker 
                bertujuan untuk edukasi dan informasi semata. Tidak ada konten di platform ini yang 
                merupakan saran investasi, rekomendasi trading, atau ajakan untuk membeli/menjual aset digital.
            </p>
            <p>
                <strong>Risiko Investasi:</strong> Trading cryptocurrency mengandung risiko tinggi termasuk 
                volatilitas ekstrim dan potensi kehilangan seluruh modal. Sebelum melakukan investasi, 
                pastikan Anda memahami risiko sepenuhnya dan berkonsultasi dengan penasihat keuangan profesional.
            </p>
            <p>
                <strong>Akurasi Data:</strong> Meskipun kami berusaha menyediakan data seakurat mungkin, 
                kami tidak menjamin kelengkapan, keakuratan, atau ketepatan waktu informasi. 
                Gunakan data ini sebagai referensi, bukan sebagai dasar tunggal keputusan finansial.
            </p>
        </div>
    </div>
    
    <!-- Development Team -->
    <div class="feature-card" style="max-width: 800px; margin: 60px auto;">
        <div class="feature-icon">
            <i class="fas fa-code"></i>
        </div>
        <h3 class="feature-title">Tentang Pengembangan</h3>
        <p class="feature-desc">
            Crypto Tracker dikembangkan sebagai proyek open-source untuk mendemonstrasikan integrasi 
            API cryptocurrency dengan PHP modern. Proyek ini menekankan pada:
        </p>
        <ul style="color: #a0aec0; margin-top: 15px; padding-left: 20px; line-height: 1.8;">
            <li>Best practices dalam pengembangan web</li>
            <li>Implementasi API publik yang efisien</li>
            <li>User experience yang optimal</li>
            <li>Kode yang bersih dan terstruktur</li>
            <li>Dokumentasi yang komprehensif</li>
        </ul>
        <p class="feature-desc" style="margin-top: 20px;">
            Kontribusi, saran, dan feedback selalu diterima untuk pengembangan platform yang lebih baik.
        </p>
    </div>
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
                Platform monitoring cryptocurrency gratis dan tanpa iklan. 
                Berkomitmen untuk menyediakan data yang transparan dan aksesibel.
            </p>
            <div class="footer-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="market.php"><i class="fas fa-chart-line"></i> Market</a>
                <a href="portfolio.php"><i class="fas fa-wallet"></i> Simulator</a>
                <a href="about.php"><i class="fas fa-info-circle"></i> Tentang</a>
                <a href="#"><i class="fas fa-file-code"></i> Dokumentasi</a>
                <a href="#"><i class="fas fa-envelope"></i> Kontak</a>
            </div>
            <div class="social-links" style="margin: 20px 0;">
                <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-discord"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-telegram"></i></a>
            </div>
            <p class="disclaimer">
                &copy; <?php echo date('Y'); ?> Crypto Tracker — Data oleh CoinGecko API
                <br>
                <small style="opacity: 0.7;">Versi 2.1.0 | Terakhir diperbarui: <?php echo date('d M Y'); ?></small>
            </p>
        </div>
    </div>
</footer>

<!-- JS -->
<script src="script.js"></script>
<script>
    // Tambahkan efek partikel background
    document.addEventListener('DOMContentLoaded', function() {
        const hero = document.querySelector('.about-hero');
        if (hero) {
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = 'bg-particle';
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                particle.style.animationDelay = `${Math.random() * 5}s`;
                particle.style.animation = `float ${3 + Math.random() * 4}s ease-in-out infinite`;
                hero.appendChild(particle);
            }
        }
        
        // Tambahkan efek hover pada semua card
        const cards = document.querySelectorAll('.feature-card, .stack-item, .stat-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            });
        });
    });
</script>

</body>
</html>