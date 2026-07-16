<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Pengadaan Barang</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 10px;
        }

        .label-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .label-cell {
            width: 50%;
            padding: 3px;
            vertical-align: top;
        }

        .label-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
        }

        .qr-cell {
            width: 70px;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #000;
        }

        .text-cell {
            padding: 0;
            vertical-align: middle;
            text-align: center;
            align-content: start;
        }

        .ppda-bar {
            background: #1b3a5c;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            padding: 6px;
        }

        .nama-barang {
            font-weight: bold;
            font-size: 12px;
            padding: 4px 6px;
        }

        .kode-inventaris {
            padding: 4px 6px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    @foreach ($items->chunk(2) as $rowItems)
        <table class="label-row">
            <tr>
                @foreach ($rowItems as $item)
                    <td class="label-cell">
                        <table class="label-table">
                            <tr>
                                <td class="qr-cell">
                                    <img src="{{ $logoDataUri }}" alt="Logo Darul Arqam" width="60" height="60">
                                </td>
                                <td class="text-cell">
                                    <div class="ppda-bar">HAK MILIK PPDA</div>
                                    <div class="nama-barang">{{ $item->barang->nama_barang ?? '-' }}</div>
                                    <div class="kode-inventaris">{{ $item->kode_inventaris }}</div>
                                </td>
                                <td class="qr-cell">
                                    <img src="{{ $item->qr_data_uri }}" width="60" height="60">
                                </td>
                            </tr>
                        </table>
                    </td>
                @endforeach
                @if ($rowItems->count() < 2)
                    <td class="label-cell"></td>
                @endif
            </tr>
        </table>
    @endforeach
</body>
</html>
