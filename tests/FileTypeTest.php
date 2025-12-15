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

    // Validator 1: Cek semua file PHP ada
    public function test_all_required_php_files_exist()
    {
        $this->assertCount(4, $this->projectFiles, "Harus ada tepat 4 file PHP dalam proyek ini");
        
        foreach ($this->projectFiles as $file) {
            $this->assertFileExists(
                $file, 
                "File wajib '$file' tidak ditemukan! File yang dibutuhkan: " . implode(', ', $this->projectFiles)
            );
        }
    }

    // Validator 2: Cek semua file PHP memiliki sintaks yang valid
    public function test_all_php_files_have_valid_syntax()
    {
        foreach ($this->projectFiles as $file) {
            $content = file_get_contents($file);
            
            // Cek tag pembuka PHP
            $this->assertStringContainsString(
                '<?php', 
                $content, 
                "File '$file' harus mengandung tag pembuka PHP (<?php)"
            );
            
            // Cek sintaks PHP dengan lint
            $output = null;
            $return_var = null;
            exec("php -l $file", $output, $return_var);
            
            $this->assertEquals(
                0, 
                $return_var, 
                "File '$file' memiliki syntax error PHP: " . implode("\n", $output)
            );
        }
    }

    // Validator 3: Cek struktur HTML dasar di semua file
    public function test_all_files_have_basic_html_structure()
    {
        foreach ($this->projectFiles as $file) {
            $content = file_get_contents($file);
            
            // Cek tag HTML dasar
            $this->assertStringContainsStringIgnoringCase(
                '<!DOCTYPE html>', 
                $content, 
                "File '$file' harus memiliki DOCTYPE HTML"
            );
            
            $this->assertStringContainsStringIgnoringCase(
                '<html', 
                $content, 
                "File '$file' harus memiliki tag <html>"
            );
            
            $this->assertStringContainsStringIgnoringCase(
                '<head', 
                $content, 
                "File '$file' harus memiliki tag <head>"
            );
            
            $this->assertStringContainsStringIgnoringCase(
                '<body', 
                $content, 
                "File '$file' harus memiliki tag <body>"
            );
        }
    }

    // Validator 4: Cek penggunaan CSS dan JavaScript
    public function test_all_files_have_css_and_javascript_references()
    {
        foreach ($this->projectFiles as $file) {
            $content = file_get_contents($file);
            
            // Cek adanya CSS (inline atau external)
            $hasCSS = preg_match('/<style[^>]*>|\.css|style\s*=/i', $content);
            $this->assertTrue(
                (bool)$hasCSS,
                "File '$file' harus memiliki referensi CSS (tag <style>, file .css, atau atribut style)"
            );
            
            // Cek adanya JavaScript (inline atau external)
            $hasJS = preg_match('/<script[^>]*>|\.js|onclick\s*=|onload\s*=/i', $content);
            $this->assertTrue(
                (bool)$hasJS,
                "File '$file' harus memiliki referensi JavaScript (tag <script>, file .js, atau event handler)"
            );
            
            // Cek minimal ada 1 link atau meta tag
            $hasLinks = preg_match('/<link[^>]*>|<meta[^>]*>|<a[^>]*href/i', $content);
            $this->assertTrue(
                (bool)$hasLinks,
                "File '$file' harus memiliki minimal satu link, meta tag, atau anchor tag"
            );
        }
    }

    // Validator 5: Cek konten dan struktur konten yang bermakna
    public function test_all_files_have_meaningful_content()
    {
        $minContentLength = 200; // Minimal 200 karakter
        $minNonWhitespace = 80;  // Minimal 80 karakter non-whitespace
        
        foreach ($this->projectFiles as $file) {
            $content = file_get_contents($file);
            $contentLength = strlen($content);
            
            // Cek panjang konten
            $this->assertGreaterThanOrEqual(
                $minContentLength, 
                $contentLength, 
                "File '$file' terlalu pendek ($contentLength karakter). Minimal $minContentLength karakter."
            );
            
            // Cek konten non-whitespace
            $nonWhitespaceLength = strlen(preg_replace('/\s+/', '', $content));
            $this->assertGreaterThanOrEqual(
                $minNonWhitespace,
                $nonWhitespaceLength,
                "File '$file' memiliki terlalu sedikit konten non-whitespace ($nonWhitespaceLength karakter). Minimal $minNonWhitespace karakter."
            );
            
            // Cek ada heading atau paragraf
            $hasContentElements = preg_match('/<(h[1-6]|p|div|section|article)[^>]*>/i', $content);
            $this->assertTrue(
                (bool)$hasContentElements,
                "File '$file' harus memiliki minimal satu elemen konten (heading, paragraf, div, section, atau article)"
            );
            
            // Cek penutup tag yang benar
            $this->assertStringContainsStringIgnoringCase(
                '</body>', 
                $content, 
                "File '$file' harus memiliki tag penutup </body>"
            );
            
            $this->assertStringContainsStringIgnoringCase(
                '</html>', 
                $content, 
                "File '$file' harus memiliki tag penutup </html>"
            );
        }
    }
}