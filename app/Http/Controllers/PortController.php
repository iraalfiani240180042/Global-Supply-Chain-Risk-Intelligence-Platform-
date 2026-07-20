<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use App\Models\PortStatus;
use Illuminate\Http\Request;

class PortController extends Controller
{
    /**
     * Port Dashboard
     */
    public function index()
    {
        $ports = Port::with(['country', 'status'])
            ->latest()
            ->paginate(20);

        $countries = Country::orderBy('name')->get();

        $totalPorts = Port::count();
        $totalCountries = Country::count();
        $activePorts = Port::whereHas('status', function ($q) {
            $q->where('status', 'Active');
        })->count();

        $totalRegions = Country::distinct('region_id')->count('region_id');

        return view('ports.index', compact(
            'ports',
            'countries',
            'totalPorts',
            'totalCountries',
            'activePorts',
            'totalRegions'
        ));
    }

    /**
     * Form Create
     */
    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $statuses = PortStatus::orderBy('status')->get();

        return view('ports.create', compact(
            'countries',
            'statuses'
        ));
    }

    /**
     * Store
     */
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

    /**
     * Sync Dataset Port
     */
    public function sync()
    {
        $file = storage_path('app/ports/allCountries.txt');

        if (!file_exists($file)) {
            return redirect()
                ->route('ports.index')
                ->with('error', 'Dataset allCountries.txt tidak ditemukan.');
        }

        $activeStatus = PortStatus::firstOrCreate([
            'status' => 'Active'
        ]);

        $handle = fopen($file, 'r');

        $success = 0;

        while (($line = fgets($handle)) !== false) {

            $row = explode("\t", trim($line));

            if (count($row) < 19) {
                continue;
            }

            $featureCode  = $row[7];

            // Hanya ambil pelabuhan berdasarkan feature code 'PRT'
            if ($featureCode != 'PRT') {
                continue;
            }

            $countryCode = strtoupper($row[8]);

            $country = Country::where('iso_code', $countryCode)->first();

            if (!$country) {
                continue;
            }

            Port::updateOrCreate(
                [
                    'country_id' => $country->id,
                    'name'       => $row[1],
                ],
                [
                    'status_id' => $activeStatus->id,
                    'city'      => $row[2] ?: $row[1],
                    'latitude'  => $row[4],
                    'longitude' => $row[5],
                ]
            );

            $success++;
        }

        fclose($handle);

        return redirect()
            ->route('ports.index')
            ->with('success', "$success ports imported successfully.");
    }

    /**
     * Get ports by country ID
     */
    public function getPortsByCountry($country)
    {
        $ports = Port::where('country_id', $country)
            ->orderBy('name')
            ->get([
                'id',
                'name'
            ]);

        return response()->json($ports);
    }

    /**
     * Get single port data details
     */
    public function getPortDetail(Port $port)
    {
        return response()->json([
            'id' => $port->id,
            'name' => $port->name,
            'country' => $port->country->name,
            'region' => $port->country->region->name ?? '-',
            'latitude' => $port->latitude,
            'longitude' => $port->longitude,
            'status' => $port->status->status,
        ]);
    }

    /**
     * Hapus Port
     */
    public function destroy(Port $port)
    {
        $port->delete();

        return redirect()
            ->route('ports.index')
            ->with('success', 'Port deleted successfully.');
    }
}