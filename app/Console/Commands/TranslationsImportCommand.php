<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class TranslationsImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:import
        {file : Path to the translation file}
        {--locale= : Locale to import. Defaults to APP_LOCALE}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import translation keys from a JSON or CSV file.';

    public function handle(Filesystem $files): int
    {
        $source = $this->argument('file');
        $locale = $this->option('locale') ?: config('app.locale', 'en');
        $supported = config('app.supported_locales', ['en']);

        if (! in_array($locale, $supported, true)) {
            $this->error("Locale [{$locale}] is not configured. Supported: ".implode(', ', $supported));

            return self::FAILURE;
        }

        if (! $files->exists($source)) {
            $this->error("Translation file not found at {$source}");

            return self::FAILURE;
        }

        $payload = $this->readTranslations($source);

        if ($payload === []) {
            $this->warn('No translations detected in the provided file.');

            return self::SUCCESS;
        }

        [$grouped, $jsonEntries] = $this->separateGroups($payload);

        foreach ($grouped as $group => $values) {
            $path = resource_path("lang/{$locale}/{$group}.php");
            $existing = $files->exists($path) ? require $path : [];

            if (! is_array($existing)) {
                $existing = [];
            }

            $merged = array_replace_recursive($existing, $values);

            $files->ensureDirectoryExists(dirname($path));
            $files->put($path, $this->exportPhp($merged));
        }

        $jsonPath = resource_path("lang/{$locale}.json");
        $existingJson = [];

        if ($files->exists($jsonPath)) {
            $decoded = json_decode($files->get($jsonPath), true);
            $existingJson = is_array($decoded) ? $decoded : [];
        }

        if ($jsonEntries !== []) {
            $mergedJson = array_replace($existingJson, $jsonEntries);
            $files->put(
                $jsonPath,
                json_encode($mergedJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n"
            );
        }

        $this->info('Translations imported successfully.');

        return self::SUCCESS;
    }

    /**
     * Read JSON or CSV translations into an array.
     *
     * @return array<string, string>
     */
    protected function readTranslations(string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            return $this->readCsv($path);
        }

        return $this->readJson($path);
    }

    /**
     * @return array<string, string>
     */
    protected function readJson(string $path): array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? array_map('strval', $decoded) : [];
    }

    /**
     * @return array<string, string>
     */
    protected function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return [];
        }

        $translations = [];
        $headerSkipped = false;

        while (($row = fgetcsv($handle)) !== false) {
            if ($headerSkipped === false) {
                $headerSkipped = true;

                // Skip header rows containing "key" and "value".
                if (isset($row[0], $row[1]) && strtolower($row[0]) === 'key' && strtolower($row[1]) === 'value') {
                    continue;
                }
            }

            if (! isset($row[0]) || ! array_key_exists(1, $row)) {
                continue;
            }

            $translations[(string) $row[0]] = (string) $row[1];
        }

        fclose($handle);

        return $translations;
    }

    /**
     * Break translation keys into grouped PHP files and JSON entries.
     *
     * @param  array<string, string>  $translations
     * @return array{0: array<string, array>, 1: array<string, string>}
     */
    protected function separateGroups(array $translations): array
    {
        $grouped = [];
        $jsonEntries = [];

        foreach ($translations as $key => $value) {
            if (str_contains($key, '.')) {
                [$group, $nested] = explode('.', $key, 2);
                Arr::set($grouped[$group], $nested, $value);
            } else {
                $jsonEntries[$key] = $value;
            }
        }

        return [$grouped, $jsonEntries];
    }

    /**
     * Export an array as a PHP translation file string.
     */
    protected function exportPhp(array $values): string
    {
        $export = var_export($values, true);

        return "<?php\n\nreturn {$export};\n";
    }
}
