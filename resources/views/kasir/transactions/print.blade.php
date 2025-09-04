@php
    $shopName = 'Sistem Koperasi';
    $address = 'Jl. Contoh No. 123, Indonesia';
    $phone = '0812-3456-7890';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Struk {{ $transaction->transaction_number }}</title>
	<style>
		* { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, Noto Sans, "Apple Color Emoji", "Segoe UI Emoji"; }
		body { margin: 0; padding: 16px; }
		.receipt { width: 320px; margin: 0 auto; }
		.header { text-align: center; margin-bottom: 12px; }
		.header h1 { font-size: 16px; margin: 0; }
		.header p { font-size: 11px; margin: 2px 0; color: #4b5563; }
		.hr { border: 0; border-top: 1px dashed #9ca3af; margin: 8px 0; }
		.row { display: flex; justify-content: space-between; font-size: 12px; }
		.items { margin-top: 8px; font-size: 12px; }
		.item { display: flex; justify-content: space-between; margin: 4px 0; }
		.total { font-weight: 700; font-size: 13px; }
		.small { font-size: 11px; color: #6b7280; }
		.actions { text-align: center; margin-top: 12px; }
		@media print { .no-print { display: none; } body { padding: 0; } }
	</style>
</head>
<body>
	<div class="receipt">
		<div class="header">
			<h1>{{ $shopName }}</h1>
			<p>{{ $address }}</p>
			<p>{{ $phone }}</p>
		</div>

		<div class="row"><span>No: {{ $transaction->transaction_number }}</span><span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span></div>
		<div class="row"><span>Kasir</span><span>{{ $transaction->user->name }}</span></div>
		<hr class="hr"/>

		<div class="items">
			@foreach($transaction->details as $detail)
				<div class="item">
					<span>{{ $detail->product->name }} x{{ $detail->quantity }}</span>
					<span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
				</div>
			@endforeach
		</div>
		<hr class="hr"/>
		<div class="row total"><span>Total</span><span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span></div>
		@if($transaction->notes)
			<hr class="hr"/>
			<div class="small">Catatan: {{ $transaction->notes }}</div>
		@endif

		<div class="actions no-print">
			<button onclick="window.print()">Print</button>
		</div>
		<div class="small" style="text-align:center;margin-top:8px;">Terima kasih</div>
	</div>

	<script>window.addEventListener('load', function(){ setTimeout(() => window.print(), 200); });</script>
</body>
</html>

