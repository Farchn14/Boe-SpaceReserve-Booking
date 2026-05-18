<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $facilities = \App\Models\Fasilitas::orderBy('nama')->get();
        $selectedFasilitasId = $request->query('fasilitas_id');

        return view('schedule_booking', compact('facilities', 'selectedFasilitasId'));
    }
}
