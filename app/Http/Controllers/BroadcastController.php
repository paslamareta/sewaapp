<?php

namespace App\Http\Controllers;

use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BroadcastController extends Controller
{
    /**
     * Ambil semua riwayat broadcast
     */
    public function index()
    {
        $items = Broadcast::latest()->get();

        return response()->json([
            'items' => $items
        ]);
    }

    /**
     * Kirim broadcast email ke banyak penerima
     */
    public function send(Request $request)
    {
        // Validasi input
        $request->validate([
            'emails' => 'required|array',
            'emails.*' => 'email',
            'message' => 'required|string',
            'subject' => 'nullable|string'
        ]);

        $subject = $request->subject ?? 'Informasi terbaru dari kami';

        try {
            // Kirim email ke setiap penerima
            foreach ($request->emails as $email) {
                Mail::to($email)->send(new BroadcastMail(
                    $subject,
                    $request->message
                ));
            }

            // Simpan riwayat broadcast ke database
            Broadcast::create([
                'emails'  => implode(', ', $request->emails),
                'subject' => $subject,
                'message' => $request->message,
            ]);

            return response()->json([
                'message' => 'Broadcast berhasil dikirim!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengirim broadcast: ' . $e->getMessage()
            ], 500);
        }
    }
}