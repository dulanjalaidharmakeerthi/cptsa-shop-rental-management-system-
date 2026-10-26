<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusStand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusStandController extends Controller
{
    public function create()
    {
        return view('admin.bus_stands.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:bus_stands,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        BusStand::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Bus stand created successfully!');
    }
}