<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SuratUndangan;
use App\Models\SuketAbsen;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPegawai = Pegawai::count();
        $totalSuratUndangan = SuratUndangan::count();
        $totalSuket = SuketAbsen::count();
        $totalSuratKeluar = $totalSuratUndangan + $totalSuket;

        $suratFinalCount = SuratUndangan::where('status', 'final')->count();
        $suratDraftCount = SuratUndangan::where('status', '!=', 'final')->count();

        $suratTerbaru = SuratUndangan::with('penerimas')
            ->latest()
            ->take(5)
            ->get();

        $suketTerbaru = SuketAbsen::latest()
            ->take(5)
            ->get();

        $suratMendatang = SuratUndangan::latest()->first();
        $pegawaiTerbaru = Pegawai::orderBy('id', 'desc')->take(4)->get();
        $unitKerjaCount = Pegawai::distinct('unit')->count('unit');

        // Data grafik 7 bulan terakhir
        $chartLabels = [];
        $chartValues = [];
        $bulanIndo = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $countUndangan = SuratUndangan::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
            $countSuket = SuketAbsen::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $chartLabels[] = $bulanIndo[$month - 1];
            $chartValues[] = $countUndangan + $countSuket;
        }

        return view('dashboard.index', compact(
            'totalPegawai',
            'totalSuratUndangan',
            'totalSuket',
            'totalSuratKeluar',
            'suratFinalCount',
            'suratDraftCount',
            'suratTerbaru',
            'suketTerbaru',
            'suratMendatang',
            'pegawaiTerbaru',
            'unitKerjaCount',
            'chartLabels',
            'chartValues'
        ));
    }
}
