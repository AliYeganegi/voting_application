<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallDomPdfFonts extends Command
{
    protected $signature = 'pdf:fonts';
    protected $description = 'Install and cache fonts for DomPDF';

    public function handle()
    {
        // Path to dompdf font cache directory
        $fontDir = storage_path('fonts/');
        $this->info("Working with font directory: $fontDir");

        // Make sure the directory exists
        if (!File::exists($fontDir)) {
            File::makeDirectory($fontDir, 0755, true);
            $this->info("Created font directory");
        }

        // Font metrics installer (manual approach since we don't have dompdf:fonts)
        $this->info("Installing font metrics...");

        // First, let's make sure the font cache is cleared
        foreach (File::glob("$fontDir*.{ufm,ufm.json,afm.json}", GLOB_BRACE) as $file) {
            File::delete($file);
            $this->info("Deleted old font metric: " . basename($file));
        }

        // Now let's manually create the font metrics using DOMPDF's FontLib
        $this->info("Registering Vazirmatn fonts...");

        // Create a temporary PDF to force font registration
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'vazirmatn';
            font-weight: normal;
            font-style: normal;
            src: url('{$fontDir}Vazirmatn-Regular.ttf') format('truetype');
        }
        @font-face {
            font-family: 'vazirmatn';
            font-weight: bold;
            font-style: normal;
            src: url('{$fontDir}Vazirmatn-Bold.ttf') format('truetype');
        }
        body { font-family: 'vazirmatn', sans-serif; direction: rtl; text-align: right; }
        p { font-family: 'vazirmatn', sans-serif; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <p>این یک متن تست است</p>
    <p class="bold">این یک متن پررنگ است</p>
</body>
</html>
HTML;

        // Force generation of font cache
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->output();

        // List newly created font files
        $newFontFiles = File::glob("$fontDir*.{ufm,ufm.json,afm.json}", GLOB_BRACE);
        $this->info("Created " . count($newFontFiles) . " font metric files");

        // Create a simple installed-fonts.json file for reference
        $fonts = [];
        foreach (File::glob("$fontDir*.ttf") as $fontFile) {
            $fonts[] = basename($fontFile);
        }

        File::put("$fontDir/installed-fonts.json", json_encode($fonts, JSON_PRETTY_PRINT));
        $this->info("Created installed-fonts.json with " . count($fonts) . " font entries");

        $this->info("Font installation complete!");

        return Command::SUCCESS;
    }
}
