<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuoteRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'whatsapp' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'qty' => 'nullable|integer|min:1',
            'message' => 'nullable|string',
            'product_name' => 'nullable|string|max:255',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,svg|max:4096',
            'simulation_snapshot' => 'nullable|string', // Base64 dataURL from Canvas
            'simulation_data' => 'nullable|array',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $snapshotPath = null;
        if (!empty($validated['simulation_snapshot'])) {
            $base64Image = $validated['simulation_snapshot'];
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]); // png, jpg, etc.

                if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    $base64Image = base64_decode($base64Image);

                    if ($base64Image !== false) {
                        $filename = 'sim_' . time() . '_' . uniqid() . '.' . $type;
                        Storage::disk('public')->put('simulations/' . $filename, $base64Image);
                        $snapshotPath = 'simulations/' . $filename;
                    }
                }
            }
        }

        $quote = QuoteRequest::create([
            'name' => $validated['name'],
            'company' => $validated['company'] ?? null,
            'whatsapp' => $validated['whatsapp'],
            'email' => $validated['email'] ?? null,
            'qty' => $validated['qty'] ?? null,
            'message' => $validated['message'] ?? null,
            'product_name' => $validated['product_name'] ?? null,
            'logo_path' => $logoPath,
            'simulation_image_path' => $snapshotPath,
            'simulation_data' => $validated['simulation_data'] ?? null,
        ]);

        // Construir mensaje de WhatsApp
        $whatsappNumber = SiteSetting::getValue('whatsapp_number', '593900000000');
        
        $text = "Hola, he enviado una cotización desde la web.\n\n";
        $text .= "*Nombre:* " . $quote->name . "\n";
        if ($quote->company) $text .= "*Empresa:* " . $quote->company . "\n";
        $text .= "*WhatsApp:* " . $quote->whatsapp . "\n";
        if ($quote->product_name) $text .= "*Producto:* " . $quote->product_name . "\n";
        if ($quote->qty) $text .= "*Cantidad:* " . $quote->qty . "\n";
        if ($quote->message) $text .= "*Mensaje:* " . $quote->message . "\n";

        if ($snapshotPath) {
            $text .= "\n_Adjunto simulación realizada en Canvas._";
        }

        $whatsappUrl = "https://wa.me/" . $whatsappNumber . "?text=" . urlencode($text);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de cotización enviada correctamente.',
            'redirect_url' => $whatsappUrl,
            'quote_id' => $quote->id
        ]);
    }
}
