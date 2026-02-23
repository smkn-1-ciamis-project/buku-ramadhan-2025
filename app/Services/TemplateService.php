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

    /**
     * Generate & download Kelas import template (multi-sheet).
     *
     * Sheet 1: "Daftar Kelas" — nama kelas + nama/email wali guru
     * Sheet 2: "Data Guru" — data guru yang akan dijadikan wali
     * Sheet 3-N: satu sheet per contoh kelas — data siswa
     * Sheet terakhir: "Petunjuk Pengisian"
     */
    public static function downloadKelasTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $guruHeaderStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $exampleStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => '9CA3AF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ];
        $noteStyle = [
            'font' => ['italic' => true, 'color' => ['rgb' => 'DC2626'], 'size' => 10],
        ];

        // ═══════════════════════════════════════
        // SHEET 1: DAFTAR KELAS
        // ═══════════════════════════════════════
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Kelas');

        $sheet->setCellValue('A1', 'NAMA KELAS *');
        $sheet->setCellValue('B1', 'WALI KELAS (EMAIL GURU)');
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        $examples = [
            ['10 AKL 1', 'ahmad.suryadi@smkn1ciamis.sch.id'],
            ['10 AKL 2', 'siti.aminah@smkn1ciamis.sch.id'],
            ['10 RPL 1', ''],
        ];
        $row = 2;
        foreach ($examples as $ex) {
            $sheet->fromArray($ex, null, "A{$row}");
            $row++;
        }
        $sheet->getStyle('A2:B4')->applyFromArray($exampleStyle);

        $sheet->setCellValue('A6', '* Nama kelas HARUS SAMA PERSIS dengan nama sheet siswa.');
        $sheet->setCellValue('A7', '* Email wali kelas harus sama dengan email di sheet "Data Guru".');
        $sheet->setCellValue('A8', '* Guru akan otomatis didaftarkan dari sheet "Data Guru" jika belum ada.');
        $sheet->getStyle('A6:A8')->applyFromArray($noteStyle);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // ═══════════════════════════════════════
        // SHEET 2: DATA GURU
        // ═══════════════════════════════════════
        $guruSheet = $spreadsheet->createSheet();
        $guruSheet->setTitle('Data Guru');

        $guruHeaders = ['NAMA LENGKAP *', 'EMAIL *', 'JENIS KELAMIN (L/P) *', 'AGAMA', 'NO. HP', 'PASSWORD'];
        $col = 'A';
        foreach ($guruHeaders as $h) {
            $guruSheet->setCellValue($col . '1', $h);
            $col++;
        }
        $guruSheet->getStyle('A1:F1')->applyFromArray($guruHeaderStyle);

        $guruExamples = [
            ['Drs. Ahmad Suryadi, M.Pd', 'ahmad.suryadi@smkn1ciamis.sch.id', 'L', 'Islam', '081234567890', 'guru123'],
            ['Hj. Siti Aminah, S.Pd', 'siti.aminah@smkn1ciamis.sch.id', 'P', 'Islam', '081234567891', 'guru123'],
            ['Budi Hartono, S.Kom', 'budi.hartono@smkn1ciamis.sch.id', 'L', 'Islam', '', 'guru123'],
        ];
        $row = 2;
        foreach ($guruExamples as $ex) {
            $guruSheet->fromArray($ex, null, "A{$row}");
            $row++;
        }
        $guruSheet->getStyle('A2:F4')->applyFromArray($exampleStyle);

        $guruSheet->setCellValue('A6', '* Guru yang email-nya sudah terdaftar akan dilewati (tidak duplikat).');
        $guruSheet->setCellValue('A7', '* Email guru digunakan untuk mencocokkan wali kelas di sheet "Daftar Kelas".');
        $guruSheet->setCellValue('A8', '* Jika EMAIL dikosongkan, email otomatis dibuat dari nama guru.');
        $guruSheet->getStyle('A6:A8')->applyFromArray($noteStyle);

        $guruWidths = ['A' => 28, 'B' => 38, 'C' => 22, 'D' => 15, 'E' => 18, 'F' => 15];
        foreach ($guruWidths as $c => $w) {
            $guruSheet->getColumnDimension($c)->setWidth($w);
        }
        $guruSheet->getRowDimension(1)->setRowHeight(25);

        // ═══════════════════════════════════════
        // SHEET 3: CONTOH KELAS "10 AKL 1"
        // ═══════════════════════════════════════
        $siswaHeaders = ['NAMA LENGKAP *', 'NISN *', 'JENIS KELAMIN (L/P) *', 'AGAMA *', 'NO. HP', 'PASSWORD'];
        $siswaWidths = ['A' => 25, 'B' => 15, 'C' => 22, 'D' => 15, 'E' => 18, 'F' => 15];

        $siswaSheet = $spreadsheet->createSheet();
        $siswaSheet->setTitle('10 AKL 1');

        $col = 'A';
        foreach ($siswaHeaders as $h) {
            $siswaSheet->setCellValue($col . '1', $h);
            $col++;
        }
        $siswaSheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        $siswaExamples = [
            ['Ahmad Fauzi', '0012345001', 'L', 'Islam', '081234567890', 'siswa123'],
            ['Siti Nurhaliza', '0012345002', 'P', 'Islam', '081234567891', 'siswa123'],
            ['Maria Kristina', '0012345003', 'P', 'Kristen', '', 'siswa123'],
        ];
        $row = 2;
        foreach ($siswaExamples as $ex) {
            $siswaSheet->fromArray($ex, null, "A{$row}");
            $row++;
        }
        $siswaSheet->getStyle('A2:F4')->applyFromArray($exampleStyle);

        foreach ($siswaWidths as $c => $w) {
            $siswaSheet->getColumnDimension($c)->setWidth($w);
        }
        $siswaSheet->getRowDimension(1)->setRowHeight(25);

        // ═══════════════════════════════════════
        // SHEET 4: CONTOH KELAS "10 AKL 2"
        // ═══════════════════════════════════════
        $siswaSheet2 = $spreadsheet->createSheet();
        $siswaSheet2->setTitle('10 AKL 2');

        $col = 'A';
        foreach ($siswaHeaders as $h) {
            $siswaSheet2->setCellValue($col . '1', $h);
            $col++;
        }
        $siswaSheet2->getStyle('A1:F1')->applyFromArray($headerStyle);

        $siswaExamples2 = [
            ['Budi Santoso', '0012345010', 'L', 'Islam', '081234567900', 'siswa123'],
            ['Dewi Rahayu', '0012345011', 'P', 'Hindu', '', 'siswa123'],
        ];
        $row = 2;
        foreach ($siswaExamples2 as $ex) {
            $siswaSheet2->fromArray($ex, null, "A{$row}");
            $row++;
        }
        $siswaSheet2->getStyle('A2:F3')->applyFromArray($exampleStyle);

        foreach ($siswaWidths as $c => $w) {
            $siswaSheet2->getColumnDimension($c)->setWidth($w);
        }
        $siswaSheet2->getRowDimension(1)->setRowHeight(25);

        // ═══════════════════════════════════════
        // SHEET TERAKHIR: PETUNJUK PENGISIAN
        // ═══════════════════════════════════════
        $instrSheet = $spreadsheet->createSheet();
        $instrSheet->setTitle('Petunjuk Pengisian');

        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT KELAS'],
            [''],
            ['CARA PENGISIAN:'],
            ['1. Isi sheet "Data Guru" dengan data guru yang akan menjadi wali kelas.'],
            ['2. Buka sheet "Daftar Kelas", isi nama kelas dan email wali (harus sama dengan email di "Data Guru").'],
            ['3. Untuk SETIAP kelas, buat sheet baru dengan nama sheet = nama kelas (persis sama).'],
            ['4. Di setiap sheet kelas, isi data siswa sesuai format header di baris pertama.'],
            ['5. Hapus contoh data sebelum mengisi data sebenarnya.'],
            ['6. Upload file ini melalui tombol "Import Kelas" di halaman manajemen kelas.'],
            [''],
            ['URUTAN PROSES IMPORT:'],
            ['1. Guru didaftarkan terlebih dahulu dari sheet "Data Guru".'],
            ['2. Kelas dibuat dari sheet "Daftar Kelas" dan wali otomatis ditugaskan.'],
            ['3. Siswa didaftarkan dari sheet per kelas dan otomatis masuk ke kelas masing-masing.'],
            [''],
            ['KOLOM SHEET "Data Guru"', 'Keterangan', 'Wajib'],
            ['NAMA LENGKAP', 'Nama lengkap guru (dengan gelar)', 'Ya'],
            ['EMAIL', 'Email guru. Jika kosong, otomatis dibuat dari nama.', 'Ya'],
            ['JENIS KELAMIN', 'Isi dengan L (Laki-laki) atau P (Perempuan)', 'Ya'],
            ['AGAMA', 'Islam / Kristen / Katolik / Hindu / Buddha / Konghucu', 'Tidak'],
            ['NO. HP', 'Nomor HP guru', 'Tidak'],
            ['PASSWORD', 'Password login. Default: guru123', 'Tidak'],
            [''],
            ['KOLOM SHEET "Daftar Kelas"', 'Keterangan', 'Wajib'],
            ['NAMA KELAS', 'Nama kelas (contoh: 10 AKL 1). Harus sama persis dengan nama sheet siswa.', 'Ya'],
            ['WALI KELAS', 'Email guru dari sheet "Data Guru". Kosongkan jika belum ada.', 'Tidak'],
            [''],
            ['KOLOM SHEET SISWA (per kelas)', 'Keterangan', 'Wajib'],
            ['NAMA LENGKAP', 'Nama lengkap siswa', 'Ya'],
            ['NISN', 'Nomor Induk Siswa Nasional (10 digit)', 'Ya'],
            ['JENIS KELAMIN', 'Isi dengan L (Laki-laki) atau P (Perempuan)', 'Ya'],
            ['AGAMA', 'Islam / Kristen / Katolik / Hindu / Buddha / Konghucu', 'Ya'],
            ['NO. HP', 'Nomor HP siswa', 'Tidak'],
            ['PASSWORD', 'Password login. Default: siswa123', 'Tidak'],
            [''],
            ['CATATAN:'],
            ['- Guru yang email-nya sudah terdaftar akan dilewati (tidak duplikat).'],
            ['- Email siswa otomatis dibuat dari NISN: {NISN}@siswa.smkn1ciamis.sch.id'],
            ['- NISN tidak boleh duplikat.'],
            ['- Jika PASSWORD dikosongkan, guru default: guru123, siswa default: siswa123'],
            ['- Nama sheet siswa HARUS SAMA PERSIS dengan nama kelas di sheet "Daftar Kelas".'],
        ];

        foreach ($instructions as $i => $rowData) {
            $instrSheet->fromArray($rowData, null, 'A' . ($i + 1));
        }

        $instrSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
        $instrSheet->getStyle('A3')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
        $instrSheet->getStyle('A11')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
        $instrSheet->getStyle('A15:C15')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
        ]);
        $instrSheet->getStyle('A23:C23')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $instrSheet->getStyle('A27:C27')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
        ]);
        $instrSheet->getColumnDimension('A')->setWidth(35);
        $instrSheet->getColumnDimension('B')->setWidth(60);
        $instrSheet->getColumnDimension('C')->setWidth(10);

        // Set active sheet to first
        $spreadsheet->setActiveSheetIndex(0);

        return self::streamDownload($spreadsheet, 'template_import_kelas.xlsx');
    }
}
