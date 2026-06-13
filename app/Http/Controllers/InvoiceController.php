<?php

namespace App\Http\Controllers;

use App\Models\Core\Order;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Factures PDF (feature #12). Génère la facture d'une commande à la demande
 * (toujours à jour) via dompdf. Réservé au propriétaire de la commande.
 */
class InvoiceController extends Controller
{
    public function download(Request $request, $token)
    {
        $subscriber = Auth::guard('subscribers')->user();
        if (!$subscriber) {
            abort(403);
        }

        $order = Order::where('token', $token)
            ->where('subscriber_id', $subscriber->id)
            ->firstOrFail();

        $pdf = Pdf::loadView('pages.invoice', [
            'order' => $order,
            'subscriber' => $subscriber,
            'purchases' => $order->purchases,
        ]);

        return $pdf->download('facture-cirkle-' . $order->id . '.pdf');
    }
}
