@php
    $layout = 'layouts.app';
    if (auth()->check() && auth()->user()->position === 'Cashier') {
        $layout = 'layouts.chap';
    }
@endphp
@extends($layout)
@section('title', 'Shop Management')

@push('head')
<style>
  .manage-card { max-width:1100px; margin:10px auto; }
  .row-grid { display:grid; grid-template-columns: 1fr 1fr; gap:18px; }
  .full { grid-column: 1 / -1; }
  .form-label { font-weight:700; color:var(--primary-color); display:block; margin-bottom:6px }
  .form-control { padding:10px; border-radius:8px; border:1px solid #e6eef6; width:100% }
  .section-title { font-size:16px; color:var(--primary-color); margin:8px 0 12px }
  .muted { color:#6b7280 }
  .form-group { margin: 10px 0; }
  .d-flex { display: flex; }
  .gap-8 { gap: 8px; }
  .gap-10 { gap: 10px; }
  .align-items-center { align-items: center; }
  .justify-content-center { justify-content: center; }
  .flex-1 { flex: 1; }
  .spacer-18 { height: 18px; }
  .mt-12 { margin-top: 12px; }
  .mt-16 { margin-top: 16px; }
  .d-inline { display: inline; }
  .btn-primary { background:var(--primary-color); color:#fff; padding:8px 12px; border-radius:8px; }
  .btn-teal { background:#0ea5a4; color:#fff; padding:9px 6px; border-radius:8px; }
  .btn-danger { background:#ef4444; color:#fff; padding:8px 12px; border-radius:8px; }
  @media (max-width:900px){ .row-grid{grid-template-columns:1fr} }
</style>
@endpush

@section('content')
<div class="container manage-card">
  <div class="dashboard-card">
    <div class="dashboard-card-header">
      <h1>Shop Management</h1>
      <p class="muted">Search shops, view and calculate payments and dues for selected year.</p>
    </div>

    <div class="form-group">
      <label class="form-label">Search by Owner Name or NIC</label>
      <div class="d-flex gap-8">
        <input id="searchOwner" class="form-control" placeholder="Enter owner name or NIC to search...">
        <button id="btnSearch" class="btn btn-primary">Search</button>
      </div>
    </div>

    <form id="shopDetailsForm" action="{{ route('shops.manage.update') }}" method="POST">
      @csrf
      <h3 class="section-title">Shop Details</h3>
      <div class="row-grid">
        <div>
          <label class="form-label">Bus Stand</label>
          <select id="busStand" name="bus_stand_id" class="form-control">
            <option value="">-- Select Bus Stand --</option>
            @foreach($busStands ?? [] as $bs)
              <option value="{{ $bs->id }}">{{ $bs->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="form-label">Shop Number/කඩ අංකය	</label>
          <input id="shopNumber" name="shop_number" class="form-control" placeholder="e.g. A-12" required>
        </div>

        <div>
          <label class="form-label">Owner Name/කඩ කාමර හිමියාගේ නම</label>
          <input id="ownerName" name="shop_owner_name" class="form-control" placeholder="Owner full name" required>
        </div>

        <div>
          <label class="form-label">Value of Shop</label>
          <input id="shopValue" name="shop_value" type="number" step="0.01" class="form-control" value="0">
        </div>

        <div>
          <label class="form-label">Monthly Rental/මාසික කුලිය(රු) </label>
          <input id="monthlyRental" name="monthly_rent" type="number" step="0.01" class="form-control" value="0" readonly>
        </div>

        <div>
          <label class="form-label">Rate</label>
          <input id="rate" name="rate" type="number" step="0.01" class="form-control" value="0">
        </div>

        <div>
          <label class="form-label">Yearly Rental/වර්ෂයට අදාලව ගෙවිය  යුතු මුදල </label>
          <input id="yearlyRental" name="year_charge" type="number" step="0.01" class="form-control" value="0" readonly>
        </div>

        <div class="d-flex gap-10">
          <div class="flex-1">
            <label class="form-label">Agreement Start</label>
            <input id="agreementStart" name="agreement_start" type="date" class="form-control" value="">
          </div>
          <div class="flex-1">
            <label class="form-label">Agreement End</label>
            <input id="agreementEnd" name="agreement_end" type="date" class="form-control" value="">
          </div>
        </div>

        <div>
          <label class="form-label">Due Payment/හිඟ  මුදල </label>
          <input id="duePayment" name="due_payment" type="number" step="0.01" class="form-control" value="0">
        
          <label class="form-label">Due Interest/හිඟ  අධිභාර</label>
          <input id="dueInterest" name="due_interest" type="number" step="0.01" class="form-control" value="0">
        </div>

        <div class="d-flex align-items-center justify-content-center">
          <button type="button" id="btnCalculate" class="btn btn-teal">Calculate Year Totals</button>
        </div>
      </div>
      <input type="hidden" name="record_year" value="{{ now()->year }}">
    </form>

    <div class="spacer-18"></div>

     <button type="button">Full Payment Details when 12.31/ වර‍්ෂයේ 12.31 දක්වා ගෙවීම් </button>

    <br>
    <br>
    <form id="yearDetailsForm">
      <div class="row-grid">
        <div>
          <label class="form-label">Due Payment/හිඟ මුදල</label>
          <input id="yearDuePayment" name="year_due_payment" type="number" step="0.01" class="form-control" value="0">
        </div>

        <div>
          <label class="form-label">Due Interest/හිඟ අධිභාර</label>
          <input id="yearDueInterest" name="year_due_interest" type="number" step="0.01" class="form-control" value="0">
        </div>

        <div>
          <label class="form-label">Year Charge/ මුදල</label>
          <input id="yearCharge" name="year_charge" type="number" step="0.01" class="form-control" value="0">
        </div>

        <div>
          <label class="form-label">Year Interest/ අධිභාර</label>
          <input id="yearInterest" name="year_interest" type="number" step="0.01" class="form-control" value="0">
        </div>

        <div>
          <label class="form-label">Year Total / වර්ෂයේ ගෙවීම් වල එකතුව(රු)</label>
          <input id="yearTotalTaken" name="year_total_taken" type="number" step="0.01" class="form-control" value="0" readonly>
        </div>
        <div>
          <label class="form-label">diduct from saving / ඉදිරියට ගෙවීම් වලින් හිලවි කල මුදල</label>
          <input id="diductFromSaving" name="diduct_from_saving" type="number" step="0.01" class="form-control" value="0" readonly>
        </div>
      </div>
    </form>

       

    <div class="spacer-18"></div>

    <h3 class="section-title">Total for Selected Year වර්ෂයෙ අවසානයේ අයවිය යුතු ගෙවීම්</h3>
    <form id="totalYearForm">
      <div class="row-grid">
        <div>
          <label class="form-label">Total Due/හිඟ මුදල</label>
          <input id="totalDueSelected" name="total_due_selected" type="number" step="0.01" class="form-control" value="0" readonly>
        </div>

        <div>
          <label class="form-label">Total Interest/අධිභාර</label>
          <input id="totalInterestSelected" name="total_interest_selected" type="number" step="0.01" class="form-control" value="0" readonly>
        </div>
      </div>
      
        <div>
          <label class="form-label">saving for next yera /ඊලඟ වර්ශය සදහා ඉතුරුම්</label>
          <input id="savingfornextyear" name="savingfornextyear" type="number" step="0.01" class="form-control" value="0" readonly>
        </div>
    </form>

    <div class="spacer-18"></div>

    <div>
      <button id="btnToggleDeduct" class="btn btn-danger">Deduct from Security Payment</button>
    </div>

    <div id="deductSection" class="mt-12" style="display:none;">
      <h3 class="section-title">Deducted from Security Payment</h3>
      <div class="row-grid">
        <div>
          <label class="form-label">Payment</label>
          <input id="deductPayment" name="deduct_payment" type="number" step="0.01" class="form-control" value="0">
        </div>
        <div>
          <label class="form-label">Interest</label>
          <input id="deductInterest" name="deduct_interest" type="number" step="0.01" class="form-control" value="0">
        </div>
      </div>
    </div>

    <div class="mt-16">
      <form action="{{ route('shops.manage.update') }}" method="POST" class="d-inline">
        @csrf
        <input type="hidden" name="shop_number" id="submitShopNumber">
        <input type="hidden" name="shop_value" id="submitShopValue">
        <input type="hidden" name="shop_owner_name" id="submitShopOwner">
        <input type="hidden" name="bus_stand_id" id="submitBusStand">
        <input type="hidden" name="monthly_rent" id="submitMonthly">
        <input type="hidden" name="year_charge" id="submitYearCharge">
        <input type="hidden" name="due_payment" id="submitDue">
        <input type="hidden" name="due_interest" id="submitDueInterest">
        <input type="hidden" name="year_interest" id="submitYearInterest">
        <input type="hidden" name="year_total_taken" id="submitYearTotalTaken">
        <input type="hidden" name="rate" id="submitRate">
        <input type="hidden" name="deduct_payment" id="submitDeductPayment">
        <input type="hidden" name="deduct_interest" id="submitDeductInterest">
        <input type="hidden" name="agreement_start" id="submitAgreementStart">
        <input type="hidden" name="agreement_end" id="submitAgreementEnd">
        <input type="hidden" name="record_year" value="{{ now()->year }}">
        <button type="submit" id="btnUpdate" class="btn btn-primary">Update</button>
      </form>
    </div>

    <div class="mt-12">
      <button id="btnPayNow" type="button" class="btn btn-teal">Pay Now</button>
    </div>

    <div id="payNowSection" class="mt-12" style="display:none;">
      <h3 class="section-title">Record Payment</h3>
      <div class="row-grid">
        <div>
          <label class="form-label">Receipt Number</label>
          <input id="receiptNumber" class="form-control" placeholder="Enter receipt number">
        </div>
        <div>
          <label class="form-label">Amount</label>
          <input id="payAmount" type="number" step="0.01" class="form-control" value="0">
        </div>
        <div>
          <label class="form-label">Owner Name</label>
          <input id="payOwnerName" class="form-control" placeholder="Owner name">
        </div>
        <div>
          <label class="form-label">Shop Number</label>
          <input id="payShopNumber" class="form-control" placeholder="Shop number">
        </div>
        <div>
          <label class="form-label">Paid At</label>
          <input id="payPaidAt" type="date" class="form-control" value="{{ now()->toDateString() }}">
        </div>
        <div class="d-flex align-items-center justify-content-center">
          <button id="btnSavePayment" type="button" class="btn btn-primary">Save Payment</button>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const shopValueInput = document.getElementById('shopValue');
  const btnCalculate = document.getElementById('btnCalculate');
  const monthly = document.getElementById('monthlyRental');
  const yearly = document.getElementById('yearlyRental');
  const due = document.getElementById('duePayment');
  const dueInterest = document.getElementById('dueInterest');

  const yearDuePayment = document.getElementById('yearDuePayment');
  const yearDueInterest = document.getElementById('yearDueInterest');
  const yearCharge = document.getElementById('yearCharge');
  const yearInterest = document.getElementById('yearInterest');
  const yearTotalTaken = document.getElementById('yearTotalTaken');
  const rate = document.getElementById('rate');

  const totalDueSelected = document.getElementById('totalDueSelected');
  const totalInterestSelected = document.getElementById('totalInterestSelected');

  // Default Agreement Dates: Start = Today, End = Today + 1 Year
  const agreementStart = document.getElementById('agreementStart');
  const agreementEnd = document.getElementById('agreementEnd');
  
  const formatDate = (d) => {
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  if (agreementStart && !agreementStart.value) {
    const today = new Date();
    agreementStart.value = formatDate(today);
    if (agreementEnd) {
      const nextYear = new Date(today);
      nextYear.setFullYear(today.getFullYear() + 1);
      agreementEnd.value = formatDate(nextYear);
    }
  }

  if (agreementStart && agreementEnd) {
    agreementStart.addEventListener('change', function() {
      if (this.value) {
        const parts = this.value.split('-');
        const d = new Date(parts[0], parts[1] - 1, parts[2]);
        d.setFullYear(d.getFullYear() + 1);
        agreementEnd.value = formatDate(d);
      }
    });
  }

  function calculateRentalsAndTotals() {
    const shopValue = parseFloat(shopValueInput.value) || 0;
    const rateValue = parseFloat(rate.value) || 0;

    // Monthly Rental = (Value of Shop * Rate / 100) + Value of Shop
    const calculatedMonthly = shopValue * (1 + (rateValue / 100));
    monthly.value = calculatedMonthly.toFixed(2);

    // Yearly Rental = Monthly Rental * 12
    const calculatedYearly = calculatedMonthly * 12;
    yearly.value = calculatedYearly.toFixed(2);

    // Basic calculations
    const d = parseFloat(due.value) || 0;
    const di = parseFloat(dueInterest.value) || 0;

    let y = parseFloat(yearly.value); // This is now the calculated yearly value.
    const yi = y * 0.05; // example 5% yearly interest

    yearCharge.value = y.toFixed(2);
    yearInterest.value = yi.toFixed(2);
    yearDuePayment.value = d.toFixed(2);
    yearDueInterest.value = di.toFixed(2);

    // Year total taken = sum of due payment + due interest + year charge + year interest
    const taken = d + di + y + yi;
    yearTotalTaken.value = taken.toFixed(2);

    // Totals for selected year
    totalDueSelected.value = (d + y).toFixed(2);
    totalInterestSelected.value = (di + yi).toFixed(2);
  }

  // Calculate on button click, or when value/rate changes
  btnCalculate.addEventListener('click', calculateRentalsAndTotals);
  shopValueInput.addEventListener('input', calculateRentalsAndTotals);
  rate.addEventListener('input', calculateRentalsAndTotals);

  // Toggle deduct section
  const btnToggle = document.getElementById('btnToggleDeduct');
  const deductSection = document.getElementById('deductSection');
  btnToggle.addEventListener('click', function(){
    deductSection.style.display = (deductSection.style.display === 'none' || deductSection.style.display === '') ? 'block' : 'none';
  });

  // Simple search button (no backend) — just fills owner name into the form for demo
  const btnSearch = document.getElementById('btnSearch');
  const searchOwner = document.getElementById('searchOwner');
  // Search by owner name — call backend route and populate form fields with DB data
  const searchUrl = "{{ route('shops.search') }}";
    btnSearch.addEventListener('click', async function(e){
    e.preventDefault();
    const q = searchOwner.value.trim();
      if (!q) { alert('Please enter owner name or NIC to search'); return; }
    try {
      const res = await fetch(searchUrl + '?owner=' + encodeURIComponent(q), {
        headers: { 'Accept': 'application/json' }
      });
      if (res.status === 404) { alert('No shop found for that owner or NIC'); return; }
      if (!res.ok) { const body = await res.json().catch(()=>({})); alert(body.message || 'Search failed'); return; }
      const s = await res.json();

      // populate shop details
      document.getElementById('shopValue').value = (s.shop_value !== undefined) ? parseFloat(s.shop_value).toFixed(2) : 0;
      document.getElementById('ownerName').value = s.shop_owner_name || '';
      document.getElementById('shopNumber').value = s.shop_number || '';
      document.getElementById('monthlyRental').value = (s.monthly_rent !== undefined) ? parseFloat(s.monthly_rent).toFixed(2) : 0;
      document.getElementById('yearlyRental').value = (s.year_charge !== undefined) ? parseFloat(s.year_charge).toFixed(2) : 0;
      document.getElementById('duePayment').value = (s.due_payment !== undefined) ? parseFloat(s.due_payment).toFixed(2) : 0;
      document.getElementById('dueInterest').value = (s.due_interest !== undefined) ? parseFloat(s.due_interest).toFixed(2) : 0;
      document.getElementById('rate').value = (s.rate !== undefined) ? parseFloat(s.rate).toFixed(2) : 0;
      document.getElementById('busStand').value = s.bus_stand_id || '';

      // agreement dates (ensure ISO date YYYY-MM-DD for date inputs)
      if (document.getElementById('agreementStart')) {
        const as = s.agreement_start ? String(s.agreement_start).split(/T| /)[0] : (s.agreement_start || '');
        document.getElementById('agreementStart').value = as;
      }
      if (document.getElementById('agreementEnd')) {
        const ae = s.agreement_end ? String(s.agreement_end).split(/T| /)[0] : (s.agreement_end || '');
        document.getElementById('agreementEnd').value = ae;
      }

      // populate year details and totals
      document.getElementById('yearCharge').value = (s.year_charge !== undefined) ? parseFloat(s.year_charge).toFixed(2) : 0;
      document.getElementById('yearInterest').value = (s.year_interest !== undefined) ? parseFloat(s.year_interest).toFixed(2) : 0;
      document.getElementById('yearTotalTaken').value = (s.year_total_taken !== undefined) ? parseFloat(s.year_total_taken).toFixed(2) : 0;
      document.getElementById('yearDuePayment').value = (s.due_payment !== undefined) ? parseFloat(s.due_payment).toFixed(2) : 0;
      document.getElementById('yearDueInterest').value = (s.due_interest !== undefined) ? parseFloat(s.due_interest).toFixed(2) : 0;

      totalDueSelected.value = ((parseFloat(s.due_payment)||0) + (parseFloat(s.year_charge)||0)).toFixed(2);
      totalInterestSelected.value = ((parseFloat(s.due_interest)||0) + (parseFloat(s.year_interest)||0)).toFixed(2);
    } catch (err) {
      console.error(err);
      alert('Search failed');
    }
  });

  // Populate hidden fields before submitting the Update form
  const btnUpdate = document.getElementById('btnUpdate');
  if (btnUpdate) {
    btnUpdate.addEventListener('click', function(e){
      // set hidden inputs
      document.getElementById('submitShopNumber').value = document.getElementById('shopNumber').value || '';
      document.getElementById('submitShopValue').value = document.getElementById('shopValue').value || 0;
      document.getElementById('submitShopOwner').value = document.getElementById('ownerName').value || '';
      document.getElementById('submitBusStand').value = document.getElementById('busStand').value || '';
  document.getElementById('submitMonthly').value = document.getElementById('monthlyRental').value || 0;
  // Prefer explicit Year charge field (calculated), but fall back to the Yearly Rental input
  document.getElementById('submitYearCharge').value = document.getElementById('yearCharge').value || document.getElementById('yearlyRental').value || 0;
      document.getElementById('submitDue').value = document.getElementById('duePayment').value || 0;
      document.getElementById('submitDueInterest').value = document.getElementById('dueInterest').value || 0;
      document.getElementById('submitYearInterest').value = document.getElementById('yearInterest').value || 0;
      document.getElementById('submitYearTotalTaken').value = document.getElementById('yearTotalTaken').value || 0;
      document.getElementById('submitRate').value = document.getElementById('rate').value || 0;
      document.getElementById('submitDeductPayment').value = document.getElementById('deductPayment').value || 0;
      document.getElementById('submitDeductInterest').value = document.getElementById('deductInterest').value || 0;
      // agreement dates hidden values
      if (document.getElementById('submitAgreementStart')) document.getElementById('submitAgreementStart').value = document.getElementById('agreementStart').value || '';
      if (document.getElementById('submitAgreementEnd')) document.getElementById('submitAgreementEnd').value = document.getElementById('agreementEnd').value || '';
      // allow the form to submit normally
    });
  }
});

  // Pay Now UI and submit (ensure DOM loaded)
  document.addEventListener('DOMContentLoaded', function(){
    const btnPayNow = document.getElementById('btnPayNow');
    const payNowSection = document.getElementById('payNowSection');
    const btnSavePayment = document.getElementById('btnSavePayment');
    const receiptNumberInput = document.getElementById('receiptNumber');
    const payAmountInput = document.getElementById('payAmount');
    const payOwnerName = document.getElementById('payOwnerName');
    const payShopNumber = document.getElementById('payShopNumber');
    const payPaidAt = document.getElementById('payPaidAt');

    if (!btnPayNow) return;

    btnPayNow.addEventListener('click', function(){
      payNowSection.style.display = (payNowSection.style.display === 'none' || payNowSection.style.display === '') ? 'block' : 'none';
      // prefill some fields from form
      payOwnerName.value = document.getElementById('ownerName').value || '';
      payShopNumber.value = document.getElementById('shopNumber').value || '';
      payAmountInput.value = document.getElementById('yearTotalTaken').value || document.getElementById('monthlyRental').value || 0;
    });

    btnSavePayment.addEventListener('click', async function(){
      const receipt = receiptNumberInput.value.trim();
      if (!receipt) { alert('Receipt number is required'); return; }
      const payload = {
        shop_id: document.getElementById('busStand').value || null,
        shop_number: payShopNumber.value || '',
        owner_name: payOwnerName.value || '',
        amount: parseFloat(payAmountInput.value) || 0,
        interest: parseFloat(document.getElementById('yearInterest').value) || 0,
        receipt_number: receipt,
        paid_at: payPaidAt.value || null,
        _token: '{{ csrf_token() }}'
      };

      try {
        const res = await fetch('{{ route('payment_details.store') }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify(payload)
        });
        if (!res.ok) {
          const body = await res.json().catch(()=>({}));
          alert(body.message || 'Save failed');
          return;
        }
        const body = await res.json();
        alert('Payment saved (receipt: ' + (body.data.receipt_number||receipt) + ')');
        // hide section and reset receipt
        payNowSection.style.display = 'none';
        receiptNumberInput.value = '';
      } catch (err) {
        console.error(err);
        alert('Save failed');
      }
    });
  });
</script>
@endpush
