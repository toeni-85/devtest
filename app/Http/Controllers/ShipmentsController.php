<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Referent;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Shipments/Index', [
            'shipments' => Shipment::with('referents', 'team')->limit(100)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        return Inertia::render('Shipments/Show', [
            'shipment' => $shipment->load('referents', 'team'),
        ]);
    }

    /**
     * Check if a referent email already exists for the shipment's team.
     * Used mainly for frontend validation / duplicate prevention.
     */
    public function checkEmail(Request $request, Shipment $shipment)
    {
        $email = $request->query('email');

        // Check if a referent with this email already exists within the same team
        $exists = $shipment->referents()
            ->where('team_id', $shipment->team_id)
            ->where('email', $email)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Add a new referent to a shipment team for a specific scope (start or end).
     */
    public function addReferent(Request $request, Shipment $shipment)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:20',
            'scope'     => 'required|in:start,end',
        ]);

        // Prevent attaching the same referent email twice for the same shipment and scope
        if ($shipment->referents()
            ->where('email', $validated['email'])
            ->wherePivot('scope', $validated['scope'])
            ->exists()) {

            return response()->json([
                'message' => 'Referent already exists for this scope'
            ], 422);
        }

        // Create the referent and associate it with the shipment's team
        $referent = Referent::create([
            'name'      => $validated['name'],
            'last_name' => $validated['last_name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'team_id'   => $shipment->team_id,
        ]);

        // Attach referent to shipment with scope stored on pivot table
        $shipment->referents()->attach($referent->id, [
            'scope' => $validated['scope'],
        ]);

        return response()->json($referent, 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipment $shipment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipment $shipment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        //
    }
}
