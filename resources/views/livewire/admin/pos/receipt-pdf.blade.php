<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: {{ $options['font_size'] === 'small' ? '10px' : ($options['font_size'] === 'medium' ? '12px' : '14px') }};
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #000;
        }
        
        .receipt {
            max-width: {{ $options['paper_size'] === 'thermal' ? '300px' : '600px' }};
            margin: 0 auto;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .business-name {
            font-size: {{ $options['font_size'] === 'small' ? '16px' : ($options['font_size'] === 'medium' ? '18px' : '20px') }};
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .business-info {
            font-size: {{ $options['font_size'] === 'small' ? '9px' : ($options['font_size'] === 'medium' ? '10px' : '11px') }};
            margin-bottom: 3px;
        }
        
        .receipt-title {
            font-size: {{ $options['font_size'] === 'small' ? '14px' : ($options['font_size'] === 'medium' ? '16px' : '18px') }};
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        
        .receipt-info {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th,
        .items-table td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .items-table th {
            font-weight: bold;
            border-bottom: 2px solid #000;
        }
        
        .items-table .qty {
            text-align: center;
            width: 15%;
        }
        
        .items-table .price {
            text-align: right;
            width: 20%;
        }
        
        .items-table .total {
            text-align: right;
            width: 20%;
        }
        
        .totals {
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-bottom: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: {{ $options['font_size'] === 'small' ? '12px' : ($options['font_size'] === 'medium' ? '14px' : '16px') }};
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 10px;
        }
        
        .payment-info {
            margin-bottom: 20px;
        }
        
        .footer {
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 15px;
            margin-top: 20px;
            font-size: {{ $options['font_size'] === 'small' ? '8px' : ($options['font_size'] === 'medium' ? '9px' : '10px') }};
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 15px 0;
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
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            @if($options['include_logo'] && $business['logo'])
                <img src="{{ $business['logo'] }}" alt="Logo" style="max-height: 50px; margin-bottom: 10px;">
            @endif
            
            <div class="business-name">{{ $business['name'] }}</div>
            
            @if($options['include_address'])
                <div class="business-info">{{ $business['address'] }}</div>
            @endif
            
            @if($options['include_phone'])
                <div class="business-info">Tel: {{ $business['phone'] }}</div>
            @endif
            
            @if($options['include_email'])
                <div class="business-info">Email: {{ $business['email'] }}</div>
            @endif
            
            @if($options['include_website'])
                <div class="business-info">Web: {{ $business['website'] }}</div>
            @endif
            
            @if($options['include_tax_id'])
                <div class="business-info">Tax ID: {{ $business['tax_id'] }}</div>
            @endif
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">RECEIPT</div>

        <!-- Receipt Information -->
        <div class="receipt-info">
            <div class="info-row">
                <span>Receipt #:</span>
                <span class="bold">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span>Date:</span>
                <span>{{ $invoice->created_at->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span>Time:</span>
                <span>{{ $invoice->created_at->format('H:i') }}</span>
            </div>
            @if($invoice->client)
                <div class="info-row">
                    <span>Customer:</span>
                    <span>{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</span>
                </div>
                @if($invoice->client->email)
                    <div class="info-row">
                        <span>Email:</span>
                        <span>{{ $invoice->client->email }}</span>
                    </div>
                @endif
            @else
                <div class="info-row">
                    <span>Customer:</span>
                    <span>Walk-in Customer</span>
                </div>
            @endif
        </div>

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
                        <td>
                            {{ $item->product ? $item->product->name : 'Service' }}
                            @if($item->product && $item->product->sku)
                                <br><small>({{ $item->product->sku }})</small>
                            @endif
                        </td>
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
            <div class="payment-info">
                <div class="info-row">
                    <span>Payment Method:</span>
                    <span class="bold">{{ ucfirst($payment->payment_method) }}</span>
                </div>
                <div class="info-row">
                    <span>Amount Paid:</span>
                    <span class="bold">${{ number_format($payment->amount, 2) }}</span>
                </div>
                @if($payment->reference_number)
                    <div class="info-row">
                        <span>Reference:</span>
                        <span>{{ $payment->reference_number }}</span>
                    </div>
                @endif
            </div>
        @endif

        <div class="divider"></div>

        <!-- Footer -->
        @if($options['include_footer'])
            <div class="footer">
                <div>Thank you for your business!</div>
                <div>Please keep this receipt for your records.</div>
                @if($options['include_website'])
                    <div>Visit us at {{ $business['website'] }}</div>
                @endif
            </div>
        @endif
    </div>
</body>
</html>
