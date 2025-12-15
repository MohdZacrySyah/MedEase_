@forelse ($reseps as $resep) 
    <tr class="table-row">
        <td>{{ $resep->created_at->isoFormat('D MMM YYYY, HH:mm') }}</td>
        <td>{{ $resep->pasien->name ?? 'N/A' }}</td>
        <td>{{ $resep->pemeriksaan->tenagaMedis->name ?? 'N/A' }}</td>
        <td>
            @if($resep->status == 'Diproses')
                <span class="status-badge status-diproses">
                    <i class="fas fa-sync-alt"></i> Sedang Diproses
                </span>
            @else
                <span class="status-badge status-menunggu">
                    <i class="fas fa-hourglass-start"></i> Menunggu
                </span>
            @endif
        </td>
        <td class="text-center">
            <button class="btn-action-primary btn-proses" 
                    data-id_resep="{{ $resep->id }}"
                    data-pasien="{{ $resep->pasien->name ?? 'N/A' }}"
                    data-dokter="{{ $resep->pemeriksaan->tenagaMedis->name ?? 'N/A' }}"
                    data-resep="{{ $resep->pemeriksaan->resep_obat ?? 'Tidak ada resep' }}"
                    data-catatan_lama="{{ $resep->catatan_apoteker ?? '' }}"
                    data-action_url="{{ route('apoteker.antrian.selesaikan', $resep->id) }}">
                <i class="fas fa-eye"></i>
                <span>Lihat & Proses</span>
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5">
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>Tidak ada resep yang menunggu. Kerja bagus!</p>
            </div>
        </td>
    </tr>
@endforelse