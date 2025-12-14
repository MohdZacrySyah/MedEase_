@forelse ($kunjunganData as $index => $data)
    <tr class="schedule-row" style="animation-delay: {{ $index * 0.05 }}s">
        <td>
            <div class="no-antrian-badge">
                {{ $data->pasien_id }}
            </div>
        </td>
        <td>
            <div class="doctor-info">
                <div class="doctor-avatar">
                    @if($data->profile_photo_path)
                        <img src="{{ asset('storage/' . $data->profile_photo_path) }}" alt="Foto">
                    @else
                        {{ substr($data->nama_pasien ?? 'P', 0, 1) }}
                    @endif
                </div>
                <span class="doctor-name">{{ $data->nama_pasien }}</span>
            </div>
        </td>
        <td>
            <span class="status-badge status-diperiksa-awal">
                <i class="fas fa-briefcase-medical"></i>
                {{ $data->layanan }}
            </span>
        </td>
        <td>
            <span class="status-badge status-selesai">
                <i class="far fa-clock"></i>
                {{ \Carbon\Carbon::parse($data->tanggal_kunjungan)->isoFormat('DD MMM YYYY, HH:mm') }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4">
            <div class="empty-schedule">
                <i class="fas fa-inbox"></i>
                <p>Tidak ada data pemeriksaan</p>
                <small>Untuk periode yang dipilih</small>
            </div>
        </td>
    </tr>
@endforelse