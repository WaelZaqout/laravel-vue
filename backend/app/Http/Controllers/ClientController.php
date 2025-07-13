<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
public function index(Request $request)
{
    $perPage = $request->input('per_page', 7); // عدد العناصر لكل صفحة، افتراضي 10

    $clients = Client::latest()->paginate($perPage);

        return response()->json($clients);
    }
  
  

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $client = new Client();
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'role' => 'required|in:client,admin',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $client = Client::create($request->all());

        return response()->json([
            'message' => 'Client created successfully.',
            'client' => $client,
        ], 201);
    }


public function update(Request $request, $id)
{
    $client = Client::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'title' => 'nullable|string|max:255',
        'role' => 'required|in:client,admin',
        'email' => [
            'required',
            'email',
            Rule::unique('clients')->ignore($client->id),
        ],
        'phone' => 'nullable|string|max:20',
    ]);

    $client->update($request->all());

    return response()->json([
        'message' => 'Client updated successfully.',
        'client' => $client,
    ]);
}

  
  
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::find($id);
        $client->delete();
        
        return response(null, 204);

    }
}
