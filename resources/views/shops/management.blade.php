@php
    // Always render the advanced layout for everyone except cashiers.
    // This ensures the sidebar remains consistent across pages.
    $isAdminSession = session()->has('admin_id') && !auth()->check();
    $layout = $isAdminSession ? 'layouts.admin' : 'layouts.app';
    $updateRoute = $isAdminSession ? 'admin.shop_management.update' : 'shops.manage.update';
    $searchRoute = $isAdminSession ? 'admin.shop_management.search' : 'shops.search';
    if (!$isAdminSession && auth()->check() && auth()->user()->position === 'Cashier') {
        $layout = 'layouts.chap';
    }
@endphp
@extends($layout)
@section('title', 'Shop Management')

@push('head')
<style>
  .manage-card { max-width:1100px; margin:16px auto; }
  .dashboard-card { background: linear-gradient(160deg, #ffffff, #f7fbff); border-radius:16px; border:1px solid #e5effc; box-shadow:0 8px 25px rgba(15,23,42,0.1); }
  .dashboard-card-header { padding:22px 24px 10px; }
  .row-grid { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:18px; }
  .row-grid > div { min-width: 0; }
  .full { grid-column: 1 / -1; }
  .form-label { font-weight:700; color:#1d4ed8; display:block; margin-bottom:8px; font-size:13px; }
  .form-control { padding:10px 12px; border-radius:10px; border:1px solid #cbd5e1; width:100%; box-sizing:border-box; background:#ffffff; font-size:14px; }
  .form-control:hover { border-color:#93c5fd; }
  .form-control:focus { outline:none; border-color:var(--primary-color); box-shadow:0 0 0 3px rgba(59,130,246,0.14); }
  .form-control[readonly], .form-control:disabled { background:#f1f5f9; color:#334155; cursor:not-allowed; }
  .section-title { font-size:1.05rem; color:#0f4a8f; margin:12px 0 14px; font-weight:700; }
  .muted { color:#607089; }
  .form-group { margin: 18px 0; }
  .d-flex { display:flex; }
  .gap-8 { gap:8px; }
  .gap-10 { gap:10px; }
  .align-items-center { align-items:center; }
  .justify-content-center { justify-content:center; }
  .flex-1 { flex:1; }
  .spacer-18 { height:18px; }
  .mt-12 { margin-top:12px; }
  .mt-16 { margin-top:16px; }
  .d-inline { display:inline; }
  .btn-primary { background:var(--primary-color); color:#fff; padding:10px 18px; border-radius:10px; border:none; font-weight:700; }
  .btn-primary:hover { background: #0a4a98; }
  .btn-teal { background:#0ea5a4; color:#fff; padding:10px 18px; border-radius:10px; border:none; font-weight:700; }
  .btn-danger { background:#ef4444; color:#fff; padding:10px 18px; border-radius:10px; border:none; font-weight:700; }
  .charge-history { margin:24px 0; padding:18px; border:1px solid #dbeafe; border-radius:12px; background:#f8fbff; }
  .charge-history table { width:100%; border-collapse:collapse; background:#fff; }
  .charge-history th, .charge-history td { padding:10px; border:1px solid #e2e8f0; text-align:left; vertical-align:top; }
  .charge-history th { color:#0f4a8f; background:#eff6ff; }
  .charge-history-details { white-space:pre-line; min-width:180px; }
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
      <label class="form-label">Search by Owner Name, Shop Number or NIC</label>
      <div class="d-flex gap-8">
        <input id="searchOwner" class="form-control" placeholder="Enter owner name, shop number or NIC to search...">
        <button type="button" id="btnSearch" class="btn btn-primary">Search</button>
      </div>
    </div>

    <form id="shopDetailsForm" action="{{ route($updateRoute) }}" method="POST">
      @csrf
      <input type="hidden" id="shopId" name="shop_id" value="">
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
          <label class="form-label">Monthly Rental/මාසික කුලිය(රු) </label>
          <input id="monthlyRental" name="monthly_rent" type="number" step="0.01" class="form-control" value="0">
        </div>

        <div>
          <label class="form-label">Rate</label>
          <input id="rate" name="rate" type="number" step="0.01" class="form-control" value="0">
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
          <label class="form-label">Due Payment/හිඟ  මුදල</label>
          <input id="duePayment" name="due_payment" type="number" step="0.01" class="form-control" value="0">
        </div>

        <div>
          <label class="form-label">Due Interest/හිඟ  අධිභාර</label>
          <input id="dueInterest" name="due_interest" type="number" step="0.01" class="form-control" value="0">
        </div>

      </div>
      <input type="hidden" name="record_year" value="{{ now()->year }}">
      <input type="hidden" name="year_interest" id="yearInterestHidden" value="0">
      <input type="hidden" name="year_total_taken" id="yearTotalTakenHidden" value="0">
      <input type="hidden" name="deduct_payment" id="deductPaymentHidden" value="0">
      <input type="hidden" name="deduct_interest" id="deductInterestHidden" value="0">
      <div class="spacer-18"></div>
      <div class="full d-flex justify-content-center">
        <button type="submit" id="btnUpdate" class="btn btn-primary">Update</button>
      </div>
    </form>

    <section id="chargeHistorySection" class="charge-history" hidden>
      <h3 class="section-title">Rate and Monthly Interest History</h3>
      <p id="currentChargeSummary" class="muted"></p>
      <p id="chargeHistoryEmpty" class="muted">No rate or charge changes have been recorded for this shop.</p>
      <div style="overflow-x:auto;">
        <table>
          <thead>
            <tr>
              <th>Changed At</th>
              <th>Changed By</th>
              <th>Previous Details</th>
              <th>Updated Details</th>
            </tr>
          </thead>
          <tbody id="chargeHistoryRows"></tbody>
        </table>
      </div>
    </section>

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
  // Search inputs
  const searchOwner = document.getElementById('searchOwner');
  const btnSearch = document.getElementById('btnSearch');
  const searchUrl = "{{ route($searchRoute) }}";

  // Agreement date handling
  const agreementStart = document.getElementById('agreementStart');
  const agreementEnd = document.getElementById('agreementEnd');

  
  const formatDate = (d) => {
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  const parseNumber = (value) => {
    const n = parseFloat(String(value).replace(/,/g, ''));
    return Number.isFinite(n) ? n : 0;
  };

  const money = (value) => 'Rs. ' + parseNumber(value).toFixed(2);

  const chargeDetailsText = (details) => [
    'Rate: ' + parseNumber(details.rate).toFixed(2) + '%',
    'Monthly interest: ' + money(details.monthly_interest),
    'Monthly rent: ' + money(details.monthly_rent)
  ].join('\n');

  const renderChargeHistory = (shop) => {
    const section = document.getElementById('chargeHistorySection');
    const summary = document.getElementById('currentChargeSummary');
    const emptyMessage = document.getElementById('chargeHistoryEmpty');
    const tableBody = document.getElementById('chargeHistoryRows');
    const histories = Array.isArray(shop.charge_histories) ? shop.charge_histories : [];
    const currentDetails = {
      rate: shop.rate,
      monthly_interest: parseNumber(shop.monthly_rent) * parseNumber(shop.rate) / 100,
      monthly_rent: shop.monthly_rent
    };

    section.hidden = false;
    summary.textContent = 'Current details — ' + chargeDetailsText(currentDetails).replace(/\n/g, ' | ');
    tableBody.replaceChildren();
    emptyMessage.hidden = histories.length > 0;

    histories.forEach((history) => {
      const row = document.createElement('tr');
      const changedAt = history.created_at ? new Date(history.created_at).toLocaleString() : '—';
      [
        changedAt,
        history.changed_by || '—',
        chargeDetailsText(history.old_values || {}),
        chargeDetailsText(history.new_values || {})
      ].forEach((value, index) => {
        const cell = document.createElement('td');
        cell.textContent = value;
        if (index > 1) cell.className = 'charge-history-details';
        row.appendChild(cell);
      });
      tableBody.appendChild(row);
    });
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

  // Search button logic
  btnSearch.addEventListener('click', async function(e){
    e.preventDefault();
    const q = searchOwner.value.trim();
    console.log('[ShopSearch] q=', q);
    if (!q) { alert('Please enter owner name, shop number or NIC to search'); return; }
    try {
      const res = await fetch(searchUrl + '?owner=' + encodeURIComponent(q), {
        headers: { 'Accept': 'application/json' }
      });
      console.log('[ShopSearch] response', res.status);
      if (res.status === 404) { alert('No shop found for that owner or NIC'); return; }
      if (!res.ok) {
        const body = await res.json().catch(()=>({}));
        alert(body.message || 'Search failed (server error)');
        return;
      }
      if (res.status === 404) { alert('No shop found for that owner or NIC'); return; }
      if (!res.ok) { return; }
      const s = await res.json();

      const setInput = (id, value) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.value = value;
      };

      // populate shop details
      setInput('ownerName', s.shop_owner_name || '');
      setInput('shopNumber', s.shop_number || '');
      setInput('monthlyRental', (s.monthly_rent !== undefined) ? parseFloat(s.monthly_rent).toFixed(2) : 0);
      setInput('duePayment', (s.due_payment !== undefined) ? parseFloat(s.due_payment).toFixed(2) : 0);
      setInput('dueInterest', (s.due_interest !== undefined) ? parseFloat(s.due_interest).toFixed(2) : 0);
      setInput('rate', (s.rate !== undefined) ? parseFloat(s.rate).toFixed(2) : 0);
      setInput('busStand', s.bus_stand_id || '');
      setInput('shopId', s.id || '');
      renderChargeHistory(s);

      // agreement dates (ensure ISO date YYYY-MM-DD for date inputs)
      if (document.getElementById('agreementStart')) {
        const as = s.agreement_start ? String(s.agreement_start).split(/T| /)[0] : (s.agreement_start || '');
        document.getElementById('agreementStart').value = as;
      }
      if (document.getElementById('agreementEnd')) {
        const ae = s.agreement_end ? String(s.agreement_end).split(/T| /)[0] : (s.agreement_end || '');
        document.getElementById('agreementEnd').value = ae;
      }
    } catch (err) {
      console.error(err);
    }
  });

  // Populate hidden fields before submitting the Update form
  const btnUpdate = document.getElementById('btnUpdate');
  if (btnUpdate) {
    btnUpdate.addEventListener('click', function(e){
      // not required for named inputs, but preserve supplementary hidden values
      const shopIdEl = document.getElementById('shopId');
      if (shopIdEl) {
        shopIdEl.value = shopIdEl.value || '';
      }
      const yearInterestEl = document.getElementById('yearInterestHidden');
      const yearInterestSource = document.getElementById('yearInterest');
      if (yearInterestEl && yearInterestSource) {
        yearInterestEl.value = yearInterestSource.value || 0;
      }
      const yearTotalTakenEl = document.getElementById('yearTotalTakenHidden');
      const yearTotalTakenSource = document.getElementById('yearTotalTaken');
      if (yearTotalTakenEl && yearTotalTakenSource) {
        yearTotalTakenEl.value = yearTotalTakenSource.value || 0;
      }
      const deductPaymentEl = document.getElementById('deductPaymentHidden');
      const deductPaymentSource = document.getElementById('deductPayment');
      if (deductPaymentEl && deductPaymentSource) {
        deductPaymentEl.value = deductPaymentSource.value || 0;
      }
      const deductInterestEl = document.getElementById('deductInterestHidden');
      const deductInterestSource = document.getElementById('deductInterest');
      if (deductInterestEl && deductInterestSource) {
        deductInterestEl.value = deductInterestSource.value || 0;
      }
      // direct fields in form are posted directly
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
