<?php

namespace App\Services;

// Jhon | 2026-11-23 18:31 WITA | TAMBAH: pencocokan konservatif kandidat kartu/tanggal tanpa memilih surat pertama.
class SuratKontrolMatcher
{
    private function value($row, array $keys): string
    {
        foreach ($keys as $key) {
            $value = is_array($row) ? ($row[$key] ?? null) : ($row->$key ?? null);
            if (is_scalar($value) && trim((string) $value) !== '' && trim((string) $value) !== '-') {
                return trim((string) $value);
            }
        }
        return '';
    }

    private function normalize(string $value): string
    {
        return strtoupper(preg_replace('/\s+/', ' ', trim($value)));
    }

    public function number($row): string
    {
        return $this->value($row, ['noSuratKontrol', 'no_surat_kontrol', 'nosuratkontrol', 'nosurat']);
    }

    // Jhon | 2026-11-23 19:25 WITA | UBAH: hanya cocokkan poli, dokter tidak memengaruhi status maupun kandidat.
    private function compareField($simrs, $bpjs, string $field): ?bool
    {
        $codes = [['kodepolibpjs'], ['poliTujuan', 'kodePoli', 'kodepoli']];
        $names = [['poli', 'namaruangan'], ['namaPoliTujuan', 'namaPoli']];
        foreach ([$codes, $names] as $pair) {
            $left = $this->value($simrs, $pair[0]);
            $right = $this->value($bpjs, $pair[1]);
            if ($left !== '' && $right !== '') {
                return $this->normalize($left) === $this->normalize($right);
            }
        }
        return null;
    }

    // Jhon | 2026-11-23 19:42 WITA | TAMBAH: identitas cocok jika SEP atau kartu terisi dan sama; kosong tidak pernah cocok.
    public function identityMatches($simrs, $bpjs): bool
    {
        $cardLeft = preg_replace('/\D/', '', $this->value($simrs, ['nobpjs', 'nokartu', 'noKartu']));
        $cardRight = preg_replace('/\D/', '', $this->value($bpjs, ['noKartu', 'nokartu', 'no_kartu']));
        $sepLeft = $this->normalize($this->sep($simrs));
        $sepRight = $this->normalize($this->sep($bpjs));
        return ($cardLeft !== '' && $cardLeft === $cardRight)
            || ($sepLeft !== '' && $sepLeft === $sepRight);
    }

    public function sep($row): string
    {
        return $this->value($row, ['noSepAsalKontrol', 'nosep', 'noSEP', 'noSep', 'no_sep']);
    }

    // Jhon | 2026-11-23 19:42 WITA | UBAH: tanggal diseleksi controller; cocokkan identitas dan poli, bukan nomor surat/dokter.
    public function match($simrs, array $candidates, bool $upstreamOk = true): array
    {
        $result = function ($status, $label, $item = null) {
            return ['status' => $status, 'label' => $label, 'item' => $item];
        };
        if (!$upstreamOk) return $result('BPJS_GAGAL', 'Pengecekan BPJS gagal, belum dapat dibandingkan');
        $unique = [];
        foreach ($candidates as $item) {
            if ($this->identityMatches($simrs, $item)) $unique[json_encode($item)] = $item;
        }
        $pool = array_values($unique);
        if (!$pool) return $result('TIDAK_DITEMUKAN_BPJS', 'Tidak ditemukan surat dengan tanggal reservasi dan SEP atau kartu yang sama');
        $compatible = array_values(array_filter($pool, function ($item) use ($simrs) {
            return $this->compareField($simrs, $item, 'poli') === true;
        }));
        if (count($compatible) > 1) return $result('AMBIGU', 'Beberapa surat cocok tanggal, poli, dan SEP/kartu; tidak dipilih otomatis');
        if (count($compatible) === 1) return $result('SESUAI', 'Tanggal reservasi, poli, dan SEP atau kartu sesuai', $compatible[0]);
        if (count($pool) > 1) return $result('BELUM_TERVERIFIKASI', 'Tidak ada poli yang dapat dipastikan cocok dari beberapa kandidat');
        $item = $pool[0];
        if ($this->compareField($simrs, $item, 'poli') === null) return $result('BELUM_TERVERIFIKASI', 'Data poli pembanding belum lengkap', $item);
        return $result('TIDAK_SESUAI', 'Tanggal dan SEP/kartu cocok, tetapi poli berbeda', $item);
    }
}
