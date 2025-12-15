@forelse ($pendaftaranMenunggu ?? [] as $index => $pendaftaran)
    <tr class="schedule-row" style="animation-delay: {{ $index * 0.1 }}s">
        <td>
            <div class="no-antrian-badge">
                {{ $pendaftaran->no_antrian }}
            </div>
        </td>
        <td>
            <div class="doctor-info">
                <div class="doctor-avatar">
                    @if($pendaftaran->user?->profile_photo_path)
                        <img src="{{ asset('storage/' . $pendaftaran->user->profile_photo_path) }}" alt="Foto">
                    @else
                        {{ substr($pendaftaran->user->name ?? $pendaftaran->nama_lengkap ?? 'P', 0, 1) }}
                    @endif
                </div>
                <span class="doctor-name">{{ $pendaftaran->user->name ?? $pendaftaran->nama_lengkap }}</span>
            </div>
        </td>
        <td>
            <span class="status-badge status-{{ Str::slug($pendaftaran->status ?? 'menunggu') }}">
                @if($pendaftaran->status == 'Menunggu')
                    <i class="fas fa-clock"></i>
                @elseif($pendaftaran->status == 'Diperiksa Awal')
                    <i class="fas fa-notes-medical"></i>
                @else
                    <i class="fas fa-check-circle"></i>
                @endif
                {{ $pendaftaran->status ?? 'Menunggu' }}
            </span>
        </td>
        <td class="text-center">
            <a href="{{ route('tenaga-medis.pasien.index') }}?open_modal_for={{ $pendaftaran->id }}" 
               class="btn-action-modern">
                <span>Periksa Pasien</span>
                <i class="fas fa-stethoscope"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4">
            <div class="empty-schedule">
                <i class="fas fa-inbox"></i>
                <p>Tidak ada pasien dalam antrian saat ini</p>
                <small>Semua pasien sudah ditangani</small>
            </div>
        </td>
    </tr>
@endforelse
