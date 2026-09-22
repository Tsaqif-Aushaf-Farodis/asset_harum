<x-layout.app title="Realisasi Anggaran" activeMenu="laporan.realisasi-anggaran" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Realisasi Anggaran" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan', 'url' => route('laporan.index')],
            ['label' => 'Realisasi Anggaran'],
        ]" />

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">Pagu {{ $tahun }}</h6>
                    <h4>Rp {{ number_format($summary['pagu'], 0, ',', '.') }}</h4>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">Realisasi</h6>
                    <h4 class="text-primary">Rp {{ number_format($summary['realisasi'], 0, ',', '.') }}</h4>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">Sisa</h6>
                    <h4 class="{{ $summary['sisa'] < 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($summary['sisa'], 0, ',', '.') }}</h4>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">% Realisasi</h6>
                    <h4>{{ number_format($summary['persen'], 1, ',', '.') }}%</h4>
                </div></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Realisasi Anggaran per Lokasi</h5>
                <a href="{{ route('laporan.realisasi-anggaran.export', request()->query()) }}" class="btn btn-success">
                    <i class="bx bx-download me-1"></i>Export Excel
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select">
                            @foreach($tahunList as $th)
                                <option value="{{ $th }}" @selected($tahun == $th)>{{ $th }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Lokasi</label>
                        <select name="lokasi_id" class="form-select">
                            <option value="">Semua Lokasi</option>
                            @foreach($lokasiList as $id => $nama)
                                <option value="{{ $id }}" @selected(request('lokasi_id') == $id)>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter me-1"></i>Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th><th>Lokasi (Unit)</th>
                                <th class="text-end">Pagu</th><th class="text-end">Realisasi</th>
                                <th class="text-end">Sisa</th><th style="min-width:150px">% Realisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $a)
                                @php $p = $a->persen_realisasi; @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $a->lokasi->nama_lokasi ?? '-' }}</td>
                                    <td class="text-end">Rp {{ number_format($a->pagu, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($a->realisasi, 0, ',', '.') }}</td>
                                    <td class="text-end {{ $a->sisa < 0 ? 'text-danger fw-bold' : '' }}">Rp {{ number_format($a->sisa, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="progress" style="height: 8px">
                                            <div class="progress-bar {{ $p > 100 ? 'bg-danger' : ($p >= 80 ? 'bg-warning' : 'bg-success') }}"
                                                style="width: {{ min(100, $p) }}%"></div>
                                        </div>
                                        <small>{{ number_format($p, 1, ',', '.') }}%</small>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada anggaran untuk tahun {{ $tahun }}.</td></tr>
                            @endforelse
                            @if ($tanpa_anggaran['jumlah'] > 0)
                                <tr class="table-warning">
                                    <td></td>
                                    <td colspan="2">Belum dikaitkan anggaran ({{ $tanpa_anggaran['jumlah'] }} pengadaan tahun {{ $tahun }}, tanpa hibah)</td>
                                    <td class="text-end">Rp {{ number_format($tanpa_anggaran['nilai'], 0, ',', '.') }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Realisasi = total harga pengadaan yang dikaitkan ke anggaran (Peralatan dan Perlengkapan); pengadaan berstatus hibah tidak dihitung.</small>
            </div>
        </div>
    </div>
</x-layout.app>
