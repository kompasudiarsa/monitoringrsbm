{{-- Jhon | 2026-11-25 21:17 WITA | TAMBAH: 21 kolom dan header RL3.2 sesuai kegiatan-pelayanan-ranap-v1. --}}
{{-- Jhon | 2026-11-25 21:30 WITA | TAMBAH: kelengkapan mapping dan klasifikasi tidak disembunyikan sebagai nol valid. --}}
@if(!empty($data['warnings']['ruangan_khusus_belum_mapping']))<p role="alert" class="error">Ada {{ $data['warnings']['ruangan_khusus_belum_mapping'] }} ruangan khusus yang belum memiliki mapping RL3.2. Pemisahan layanan belum lengkap; verifikasi mapping sebelum menggunakan laporan.</p>@endif
@if(!empty($data['warnings']['kelas_belum_terpetakan']))<p role="alert" class="error">{{ $data['warnings']['kelas_belum_terpetakan'] }} episode memiliki kelas kosong/tidak terpetakan; hari perawatannya sementara masuk Kelas Khusus.</p>@endif
@if(!empty($data['warnings']['kematian_belum_terklasifikasi']))<p role="alert" class="error">{{ $data['warnings']['kematian_belum_terklasifikasi'] }} kematian belum dapat dipilah menurut jam/jenis kelamin karena data tidak lengkap.</p>@endif
@if(!empty($data['warnings']['alokasi_bed']))<p class="muted">{{ $data['warnings']['alokasi_bed'] }}</p>@endif
<h3>RL 3.2 Rekapitulasi Kegiatan Pelayanan Rawat Inap</h3>
@if(!is_array($rows) || !count($rows))<p class="muted">Tidak ada data pada rentang tanggal yang dipilih.</p>@else
<div class="table-wrap"><table id="rl32-table"><thead><tr>
<th rowspan="2">No</th><th rowspan="2">Jenis Pelayanan</th><th rowspan="2">Pasien Awal Bulan</th><th rowspan="2">Pasien Masuk</th><th rowspan="2">Pasien Pindahan</th><th rowspan="2">Pasien Dipindahkan</th><th rowspan="2">Pasien Keluar Hidup</th><th colspan="2">Pasien Keluar Mati Laki-laki</th><th colspan="2">Pasien Keluar Mati Perempuan</th><th rowspan="2">Jumlah Lama Dirawat</th><th rowspan="2">Pasien Akhir Bulan</th><th rowspan="2">Jumlah Hari Perawatan</th><th colspan="6">Rincian Hari Perawatan Perkelas</th><th rowspan="2">Jumlah Alokasi Tempat Tidur Awal Bulan</th></tr>
<tr><th>&lt; 48 Jam</th><th>≥ 48 Jam</th><th>&lt; 48 Jam</th><th>≥ 48 Jam</th><th>VVIP</th><th>VIP</th><th>I</th><th>II</th><th>III</th><th>Kelas Khusus</th></tr></thead><tbody>
@foreach($rows as $row)<tr><td>{{ $loop->iteration }}</td>
<td>{{ $row['jenis_spesialisasi'] ?? '—' }}</td>
<td>{{ $row['pasienawalbulan'] ?? '—' }}</td>
<td>{{ $row['pasienmasuk'] ?? '—' }}</td>
<td>{{ $row['pasienpindahan'] ?? '—' }}</td>
<td>{{ $row['pasiendipindahkan'] ?? '—' }}</td>
<td>{{ $row['pasienkeluarhidup'] ?? '—' }}</td>
<td>{{ $row['matikurang48jaml'] ?? '—' }}</td>
<td>{{ $row['matilebih48jaml'] ?? '—' }}</td>
<td>{{ $row['matikurang48jamp'] ?? '—' }}</td>
<td>{{ $row['matilebih48jamp'] ?? '—' }}</td>
<td>{{ $row['lamadirawat'] ?? '—' }}</td>
<td>{{ $row['pasienakhirbulan'] ?? '—' }}</td>
<td>{{ $row['jumlahharidirawat'] ?? '—' }}</td>
<td>{{ $row['rincianvvip'] ?? '—' }}</td>
<td>{{ $row['rincianvip'] ?? '—' }}</td>
<td>{{ $row['rinciankel1'] ?? '—' }}</td>
<td>{{ $row['rinciankel2'] ?? '—' }}</td>
<td>{{ $row['rinciankel3'] ?? '—' }}</td>
<td>{{ $row['rinciankelaskhusus'] ?? '—' }}</td>
<td>{{ $row['jumlah'] ?? '—' }}</td>
</tr>@endforeach</tbody></table></div>@endif
