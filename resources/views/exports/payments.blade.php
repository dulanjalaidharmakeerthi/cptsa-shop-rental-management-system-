<table>
    <thead>
        <tr>
            <th>Shop Owner</th>
            <th>Shop Number</th>
            <th>Shop Name</th>
            <th>Bus Stand</th>
            <th>Amount</th>
            <th>Interest</th>
            <th>Payable Month</th>
            <th>Paid Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    @foreach($shops as $shop)
        @foreach($shop->payments as $payment)
            <tr>
                <td>{{ $shop->shop_owner_name }}</td>
                <td>{{ $shop->shop_number }}</td>
                <td>{{ $shop->shop_name }}</td>
                <td>{{ $shop->busStand ? $shop->busStand->name : $shop->bus_stand_id }}</td>
                <td>{{ $payment->amount }}</td>
                <td>{{ $payment->interest }}</td>
                <td>{{ $payment->payable_months_display ?? $payment->payable_months ?? '-' }}</td>
                <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '-' }}</td>
                <td>
                    @php
                        $status = 'Due';
                        if($payment->amount > 0 && $payment->paid_at){
                            $day = \Carbon\Carbon::parse($payment->paid_at)->day;
                            if($day <= 27){
                                $status = 'Paid';
                            } else {
                                $status = 'Late';
                            }
                        }
                    @endphp
                    {{ $status }}
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
