<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #14181f; }
        .header { border-bottom: 2px solid #1b9c5a; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #157a47; margin: 0; }
        .meta td { padding: 2px 0; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.items th { background: #14181f; color: #fff; text-align: left; padding: 6px 8px; }
        table.items td { padding: 6px 8px; border-bottom: 1px solid #e2e6df; }
        .totals { width: 40%; margin-left: auto; margin-top: 12px; }
        .totals td { padding: 3px 8px; }
        .totals .grand { font-weight: bold; border-top: 2px solid #14181f; }
        .muted { color: #69716b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ setting('company_name') ?? 'CIRKLE' }}</h1>
        <div class="muted">cirkleservices.com</div>
    </div>

    <table class="meta">
        <tr><td><strong>{{ app()->getLocale() === 'fr' ? 'Facture' : 'Invoice' }} #</strong></td><td>{{ $order->id }}</td></tr>
        <tr><td><strong>Date</strong></td><td>{{ \Illuminate\Support\Carbon::parse($order->created_at ?? $order->order_datetime)->format('Y-m-d') }}</td></tr>
        <tr><td><strong>{{ app()->getLocale() === 'fr' ? 'Membre' : 'Member' }}</strong></td><td>{{ $subscriber->formatted_member_number ?? $subscriber->id }} — {{ $subscriber->company_name ?: $subscriber->name }}</td></tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>{{ app()->getLocale() === 'fr' ? 'Description' : 'Description' }}</th>
                <th style="text-align:right">{{ app()->getLocale() === 'fr' ? 'Quantité' : 'Qty' }}</th>
                <th style="text-align:right">{{ app()->getLocale() === 'fr' ? 'Montant' : 'Amount' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->product_name ?? $purchase->item_name }}</td>
                    <td style="text-align:right">{{ $purchase->quantity ?? 1 }}</td>
                    <td style="text-align:right">{{ prettyPrice($purchase->total_price) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="muted">—</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr><td>{{ app()->getLocale() === 'fr' ? 'Sous-total' : 'Subtotal' }}</td><td style="text-align:right">{{ prettyPrice($order->sub_total_price) }}</td></tr>
        <tr><td>TPS</td><td style="text-align:right">{{ prettyPrice($order->tps_price) }}</td></tr>
        <tr><td>TVQ</td><td style="text-align:right">{{ prettyPrice($order->tvq_price) }}</td></tr>
        <tr class="grand"><td>{{ app()->getLocale() === 'fr' ? 'Total' : 'Total' }}</td><td style="text-align:right">{{ prettyPrice($order->total_price) }}</td></tr>
    </table>
</body>
</html>
