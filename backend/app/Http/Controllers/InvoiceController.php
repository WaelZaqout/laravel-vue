<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 7);

        // جلب الفواتير مع بيانات العملاء
        $invoices = Invoice::with('client')
            ->latest()
            ->paginate($perPage);

        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // إضافة logging لتتبع البيانات المرسلة
        Log::info('Invoice store request data:', $request->all());
        
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,active,paid',
        ]);

        $invoice = Invoice::create([
            'client_id' => $request->client_id,
            'amount' => $request->amount,
            'status' => $request->status,
        ]);

        // إرجاع الفاتورة مع بيانات العميل
        $invoice->load('client');

        return response()->json([
            'message' => 'Invoice created successfully.',
            'invoice' => $invoice,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,active,paid',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'client_id' => $request->client_id,
            'amount' => $request->amount,
            'status' => $request->status,
        ]);

        // إرجاع الفاتورة مع بيانات العميل
        $invoice->load('client');

        return response()->json([
            'message' => 'Invoice updated successfully.',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully.',
        ], 200);
    }
}