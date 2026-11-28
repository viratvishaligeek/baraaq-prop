<?php

namespace App\Services\CircleRate;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class SpreadsheetReader
{
    public function read(string $path, string $extension): array
    {
        $extension = strtolower($extension);
        if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            return $this->normalizeRows($rows);
        }

        return match ($extension) {
            'csv' => $this->readCsv($path),
            'xlsx' => $this->readXlsx($path),
            'xls' => throw new RuntimeException('Legacy XLS files require PhpSpreadsheet. Save this workbook as XLSX or CSV, or install the PhpSpreadsheet dependency.'),
            default => throw new RuntimeException('Unsupported spreadsheet format.'),
        };
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        if (! $handle) throw new RuntimeException('The uploaded CSV file could not be opened.');

        $sample = fgets($handle);
        if ($sample === false) {
            fclose($handle);
            return ['headers' => [], 'rows' => []];
        }

        $counts = [
            ',' => substr_count($sample, ','),
            ';' => substr_count($sample, ';'),
            "\t" => substr_count($sample, "\t"),
        ];
        arsort($counts);
        $delimiter = array_key_first($counts);
        rewind($handle);

        $raw = [];
        while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            if (count(array_filter($row, fn ($value) => $value !== null && trim((string) $value) !== '')) === 0) continue;
            $raw[] = $row;
        }
        fclose($handle);

        return $this->normalizeRows($raw);
    }

    private function readXlsx(string $path): array
    {
        if (! class_exists(ZipArchive::class) || ! function_exists('simplexml_load_string')) {
            throw new RuntimeException('XLSX reading requires the PHP ZipArchive and SimpleXML extensions, or PhpSpreadsheet.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) throw new RuntimeException('The XLSX workbook is invalid or could not be opened.');

        try {
            $workbook = $this->xml($zip->getFromName('xl/workbook.xml'));
            $relationships = $this->xml($zip->getFromName('xl/_rels/workbook.xml.rels'));
            $mainNamespace = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
            $relationshipNamespace = 'http://schemas.openxmlformats.org/package/2006/relationships';
            $workbookMain = $workbook->children($mainNamespace);
            $relationshipRoot = $relationships->children($relationshipNamespace);
            $sheet = $workbookMain->sheets->sheet[0] ?? null;
            if (! $sheet) return ['headers' => [], 'rows' => []];

            $relationshipId = (string) $sheet->attributes('r', true)['id'];
            $target = null;
            foreach ($relationshipRoot->Relationship as $relationship) {
                if ((string) $relationship['Id'] === $relationshipId) {
                    $target = (string) $relationship['Target'];
                    break;
                }
            }
            if (! $target) throw new RuntimeException('The first worksheet could not be resolved.');

            $sheetPath = str_starts_with($target, '/') ? ltrim($target, '/') : 'xl/'.ltrim($target, '/');
            $worksheet = $this->xml($zip->getFromName($sheetPath));
            $sharedStrings = [];
            $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
            if ($sharedXml !== false) {
                $shared = $this->xml($sharedXml);
                foreach ($shared->children($mainNamespace)->si as $item) {
                    $itemMain = $item->children($mainNamespace);
                    $text = '';
                    if (isset($itemMain->t)) $text = (string) $itemMain->t;
                    else foreach ($itemMain->r as $run) $text .= (string) $run->children($mainNamespace)->t;
                    $sharedStrings[] = $text;
                }
            }

            $matrix = [];
            foreach ($worksheet->children($mainNamespace)->sheetData->row ?? [] as $row) {
                $values = [];
                foreach ($row->children($mainNamespace)->c as $cell) {
                    $reference = (string) $cell['r'];
                    preg_match('/^[A-Z]+/', $reference, $match);
                    $column = $this->columnIndex($match[0] ?? 'A');
                    $type = (string) $cell['t'];
                    $cellMain = $cell->children($mainNamespace);
                    if ($type === 's') {
                        $value = $sharedStrings[(int) ($cellMain->v ?? 0)] ?? '';
                    } elseif ($type === 'inlineStr') {
                        $value = isset($cellMain->is->t) ? (string) $cellMain->is->t : '';
                        if ($value === '') foreach ($cellMain->is->r as $run) $value .= (string) $run->t;
                    } else {
                        $value = isset($cellMain->v) ? (string) $cellMain->v : '';
                    }
                    $values[$column] = $value;
                }
                if ($values) {
                    ksort($values);
                    $matrix[] = array_values($values);
                }
            }

            return $this->normalizeRows($matrix);
        } finally {
            $zip->close();
        }
    }

    private function xml(string|false $content): SimpleXMLElement
    {
        if ($content === false) throw new RuntimeException('A required XLSX workbook component is missing.');
        $xml = simplexml_load_string($content, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
        if (! $xml) throw new RuntimeException('The XLSX workbook contains invalid XML.');
        return $xml;
    }

    private function columnIndex(string $letters): int
    {
        $number = 0;
        foreach (str_split($letters) as $letter) $number = ($number * 26) + (ord($letter) - 64);
        return $number;
    }

    private function normalizeRows(array $matrix): array
    {
        if (! $matrix) return ['headers' => [], 'rows' => []];
        $headers = array_map(fn ($value) => trim((string) $value), array_shift($matrix));
        $headers = array_map(fn ($value, $index) => $value !== '' ? $value : 'Column '.($index + 1), $headers, array_keys($headers));
        $rows = [];

        foreach ($matrix as $row) {
            $normalized = [];
            foreach ($headers as $index => $header) $normalized[$header] = isset($row[$index]) ? trim((string) $row[$index]) : '';
            if (count(array_filter($normalized, fn ($value) => $value !== '')) > 0) $rows[] = $normalized;
        }

        return ['headers' => $headers, 'rows' => $rows];
    }
}
