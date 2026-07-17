<?php

namespace App\Support;

use ZipArchive;

/**
 * SimpleXlsxWriter
 * ----------------
 * Generator file .xlsx (Open XML Spreadsheet) minimal, murni PHP — tanpa
 * dependency composer tambahan (hanya butuh ext-zip & ext-xmlwriter bawaan PHP).
 *
 * Dibuat khusus untuk kebutuhan export sederhana (tabel nilai & presensi) di
 * Asistio, sehingga tidak perlu menambah library berat seperti maatwebsite/excel.
 *
 * Cara pakai singkat:
 *   $xlsx = new SimpleXlsxWriter();
 *   $xlsx->addSheet('Rekap Nilai', $header, $rows);
 *   $xlsx->addSheet('Rekap Presensi', $header2, $rows2);
 *   return $xlsx->download('rekap.xlsx');
 */
class SimpleXlsxWriter
{
    /** @var array<int, array{name:string, header:array<int,string>, rows:array<int,array<int,mixed>>, bold_header:bool}> */
    private array $sheets = [];

    /** @var array<string, array> */
    private array $dropdowns = [];

    public function setDropdowns(string $sheetName, array $dropdowns): self
    {
        $this->dropdowns[$this->sanitizeSheetName($sheetName)] = $dropdowns;
        return $this;
    }
    public function addSheet(string $name, array $header, array $rows, bool $boldHeader = true): self
    {
        $safeName = $this->sanitizeSheetName($name);
        $this->sheets[] = [
            'name'        => $safeName,
            'header'      => $header,
            'rows'        => $rows,
            'bold_header' => $boldHeader,
        ];
        return $this;
    }

    private function sanitizeSheetName(string $name): string
    {
        $name = preg_replace('/[\\\\\/\?\*\[\]\:]/', ' ', $name) ?? $name;
        $name = trim($name);
        return mb_substr($name === '' ? 'Sheet' : $name, 0, 31);
    }

    public function render(): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        if ($tmpFile === false) {
            throw new \RuntimeException('Gagal membuat file sementara untuk export Excel.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuka writer ZIP untuk file .xlsx.');
        }

        $zip->addEmptyDir('_rels');
        $zip->addEmptyDir('docProps');
        $zip->addEmptyDir('xl');
        $zip->addEmptyDir('xl/_rels');
        $zip->addEmptyDir('xl/worksheets');

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('docProps/core.xml', $this->coreXml());
        $zip->addFromString('docProps/app.xml', $this->appXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());

        foreach ($this->sheets as $i => $sheet) {
            $zip->addFromString('xl/worksheets/sheet' . ($i + 1) . '.xml', $this->sheetXml($sheet));
        }

        $zip->close();

        $contents = file_get_contents($tmpFile);
        @unlink($tmpFile);

        if ($contents === false) {
            throw new \RuntimeException('Gagal membaca file .xlsx hasil export.');
        }

        return $contents;
    }

    public function download(string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $contents = $this->render();

        return response($contents, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => (string) strlen($contents),
            'Cache-Control'       => 'no-store, no-cache',
        ]);
    }

    private function contentTypesXml(): string
    {
        $overrides = '';
        foreach ($this->sheets as $i => $sheet) {
            $n = $i + 1;
            $overrides .= "<Override PartName=\"/xl/worksheets/sheet{$n}.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml\"/>";
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . $overrides
            . '</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function coreXml(): string
    {
        $now = now()->toAtomString();
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
            . 'xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:creator>Asistio</dc:creator>'
            . '<cp:lastModifiedBy>Asistio</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    private function appXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" '
            . 'xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>Asistio</Application>'
            . '</Properties>';
    }

    private function workbookXml(): string
    {
        $sheetsXml = '';
        foreach ($this->sheets as $i => $sheet) {
            $n = $i + 1;
            $sheetsXml .= '<sheet name="' . $this->escape($sheet['name']) . '" sheetId="' . $n . '" r:id="rId' . $n . '"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheetsXml . '</sheets>'
            . '</workbook>';
    }

    private function workbookRelsXml(): string
    {
        $rels = '';
        $n = count($this->sheets);
        foreach ($this->sheets as $i => $sheet) {
            $id = $i + 1;
            $rels .= '<Relationship Id="rId' . $id . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $id . '.xml"/>';
        }
        $stylesId = $n + 1;
        $rels .= '<Relationship Id="rId' . $stylesId . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $rels
            . '</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="2">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFE9EEF7"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="3">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="1" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            . '</cellXfs>'
            . '</styleSheet>';
    }

    private function sheetXml(array $sheet): string
    {
        $rowsXml = '';
        $rowIndex = 1;

        if (!empty($sheet['header'])) {
            $rowsXml .= $this->buildRow($rowIndex, $sheet['header'], $sheet['bold_header'] ? 1 : 0);
            $rowIndex++;
        }

        foreach ($sheet['rows'] as $row) {
            $rowsXml .= $this->buildRow($rowIndex, $row, 0);
            $rowIndex++;
        }

        $colCount = max(1, count($sheet['header']));
        $lastCol  = $this->colLetter($colCount - 1);
        $lastRow  = max(1, $rowIndex - 1);

        // Buat dataValidation jika ada dropdown untuk sheet ini
        $dvXml = '';
        if (isset($this->dropdowns[$sheet['name']])) {
            $dvItems = '';
            foreach ($this->dropdowns[$sheet['name']] as [$col, $rowStart, $rowEnd, $formula]) {
                $sqref = "{$col}{$rowStart}:{$col}{$rowEnd}";
                $dvItems .= '<dataValidation type="list" allowBlank="1" showDropDown="0"'
                    . ' showInputMessage="1" showErrorMessage="1"'
                    . ' sqref="' . $sqref . '">'
                    . '<formula1>' . htmlspecialchars($formula, ENT_XML1) . '</formula1>'
                    . '</dataValidation>';
            }
            if ($dvItems) {
                $dvXml = '<dataValidations count="' . count($this->dropdowns[$sheet['name']]) . '">' . $dvItems . '</dataValidations>';
            }
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<dimension ref="A1:' . $lastCol . $lastRow . '"/>'
            . '<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="15"/>'
            . '<sheetData>' . $rowsXml . '</sheetData>'
            . $dvXml
            . '</worksheet>';
    }

    private function buildRow(int $rowIndex, array $values, int $styleIndex): string
    {
        $cells = '';
        foreach (array_values($values) as $colIdx => $value) {
            $ref = $this->colLetter($colIdx) . $rowIndex;
            $cells .= $this->buildCell($ref, $value, $styleIndex);
        }
        return '<row r="' . $rowIndex . '">' . $cells . '</row>';
    }

    private function buildCell(string $ref, mixed $value, int $styleIndex): string
    {
        $styleAttr = $styleIndex > 0 ? ' s="' . $styleIndex . '"' : '';

        if ($value === null || $value === '') {
            return '<c r="' . $ref . '"' . $styleAttr . '/>';
        }

        if (is_int($value) || is_float($value)) {
            return '<c r="' . $ref . '"' . $styleAttr . '><v>' . $value . '</v></c>';
        }

        $escaped = $this->escape((string) $value);
        return '<c r="' . $ref . '" t="inlineStr"' . $styleAttr . '><is><t xml:space="preserve">' . $escaped . '</t></is></c>';
    }

    private function colLetter(int $index): string
    {
        $letter = '';
        $index++;
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = intdiv($index - $mod, 26);
        }
        return $letter;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}