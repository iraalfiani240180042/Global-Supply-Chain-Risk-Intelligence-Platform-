<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use App\Models\PortStatus;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index()
    {
        $ports = Port::with(['country', 'status'])
            ->latest()
            ->paginate(20);

        return view('ports.index', compact('ports'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $statuses = PortStatus::orderBy('status')->get();

        return view('ports.create', compact('countries', 'statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'status_id' => 'required|exists:port_statuses,id',
            'name' => 'required|max:255',
            'city' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Port::create($request->all());

        return redirect()
            ->route('ports.index')
            ->with('success', 'Port added successfully.');
    }

    public function sync()
    {
        $file = storage_path('app/ports/allCountries.txt');

        if (!file_exists($file)) {
            return redirect()
                ->route('ports.index')
                ->with('error', 'Dataset allCountries.txt tidak ditemukan.');
        }

        $status = PortStatus::firstOrCreate([
            'status' => 'Active'
        ]);

        $handle = fopen($file, 'r');

        while (($line = fgets($handle)) !== false) {

            $row = explode("\t", trim($line));

            if (count($row) < 19) {
                continue;
            }

            $featureClass = $row[6];
            $featureCode  = $row[7];

            // Tampilkan feature pertama yang dibaca
            dd([
                'Feature Class' => $featureClass,
                'Feature Code'  => $featureCode,
                'Name'          => $row[1],
                'Country'       => $row[8],
            ]);
        }

        fclose($handle);

        return redirect()
            ->route('ports.index')
            ->with('success', 'Done');
    }
}