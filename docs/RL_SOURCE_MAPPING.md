# Pemetaan migrasi laporan RL SIMRSBM-TM

<!-- Jhon | 2026-11-25 | TAMBAH: inventaris source, bukan bukti migrasi atau kesamaan hasil runtime. -->

Sumber: `/mnt/www/simrsbm-tm/frontend-v2/src/pages/module`. File `-v1` tidak dianggap versi aktif tanpa pemeriksaan navigasi. Autentikasi upstream menggunakan header token sesi SIMRS; token/secret tidak dicatat.

Ditemukan 33 kandidat laporan aktif/non-v1.

## laporan/rekam-medis/cara-bayar.vue

Label sumber: Formulir RL 3.19

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/indikator-pelayanan-rs.vue

Label sumber: Formulir RL 3.1

Ekspor XLSX: True; referensi cetak: False

- `GET rekammedis/indikator-pelayanan-rs?tahun=${year}&bulan=${String(month).padStart(         2,         '0'       )}`

## laporan/rekam-medis/kegiatan-farmasi.vue

Label sumber: Formulir RL 3.17

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-kebidanan.vue

Label sumber: Formulir RL 3.6

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-laboratorium.vue

Label sumber: Formulir RL 3.8

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-pelayanan-keluarga-berencana.vue

Label sumber: Formulir RL 3.16

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-pelayanan-kesehatan-jiwa.vue

Label sumber: Formulir RL 3.15

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-pelayanan-khusus.vue

Label sumber: Formulir RL 3.14

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-pelayanan-ranap.vue

Label sumber: Formulir RL 3.2

Ekspor XLSX: True; referensi cetak: False

- `GET rekammedis/rekapitulasi-pelayanan-ranap?tahun=${year}&bulan=${String(         month       ).padStart(2, '0')}`

## laporan/rekam-medis/kegiatan-pembedahan.vue

Label sumber: Formulir RL 3.12

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-perintanologi.vue

Label sumber: Formulir RL 3.7

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-radiologi.vue

Label sumber: Formulir RL 3.9

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-rehab-medis.vue

Label sumber: Formulir RL 3.13

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kegiatan-rujukan.vue

Label sumber: Formulir RL 3.10

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/kunjungan-perawatan-gigi-mulut.vue

Label sumber: Formulir RL 3.11

Ekspor XLSX: True; referensi cetak: False

- `GET /rekammedis/rekapitulasi-gimul?tglAwal=${tglAwal}&tglAkhir=${tglAkhir}`
- `GET /rekammedis/rekapitulasi-gimul-detail?tglAwal=${tglAwal}&tglAkhir=${tglAkhir}&idkonten=${idkonten}`

## laporan/rekam-medis/kunjungan-rawat-darurat.vue

Label sumber: Formulir RL 3.3

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/laporan-penyakit-terbanyak.vue

Label sumber: RL 1.2 Indikator Pelayanan RS.xlsx

Ekspor XLSX: True; referensi cetak: False

- `GET laporan/penyakit-terbanyak?tglAwal=${tglAwal}&tglAkhir=${tglAkhir}`

## laporan/rekam-medis/map-kelompok-laporan.vue

Label sumber: map-kelompok-laporan

Ekspor XLSX: False; referensi cetak: False

- `GET sysadmin/get-combo-map-laporan`
- `GET /laporan/get-produk-mapping?namaproduk=${filter.query}&limit=50`
- `GET sysadmin/get-map-laporan-rl?idjenislaporan=${e.value}`
- `GET sysadmin/get-map-laporan-rl?idkonten=${idkelompoklaporan}`
- `GET sysadmin/get-combo-map-laporan?jenis_laporan=`
- `GET /emr/dropdown/ruangan_m?select=id,namaruangan&param_search=namaruangan&query=${filter.query}&limit=10`
- `POST sysadmin/save-map-laporan-rl`
- `POST sysadmin/delete-map-laporan-rl`

## laporan/rekam-medis/pasien-by-tindakan.vue

Label sumber: RL 1.2 Indikator Pelayanan RS.xlsx

Ekspor XLSX: True; referensi cetak: False

