<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Bus Stand</th>
            <th>Shop Number</th>
            <th>Shop Name</th>
            <th>Owner</th>
            <th>Monthly Rent</th>
            <th>Due Amount</th>
            <th>Due Interest</th>
            <th>Last Payment</th>
            <th>Agreement End</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shops as $index => $shop)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $shop->busStand ? $shop->busStand->name : 'N/A' }}</td>
                <td>{{ $shop->shop_number }}</td>
                <td>{{ $shop->shop_name }}</td>
                <td>{{ $shop->shop_owner_name }}</td>
                <td>{{ $shop->monthly_rent ? number_format($shop->monthly_rent, 2) : '0.00' }}</td>
                <td>{{ number_format($shop->due_payment ?? 0, 2) }}</td>
                <td>{{ number_format($shop->due_interest ?? 0, 2) }}</td>
                <td>{{ $shop->last_payment_date }}</td>
                <td>{{ $shop->agreement_end ? \Carbon\Carbon::parse($shop->agreement_end)->format('Y-m-d') : 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
