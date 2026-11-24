<div class="table-card">
    <div class="table-card-header">
        <div class="table-title">{{ $title }}</div>
        <div class="table-subtitle">{{ $subtitle }}</div>
    </div>

    <div class="table-responsive">
        <table class="bpjs-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Booking</th>
                    <th>Nama Pasien</th>
                    <th>No Kartu</th>
                    <th>No Antrean</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td>
                        <b>
                            {{ data_get($item, 'kodebooking',
            data_get($item, 'kode_booking',
            data_get($item, 'noreservasi',
            data_get($item, 'no_reservasi',
            data_get($item, 'nobooking', '-')))))
        }}
                        </b>
                    </td>
                    <td>
                        {{ data_get($item, 'namapasien',
                                data_get($item, 'nama',
                                data_get($item, 'namaPasien', '-')))
                            }}
                    </td>

                    <td>
                        {{ data_get($item, 'nomorkartu',
                                data_get($item, 'nokapst',
                                data_get($item, 'noKartu',
                                data_get($item, 'nokartu', '-'))))
                            }}
                    </td>

                    <td>
                        {{ data_get($item, 'noantrean',
                                data_get($item, 'noantrian',
                                data_get($item, 'noAntrean', '-')))
                            }}
                    </td>

                    <td>
                        {{ data_get($item, 'namapoli',
                                data_get($item, 'poli',
                                data_get($item, 'namaPoli',
                                data_get($item, 'kodepoli', '-'))))
                            }}
                    </td>

                    <td>
                        {{ data_get($item, 'namadokter',
                                data_get($item, 'dokter',
                                data_get($item, 'namaDokter',
                                data_get($item, 'kodedokter', '-'))))
                            }}
                    </td>

                    <td>
                        <span class="badge-soft">
                            {{ data_get($item, 'status',
                                    data_get($item, 'statusantrean',
                                    data_get($item, 'label_statusperiksa',
                                    data_get($item, 'statusperiksa', '-'))))
                                }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">
                        Tidak ada selisih pada kategori ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>