- `GET laporan/pasien-by-tindakan?tglAwal=${tglAwal}&tglAkhir=${tglAkhir}`

## laporan/rekam-medis/rekapitulasi-kunjungan.vue

Label sumber: Formulir RL 3.5

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/rekapitulasi-pelayanan-farmasi-resep.vue

Label sumber: Formulir RL 3.18

Ekspor XLSX: True; referensi cetak: False


## laporan/rekam-medis/rekapitulasi-pengunjung.vue

Label sumber: Formulir RL 3.4

Ekspor XLSX: True; referensi cetak: False


## rekammedis/rl4/10-besar-kematian-penyakit-rawat-inap.vue

Label sumber: Formulir RL 4.3

Ekspor XLSX: True; referensi cetak: False


## rekammedis/rl4/10-besar-penyakit-rawat-inap.vue

Label sumber: Formulir RL 4.2

Ekspor XLSX: True; referensi cetak: False


## rekammedis/rl4/kompilasi-penyakit-rawat-inap.vue

Label sumber: Formulir RL 4.1

Ekspor XLSX: True; referensi cetak: False


## rekammedis/rl4/penyakit-rawat-inap.vue

Label sumber: Formulir RL 4A

Ekspor XLSX: True; referensi cetak: False

- `GET laporan/get-laporan-rl4a?tglAwal=${tglAwal}&tglAkhir=${tglAkhir}`

## rekammedis/rl4/penyakit-rawat-jalan.vue

Label sumber: Formulir RL 4B

Ekspor XLSX: True; referensi cetak: False

- `GET laporan/get-laporan-rl4b?tglAwal=${tglAwal}&tglAkhir=${tglAkhir}`

## rekammedis/rl5/10-besar-kasus-baru-penyakit-rawat-jalan.vue

Label sumber: Formulir RL 5.2

Ekspor XLSX: True; referensi cetak: False


## rekammedis/rl5/kompilasi-penyakit-rawat-jalan.vue

Label sumber: Formulir RL 5.1

Ekspor XLSX: True; referensi cetak: False


## rekammedis/rl5/kunjungan-rawat-jalan.vue

Label sumber: RL5.2 Kunjungan Rawat Jalan

Ekspor XLSX: True; referensi cetak: False

- `GET /laporan/get-laporan-rl5b?departemenfk=${dept}&ruanganfk=${ruangan}&tglAwal=${tglAwal}&tglAkhir=${tglAkhir}`
- `GET emr/dropdown/departemen_m?select=id,namadepartemen&param_search=namadepartemen&query=${filter.query}&limit=10`
- `GET /rekammedis/get-ruangan-by-departement?idDepartement=${dept}&ruangan=${filter.query}&limit=10`
- `GET emr/dropdown/kelompokpasien_m?select=id,kelompokpasien&param_search=kelompokpasien&query=${filter.query}&limit=10`

## rekammedis/rl5/pengunjung-rumah-sakit.vue

Label sumber: Laporan RL 5.1 PENGUNJUNG

Ekspor XLSX: True; referensi cetak: False

- `GET /laporan/get-laporan-rl5a?departemenfk=${dept}&ruanganfk=${ruangan}&tglAwal=${tglAwal}&tglAkhir=${tglAkhir}`
- `GET emr/dropdown/departemen_m?select=id,namadepartemen&param_search=namadepartemen&query=${filter.query}&limit=10`
- `GET /rekammedis/get-ruangan-by-departement?idDepartement=${dept}&ruangan=${filter.query}&limit=10`

## rekammedis/rl5/penyakit-rawat-inap.vue

Label sumber: Formulir RL 5.3

Ekspor XLSX: True; referensi cetak: False

- `GET /emr/dropdown/ruangan_m?select=id,namaruangan&param_search=namaruangan&query=${filter.query}&limit=10`
- `GET laporan/get-laporan-rl5c?tglAwal=${tglAwal}&tglAkhir=${tglAkhir}&ruanganfk=${ruangan}`

## rekammedis/rl5/penyakit-rawat-jalan.vue

Label sumber: Formulir RL 5.3

Ekspor XLSX: True; referensi cetak: False

