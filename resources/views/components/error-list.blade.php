@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@elseif (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@elseif (session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@elseif (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@elseif ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-group">
            @foreach ($errors->all() as $error)
                <li class="border-none list-group-item">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
