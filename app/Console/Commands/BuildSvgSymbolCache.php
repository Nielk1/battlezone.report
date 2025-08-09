<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class BuildSvgSymbolCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:build-svg-symbol-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build SVG symbol cache for optimized SVG usage in views';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $svgDir = resource_path('svg');
        $symbols = [];
        $symbols[] = '<svg style="display:none;">';
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($svgDir)) as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'svg') {
                $svg = file_get_contents($file->getPathname());

                // Extract all attributes from the <svg> tag
                preg_match('/<svg\s+([^>]*)>(.*?)<\/svg>/si', $svg, $matches);
                $attrString = $matches[1] ?? '';
                $content = $matches[2] ?? '';

                // Parse attributes into an array
                preg_match_all('/([a-zA-Z_:][a-zA-Z0-9:._-]*)\s*=\s*"([^"]*)"/', $attrString, $attrMatches, PREG_SET_ORDER);
                $attrs = [];
                foreach ($attrMatches as $attr) {
                    // Exclude width, height, xmlns (not needed for <symbol>)
                    if (!in_array(strtolower($attr[1]), ['width', 'height', 'xmlns'])) {
                        $attrs[] = $attr[1] . '="' . $attr[2] . '"';
                    }
                }
                $attrOut = implode(' ', $attrs);

                // Get local path relative to $svgDir, strip .svg extension, and use as id
                $localPath = str_replace('\\', '/', substr($file->getPathname(), strlen($svgDir) + 1));
                $id = 'svg/' . preg_replace('/\.svg$/i', '', $localPath);

                $symbols[] = '  <symbol id="' . $id . '" ' . $attrOut . '>' . $content . '</symbol>';
            }
        }
        $symbols[] = '</svg>';
        file_put_contents(storage_path('app/svg-symbols.svg'), implode("\n", $symbols));
        $this->info('SVG symbol cache built!');
    }
}
