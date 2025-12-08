<?php

namespace App\Support;

/**
 * Very small PDF builder for text-based exports.
 *
 * This is intentionally minimal and avoids external dependencies while
 * producing standards-compliant PDFs containing plain text content.
 */
class SimplePdf
{
    protected int $pageWidth = 612;
    protected int $pageHeight = 792;
    protected int $margin = 36;
    protected int $wrapLength = 110;
    protected int $baseFontSize = 12;

    /**
     * @var array<int, array<string, int|string>>
     */
    protected array $lines = [];

    public function addHeading(string $text): void
    {
        $this->addLine($text, $this->baseFontSize + 4);
    }

    public function addSubheading(string $text): void
    {
        $this->addLine($text, $this->baseFontSize + 2);
    }

    public function addLine(string $text, ?int $fontSize = null, int $indent = 0): void
    {
        $fontSize ??= $this->baseFontSize;

        foreach ($this->wrapLines($text, $indent) as $line) {
            $this->lines[] = [
                'text' => $line,
                'size' => $fontSize,
                'indent' => $indent,
            ];
        }
    }

    public function addSpacer(int $lines = 1): void
    {
        for ($i = 0; $i < $lines; $i++) {
            $this->lines[] = [
                'text' => '',
                'size' => $this->baseFontSize,
                'indent' => 0,
            ];
        }
    }

    /**
     * Render the PDF as a string.
     */
    public function output(): string
    {
        $pages = $this->buildPages();

        return $this->renderPdf($pages);
    }

    /**
     * Split long lines and normalise whitespace.
     *
     * @return array<int, string>
     */
    protected function wrapLines(string $text, int $indent): array
    {
        $lines = preg_split("/\\r\\n|\\r|\\n/", (string) $text) ?: [''];
        $maxWidth = max(40, $this->wrapLength - ($indent * 4));

        $wrapped = [];

        foreach ($lines as $rawLine) {
            $trimmed = trim($rawLine);

            if ($trimmed === '') {
                $wrapped[] = '';
                continue;
            }

            $chunked = wordwrap($trimmed, $maxWidth, "\n", true);
            $wrapped = array_merge($wrapped, explode("\n", $chunked));
        }

        if ($wrapped === []) {
            return [''];
        }

        return $wrapped;
    }

    /**
     * Distribute wrapped lines across pages with simple line heights.
     *
     * @return array<int, array<int, array<string, int|string>>>
     */
    protected function buildPages(): array
    {
        $pages = [];
        $cursorY = $this->pageHeight - $this->margin;
        $current = [];

        foreach ($this->lines as $line) {
            $lineHeight = $this->lineHeight((int) $line['size']);

            if (($cursorY - $lineHeight) < $this->margin) {
                $pages[] = $current;
                $current = [];
                $cursorY = $this->pageHeight - $this->margin;
            }

            $cursorY -= $lineHeight;

            $current[] = [
                'text' => $line['text'],
                'size' => $line['size'],
                'indent' => $line['indent'],
                'y' => $cursorY,
            ];
        }

        if (! empty($current)) {
            $pages[] = $current;
        }

        if ($pages === []) {
            $pages[] = [];
        }

        return $pages;
    }

    /**
     * Render a single page's content stream.
     */
    protected function renderPageContent(array $pageLines): string
    {
        if ($pageLines === []) {
            return "BT\nET";
        }

        $buffer = ["BT"];

        foreach ($pageLines as $line) {
            $fontSize = (int) $line['size'];
            $x = $this->xForIndent((int) $line['indent']);
            $y = (float) $line['y'];
            $text = $this->escapeText((string) $line['text']);

            $buffer[] = sprintf('/F1 %d Tf', $fontSize);
            $buffer[] = sprintf('1 0 0 1 %.2f %.2f Tm', $x, $y);
            $buffer[] = sprintf('(%s) Tj', $text);
        }

        $buffer[] = 'ET';

        return implode("\n", $buffer);
    }

    protected function escapeText(string $text): string
    {
        return strtr($text, [
            '\\' => '\\\\',
            '(' => '\\(',
            ')' => '\\)',
        ]);
    }

    protected function xForIndent(int $indent): int
    {
        return $this->margin + ($indent * 12);
    }

    protected function lineHeight(int $fontSize): int
    {
        return max(14, (int) round($fontSize * 1.4));
    }

    /**
     * Build the PDF structure, xref table, and trailer.
     *
     * @param  array<int, array<int, array<string, int|string>>>  $pages
     */
    protected function renderPdf(array $pages): string
    {
        $objects = [];
        $objectId = 1;

        $catalogId = $objectId++;
        $pagesId = $objectId++;
        $fontId = $objectId++;

        $pageObjects = [];

        foreach ($pages as $pageLines) {
            $content = $this->renderPageContent($pageLines);
            $contentId = $objectId++;
            $pageId = $objectId++;

            $pageObjects[] = [
                'pageId' => $pageId,
                'contentId' => $contentId,
                'content' => $content,
            ];
        }

        $objects[$catalogId] = sprintf('<< /Type /Catalog /Pages %d 0 R >>', $pagesId);
        $objects[$pagesId] = sprintf(
            '<< /Type /Pages /Kids [%s] /Count %d >>',
            implode(' ', array_map(fn ($p) => $p['pageId'].' 0 R', $pageObjects)),
            count($pageObjects)
        );
        $objects[$fontId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

        foreach ($pageObjects as $page) {
            $objects[$page['pageId']] = sprintf(
                '<< /Type /Page /Parent %d 0 R /MediaBox [0 0 %d %d] /Resources << /Font << /F1 %d 0 R >> >> /Contents %d 0 R >>',
                $pagesId,
                $this->pageWidth,
                $this->pageHeight,
                $fontId,
                $page['contentId']
            );

            $objects[$page['contentId']] = sprintf(
                "<< /Length %d >>\nstream\n%s\nendstream",
                strlen($page['content']),
                $page['content']
            );
        }

        $pdf = "%PDF-1.7\n";
        $offsets = [];

        foreach ($objects as $id => $content) {
            $offsets[$id] = strlen($pdf);
            $pdf .= sprintf("%d 0 obj\n%s\nendobj\n", $id, $content);
        }

        $maxId = max(array_keys($objects));
        $startXref = strlen($pdf);

        $pdf .= "xref\n0 ".($maxId + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= $maxId; $i++) {
            $offset = $offsets[$i] ?? 0;
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= sprintf("trailer << /Size %d /Root %d 0 R >>\n", $maxId + 1, $catalogId);
        $pdf .= "startxref\n".$startXref."\n%%EOF";

        return $pdf;
    }
}
