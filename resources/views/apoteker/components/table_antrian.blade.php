@forelse ($reseps as $index => $resep) 
    {{-- Animasi fade-in setiap baris muncul --}}
    <tr class="table-row" style="animation-delay: {{ $index * 0.05 }}s">
        {{-- Kolom Waktu --}}
        <td>
            <div style="font-weight: 600; color: #374151;">
                {{ $resep->created_at->format('H:i') }} WIB
            </div>
            <div style="font-size: 0.8rem; color: #9ca3af;">
                Antrian #{{ $loop->iteration }}
            </div>
        </td>

        {{-- Kolom Pasien --}}
        <td>
            <div style="font-weight: 600; color: #111827;">{{ $resep->pemeriksaan->pasien->name ?? 'Pasien Dihapus' }}</div>
            <div style="font-size: 0.8rem; color: #6b7280;">No. RM: {{ $resep->pemeriksaan->pasien->id ?? '-' }}</div>
        </td>

        {{-- Kolom Dokter --}}
        <td>
            <div class="d-flex align-items-center">
                <i class="fas fa-user-md text-success mr-2" style="color: #169400;"></i>
                {{ $resep->pemeriksaan->tenagaMedis->name ?? 'Dokter Tidak Ditemukan' }}
            </div>
        </td>

        {{-- Kolom Status --}}
        <td>
            @if($resep->status == 'menunggu')
                <span class="status-badge status-menunggu">
                    <i class="fas fa-clock"></i> Menunggu Obat
                </span>
            @else
                <span class="status-badge status-selesai">
                    <i class="fas fa-check-circle"></i> Selesai
                </span>
            @endif
        </td>

        {{-- Kolom Aksi --}}
        <td class="text-center">
            @if($resep->status == 'menunggu')
                {{-- TOMBOL PROSES (Memicu Modal) --}}
                <button type="button" class="btn-action-primary btn-proses" 
                        data-id_resep="{{ $resep->id }}"
                        data-pasien="{{ $resep->pemeriksaan->pasien->name ?? '-' }}"
                        data-dokter="{{ $resep->pemeriksaan->tenagaMedis->name ?? '-' }}"
                        data-resep="{{ $resep->obat }}" 
                        data-catatan_lama="{{ $resep->catatan ?? '' }}"
                        data-action_url="{{ route('apoteker.antrian.selesaikan', $resep->id) }}">
                    <i class="fas fa-mortar-pestle"></i>
                    <span>Proses</span>
                </button>
            @else
                {{-- Tombol Disabled jika sudah selesai --}}
                <button class="btn-action-primary btn-disabled" disabled>
                    <i class="fas fa-check"></i> Selesai
                </button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5">
            <div class="empty-state">
                <i class="fas fa-clipboard-check"></i>
                <p>Tidak ada antrian resep untuk tanggal ini.</p>
            </div>
        </td>
    </tr>
@endforelse