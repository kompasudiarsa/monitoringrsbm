{{-- Jhon | 2026-11-25 19:52 WITA | TAMBAH: Halaman laporan RL terproteksi sesi; nilai hasil selalu di-escape. --}}
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ strtoupper($report) }} — {{ $title }}</title>
<style>
/* Jhon | 2026-11-25 19:52 WITA | TAMBAH: Shell laporan SIRS responsif, tabel lebar dapat digeser dan dicetak. */
*{box-sizing:border-box}body{margin:0;background:#f4f7fb;color:#172033;font:14px system-ui,sans-serif}header{background:linear-gradient(110deg,#075985,#0284c7);color:white;padding:22px max(20px,calc((100% - 1380px)/2))}header a{color:white}h1{font-size:23px;margin:12px 0 6px}main{max-width:1420px;padding:20px;margin:auto}.panel{background:white;border:1px solid #e3eaf1;border-radius:14px;padding:20px;margin-bottom:16px}form,.actions{display:flex;gap:12px;flex-wrap:wrap;align-items:end}label{display:grid;gap:6px;font-weight:600}select,input,button,.button{font:inherit;border:1px solid #ccd7e3;border-radius:8px;padding:10px 14px;background:white;color:#172033}button,.button{cursor:pointer;text-decoration:none}button.primary{background:#ea780b;color:white;border-color:#ea780b}.muted{color:#64748b}.error{background:#fff1f2;color:#9f1239;border:1px solid #fecdd3}.table-wrap{overflow:auto;max-height:68vh;border:1px solid #e2e8f0;border-radius:8px}table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:10px 12px;border:1px solid #e2e8f0;vertical-align:top;min-width:100px}th{background:#eef5fb;position:sticky;top:0;z-index:1}tbody tr:nth-child(even){background:#f8fafc}summary{cursor:pointer;color:#0369a1;font-weight:600}h2{font-size:18px}h3{font-size:15px}pre{white-space:pre-wrap;overflow-wrap:anywhere;margin:0}.result-head{display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}.result-head .actions{align-items:center}.data-section{margin:12px 0}.kv th{position:static;width:35%}#busy{display:none}@media(max-width:600px){main{padding:10px}.panel{padding:13px}label,select{width:100%}h1{font-size:20px}}@media print{header a,.filters,.actions,.hint{display:none!important}header{background:white;color:black;padding:0}body{background:white;font-size:10px}main{padding:0;max-width:none}.panel{border:0;padding:0}.table-wrap{max-height:none;overflow:visible}th{position:static}details:not([open]){display:none}a{color:black}}
</style></head><body>
<header><a href="{{ route('sirs.menu') }}">← Menu SIRS Online</a><h1>{{ strtoupper(str_replace('-', '.', $report)) }} · {{ $title }}</h1><div>Periode laporan: {{ $period }}</div></header>
<main><section class="panel filters"><form method="get" id="report-form">
<label>Laporan<select id="report-select">@foreach($catalog as $code => $name)<option value="{{ route('sirs.'.$code) }}" @if($code === $report) selected @endif>{{ strtoupper($code) }} — {{ $name }}</option>@endforeach</select></label>
<label>Bulan / Tahun<input type="month" name="periode" value="{{ $period }}" min="1900-01" max="2100-12" required></label>
@if($report === 'rl3-11-detail')<label>ID Konten<input type="number" min="1" name="idkonten" value="{{ request('idkonten') }}" required></label>@endif
{{-- Jhon | 2026-11-25 21:16 WITA | TAMBAH: rentang tanggal untuk sumber RL3.2 v1. --}}
@if($report === 'rl3-2')<label>Tanggal Awal<input type="date" name="tglAwal" value="{{ request('tglAwal', $period.'-01') }}" required></label><label>Tanggal Akhir<input type="date" name="tglAkhir" value="{{ request('tglAkhir', date('Y-m-t', strtotime($period.'-01'))) }}" required></label>@endif
<button class="primary" name="tampilkan" value="1" type="submit">Tampilkan Laporan</button><span id="busy" role="status">Mengambil laporan…</span></form>
<p class="muted hint">{{ $report === 'rl3-2' ? 'RL3.2 mengikuti rentang tanggal kegiatan pelayanan ranap v1.' : 'Perhitungan menggunakan satu bulan penuh sesuai laporan RL sumber.' }}</p></section>
@if($error)<section class="panel error" role="alert">{{ $error }} Ulangi melalui tombol Tampilkan Laporan.</section>@endif
@if($data !== null)<section class="panel"><div class="result-head"><h2>Hasil {{ strtoupper($report) }}</h2><div class="actions"><button type="button" id="print-report">Cetak / PDF</button><button type="button" id="csv-report">Unduh CSV Rincian</button></div></div>
@if($report === 'rl3-2') @include('sirs.rl32-v1', ['rows'=>$data['data'] ?? []]) @else @include('sirs.report-data', ['value'=>$data,'depth'=>0]) @endif
</section>
@elseif(!$error)<section class="panel muted">Pilih laporan dan periode, lalu klik Tampilkan Laporan.</section>@endif
</main>
<script>
// Jhon | 2026-11-25 19:52 WITA | TAMBAH: Navigasi laporan tanpa kredensial browser, cegah submit berulang dan ekspor rincian aman.
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
</script></body></html>
