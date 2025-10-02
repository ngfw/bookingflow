<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .receipt-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-title h2 {
            font-size: 18px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        .receipt-title p {
            margin: 0;
            font-size: 11px;
        }
        .details-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .details-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .details-column:last-child {
            padding-right: 0;
        }
        .details-column h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .details-column p {
            margin: 3px 0;
            font-size: 11px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        .invoice-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .invoice-table td {
            vertical-align: top;
        }
        .summary {
            border-top: 2px solid #333;
            padding-top: 15px;
            margin-top: 20px;
        }
        .summary-table {
            width: 300px;
            margin-left: auto;
        }
        .summary-table tr td {
            padding: 3px 0;
            font-size: 11px;
        }
        .summary-table tr td:first-child {
            text-align: left;
        }
        .summary-table tr td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .summary-total {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            color: #666;
        }
        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $salon['name'] }}</h1>
        <p>{{ $salon['address'] }}</p>
        <p>Phone: {{ $salon['phone'] }} | Email: {{ $salon['email'] }}</p>
        <p>Website: {{ $salon['website'] }}</p>
    </div>

    <!-- Receipt Title -->
    <div class="receipt-title">
        <h2>PAYMENT RECEIPT</h2>
        <p>Receipt #{{ $payment->payment_number }}</p>
    </div>

    <!-- Details Grid -->
    <div class="details-grid">
        <div class="details-column">
            <h3>Client Information</h3>
            <p><strong>Name:</strong> {{ $client->user->name }}</p>
            <p><strong>Email:</strong> {{ $client->user->email }}</p>
            @if($client->phone)
                <p><strong>Phone:</strong> {{ $client->phone }}</p>
            @endif
        </div>
        <div class="details-column">
            <h3>Payment Details</h3>
            <p><strong>Payment Date:</strong> {{ $payment->payment_date->format('M j, Y') }}</p>
            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
            <p><strong>Amount:</strong> ${{ number_format($payment->amount, 2) }}</p>
            @if($payment->reference_number)
                <p><strong>Reference:</strong> {{ $payment->reference_number }}</p>
            @endif
            @if($payment->transaction_id)
                <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
            @endif
        </div>
    </div>

    <!-- Invoice Details -->
    <h3 style="font-size: 14px; margin-bottom: 10px; font-weight: bold;">Invoice Details</h3>
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        {{ $item->item_name }}
                        @if($item->description)
                            <br><em style="color: #666;">{{ $item->description }}</em>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Payment Summary -->
    <div class="summary">
        <table class="summary-table">
            <tr>
                <td>Subtotal:</td>
                <td>${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td style="color: green;">-${{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if($invoice->tax_amount > 0)
                <tr>
                    <td>Tax ({{ $invoice->tax_rate }}%):</td>
                    <td>${{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="summary-total">
                <td>Total Paid:</td>
                <td style="color: green;">${{ number_format($payment->amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Receipt generated on {{ now()->format('M j, Y \a\t g:i A') }}</p>
        @if($payment->processedBy)
            <p>Processed by: {{ $payment->processedBy->name }}</p>
        @endif
    </div>
</body>
</html>
