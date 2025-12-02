<?php

namespace App\Support;

class TemplateRenderer
{
    /**
     * Render a template by replacing {{placeholders}} with values.
     */
    public static function render(string $template, array $data = []): string
    {
        if ($template === '') {
            return '';
        }

        $replacements = [];

        foreach ($data as $key => $value) {
            $replacements['{{' . $key . '}}'] = (string) $value;
        }

        return strtr($template, $replacements);
    }

    /**
     * Ensure the SMS template contains the compliance footer once.
     */
    public static function ensureSmsCompliance(string $body): string
    {
        $body = trim($body);

        $compliance = config('messaging.default_sms_footer', config('notification_templates.compliance_sms_line', ''));
        if ($compliance === '') {
            return $body;
        }

        $normalizedBody = strtolower($body);
        $normalizedCompliance = strtolower($compliance);

        $hasCompliance = $normalizedCompliance !== ''
            && str_contains($normalizedBody, $normalizedCompliance);

        if (! $hasCompliance) {
            $hasCompliance = str_contains($normalizedBody, 'msg freq may vary')
                || str_contains($normalizedBody, 'reply stop')
                || str_contains($normalizedBody, 'help for help');
        }

        if ($hasCompliance) {
            return $body;
        }

        if ($body === '') {
            return $compliance;
        }

        return $body . "\n" . $compliance;
    }
}
