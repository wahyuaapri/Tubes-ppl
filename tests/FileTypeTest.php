<?php
use PHPUnit\Framework\TestCase;

class FileTypeTest extends TestCase
{
    private $projectFiles = [
        'index.php',
        'market.php',
        'portofolio.php',
        'about.php'
    ];

    // Validator 1: Cek semua file ada
    public function test_all_required_files_exist()
    {
        foreach ($this->projectFiles as $file) {
            $this->assertFileExists(
                $file, 
                "File wajib '$file' tidak ditemukan! File yang dibutuhkan: " . implode(', ', $this->projectFiles)
            );
        }
        $this->assertCount(2, $this->projectFiles, "Harus ada tepat 2 file dalam proyek ini");
    }

    // Validator 2: Cek semua file PHP memiliki kode PHP yang valid
    public function test_all_php_files_have_valid_php_code()
    {
        foreach ($this->projectFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($file);
                
                $this->assertStringContainsString(
                    '<?php', 
                    $content, 
                    "File '$file' harus mengandung tag pembuka PHP (<?php)"
                );
                
                // Cek tidak ada syntax error fatal
                $this->assertDoesNotMatchRegularExpression(
                    '/<\?=\s*[\'"]/',
                    $content,
                    "File '$file' mengandung syntax PHP yang mungkin bermasalah"
                );
            }
        }
    }

    // Validator 3: Cek semua file memiliki struktur HTML yang baik
    public function test_all_files_have_valid_html_structure()
    {
        $requiredTags = ['<html', '<head', '<body', '<div', '</html'];
        $passedFiles = 0;
        
        foreach ($this->projectFiles as $file) {
            $content = file_get_contents($file);
            $hasAllTags = true;
            
            foreach ($requiredTags as $tag) {
                if (stripos($content, $tag) === false) {
                    $hasAllTags = false;
                    break;
                }
            }
            
            if ($hasAllTags) {
                $passedFiles++;
            }
        }
        
        $this->assertGreaterThanOrEqual(
            1, 
            $passedFiles, 
            "Minimal satu file harus memiliki struktur HTML lengkap (mengandung <html>, <head>, <body>, <div>, dan </html>)"
        );
    }

    // Validator 4: Cek semua file memiliki CSS dan JavaScript yang diperlukan
    public function test_all_files_have_required_resources()
    {
        $requiredResources = [
            'style.css' => 'stylesheet',
            'script.js' => 'script',
            'font-awesome' => 'icon library',
            'googleapis.com' => 'fonts'
        ];
        
        foreach ($this->projectFiles as $file) {
            $content = file_get_contents($file);
            $foundResources = 0;
            
            foreach ($requiredResources as $resource => $description) {
                if (stripos($content, $resource) !== false) {
                    $foundResources++;
                }
            }
            
            $this->assertGreaterThanOrEqual(
                2, 
                $foundResources, 
                "File '$file' harus memiliki minimal 2 resource yang diperlukan (CSS/JS/fonts)"
            );
        }
    }

    // Validator 5: Cek semua file memiliki konten yang cukup dan tidak kosong
    public function test_all_files_have_sufficient_content()
    {
        foreach ($this->projectFiles as $file) {
            $content = file_get_contents($file);
            $contentLength = strlen($content);
            
            // Cek file tidak kosong
            $this->assertGreaterThan(
                100, 
                $contentLength, 
                "File '$file' terlalu pendek ($contentLength karakter). Minimal 100 karakter."
            );
            
            // Cek ada konten yang bermakna (bukan hanya whitespace)
            $nonWhitespaceLength = strlen(preg_replace('/\s+/', '', $content));
            $this->assertGreaterThan(
                50,
                $nonWhitespaceLength,
                "File '$file' memiliki terlalu sedikit konten non-whitespace ($nonWhitespaceLength karakter)"
            );
            
            // Cek ada minimal satu tag HTML yang bermakna
            $this->assertMatchesRegularExpression(
                '/<(div|p|span|h[1-6]|a)[^>]*>/i',
                $content,
                "File '$file' harus memiliki minimal satu elemen HTML (div, p, span, heading, atau link)"
            );
        }
    }
}