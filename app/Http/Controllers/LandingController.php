<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Ptk;
use App\Models\Rombel;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        return view('landing', [
            'totalSiswa' => Siswa::count(),
            'totalPtk' => Ptk::count(),
            'totalRombel' => Rombel::count(),
        ]);
    }
}
