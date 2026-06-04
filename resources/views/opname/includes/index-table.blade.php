<div id="table-container">
    <div class="mb-3">
        <form id="searchForm" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari kode atau nama opname..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-search me-1"></i>Cari
                </button>
                <a href="{{ route('opname.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-refresh me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Kode Opname</th>
                    <th>Nama Opname</th>
                    <th>Lokasi</th>
                    <th>Tanggal Mulai</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $index => $session)
                <tr>
                    <td>{{ $sessions->firstItem() + $index }}</td>
                    <td>{{ $session->id }}</td>
                    <td>{{ $session->nama_opname }}</td>
                    <td>{{ $session->lokasi->nama_sub_lokasi ?? 'Semua Lokasi' }}</td>
                    <td>{{ $session->tanggal_mulai ? \Carbon\Carbon::parse($session->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($session->status == 'draft')
                            <span class="badge bg-secondary">Draft</span>
                        @elseif($session->status == 'ongoing')
                            <span class="badge bg-primary">Ongoing</span>
                        @else
                            <span class="badge bg-success">Completed</span>
                        @endif
                    </td>
                    <td>{{ $session->createdBy->name ?? '-' }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                    data-bs-toggle="dropdown">
                                Aksi
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('opname.show', $session) }}">
                                    <i class="bx bx-show me-1"></i>Detail
                                </a>
                                @if($session->status == 'draft')
                                    <a class="dropdown-item" href="{{ route('opname.edit', $session) }}">
                                        <i class="bx bx-edit me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('opname.start', $session) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-primary">
                                            <i class="bx bx-play me-1"></i>Mulai Opname
                                        </button>
                                    </form>
                                @endif
                                @if($session->status == 'ongoing')
                                    <a class="dropdown-item" href="{{ route('opname.input-hasil', $session) }}">
                                        <i class="bx bx-edit me-1"></i>Input Hasil
                                    </a>
                                    <a class="dropdown-item" href="{{ route('opname.export-kertas-kerja', $session) }}">
                                        <i class="bx bx-download me-1"></i>Export Kertas Kerja
                                    </a>
                                    <form action="{{ route('opname.complete', $session) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menyelesaikan opname ini?')">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-success">
                                            <i class="bx bx-check me-1"></i>Selesaikan Opname
                                        </button>
                                    </form>
                                @endif
                                @if($session->status == 'completed')
                                    <a class="dropdown-item" href="{{ route('opname.export-laporan', $session) }}">
                                        <i class="bx bx-download me-1"></i>Export Laporan
                                    </a>
                                @endif
                                @if($session->status == 'draft')
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('opname.destroy', $session) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data opname</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $sessions->links() }}
    </div>
</div>
