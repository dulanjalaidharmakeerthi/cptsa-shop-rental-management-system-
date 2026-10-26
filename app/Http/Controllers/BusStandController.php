<?php

namespace App\Http\Controllers;

use App\Models\BusStand;
use Illuminate\Http\Request;

class BusStandController extends Controller
{
    public function create()
    {
        return view('busstands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
        ]);

    $bs = BusStand::create($data);

    // Redirect back to the create page with the newly created id so user can quickly create a shop under it
    return redirect()->to(route('busstands.create') . '?created=' . $bs->id)->with('success', 'Bus stand created successfully.');
    }
}
