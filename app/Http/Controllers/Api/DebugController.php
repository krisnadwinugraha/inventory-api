<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DebugController extends Controller
{
    public function sqlAnalysis(): JsonResponse
    {
        return response()->json([
            'title' => 'Analisis Debugging SQL',

            'original_query' => "
                SELECT v.name, SUM(p.total)
                FROM vendors v
                JOIN purchases p ON p.vendor_id = v.id
                GROUP BY v.id
            ",

            'problems' => [
                [
                    'issue'       => 'Ada kolom di SELECT yang nggak masuk ke GROUP BY',
                    'explanation' => 'Kolom v.name dipanggil di SELECT tapi nggak ada di GROUP BY. Kalau mode ONLY_FULL_GROUP_BY di MySQL nyala (bawaan sejak MySQL 5.7.5), query ini bakal error. Databasenya bingung mau nampilin nilai v.name yang mana buat tiap grup v.id.',
                ],
                [
                    'issue'       => 'Hasil SUM nggak pakai alias',
                    'explanation' => 'Fungsi SUM(p.total) nggak dikasih alias. Kolom hasilnya jadi nggak punya nama, nanti bakal repot pas datanya mau dipanggil di kode aplikasi.',
                ],
                [
                    'issue'       => 'Nggak ada ORDER BY',
                    'explanation' => 'Buat query laporan, datanya harus diurutkan biar masuk akal. Tanpa ORDER BY, urutan data yang keluar bakal acak dan nggak konsisten.',
                ],
            ],

            'why_it_matters' => 'Walaupun misal settingan MySQL-nya longgar dan query ini bisa jalan tanpa error, hasilnya tetap acak. Nilai v.name yang keluar bisa dari data mana aja di grup itu. Ini bahaya karena jatuhnya silent bug — kelihatannya jalan normal padahal datanya ngaco dan nggak bisa dipercaya. Kalau di mode strict, udah pasti langsung error.',

            'fixed_query' => "
                SELECT
                    v.id,
                    v.name,
                    COUNT(p.id)      AS total_purchases,
                    SUM(p.total)     AS total_nominal
                FROM vendors v
                INNER JOIN purchases p ON p.vendor_id = v.id
                GROUP BY v.id, v.name
                ORDER BY total_nominal DESC
            ",

            'what_changed' => [
                'Nambahin v.name ke GROUP BY'                   => 'Buat benerin error ONLY_FULL_GROUP_BY. Semua kolom di SELECT yang nggak diagregasi wajib masuk ke GROUP BY.',
                'Nambahin v.id ke SELECT'                       => 'Best practice buat selalu nampilin ID, biar di frontend/aplikasi gampang kalau mau nge-referensiin datanya.',
                'Ngasih alias total_nominal buat SUM(p.total)'  => 'Biar kolom hasilnya punya nama yang jelas dan gampang ditebak.',
                'Nambahin COUNT(p.id) AS total_purchases'       => 'Tambahan data yang lumayan berguna buat ringkasan laporan vendor.',
                'Ganti JOIN jadi INNER JOIN'                    => 'Biar lebih eksplisit aja. Nulis INNER JOIN bikin niat query-nya lebih jelas pas dibaca.',
                'Nambahin ORDER BY total_nominal DESC'          => 'Sekarang datanya udah diurutin dengan bener, dari nominal transaksi paling gede.',
            ],
        ]);
    }
}
