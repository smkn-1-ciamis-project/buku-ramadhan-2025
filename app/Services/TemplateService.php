<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TemplateService
{
    /**
     * Generate & download Siswa import template.
     */
    public static function downloadSiswaTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Siswa');

        // Headers
        $headers = [
            'A1' => 'NAMA LENGKAP',
            'B1' => 'NISN',
            'C1' => 'JENIS KELAMIN (L/P)',
            'D1' => 'AGAMA',
            'E1' => 'KELAS',
            'F1' => 'NO. HP',
            'G1' => 'PASSWORD',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Example data rows
        $examples = [
            ['Ahmad Fauzi', '0012345001', 'L', 'Islam', '10 AKL 1', '081234567890', 'siswa123'],
            ['Siti Nurhaliza', '0012345002', 'P', 'Islam', '10 AKL 1', '081234567891', 'siswa123'],
            ['Maria Kristina', '0012345003', 'P', 'Kristen', '10 AKL 1', '', 'siswa123'],
        ];

        $rowIndex = 2;
        foreach ($examples as $example) {
            $sheet->fromArray($example, null, "A{$rowIndex}");
            $rowIndex++;
        }

        // Style example rows (italic, gray)
        $exampleStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => '9CA3AF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ];
        $sheet->getStyle('A2:G4')->applyFromArray($exampleStyle);

        // Column widths
        $widths = ['A' => 25, 'B' => 15, 'C' => 22, 'D' => 15, 'E' => 20, 'F' => 18, 'G' => 15];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Row height
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Instructions sheet
        $instrSheet = $spreadsheet->createSheet();
        $instrSheet->setTitle('Petunjuk Pengisian');

        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT SISWA'],
            [''],
            ['Kolom', 'Keterangan', 'Wajib'],
            ['NAMA LENGKAP', 'Nama lengkap siswa', 'Ya'],
            ['NISN', 'Nomor Induk Siswa Nasional (10 digit)', 'Ya'],
            ['JENIS KELAMIN', 'Isi dengan L (Laki-laki) atau P (Perempuan)', 'Ya'],
            ['AGAMA', 'Islam / Kristen / Katolik / Hindu / Buddha / Konghucu', 'Ya'],
            ['KELAS', 'Nama kelas (contoh: 10 AKL 1). Kosongkan jika belum ada.', 'Tidak'],
            ['NO. HP', 'Nomor HP siswa', 'Tidak'],
            ['PASSWORD', 'Password login. Default: siswa123', 'Tidak'],
            [''],
            ['CATATAN:'],
            ['- Hapus contoh data sebelum mengisi data siswa.'],
            ['- Email otomatis dari NISN: {NISN}@siswa.smkn1ciamis.sch.id'],
            ['- NISN tidak boleh duplikat.'],
            ['- Jika kolom PASSWORD dikosongkan, default: siswa123'],
        ];

        foreach ($instructions as $i => $row) {
            $instrSheet->fromArray($row, null, 'A' . ($i + 1));
        }

        $instrSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
        $instrSheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $instrSheet->getColumnDimension('A')->setWidth(25);
        $instrSheet->getColumnDimension('B')->setWidth(55);
        $instrSheet->getColumnDimension('C')->setWidth(10);

        // Set active sheet back to template
        $spreadsheet->setActiveSheetIndex(0);

        return self::streamDownload($spreadsheet, 'template_import_siswa.xlsx');
    }

    /**
     * Generate & download Guru import template.
     */
    public static function downloadGuruTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Guru');

        // Headers
        $headers = [
            'A1' => 'NAMA LENGKAP',
            'B1' => 'EMAIL (OPSIONAL)',
            'C1' => 'JENIS KELAMIN (L/P)',
            'D1' => 'AGAMA',
            'E1' => 'NO. HP',
            'F1' => 'PASSWORD',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Example data rows
        $examples = [
            ['Drs. Ahmad Suryadi', '', 'L', 'Islam', '081234567890', 'guru123'],
            ['Hj. Siti Aminah, S.Pd', '', 'P', 'Islam', '081234567891', 'guru123'],
        ];

        $rowIndex = 2;
        foreach ($examples as $example) {
            $sheet->fromArray($example, null, "A{$rowIndex}");
            $rowIndex++;
        }

        // Style example rows
        $exampleStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => '9CA3AF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ];
        $sheet->getStyle('A2:F3')->applyFromArray($exampleStyle);

        // Column widths
        $widths = ['A' => 28, 'B' => 35, 'C' => 22, 'D' => 15, 'E' => 18, 'F' => 15];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->getRowDimension(1)->setRowHeight(25);

        // Instructions sheet
        $instrSheet = $spreadsheet->createSheet();
        $instrSheet->setTitle('Petunjuk Pengisian');

        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT GURU'],
            [''],
            ['Kolom', 'Keterangan', 'Wajib'],
            ['NAMA LENGKAP', 'Nama lengkap guru (dengan gelar)', 'Ya'],
            ['EMAIL', 'Email guru. Jika dikosongkan, otomatis dibuat dari nama (format: nama@smkn1ciamis.sch.id)', 'Tidak'],
            ['JENIS KELAMIN', 'Isi dengan L (Laki-laki) atau P (Perempuan)', 'Ya'],
            ['AGAMA', 'Islam / Kristen / Katolik / Hindu / Buddha / Konghucu', 'Tidak'],
            ['NO. HP', 'Nomor HP guru', 'Tidak'],
            ['PASSWORD', 'Password login. Default: guru123', 'Tidak'],
            [''],
            ['CATATAN:'],
            ['- Hapus contoh data sebelum mengisi data guru.'],
            ['- Jika EMAIL dikosongkan, otomatis dibuat dari nama guru.'],
            ['- Email tidak boleh duplikat.'],
            ['- Jika kolom PASSWORD dikosongkan, default: guru123'],
        ];

        foreach ($instructions as $i => $row) {
            $instrSheet->fromArray($row, null, 'A' . ($i + 1));
        }

        $instrSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
        $instrSheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $instrSheet->getColumnDimension('A')->setWidth(25);
        $instrSheet->getColumnDimension('B')->setWidth(55);
        $instrSheet->getColumnDimension('C')->setWidth(10);

        $spreadsheet->setActiveSheetIndex(0);

        return self::streamDownload($spreadsheet, 'template_import_guru.xlsx');
    }

    /**
     * Stream download the spreadsheet.
     */
    private static function streamDownload(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
