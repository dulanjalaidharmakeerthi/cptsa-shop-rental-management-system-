@extends('layouts.chap')

@section('title', 'Cashier Dashboard - Due Payments')

@section('content')
	@php
		// Fetch Bus Stands for the filter dropdown
		$busStands = \App\Models\BusStand::all();

		// Query Shops with filters
		$query = \App\Models\Shop::query();

		// Filter by Bus Stand
		if(request('bus_stand')){
			$query->where('bus_stand_id', request('bus_stand'));
		}

		// Filter by Owner Name or NIC
		if(request('search')){
			$search = request('search');
			$query->where(function($q) use ($search){
				$q->where('shop_owner_name', 'like', "%{$search}%")
				  ->orWhere('shop_owner_nic', 'like', "%{$search}%");
			});
		}

		// Get shops sorted by highest balance first
		$query->orderBy('balance_amount', 'desc');

		$shops = $query->get();
	@endphp

	<div class="dashboard-card">
		<div class="dashboard-card-header">
			<h1>Due Payments Report</h1>
		</div>

		{{-- Filter Form --}}
		<div class="filters" style="margin: 20px 0; padding: 15px; background-color: #f9f9f9; border-radius: 8px;">
			<form method="GET" action="{{ url()->current() }}">
				<div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end;">
					<div>
						<label for="bus_stand" style="display: block; margin-bottom: 5px; font-weight: bold;">Filter by Bus Stand:</label>
						{{-- This should be populated from the controller with a list of available bus stands --}}
						<select name="bus_stand" id="bus_stand" style="width: 220px; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
							<option value="">All Bus Stands</option>
							@foreach($busStands as $stand)
								<option value="{{ $stand->id }}" {{ request('bus_stand') == $stand->id ? 'selected' : '' }}>{{ $stand->name }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label for="search" style="display: block; margin-bottom: 5px; font-weight: bold;">Filter by Owner Name or NIC:</label>
						<input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Enter Name or NIC" style="width: 220px; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
					</div>
					<button type="submit" style="padding: 8px 20px; border: none; background-color: #007bff; color: white; border-radius: 4px; cursor: pointer;">Filter</button>
				</div>
			</form>
		</div>

		{{-- Due Payments Table --}}
		<div class="table-container" style="overflow-x: auto;">
			<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
				<thead>
					<tr style="background-color: #e9ecef;">
						<th style="padding: 12px; border: 1px solid #dee2e6; text-align: left;">Owner Name</th>
						<th style="padding: 12px; border: 1px solid #dee2e6; text-align: left;">NIC</th>
						<th style="padding: 12px; border: 1px solid #dee2e6; text-align: left;">Shop Number</th>
						<th style="padding: 12px; border: 1px solid #dee2e6; text-align: left;">Bus Stand</th>
						<th style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">Due Payment (LKR)</th>
						<th style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">Due Interest (LKR)</th>
						<th style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">Total Balance (LKR)</th>
						<th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Days Overdue</th>
					</tr>
				</thead>
				<tbody>
					@if($shops->count() > 0)
						@foreach($shops as $shop)
							<tr>
								<td style="padding: 12px; border: 1px solid #dee2e6;">{{ $shop->shop_owner_name }}</td>
								<td style="padding: 12px; border: 1px solid #dee2e6;">{{ $shop->shop_owner_nic ?? '-' }}</td>
								<td style="padding: 12px; border: 1px solid #dee2e6;">{{ $shop->shop_number }}</td>
								<td style="padding: 12px; border: 1px solid #dee2e6;">{{ $shop->busStand->name ?? $shop->bus_stand_id }}</td>
								<td style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">{{ number_format($shop->due_payment, 2) }}</td>
								<td style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">{{ number_format($shop->due_interest, 2) }}</td>
								<td style="padding: 12px; border: 1px solid #dee2e6; text-align: right; font-weight: bold;">{{ number_format($shop->balance_amount, 2) }}</td>
								<td style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">
																		{{ \Carbon\Carbon::parse($shop->updated_at)->format('Y-m-d H:i:s') }}

								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="8" style="padding: 12px; text-align: center; color: #6c757d;">No due payments found matching your criteria.</td>
						</tr>
					@endif
				</tbody>
			</table>
		</div>
	</div>
@endsection
