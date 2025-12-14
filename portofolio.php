<?php
include 'market.php'; // AMBIL DATA GLOBAL MARKET

// Inisialisasi portfolio dari session jika belum ada
session_start();
if (!isset($_SESSION['portfolio'])) {
    $_SESSION['portfolio'] = [];
}

// Fungsi untuk mendapatkan nama crypto berdasarkan ID
function getCryptoNameById($cryptoData, $coinId) {
    foreach ($cryptoData as $coin) {
        if ($coin['id'] == $coinId) {
            return $coin;
        }
    }
    return null;
}

// Proses tambah ke portfolio
if (isset($_POST['add'])) {
    $coinId   = $_POST['coin_id'];
    $amount   = floatval($_POST['amount']);
    $buyPrice = floatval($_POST['buy_price']);
    
    // Validasi input
    if ($amount <= 0 || $buyPrice <= 0) {
        $error = "Jumlah dan harga beli harus lebih dari 0";
    } else {
        // Cari data crypto
        $coinData = getCryptoNameById($cryptoData, $coinId);
        
        if ($coinData) {
            $currentPrice = $coinData['current_price'];
            $coinName = $coinData['name'];
            $symbol = strtoupper($coinData['symbol']);
            
            // Hitung total value
            $totalValue = $amount * $currentPrice;
            $buyValue = $amount * $buyPrice;
            $profitLoss = $totalValue - $buyValue;
            $profitLossPercent = ($buyValue > 0) ? ($profitLoss / $buyValue) * 100 : 0;
            
            // Tambah ke portfolio
            $_SESSION['portfolio'][] = [
                'id' => $coinId,
                'name' => $coinName,
                'symbol' => $symbol,
                'amount' => $amount,
                'buy_price' => $buyPrice,
                'current_price' => $currentPrice,
                'total_value' => $totalValue,
                'buy_value' => $buyValue,
                'profit_loss' => $profitLoss,
                'profit_loss_percent' => $profitLossPercent,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $success = "Berhasil menambahkan {$coinName} ke portfolio!";
        } else {
            $error = "Koin tidak ditemukan!";
        }
    }
}

// Proses hapus dari portfolio
if (isset($_GET['delete'])) {
    $index = intval($_GET['delete']);
    if (isset($_SESSION['portfolio'][$index])) {
        $coinName = $_SESSION['portfolio'][$index]['name'];
        unset($_SESSION['portfolio'][$index]);
        $_SESSION['portfolio'] = array_values($_SESSION['portfolio']); // Re-index array
        $success = "Berhasil menghapus {$coinName} dari portfolio!";
    }
}

// Proses clear semua portfolio
if (isset($_POST['clear_all'])) {
    $_SESSION['portfolio'] = [];
    $success = "Semua portfolio telah dihapus!";
}

// Hitung total portfolio
$totalPortfolioValue = 0;
$totalInvestment = 0;
$totalProfitLoss = 0;

foreach ($_SESSION['portfolio'] as $item) {
    $totalPortfolioValue += $item['total_value'];
    $totalInvestment += $item['buy_value'];
    $totalProfitLoss += $item['profit_loss'];
}

$totalProfitLossPercent = ($totalInvestment > 0) ? ($totalProfitLoss / $totalInvestment) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Simulator Crypto</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .form-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        select, input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        
        button:hover {
            background-color: #45a049;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .positive {
            color: #28a745;
            font-weight: bold;
        }
        
        .negative {
            color: #dc3545;
            font-weight: bold;
        }
        
        .summary {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
        }
        
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            table {
                font-size: 14px;
            }
            
            .summary {
                flex-direction: column;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <h2>📊 Portfolio Simulator Crypto</h2>
        <p>Data harga diambil langsung dari Global Market - Real Time</p>
        
        <hr>
        
        <!-- Form Input -->
        <div class="form-container">
            <h3>➕ Tambah Crypto ke Portfolio</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Pilih Crypto</label>
                    <select name="coin_id" required>
                        <option value="">-- Pilih Crypto --</option>
                        <?php foreach ($cryptoData as $coin): ?>
                            <option value="<?= htmlspecialchars($coin['id']) ?>">
                                <?= htmlspecialchars($coin['name']) ?> 
                                (<?= strtoupper(htmlspecialchars($coin['symbol'])) ?>)
                                - $<?= number_format($coin['current_price'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" step="0.000001" name="amount" 
                           placeholder="Contoh: 0.5" required min="0.000001">
                </div>
                
                <div class="form-group">
                    <label>Harga Beli per Unit ($)</label>
                    <input type="number" step="0.01" name="buy_price" 
                           placeholder="Contoh: 50000.00" required min="0.01">
                </div>
                
                <button type="submit" name="add">➕ Tambah ke Portfolio</button>
            </form>
        </div>
        
        <!-- Summary Portfolio -->
        <?php if (!empty($_SESSION['portfolio'])): ?>
        <div class="summary">
            <div class="summary-item">
                <div>Total Investasi</div>
                <div class="summary-value">$<?= number_format($totalInvestment, 2) ?></div>
            </div>
            <div class="summary-item">
                <div>Nilai Portfolio Saat Ini</div>
                <div class="summary-value">$<?= number_format($totalPortfolioValue, 2) ?></div>
            </div>
            <div class="summary-item">
                <div>Total Profit/Loss</div>
                <div class="summary-value <?= ($totalProfitLoss >= 0) ? 'positive' : 'negative' ?>">
                    $<?= number_format($totalProfitLoss, 2) ?> 
                    (<?= number_format($totalProfitLossPercent, 2) ?>%)
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Tabel Portfolio -->
        <?php if (empty($_SESSION['portfolio'])): ?>
            <div class="alert info">
                Portfolio kosong. Tambahkan crypto untuk memulai!
            </div>
        <?php else: ?>
            <h3>📋 Portfolio Anda</h3>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Crypto</th>
                        <th>Jumlah</th>
                        <th>Harga Beli</th>
                        <th>Harga Sekarang</th>
                        <th>Total Beli</th>
                        <th>Total Sekarang</th>
                        <th>Profit/Loss</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['portfolio'] as $index => $p): 
                        // Update harga terkini dari market data
                        $coinData = getCryptoNameById($cryptoData, $p['id']);
                        if ($coinData) {
                            $currentPrice = $coinData['current_price'];
                            $totalValue = $p['amount'] * $currentPrice;
                            $profitLoss = $totalValue - $p['buy_value'];
                            $profitLossPercent = ($p['buy_value'] > 0) ? ($profitLoss / $p['buy_value']) * 100 : 0;
                        } else {
                            $currentPrice = $p['current_price'];
                            $totalValue = $p['total_value'];
                            $profitLoss = $p['profit_loss'];
                            $profitLossPercent = $p['profit_loss_percent'];
                        }
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($p['name']) ?> (<?= $p['symbol'] ?>)</td>
                        <td><?= number_format($p['amount'], 6) ?></td>
                        <td>$<?= number_format($p['buy_price'], 2) ?></td>
                        <td>$<?= number_format($currentPrice, 2) ?></td>
                        <td>$<?= number_format($p['buy_value'], 2) ?></td>
                        <td>$<?= number_format($totalValue, 2) ?></td>
                        <td class="<?= ($profitLoss >= 0) ? 'positive' : 'negative' ?>">
                            $<?= number_format($profitLoss, 2) ?><br>
                            <small><?= number_format($profitLossPercent, 2) ?>%</small>
                        </td>
                        <td>
                            <a href="?delete=<?= $index ?>" 
                               onclick="return confirm('Hapus <?= htmlspecialchars($p['name']) ?> dari portfolio?')"
                               class="btn-danger" 
                               style="color: white; padding: 5px 10px; border-radius: 3px; text-decoration: none; font-size: 12px;">
                                Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="actions">
                <form method="POST" onsubmit="return confirm('Hapus SEMUA portfolio?')">
                    <button type="submit" name="clear_all" class="btn-danger">
                        🗑️ Hapus Semua Portfolio
                    </button>
                </form>
                <form method="POST">
                    <button type="submit" name="refresh" class="btn-warning">
                        🔄 Refresh Harga
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Auto refresh halaman setiap 60 detik untuk update harga
        setTimeout(function() {
            window.location.reload();
        }, 60000);
        
        // Format input numbers
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 0) this.value = 0;
            });
        });
    </script>
</body>
</html>