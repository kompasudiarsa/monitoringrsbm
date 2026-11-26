{{-- Jhon | 2026-11-25 19:52 WITA | TAMBAH: Halaman laporan RL terproteksi sesi; nilai hasil selalu di-escape. --}}
@extends('layouts.app', ['title' => strtoupper($report).' — '.$title])
@section('content')
<style>
/* Jhon | 2026-11-26 13:27 WITA | UBAH: aksen amber SIRS selaras dashboard; warna status semantik dipertahankan. */

/* Jhon | 2026-11-26 13:15 WITA | UBAH: shell/form/tabel mengikuti Surat Kontrol, CSS dibatasi halaman RL. */
.rl-page{min-height:100vh;background:radial-gradient(circle at top left,rgba(217,119,6,.045),transparent 28%),#f8fafc;padding:20px 24px;color:#0f172a;font-size:14px}
.rl-page .panel{background:#fff;border:1px solid #e2e8f0;border-radius:20px;box-shadow:0 14px 35px rgba(15,23,42,.06);padding:18px;margin-bottom:16px;min-width:0}
.rl-page .report-heading{display:flex;justify-content:space-between;gap:18px;align-items:center;background:linear-gradient(120deg,#fff,#fffbeb);border-bottom:1px solid #e2e8f0;padding:0 0 14px;margin-bottom:16px}
.rl-page h1{font-size:20px;letter-spacing:-.025em;margin:0 0 7px;font-weight:750}.rl-page .period-chip{display:inline-block;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:7px;color:#475569;padding:4px 8px;font-size:12px}
.rl-page .btn-back{background:#0f172a;color:white;text-decoration:none;padding:9px 13px;border-radius:10px;font-size:12px;font-weight:700;white-space:nowrap}
.rl-page form{display:grid;grid-template-columns:minmax(260px,2fr) repeat(3,minmax(140px,1fr));gap:14px;align-items:end}
.rl-page label{display:grid;gap:7px;font-size:12px;font-weight:700;color:#334155;min-width:0}
.rl-page select,.rl-page input{width:100%;height:42px;padding:10px 12px;border:1px solid #dbe2ea;border-radius:11px;background:#fff;color:#0f172a;font:inherit;min-width:0}
.rl-page select:focus,.rl-page input:focus{outline:2px solid #fde68a;border-color:#d97706}
.rl-page button{font:inherit;cursor:pointer;border:1px solid #e2e8f0;border-radius:11px;padding:10px 14px;background:#f1f5f9;color:#0f172a;font-size:12px;font-weight:700}
.rl-page button.primary{background:#b45309;color:#fff;border-color:#b45309;box-shadow:0 4px 12px #b4530926;justify-self:start}
.rl-page .muted{color:#64748b;font-size:12px;line-height:1.6}.rl-page .hint{margin:12px 0 0}.rl-page .error{background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;padding:12px;border-radius:11px;line-height:1.6;overflow-wrap:anywhere}
.rl-page .result-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px}.rl-page h2{font-size:17px;margin:0}.rl-page h3{font-size:15px;margin:12px 0}.rl-page .actions{display:flex;gap:8px;flex-wrap:wrap}
/* Jhon | 2026-11-26 13:19 WITA | UBAH: tinggi tabel mengikuti isi; scroll vertikal memakai halaman, horizontal tetap tersedia. */
.rl-page .table-wrap{overflow-x:auto;max-height:none;border:1px solid #e2e8f0;border-radius:12px}.rl-page table{width:100%;border-collapse:separate;border-spacing:0;font-size:12px}.rl-page th,.rl-page td{text-align:left;padding:11px 12px;border-bottom:1px solid #e2e8f0;border-right:1px solid #e2e8f0;vertical-align:middle;min-width:90px}.rl-page th{background:#f1f5f9;color:#334155;font-weight:800;white-space:normal}.rl-page tbody tr:nth-child(even){background:#f8fafc}.rl-page tbody tr:hover{background:#fffbeb}.rl-page .kv th{width:35%}.rl-page summary{cursor:pointer;color:#b45309;font-weight:700}.rl-page pre{white-space:pre-wrap;overflow-wrap:anywhere;margin:0}.rl-page .data-section{margin:12px 0}#busy{display:none;color:#64748b;font-size:12px}
@media(max-width:1000px){.rl-page form{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:600px){.rl-page{padding:12px}.rl-page .panel{padding:14px;border-radius:16px}.rl-page form{grid-template-columns:1fr}.rl-page .report-heading{align-items:flex-start}.rl-page h1{font-size:17px}.rl-page .btn-back{font-size:11px;padding:8px}.rl-page .result-head{align-items:flex-start}}
@media print{.monitor-topbar,.rl-page .filters,.rl-page .actions,.rl-page .hint{display:none!important}.rl-page{padding:0;background:#fff}.rl-page .panel{box-shadow:none;border:0;padding:0}.rl-page .table-wrap{max-height:none;overflow:visible;border:0}.rl-page th,.rl-page td{font-size:9px;padding:4px;min-width:0}.rl-page details:not([open]){display:none}}

/* Jhon | 2026-11-26 13:24 WITA | UBAH: aksen SIRS #7c3aed dan soft #ede9fe mengikuti kartu Dashboard Monitoring. */
.rl-page .btn-back,.rl-page .back-button{background:#b45309;color:#fff}
</style>
{{-- Jhon | 2026-11-26 13:15 WITA | PINDAH: judul, periode, kembali, dan filter dalam satu kartu seperti Surat Kontrol. --}}
<div class="rl-page"><section class="panel filters"><div class="report-heading"><div><h1>{{ strtoupper(str_replace('-', '.', $report)) }} · {{ $title }}</h1><span class="period-chip">Periode {{ $period }}</span></div><a href="{{ route('sirs.menu') }}" class="btn-back">Kembali ke Menu</a></div>
<form method="get" id="report-form">
<label>Laporan<select id="report-select">@foreach($catalog as $code => $name)<option value="{{ route('sirs.'.$code) }}" @if($code === $report) selected @endif>{{ strtoupper($code) }} — {{ $name }}</option>@endforeach</select></label>
<label>Bulan / Tahun<input type="month" name="periode" value="{{ $period }}" min="1900-01" max="2100-12" required></label>
@if($report === 'rl3-11-detail')<label>ID Konten<input type="number" min="1" name="idkonten" value="{{ request('idkonten') }}" required></label>@endif
{{-- Jhon | 2026-11-25 21:16 WITA | TAMBAH: rentang tanggal untuk sumber RL3.2 v1. --}}
@if($report === 'rl3-2')<label>Tanggal Awal<input type="date" name="tglAwal" value="{{ request('tglAwal', $period.'-01') }}" required></label><label>Tanggal Akhir<input type="date" name="tglAkhir" value="{{ request('tglAkhir', date('Y-m-t', strtotime($period.'-01'))) }}" required></label>@endif
<button class="primary" name="tampilkan" value="1" type="submit">Tampilkan Laporan</button><span id="busy" role="status">Mengambil laporan…</span></form>
<p class="muted hint">{{ $report === 'rl3-2' ? 'RL3.2 mengikuti rentang tanggal kegiatan pelayanan ranap v1.' : 'Perhitungan menggunakan satu bulan penuh sesuai laporan RL sumber.' }}</p></section>
@if($error)<section class="panel error" role="alert">{{ $error }} Ulangi melalui tombol Tampilkan Laporan.</section>@endif
@if($data !== null)<section class="panel"><div class="result-head"><h2>{{ strtoupper($report) }} · {{ $title }} — {{ $period }}</h2><div class="actions"><button type="button" id="print-report">Cetak / PDF</button><button type="button" id="csv-report">Unduh CSV Rincian</button></div></div>
@if($report === 'rl3-2') @include('sirs.rl32-v1', ['rows'=>$data['data'] ?? []]) @else @include('sirs.report-data', ['value'=>$data,'depth'=>0]) @endif
</section>
@elseif(!$error)<section class="panel muted">Pilih laporan dan periode, lalu klik Tampilkan Laporan.</section>@endif
</div>
<script>
// Jhon | 2026-11-25 19:52 WITA | TAMBAH: Navigasi laporan tanpa kredensial browser, cegah submit berulang dan ekspor rincian aman.
// Jhon | 2026-11-26 22:08 WITA | TAMBAH: perubahan bulan menyetel rentang kalender, termasuk Februari tahun kabisat; tanggal manual tetap boleh.
const periodInput = document.querySelector('#report-form [name="periode"]');
function syncReportDates() {
    const start = document.querySelector('#report-form [name="tglAwal"]');
    const end = document.querySelector('#report-form [name="tglAkhir"]');
    if (!start || !end || !/^\d{4}-(0[1-9]|1[0-2])$/.test(periodInput.value)) return;
    const [year, month] = periodInput.value.split('-').map(Number);
    const lastDay = new Date(Date.UTC(year, month, 0)).getUTCDate();
    start.value = periodInput.value + '-01';
    end.value = periodInput.value + '-' + String(lastDay).padStart(2, '0');
}
periodInput.addEventListener('change', syncReportDates);
const select = document.getElementById('report-select');
select.addEventListener('change', () => { const url = new URL(select.value, location.origin); url.searchParams.set('periode', document.querySelector('[name="periode"]').value); location.assign(url); });
document.getElementById('report-form').addEventListener('submit', function (event) { if (this.dataset.busy) { event.preventDefault(); return; } this.dataset.busy = '1'; document.getElementById('busy').style.display='inline'; });
window.addEventListener('pageshow', () => { delete document.getElementById('report-form').dataset.busy; document.getElementById('busy').style.display='none'; });
const printButton = document.getElementById('print-report'); if (printButton) printButton.addEventListener('click', () => window.print());
const csvButton = document.getElementById('csv-report');
if (csvButton) csvButton.addEventListener('click', () => {
 const payload = @json($data); const rows = [['Field', 'Nilai']];
 const walk = (value, path) => { if (value !== null && typeof value === 'object') { const entries=Object.entries(value); if (!entries.length) rows.push([path,'[]']); entries.forEach(([key,item])=>walk(item,path ? path+'.'+key : key)); } else rows.push([path,value === null ? '' : String(value)]); };
 walk(payload,'');
 const quote = value => { let text=String(value); if (/^[\s]*[=+@-]/.test(text)) text="'"+text; return '"'+text.replace(/"/g,'""')+'"'; };
 const blob=new Blob(['\ufeff'+rows.map(row=>row.map(quote).join(',')).join('\r\n')],{type:'text/csv;charset=utf-8'}); const url=URL.createObjectURL(blob); const link=document.createElement('a'); link.href=url;link.download=@json($report.'-'.$period.'-rincian.csv');link.click();setTimeout(()=>URL.revokeObjectURL(url),1000);
});
</script>
@endsection
