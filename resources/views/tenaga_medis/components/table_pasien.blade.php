@forelse ($pendaftarans as $index => $pendaftaran)
    <tr class="schedule-row" style="animation-delay: {{ $index * 0.1 }}s">
        <td>
            <div class="no-urut">{{ $loop->iteration }}</div>
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
            <span class="service-badge">
                <i class="fas fa-briefcase-medical"></i>
                {{ $pendaftaran->nama_layanan }}
            </span>
        </td>
        <td>
            @if($pendaftaran->status == 'Diperiksa Awal')
                <span class="status-badge status-diperiksa-awal">
                    <i class="fas fa-notes-medical"></i> Diperiksa Awal
                </span>
            @elseif($pendaftaran->status == 'Selesai')
                <span class="status-badge status-selesai">
                    <i class="fas fa-check-double"></i> Selesai
                </span>
            @else
                <span class="status-badge status-menunggu">
                    <i class="fas fa-clock"></i> {{ $pendaftaran->status }}
                </span>
            @endif
        </td>
        <td class="text-center">
            <div class="button-group">
                {{-- Tombol Input SOAP --}}
                <button class="btn-action-modern open-pemeriksaan-modal" 
                        data-id="{{ $pendaftaran->id }}">
                    <span>
                        {{ $pendaftaran->status == 'Selesai' ? 'Lihat/Edit SOAP' : 'Input SOAP' }}
                    </span>
                    <i class="fas fa-stethoscope"></i>
                </button>
                
                {{-- Tombol Riwayat --}}
                @if($pendaftaran->user_id)
                    <a href="{{ route('tenaga-medis.riwayat.index', ['pasien_id' => $pendaftaran->user_id]) }}" 
                       class="btn-action-secondary-history" 
                       title="Riwayat untuk {{ $pendaftaran->user->name ?? $pendaftaran->nama_lengkap }}">
                        <i class="fas fa-history"></i>
                    </a>
                @else
                    <button class="btn-action-secondary-history" 
                            title="Riwayat tidak tersedia (Pasien tidak terhubung ke akun user)" 
                            disabled>
                        <i class="fas fa-history"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5">
            <div class="empty-schedule">
                <i class="fas fa-inbox"></i>
                <p>Tidak ada pasien yang menunggu pemeriksaan Anda</p>
                <small>Semua pasien sudah ditangani atau belum ada antrian</small>
            </div>
        </td>
    </tr>
@endforelse