<?php
// Konfigurasi API publik untuk data cryptocurrency
define('API_URL', 'https://api.coingecko.com/api/v3');
define('CACHE_DURATION', 300); // Cache selama 5 menit (dalam detik)
define('USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

// Daftar cryptocurrency populer yang akan ditampilkan
$cryptoList = [
    'bitcoin', 
    'ethereum', 
    'cardano', 
    'solana', 
    'ripple',
    'dogecoin',
    'polkadot',
    'litecoin',
    'chainlink',
    'stellar'
];

// Pastikan folder cache ada dan dapat ditulis
if (!is_dir('cache')) {
    mkdir('cache', 0755, true);
}

// Buat file .htaccess di folder cache untuk keamanan
$htaccess_content = "Order deny,allow\nDeny from all\n";
$htaccess_file = 'cache/.htaccess';
if (!file_exists($htaccess_file)) {
    file_put_contents($htaccess_file, $htaccess_content);
}

// Fungsi untuk mengambil data dari API dengan caching
function getCryptoData($endpoint) {
    $cacheFile = 'cache/' . md5($endpoint) . '.json';
    
    // Cek apakah cache masih valid
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < CACHE_DURATION)) {
        $cachedData = file_get_contents($cacheFile);
        if ($cachedData !== false) {
            return json_decode($cachedData, true);
        }
    }
    
    // Jika tidak, ambil data dari API
    $url = API_URL . $endpoint;
    
    // Gunakan file_get_contents dengan stream context sebagai fallback
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: ' . USER_AGENT,
                'Accept: application/json',
            ],
            'timeout' => 15
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ];
    
    $context = stream_context_create($options);
    
    // Coba ambil data dengan file_get_contents
    $response = @file_get_contents($url, false, $context);
    
    // Jika file_get_contents gagal, coba dengan curl
    if ($response === false) {
        $response = getWithCurl($url);
    }
    
    if ($response !== false) {
        // Simpan ke cache
        file_put_contents($cacheFile, $response);
        return json_decode($response, true);
    }
    
    // Jika masih gagal, coba gunakan data fallback dari file lokal
    return getFallbackData();
}

// Fungsi alternatif dengan cURL
function getWithCurl($url) {
    if (!function_exists('curl_init')) {
        return false;
    }
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => USER_AGENT,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("cURL Error: " . $error);
        return false;
    }
    
    return $response;
}

// Fungsi untuk data fallback jika API tidak bisa diakses
function getFallbackData() {
    // Data contoh untuk fallback
    $fallbackData = [
        [
            'id' => 'bitcoin',
            'symbol' => 'btc',
            'name' => 'Bitcoin',
            'image' => 'https://coin-images.coingecko.com/coins/images/1/large/bitcoin.png',
            'current_price' => 45000,
            'market_cap' => 880000000000,
            'total_volume' => 25000000000,
            'price_change_percentage_1h_in_currency' => 0.5,
            'price_change_percentage_24h_in_currency' => 1.2,
            'price_change_percentage_7d_in_currency' => 5.5,
            'sparkline_in_7d' => [
                'price' => array_fill(0, 168, rand(43000, 47000))
            ]
        ],
        [
            'id' => 'ethereum',
            'symbol' => 'eth',
            'name' => 'Ethereum',
            'image' => 'https://coin-images.coingecko.com/coins/images/279/large/ethereum.png',
            'current_price' => 3000,
            'market_cap' => 360000000000,
            'total_volume' => 15000000000,
            'price_change_percentage_1h_in_currency' => 0.3,
            'price_change_percentage_24h_in_currency' => 2.1,
            'price_change_percentage_7d_in_currency' => 8.2,
            'sparkline_in_7d' => [
                'price' => array_fill(0, 168, rand(2800, 3200))
            ]
        ]
    ];
    
    // Tambahkan data untuk crypto lainnya
    $cryptoPrices = [
        'cardano' => 2.5,
        'solana' => 100,
        'ripple' => 0.8,
        'dogecoin' => 0.15,
        'polkadot' => 7,
        'litecoin' => 70,
        'chainlink' => 15,
        'stellar' => 0.3
    ];
    
    foreach ($cryptoPrices as $id => $price) {
        $fallbackData[] = [
            'id' => $id,
            'symbol' => substr($id, 0, 3),
            'name' => ucfirst($id),
            'image' => "https://coin-images.coingecko.com/coins/images/1/large/bitcoin.png", // Placeholder
            'current_price' => $price,
            'market_cap' => $price * rand(1000000, 10000000),
            'total_volume' => $price * rand(100000, 1000000),
            'price_change_percentage_1h_in_currency' => rand(-2, 5),
            'price_change_percentage_24h_in_currency' => rand(-5, 10),
            'price_change_percentage_7d_in_currency' => rand(-10, 20),
            'sparkline_in_7d' => [
                'price' => array_fill(0, 168, rand($price * 0.8, $price * 1.2))
            ]
        ];
    }
    
    return $fallbackData;
}

// Fungsi untuk data global fallback
function getGlobalFallbackData() {
    return [
        'data' => [
            'active_cryptocurrencies' => 12456,
            'total_market_cap' => ['usd' => 1800000000000],
            'total_volume' => ['usd' => 75000000000],
            'market_cap_percentage' => ['btc' => 42.5]
        ]
    ];
}

// Ambil data cryptocurrency
$cryptoIds = implode(',', $cryptoList);
$cryptoData = getCryptoData("/coins/markets?vs_currency=usd&ids={$cryptoIds}&order=market_cap_desc&per_page=10&page=1&sparkline=true&price_change_percentage=1h,24h,7d");

// Jika data tidak ada, gunakan fallback
if (!$cryptoData) {
    $cryptoData = getFallbackData();
}

// Ambil data global market
$globalData = getCryptoData("/global");
if (!$globalData) {
    $globalData = getGlobalFallbackData();
}
?>