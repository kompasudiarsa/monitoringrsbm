<?php
namespace App\Services;
// Jhon | 2026-11-27 16:07 WITA | TAMBAH: ekspor Excel SpreadsheetML bertipe teks serta halaman cetak/PDF, tanpa identitas token/session.
class SuratKontrolExportService
{
    public function export($items, array $filters, string $format)
    {
        $headers=['No','Status','Verifikasi','Surat Kontrol BPJS (Verifikasi)','Permasalahan','Keterangan','No SEP Asal','No Surat Kontrol SIMRS','Poli','No RM','Nama Pasien','No BPJS','No Reservasi','Tanggal Reservasi','Tanggal Input','Dokter','No Antrean'];
        $rows=[];
        foreach ($items->values() as $index=>$item) {
            $verified=$item['verification_saved'] ?? null;
            // Jhon | 2026-11-27 16:42 WITA | UBAH: label kategori baru untuk Excel/cetak.
            $problem=['rujukan'=>'Rujukan','sep'=>'SEP','jadwal_poli'=>'Jadwal Poli','kontrol_internal'=>'Kontrol Internal','lainnya'=>'Lainnya','rujukan_habis'=>'Rujukan Habis','rujuk_internal'=>'Rujuk Internal'][$item['verification_problem'] ?? ''] ?? '';
            $answer=['ada'=>'Ada','tidak'=>'Tidak'][$item['verification_bpjs'] ?? ''] ?? '';
            $rows[] = [$index+1,$item['status_label'] ?? '',$verified === null ? 'Belum terbaca' : ($verified ? 'Sudah diverifikasi' : 'Belum diverifikasi'),$answer,$problem,$item['verification_note'] ?? '',$item['sep_simrs'] ?? '',$item['nosurat_simrs'] ?? '',$item['poli'] ?? '',$item['nocm'] ?? '',$item['namapasien'] ?? '',$item['nobpjs'] ?? '',$item['noreservasi'] ?? '',$item['tanggalreservasi_tampilan'] ?? '',$item['tanggalinput_tampilan'] ?? '',$item['dokter'] ?? '',$item['noantrianpoli'] ?? ''];
        }
        if ($format === 'print') return response()->view('bpjs-antrean.surat-kontrol-print',compact('headers','rows','filters'))->header('Cache-Control','private, no-store');
        $xml = static function ($value) {
            $value=preg_replace('/[^\x09\x0A\x0D\x20-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u','',(string) $value);
            return htmlspecialchars($value ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');
        };
        // Semua sel String: nomor nol depan tetap utuh dan nilai berawalan = tidak dieksekusi sebagai formula.
        return response()->streamDownload(function () use ($headers,$rows,$xml,$filters) {
            echo '<?xml version="1.0" encoding="UTF-8"?><?mso-application progid="Excel.Sheet"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Worksheet ss:Name="Surat Kontrol"><Table>';
            foreach (array_merge([['Monitoring Surat Kontrol'],['Periode',$filters['dari'],$filters['sampai']],['Filter Status',$filters['status']],$headers],$rows) as $row) {
                echo '<Row>'; foreach ($row as $value) echo '<Cell><Data ss:Type="String">'.$xml($value).'</Data></Cell>'; echo '</Row>';
            }
            echo '</Table></Worksheet></Workbook>';
        },'Surat-Kontrol-'.$filters['dari'].'-'.$filters['sampai'].'.xml',['Content-Type'=>'application/vnd.ms-excel; charset=UTF-8','Cache-Control'=>'private, no-store']);
    }
}
