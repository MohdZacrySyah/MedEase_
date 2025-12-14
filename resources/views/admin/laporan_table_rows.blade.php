@forelse ($kunjunganData as $data)
    <tr class="schedule-row">
        <td>
            <span class="number-badge">{{ $data->pasien_id }}</span>
        </td>
        <td>
            <div class="doctor-info">
                <div class="doctor-avatar">
                    @if($data->profile_photo_path)
                        <img src="{{ asset('storage/' . $data->profile_photo_path) }}" alt="Foto" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        {{ substr($data->nama_pasien, 0, 1) }}
                    @endif
                </div>
                <span class="doctor-name">{{ $data->nama_pasien }}</span>
            </div>
        </td>
        <td>
            <span class="text-secondary-color">{{ $data->nama_dokter ?? 'N/A' }}</span>
        </td>
        <td>
            <span class="service-badge">
                <i class="fas fa-briefcase-medical"></i>
                {{ $data->layanan }}
            </span>
        </td>
        <td>
            <div class="time-badge-modern">
                <i class="far fa-clock"></i>
                <span>{{ \Carbon\Carbon::parse($data->tanggal_kunjungan)->isoFormat('DD MMM YYYY, HH:mm') }}</span>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" style="text-align: center; padding: 40px;">
            <div class="empty-schedule">
                <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3; margin-bottom: 10px;"></i>
                <p style="color: #6b7280;">Tidak ada data kunjungan untuk periode ini</p>
            </div>
        </td>
    </tr>
@endforelse