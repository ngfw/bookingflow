<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Receipt - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.2;
            margin: 0;
            padding: 5px;
            color: #000;
            width: 200px;
        }
        
        .receipt {
            width: 100%;
            margin: 0 auto;
        }
        
        .center {
            text-align: center;
        }
        
        .right {
            text-align: right;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        
        .business-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .business-info {
            font-size: 8px;
            margin-bottom: 2px;
        }
        
        .receipt-title {
            font-size: 11px;
            font-weight: bold;
            margin: 8px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 9px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 8px;
        }
        
        .items-table th,
        .items-table td {
            padding: 2px;
            text-align: left;
        }
        
        .items-table th {
            font-weight: bold;
            border-bottom: 1px solid #000;
        }
        
        .items-table .qty {
            text-align: center;
            width: 15%;
        }
        
        .items-table .price {
            text-align: right;
            width: 25%;
        }
        
        .items-table .total {
            text-align: right;
            width: 25%;
        }
        
        .totals {
            margin: 8px 0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 9px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 7px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="center">
            <div class="business-name">{{ $business['name'] }}</div>
            
            @if($options['include_address'])
                <div class="business-info">{{ $business['address'] }}</div>
            @endif
            
            @if($options['include_phone'])
                <div class="business-info">{{ $business['phone'] }}</div>
            @endif
            
            @if($options['include_email'])
                <div class="business-info">{{ $business['email'] }}</div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Receipt Title -->
        <div class="center receipt-title">RECEIPT</div>

        <!-- Receipt Information -->
        <div class="info-row">
            <span>#{{ $invoice->invoice_number }}</span>
            <span>{{ $invoice->created_at->format('M d, Y H:i') }}</span>
        </div>
        
        @if($invoice->client)
            <div class="info-row">
                <span>Customer:</span>
                <span>{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</span>
            </div>
        @endif

        <div class="divider"></div>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="qty">Qty</th>
                    <th class="price">Price</th>
                    <th class="total">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product ? $item->product->name : 'Service' }}</td>
                        <td class="qty">{{ $item->quantity }}</td>
                        <td class="price">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="total">${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            
            @if($invoice->discount_amount > 0)
                <div class="total-row">
                    <span>Discount:</span>
                    <span>-${{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
            @endif
            
            @if($invoice->tax_amount > 0)
                <div class="total-row">
                    <span>Tax:</span>
                    <span>${{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
            @endif
            
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>${{ number_format($invoice->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Information -->
        @if($payment)
            <div class="divider"></div>
            <div class="info-row">
                <span>Payment:</span>
                <span>{{ ucfirst($payment->payment_method) }}</span>
            </div>
            <div class="info-row">
                <span>Amount:</span>
                <span>${{ number_format($payment->amount, 2) }}</span>
            </div>
        @endif

        <div class="divider"></div>

        <!-- Footer -->
        <div class="footer">
            <div>Thank you!</div>
            <div>Keep this receipt</div>
        </div>
    </div>
</body>
</html>
