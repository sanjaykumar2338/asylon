<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class TranslationsExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:export
        {--locale= : Locale to export}
        {--format=json : File format (json or csv)}
        {--path= : Custom output path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translation keys for a locale to a single file';

    public function handle(Filesystem $files): int
    {
        $locale = $this->option('locale') ?: config('app.locale', 'en');
        $format = strtolower((string) $this->option('format') ?: 'json');
        $supported = config('app.supported_locales', ['en']);

        if (! in_array($locale, $supported, true)) {
            $this->error("Locale [{$locale}] is not configured. Supported: ".implode(', ', $supported));

            return self::FAILURE;
        }

        $translations = $this->collectTranslations($locale, $files);

        if ($translations === []) {
            $this->warn("No translation files found for locale [{$locale}].");

            return self::SUCCESS;
        }

        $defaultPath = storage_path("app/translations/{$locale}.{$format}");
        $destination = $this->option('path') ?: $defaultPath;

        $files->ensureDirectoryExists(dirname($destination));

        if ($format === 'csv') {
            $this->writeCsv($destination, $translations);
        } else {
            $this->writeJson($destination, $translations);
        }

        $this->info("Exported ".count($translations)." keys to {$destination}");

        return self::SUCCESS;
    }

    /**
     * Collect translation lines for the locale.
     *
     * @return array<string, string>
     */
    protected function collectTranslations(string $locale, Filesystem $files): array
    {
        $basePath = resource_path("lang/{$locale}");
        $translations = [];

        if ($files->isDirectory($basePath)) {
            foreach ($files->files($basePath) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $group = $file->getFilenameWithoutExtension();
                $lines = require $file->getPathname();

                if (! is_array($lines)) {
                    continue;
                }

                $flattened = Arr::dot($lines);

                foreach ($flattened as $key => $value) {
                    $translations["{$group}.{$key}"] = (string) $value;
                }
            }
        }

        $jsonPath = resource_path("lang/{$locale}.json");

        if ($files->exists($jsonPath)) {
            $json = json_decode($files->get($jsonPath), true);

            if (is_array($json)) {
                foreach ($json as $key => $value) {
                    $translations[(string) $key] = (string) $value;
                }
            }
        }

        ksort($translations);

        return $translations;
    }

    /**
     * Write the translations to a JSON file.
     *
     * @param  array<string, string>  $translations
     */
    protected function writeJson(string $path, array $translations): void
    {
        $payload = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        file_put_contents($path, $payload !== false ? "{$payload}\n" : "{}\n");
    }

    /**
     * Write the translations to a CSV file.
     *
     * @param  array<string, string>  $translations
     */
    protected function writeCsv(string $path, array $translations): void
    {
        $handle = fopen($path, 'w');

        if ($handle === false) {
            throw new \RuntimeException("Unable to open {$path} for writing.");
        }

        fputcsv($handle, ['key', 'value']);

        foreach ($translations as $key => $value) {
            fputcsv($handle, [$key, $value]);
        }

        fclose($handle);
    }
}
