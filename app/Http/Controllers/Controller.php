<?php

namespace App\Http\Controllers;

use App\Models\Pesanwa;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

// extends \Illuminate\Routing\Controller
abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected function kirimWa($phone_no, $message, $jenis_pesan, $id)
    {
        // Pastikan input diproses dengan benar
        $message = str_replace("'", " ", $message);
        $message = str_replace("<ENTER>", "\n", $message);

        // Menghapus newline dan carriage return pada nomor telepon
        $phone_no = preg_replace("/(\n|\r)/", "", $phone_no);

        // Cek apakah nomor telepon diawali dengan '0', lalu ganti dengan kode negara +62
        if (substr($phone_no, 0, 1) == '0') {
            $phone_no = "+62" . substr($phone_no, 1);
        }

        // Ambil token dari .env file untuk keamanan
        $token = env('ULTRAMS_MSG_API_TOKEN', 'xf2gh72w4ly3jzuh');

        // Inisialisasi curl
        $curl = curl_init();

        // Setel opsi curl
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.ultramsg.com/instance16001/messages/chat",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query([
                'token' => $token,
                'to' => $phone_no,
                'body' => $message,
                'priority' => 1,
                'referenceId' => $id
            ]),
            CURLOPT_HTTPHEADER => [
                "content-type: application/x-www-form-urlencoded"
            ],
        ]);

        // Eksekusi curl dan tangani error
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        Pesanwa::create([
            'isi_pesan' => $message,
            'tgl_kirim' => now(),
            'status_pesan' => $response,
            'no_pendaftaran' => $id,
            'jenis_pesan' => $jenis_pesan,
            'no_hp' => $phone_no
        ]);

        return $response;
    }
}
