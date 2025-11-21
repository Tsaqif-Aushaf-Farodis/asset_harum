<x-layout.app title="Buat Sesi Opname" activeMenu="opname.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Buat Sesi Opname" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Opname', 'url' => route('opname.index')],
            ['label' => 'Buat Sesi Opname'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('opname.store') }}" method="POST" role="form">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_opname" class="form-label">Nama Opname <span class="text-danger">*</span></label>
                            <input type="text" name="nama_opname" id="nama_opname" class="form-control" 
                                   value="{{ old('nama_opname') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lokasi_id" class="form-label">Lokasi</label>
                            <select name="lokasi_id" id="lokasi_id" class="form-select">
                                <option value="">Semua Lokasi</option>
                                @foreach($lokasi as $lok)
                                    <option value="{{ $lok->id }}" {{ old('lokasi_id') == $lok->id ? 'selected' : '' }}>
                                        {{ $lok->nama_sub_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Kosongkan jika ingin mengopname semua lokasi</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" 
                                   value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tanggal_selesai" class="form-label">Target Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" 
                                   value="{{ old('tanggal_selesai') }}">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Buat Sesi Opname</button>
                        <a href="{{ route('opname.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
