@php
    // Use the full manager layout by default; staff and supervisors
    // should see the same sidebar and header as managers. Only cashiers
    // use the compact cashier layout (`layouts.chap`).
    $layout = 'layouts.app';

    if (auth()->check()) {
        $userPosition = auth()->user()->position ?? '';
        if ($userPosition === 'Cashier') {
            $layout = 'layouts.chap';
        }
    }
@endphp

@extends($layout)
@section('title', 'Payments')

@push('head')
<style>
    :root {
        --primary-color: #0f5bb9;
        --primary-dark: #0a4a98;
        --success-color: #16a34a;
        --danger-color: #dc2626;
        --warning-color: #d97706;
        --info-color: #0ea5e9;
        --card-bg: #ffffff;
        --card-border: #dbeafe;
        --shadow: 0 8px 25px rgba(15, 23, 42, 0.1);
    }

    .manage-card { max-width: 1100px; margin: 16px auto; }
    .dashboard-card { background: linear-gradient(160deg, #ffffff, #f7fbff); border-radius: 16px; border: 1px solid #e5effc; box-shadow: var(--shadow); }
    .dashboard-card-header { padding: 22px 24px 10px; }

    h1 { margin: 0; font-size: 1.55rem; color: #0f3f7a; }
    .section-title { font-size: 1.08rem; margin-bottom: 12px; color: #0f4a8f; font-weight: 700; }

    .muted { color: #607089; }
    .d-flex { display: flex; }
    .gap-8 { gap: 8px; }
    .gap-10 { gap: 10px; }
    .justify-center { justify-content: center; }
    .flex-1 { flex: 1; }
    .spacer-18 { height: 18px; }
    .w-full { width: 100%; }

    .btn { cursor: pointer; border-radius: 10px; border: none; font-weight: 700; transition: all 0.2s ease-in-out; }
    .btn-primary { background: var(--primary-color); color: #fff; padding: 10px 18px; box-shadow: 0 5px 14px rgba(15, 91, 185, 0.25); }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-success { background: var(--success-color); color: #fff; padding: 11px 20px; }
    .btn-success:hover { background: #138442; }
    .btn-secondary { background: #64748b; color: #fff; padding: 11px 20px; }
    .btn-light { background: #f8fafc; border: 1px solid #cbd5e1; padding: 9px 13px; color: #334155; }
    .btn-light:hover { background: #eef2ff; }

    .form-label { font-weight: 700; color: #1d4ed8; display: block; margin-bottom: 5px; font-size: 13px; }
    .form-control { padding: 10px 12px; border-radius: 10px; border: 1px solid #cbd5e1; width: 100%; box-sizing: border-box; background: #ffffff; font-size: 14px; }
    .form-control:hover { border-color: #93c5fd; }
    .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.14); }
    .form-control[readonly], .form-control:disabled { background-color: #f1f5f9; color: #334155; cursor: not-allowed; }

    .input-with-icon { position: relative; }
    .input-with-icon .hint-icon { position: absolute; right: 11px; top: 50%; transform: translateY(-50%); color: #475569; }

    .shop-info-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 22px;
        border: 1px solid var(--card-border);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.07);
        margin-bottom: 24px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .info-item { display: flex; flex-direction: column; }
    .info-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        font-weight: 700;
    }
    .info-value { font-size: 17px; color: #0f172a; font-weight: 800; }
    .info-value.highlight { color: var(--primary-color); font-size: 20px; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        background: #fff;
        padding: 18px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
    }

    .stat-card {
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 12px;
    }

    .modal-backdrop { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 1000; }
    .modal { position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 1001; background: #ffffff; border-radius: 14px; box-shadow: 0 16px 35px rgba(15, 23, 42, 0.35); }
    #paymentForm.modal { width: 620px; max-width: 95vw; padding: 18px 22px; max-height: 88vh; overflow-y: auto; }
    #paymentHistoryModal.modal { width: 620px; max-width: 95vw; padding: 16px 20px; max-height: 76vh; overflow-y: auto; }

    #paymentForm .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    #paymentForm .form-control { padding: 10px; font-size: 14px; }
    #paymentForm .btn { padding: 9px 13px; font-size: 13px; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .full { grid-column: 1 / -1; }

           
    .month-grid { display: grid; grid-template-columns: repeat(4, minmax(110px, 1fr)); gap: 10px; margin-top: 4px; }
    .month-item {
        border: 1px solid #cbd5e1;
        padding: 8px;
        border-radius: 8px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fbfcfe;
        justify-content: space-between;
    }
    .month-item.due { border-color: #fbbf24; background: #fffbeb; }
    .month-item.paid { border-color: #22c55e; background: #ecfdf5; }
    .month-item.checked { border-color: #0284c7; background: #e0f2fe; }

    .month-item .month-name { font-weight: 700; color: #0f172a; }
    .month-item .month-status {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #334155;
    }

    .month-selector-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .btn-check-all { background: var(--info-color); color: #fff; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-size: 13px; font-weight: 700; transition: 0.2s; }
    .btn-check-all:hover { background: #0284c7; }
    .btn-check-all:disabled { background: #94a3b8; cursor: not-allowed; opacity: 0.7; }

    .month-item:has(input:disabled) { opacity: 0.75; pointer-events: none; }

    .paid-checkbox, .paid-due-checkbox { cursor: not-allowed !important; }

    .balance-summary { background: #f8fafc; border: 1px solid #dbeafe; border-radius: 10px; padding: 10px; margin-top: 10px; }
    .balance-summary h5 { margin: 0 0 6px; color: #1d4ed8; font-weight: 700; font-size: 0.92rem; }
    .balance-summary p { margin: 4px 0; font-size: 0.88rem; color: #334155; }

    @media (max-width: 900px) { .shop-info-card { grid-template-columns: 1fr 1fr; } .month-grid { grid-template-columns: repeat(3, minmax(100px, 1fr)); } }
    @media (max-width: 620px) { .shop-info-card { grid-template-columns: 1fr; } .month-grid { grid-template-columns: repeat(2, minmax(90px, 1fr)); } }
</style>
@endpush

@section('content')
<div class="container manage-card">
    <div class="dashboard-card">
        <div class="dashboard-card-header" style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
            <div>
                <h1>Payments</h1>
                <p class="muted">Search shops, view details and record payments.</p>
            </div>
            
        </div>

        <div class="form-group" style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
            <label class="form-label">Search by Owner Name, Shop Number or NIC</label>
            <div class="d-flex gap-8">
                <input id="searchOwner" class="form-control" placeholder="Enter owner name, shop number, or NIC..." style="background: white; height: 45px;">
                <button id="btnSearch" class="btn btn-primary" style="min-width: 100px;">Search</button>
            </div>
        </div>

        <div id="shopDetailsSection" style="display:none; margin-top: 25px;">
            
            <h3 class="section-title" style="margin-bottom: 10px;">Shop Identity Overview</h3>
            <div class="shop-info-card">
                <div class="info-item">
                    <span class="info-label">Bus Stand</span>
                    <span id="displayBusStand" class="info-value">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Shop Number / කඩ අංකය</span>
                    <span id="displayShopNumber" class="info-value highlight">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Owner Name / හිමිකරුගේ නම</span>
                    <span id="displayOwnerName" class="info-value">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Monthly Rental / මාසික කුලිය</span>
                    <span id="displayMonthlyRental" class="info-value">Rs. 0.00</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Rate</span>
                    <span id="displayRate" class="info-value">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Yearly Rental / වර්ෂයට අදාල මුදල</span>
                    <span id="displayYearlyRental" class="info-value">Rs. 0.00</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Agreement Start / ගිවිසුම ආරම්භක දිනය</span>
                    <span id="displayAgreementStart" class="info-value">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Agreement End / ගිවිසුම අවසන් දිනය</span>
                    <span id="displayAgreementEnd" class="info-value">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Remaining Days / ඉතිරි දින</span>
                    <span id="displayAgreementRemaining" class="info-value">-</span>
                </div>
            </div>

            <h3 class="section-title" style="margin-bottom: 10px;">Financial & Agreement Status</h3>
            <div class="stats-grid">
                <div>
                    <label class="form-label">Advance Payment</label>
                    <input id="advancePayment" class="form-control" readonly style="color: var(--primary-color); font-weight: bold;">
                </div>
                <div>
                    <label class="form-label">Due Payment / හිඟ මුදල</label>
                    <input id="duePayment" class="form-control" readonly style="color: var(--danger-color); font-weight: bold;">
                </div>
                <div>
                    <label class="form-label">Due Interest / හිඟ අධිභාර</label>
                    <input id="dueInterest" class="form-control" readonly style="color: var(--warning-color); font-weight: bold;">
                </div>
               
                <div style="grid-column: span 1;">
                    <label class="form-label">Total Due Balance</label>
                    <input id="totalNeedToPay" class="form-control" readonly style="background: #fff1f2; color: #be123c; font-weight: 800; border: 2px solid #fda4af;">
                </div>
            </div>

            <div class="spacer-18"></div>
            <h3 class="section-title" style="margin-bottom: 10px;">Month-wise Due Breakdown</h3>
            <div id="monthWiseDues" class="balance-summary" style="max-height: 300px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: left;">Month</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">Due Rent</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">Due Interest</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">Total Due</th>
                        </tr>
                    </thead>
                    <tbody id="unpaidMonthsTableBody">
                        <!-- Unpaid months will be populated here -->
                    </tbody>
                </table>
            </div>
                <button type="button" id="btnPay" class="btn btn-success">Record Payment</button>
                <button type="button" id="btnHistory" class="btn btn-secondary">View History</button>
            </div>
        </div>

        <div id="noResultsMsg" class="no-results" style="display:none; text-align: center; padding: 40px;">
            <p style="color: #94a3b8;">No shop found matches your search.</p>
        </div>
    </div>
</div>

<div id="paymentBackdrop" class="modal-backdrop" style="display:none;"></div>
<div id="paymentForm" class="modal" style="display:none;">
    <h4 class="modal-title" style="color:var(--primary-color); margin-bottom:15px;">New Payment Transaction</h4>
    <div class="form-grid-2">
        <div>
            <label class="form-label">Shop Number</label>
            <div id="pfShop" style="font-weight: bold; color: #1e293b;"></div>
        </div>
        <div>
            <label class="form-label">Owner Name</label>
            <div id="pfOwner" style="font-weight: bold; color: #1e293b;"></div>
        </div>
        <input type="hidden" id="pfShopId">

        <div class="full">
            <label class="form-label">Payment Year</label>
            <select id="pfYear" class="form-control"></select>
            <div style="margin-top:6px; font-size:13px; color:#64748b;">Year choices are limited to the agreement period and the system's due history from 2023 onward.</div>
        </div>

        <div class="full">
            <label class="form-label">Select Month(s) to Pay</label>
            <div class="month-selector-header">
                <span></span>
                <button type="button" id="btnCheckAll" class="btn-check-all">Check Selected Months</button>
            </div>
            <div id="monthCheckboxContainer" class="month-grid"></div>
        </div>

        <div class="full" style="display:flex; align-items:center; gap:10px;">
            <!-- Include due checkbox removed as due is now view-only and reduced by selected months -->
        </div>

        <div class="form-grid-2">
            <div>
                <label class="form-label">Selected Due Months</label>
                <input id="pfSelectedDueMonthsCount" class="form-control" readonly value="0">
            </div>
            <div>
                <label class="form-label">Selected Total Months</label>
                <input id="pfSelectedMonthsCount" class="form-control" readonly value="0">
            </div>
        </div>
        <input id="pfSelectedMonthValues" name="selected_months" type="hidden" value="[]">
        <input id="pfSelectedDueMonths" name="selected_dues" type="hidden" value="[]">
        <input id="pfYearInput" name="year" type="hidden" value="">
        <input id="pfRentTotal" type="hidden" value="0.00">

        <div class="form-grid-2">
            <div>
                <label class="form-label">Due Payment</label>
                <input id="pfDuePaymentView" class="form-control" readonly value="0.00">
            </div>
            <div>
                <label class="form-label">Due Interest (shop)</label>
                <input id="pfDueInterestView" class="form-control" readonly value="0.00">
            </div>
        </div>

        <div class="form-grid-2">
            <div>
                <label class="form-label">Total Due Balance (shop)</label>
                <input id="pfTotalDueBalance" class="form-control" readonly value="0.00">
            </div>
            <div>
                <label class="form-label"></label>
                <input class="form-control" readonly value="" style="color:var(--primary-color); font-weight:700; border:none; background:transparent;">
            </div>
        </div>
        <div class="full">
            <label class="form-label">Payment Amount (Selected Months)</label>
            <input id="pfCalculatedAmount" class="form-control" readonly value="0.00">
        </div>

        <div class="full">
            <label class="form-label">Selected Due Interest</label>
            <input id="pfSelectedDueInterest" class="form-control" readonly value="0.00">
        </div>

        <div class="full">
            <label class="form-label">Paid Balance</label>
            <input id="pfSelectedMonthBalance" class="form-control" readonly value="0.00">
        </div>

        <div class="full">
            <label class="form-label">Receipt Number / රිසිට්පත් අංකය</label>
            <input id="pfReceiptNo" class="form-control" placeholder="Enter receipt number">
        </div>
        <!-- Amount and Interest are computed by the modal logic from selected months + due values -->
        <input id="pfAmount" name="amount" type="hidden" value="0.00">
        <input id="pfInterest" name="interest" type="hidden" value="0.00">

        @if($paymentEditModeEnabled)
        <div>
            <label class="form-label">
                <input type="checkbox" id="pfEditMode"> Enable Edit Mode (for historical data entry)
            </label>
        </div>
        @endif
        <div>
            <label class="form-label">Payment Date</label>
            <input id="pfDateDisplay" type="text" class="form-control" readonly disabled>
            <input id="pfDate" type="hidden">
        </div>
        <div>
            <label class="form-label">Payment Slip</label>
            <input id="pfSlip" type="file" accept="image/*,.pdf" class="form-control">
        </div>
        <div class="d-flex gap-8 full" style="margin-top: 10px;">
            <button id="pfSubmit" class="btn btn-primary flex-1">Submit Payment</button>
            <button id="pfCancel" class="btn btn-light flex-1">Cancel</button>
        </div>
    </div>
</div>

<div id="paymentHistoryBackdrop" class="modal-backdrop" style="display:none;"></div>
<div id="paymentHistoryModal" class="modal" style="display:none;">
    <div class="modal-header d-flex" style="justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
        <h4 style="margin:0; color:var(--primary-color);">Payment History</h4>
        <button id="historyClose" class="btn btn-light" aria-label="Close payment history" style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 14px; flex: 0 0 28px;">✕</button>
    </div>
    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
        <table class="w-full" style="border-collapse: collapse; font-size: 14px;">
            <thead style="background: #f8fafc; text-align: left;">
                <tr>
                    <th style="padding: 10px; border-bottom: 2px solid #eef2f6;">Amount</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eef2f6;">Interest</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eef2f6;">Payable Month</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eef2f6;">Date</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eef2f6;">Receipt Number</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eef2f6;">Payment Receipt</th>
                </tr>
            </thead>
            <tbody id="historyTableBody"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const btnSearch = document.getElementById('btnSearch');
    const searchOwner = document.getElementById('searchOwner');
    const shopDetailsSection = document.getElementById('shopDetailsSection');
    const noResultsMsg = document.getElementById('noResultsMsg');

    const searchUrl = "{{ route('payments.search') }}";
    const recordUrl = "{{ route('payments.record') }}";
    const historyUrl = "{{ route('payments.history') }}";
    const csrfToken = "{{ csrf_token() }}";

    let currentShop = null;
    let paymentModalShop = null;
    let paymentModalHistory = [];
    let isCheckSelectedClicked = false; // Flag to track if Check Selected Months was clicked
    const shopByIdUrl = "{{ url('/payments/shop') }}";

    function updateAgreementDetails(shop) {
        const formatDate = (value) => value ? String(value).split(/T| /)[0] : 'N/A';
        const agreementStart = formatDate(shop.agreement_start);
        const agreementEnd = formatDate(shop.agreement_end);

        document.getElementById('displayAgreementStart').textContent = agreementStart;
        document.getElementById('displayAgreementEnd').textContent = agreementEnd;

        if (agreementEnd === 'N/A' || !/^\d{4}-\d{2}-\d{2}$/.test(agreementEnd)) {
            document.getElementById('displayAgreementRemaining').textContent = 'N/A';
            return;
        }

        const [year, month, day] = agreementEnd.split('-').map(Number);
        const endTime = Date.UTC(year, month - 1, day);
        const endDate = new Date(endTime);
        if (endDate.getUTCFullYear() !== year || endDate.getUTCMonth() !== month - 1 || endDate.getUTCDate() !== day) {
            document.getElementById('displayAgreementRemaining').textContent = 'N/A';
            return;
        }

        const today = new Date();
        const todayTime = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());
        const remainingDays = Math.round((endTime - todayTime) / 86400000);
        const remainingText = remainingDays < 0
            ? `Expired ${Math.abs(remainingDays)} days`
            : remainingDays === 0
                ? 'Expires today'
                : `${remainingDays} days remaining`;

        document.getElementById('displayAgreementRemaining').textContent = remainingText;
    }

    async function loadPaymentHistory(shopId) {
        try {
            const res = await fetch(historyUrl + '?shop_id=' + shopId, { headers: { 'Accept': 'application/json' } });
            if (res.ok) {
                paymentModalHistory = await res.json();
            } else {
                paymentModalHistory = [];
            }
        } catch (err) {
            paymentModalHistory = [];
        }
    }

    async function loadShopById(id) {
        try {
            const res = await fetch(`${shopByIdUrl}/${id}`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) {
                noResultsMsg.style.display = 'block';
                return;
            }
            const s = await res.json();
            currentShop = s;
            document.getElementById('displayBusStand').textContent = (s.bus_stand && s.bus_stand.name) ? s.bus_stand.name : 'N/A';
            document.getElementById('displayShopNumber').textContent = s.shop_number || 'N/A';
            document.getElementById('displayOwnerName').textContent = s.shop_owner_name || 'N/A';
            document.getElementById('displayMonthlyRental').textContent = 'Rs. ' + formatMoney(s.monthly_rent);
            document.getElementById('displayRate').textContent = s.rate || '0%';
            document.getElementById('displayYearlyRental').textContent = 'Rs. ' + formatMoney((parseFloat(s.monthly_rent) || 0) * 12);
            updateAgreementDetails(s);
            document.getElementById('advancePayment').value = formatMoney(s.advance_payment);
            document.getElementById('duePayment').value = formatMoney(s.due_payment);
            document.getElementById('dueInterest').value = formatMoney(s.due_interest);
            document.getElementById('totalNeedToPay').value = formatMoney(s.total_need_to_pay);
            shopDetailsSection.style.display = 'block';
            noResultsMsg.style.display = 'none';

            // Populate unpaid months table
            populateUnpaidMonthsTable(s.unpaid_months);
        } catch (err) {
            noResultsMsg.style.display = 'block';
        }
    }

    const params = new URLSearchParams(window.location.search);
    const shopIdFromQuery = params.get('shop_id');
    if (shopIdFromQuery) {
        loadShopById(shopIdFromQuery);
    }

    async function performSearch(){
        const q = searchOwner.value.trim();
        shopDetailsSection.style.display = 'none';
        noResultsMsg.style.display = 'none';
        
        if (!q) { alert('Please enter owner name or shop number'); return; }
        
        try {
            const res = await fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
            if (res.status === 404) { noResultsMsg.style.display = 'block'; return; }
            const data = await res.json();
            
            if (!data || data.length === 0) { noResultsMsg.style.display = 'block'; return; }

            const s = data[0];
            currentShop = s;

            // Identity Card
            document.getElementById('displayBusStand').textContent = (s.bus_stand && s.bus_stand.name) ? s.bus_stand.name : 'N/A';
            document.getElementById('displayShopNumber').textContent = s.shop_number || 'N/A';
            document.getElementById('displayOwnerName').textContent = s.shop_owner_name || 'N/A';
            document.getElementById('displayMonthlyRental').textContent = 'Rs. ' + formatMoney(s.monthly_rent);
            document.getElementById('displayRate').textContent = s.rate || '0%';
            document.getElementById('displayYearlyRental').textContent = 'Rs. ' + formatMoney((parseFloat(s.monthly_rent) || 0) * 12);
            updateAgreementDetails(s);

            // Financial Stats
            document.getElementById('advancePayment').value = formatMoney(s.advance_payment);
            document.getElementById('duePayment').value = formatMoney(s.due_payment);
            document.getElementById('dueInterest').value = formatMoney(s.due_interest);
            document.getElementById('totalNeedToPay').value = formatMoney(s.total_need_to_pay);

            // Populate unpaid months table
            populateUnpaidMonthsTable(s.unpaid_months);

            shopDetailsSection.style.display = 'block';
        } catch (err) {
            alert('An error occurred during search.');
        }
    }

    btnSearch.addEventListener('click', performSearch);
    searchOwner.addEventListener('keydown', (e) => { if (e.key === 'Enter') performSearch(); });

    function formatMoney(v){
        const n = parseFloat(v);
        return isNaN(n) ? '0.00' : n.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function formatDateDisplay(isoDate) {
        return isoDate ? isoDate.replace(/-/g, '/') : '';
    }

    function populateUnpaidMonthsTable(unpaidMonths) {
        const tbody = document.getElementById('unpaidMonthsTableBody');
        tbody.innerHTML = '';

        if (!unpaidMonths || !Array.isArray(unpaidMonths) || unpaidMonths.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="4" style="padding: 8px; text-align: center; color: #64748b;">No unpaid months</td>';
            tbody.appendChild(row);
            return;
        }

        unpaidMonths.forEach(month => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td style="padding: 8px; border-bottom: 1px solid #e2e8f0;">${month.month_name}</td>
                <td style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rs. ${formatMoney(month.due_rent)}</td>
                <td style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rs. ${formatMoney(month.due_interest)}</td>
                <td style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold;">Rs. ${formatMoney(month.total_due)}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderMonthCheckboxes(shop) {
        monthCheckboxContainer.innerHTML = '';
        const selectedYear = parseInt(pfYear.value, 10);
        const currentYear = new Date().getFullYear();
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        const monthPaidInfo = {};
        paymentModalHistory.forEach(p => {
            if (p.monthly_payment && p.year == selectedYear) {
                monthPaidInfo[p.month] = { monthly_payment: p.monthly_payment, due_interest: p.due_interest };
            }
        });
        const unpaidMonths = (shop.unpaid_months || []).filter(month => Number(month.year) === selectedYear);
        const monthsDue = new Set(unpaidMonths.map(month => Number(month.month)));
        const monthsWithInterest = new Set(unpaidMonths
            .filter(month => parseFloat(month.due_interest) > 0)
            .map(month => Number(month.month)));

        const monthlyRent = parseFloat(shop.monthly_rent) || 0;
        const agreementStart = shop.agreement_start ? new Date(shop.agreement_start) : null;
        const agreementEnd = shop.agreement_end ? new Date(shop.agreement_end) : null;
        const agreementStartYear = agreementStart ? agreementStart.getFullYear() : 2023;
        const agreementStartMonth = agreementStart ? agreementStart.getMonth() + 1 : 1;
        const agreementEndYear = agreementEnd ? agreementEnd.getFullYear() : currentYear;
        const agreementEndMonth = agreementEnd ? agreementEnd.getMonth() + 1 : 12;

        monthNames.forEach((name, index) => {
            const monthIndex = index + 1;
            const paidInfo = monthPaidInfo[monthIndex];
            const isPaid = paidInfo && paidInfo.monthly_payment == 1;

            const div = document.createElement('label');
            div.className = 'month-item';
            if (isPaid) {
                div.classList.add('paid');
            }

            const paymentBox = document.createElement('input');
            paymentBox.type = 'checkbox';
            paymentBox.value = monthIndex;
            paymentBox.dataset.month = monthIndex;
            paymentBox.className = 'month-checkbox';

            const interestAvailable = monthsWithInterest.has(monthIndex);
            let dueBox = null;
            if (interestAvailable) {
                dueBox = document.createElement('input');
                dueBox.type = 'checkbox';
                dueBox.dataset.month = monthIndex;
                dueBox.className = 'month-due-checkbox';
                dueBox.style.marginLeft = '8px';
            }

            const isDue = monthsDue.has(monthIndex);

            const withinAgreementRange = (selectedYear > agreementStartYear || (selectedYear === agreementStartYear && monthIndex >= agreementStartMonth))
                && (selectedYear < agreementEndYear || (selectedYear === agreementEndYear && monthIndex <= agreementEndMonth));

            const editMode = pfEditMode ? pfEditMode.checked : false;
            paymentBox.dataset.paid = isPaid ? 'true' : 'false';
            paymentBox.dataset.due = isDue ? 'true' : 'false';

            if (isPaid) {
                paymentBox.checked = true;
                paymentBox.disabled = true; // PERMANENTLY DISABLED - no edit mode can change this
                paymentBox.classList.add('paid-checkbox');
                if (dueBox) {
                    dueBox.checked = isDue;
                    dueBox.disabled = true; // PERMANENTLY DISABLED - due checkbox also locked for paid months
                    dueBox.classList.add('paid-due-checkbox');
                }
                // Add visual lock indicator
                const lockIcon = document.createElement('span');
                lockIcon.textContent = '🔒';
                lockIcon.style.fontSize = '10px';
                lockIcon.style.marginLeft = '4px';
                lockIcon.title = 'Paid and locked';
                div.appendChild(lockIcon);
            } else {
                paymentBox.checked = false;
                paymentBox.disabled = !withinAgreementRange;
                if (dueBox) {
                    dueBox.checked = false;
                    dueBox.disabled = !withinAgreementRange;
                }
                if (!withinAgreementRange) {
                    div.style.opacity = '0.6';
                    div.title = 'Outside agreement date range';
                }
            }

            if (isDue && !isPaid) {
                div.classList.add('due');
            }

            if (dueBox) {
                dueBox.addEventListener('change', () => {
                    updateMonthlyPayment(shop);
                });
            }

            paymentBox.addEventListener('change', () => {
                if (paymentBox.checked) {
                    div.classList.add('checked');
                } else {
                    div.classList.remove('checked');
                }
                updateMonthlyPayment(shop);
            });

            const monthLabel = document.createElement('span');
            monthLabel.textContent = name;
            monthLabel.style.marginLeft = '6px';

            div.appendChild(paymentBox);
            div.appendChild(monthLabel);
            if (dueBox) {
                const dueLabel = document.createElement('span');
                dueLabel.textContent = 'Interest';
                dueLabel.style.marginLeft = '6px';
                dueLabel.style.fontSize = '11px';
                dueLabel.style.color = '#b45309';
                div.appendChild(dueBox);
                div.appendChild(dueLabel);
            }
            monthCheckboxContainer.appendChild(div);
        });

        updateMonthlyPayment(shop);
    }

    function updateMonthlyPayment(shop) {
        const monthlyRent = parseFloat(shop.monthly_rent) || 0;
        const duePayment = parseFloat(shop.due_payment) || 0;
        const dueInterest = parseFloat(shop.due_interest) || 0;
        const rate = parseFloat(shop.rate) || 0;

        const totalDueBalance = duePayment + dueInterest;
        const pfTotalDueBalance = document.getElementById('pfTotalDueBalance');
        if (pfTotalDueBalance) {
            pfTotalDueBalance.value = formatMoney(totalDueBalance);
        }

        const checkboxes = monthCheckboxContainer.querySelectorAll('.month-checkbox');
        let selectedCount = 0;
        let selectedDueMonthCount = 0;
        let dueMonthCount = 0;
        const editMode = pfEditMode ? pfEditMode.checked : false;

        const selectedMonths = [];
        const selectedDues = [];

        checkboxes.forEach(ch => {
            const isPaid = ch.dataset.paid === 'true';
            const month = parseInt(ch.dataset.month, 10);
            const dueBox = monthCheckboxContainer.querySelector(`.month-due-checkbox[data-month="${month}"]`);
            const isDueChecked = dueBox ? dueBox.checked : false;

            if (ch.checked && !isPaid) {
                selectedCount += 1;
                selectedMonths.push(month);
                if (ch.dataset.due === 'true') {
                    selectedDueMonthCount += 1;
                }
                if (isDueChecked) {
                    dueMonthCount += 1;
                    selectedDues.push(month);
                }
            }

            if (isPaid) {
                ch.closest('.month-item')?.classList.add('paid');
            }
        });

        const rentTotal = selectedCount * monthlyRent;
        const dueInterestSelected = dueMonthCount * (monthlyRent * rate / 100);
        const selectedMonthBalance = rentTotal + dueInterestSelected;

        pfSelectedMonthsCount.value = selectedCount;
        document.getElementById('pfSelectedDueMonthsCount').value = selectedDueMonthCount;
        pfSelectedMonthValues.value = JSON.stringify(selectedMonths);
        pfSelectedDueMonths.value = JSON.stringify(selectedDues);
        pfYearInput.value = pfYear.value;
        pfRentTotal.value = formatMoney(rentTotal);
        pfDuePaymentView.value = formatMoney(duePayment);
        pfDueInterestView.value = formatMoney(dueInterest);

        // Payment Amount (Selected Months) + Selected Due Interest = Paid Balance (incl. interest)
        pfCalculatedAmount.value = formatMoney(rentTotal);
        pfSelectedDueInterest.value = formatMoney(dueInterestSelected);
        pfSelectedMonthBalance.value = formatMoney(selectedMonthBalance);

        // POST values: rent and due interest separate!
        pfAmount.value = rentTotal.toFixed(2);
        pfInterest.value = dueInterestSelected.toFixed(2);
    }

    async function setupPaymentModal(shop) {
        if (!shop) return;

        await loadPaymentHistory(shop.id);

        pfYear.innerHTML = '';
        const thisYear = new Date().getFullYear();
        const agreementStartYear = shop.agreement_start ? new Date(shop.agreement_start).getFullYear() : 2023;
        const agreementEndYear = shop.agreement_end ? new Date(shop.agreement_end).getFullYear() : thisYear;
        const startYear = Math.max(2023, agreementStartYear);
        let endYear = Math.min(thisYear, agreementEndYear);
        if (endYear < startYear) {
            endYear = thisYear;
        }

        for (let year = endYear; year >= startYear; year--) {
            const opt = document.createElement('option');
            opt.value = year;
            opt.textContent = year;
            pfYear.appendChild(opt);
        }

        const defaultYear = (startYear <= thisYear && thisYear <= endYear) ? thisYear : endYear;
        pfYear.value = defaultYear;
        pfDuePaymentView.value = formatMoney(shop.due_payment);
        pfDueInterestView.value = formatMoney(shop.due_interest);
        if (pfEditMode) {
            pfEditMode.checked = false; // Reset edit mode to ensure paid months stay PERMANENTLY locked
        }
        pfDate.value = new Date().toISOString().split('T')[0];
        pfDateDisplay.value = formatDateDisplay(pfDate.value);
        pfDateDisplay.setAttribute('readonly', 'readonly');
        pfDateDisplay.setAttribute('disabled', 'disabled');
        isCheckSelectedClicked = false; // Reset flag when opening modal
        btnCheckAll.disabled = false; // Enable Check Selected Months button
        // pfIncludeDue.checked = true; removed

        paymentModalShop = shop;
        renderMonthCheckboxes(shop); // This will render paid months as PERMANENTLY DISABLED
    }

    // Payment Logic
    const paymentForm = document.getElementById('paymentForm');
    const pfReceiptNo = document.getElementById('pfReceiptNo');
    const pfAmount = document.getElementById('pfAmount');
    const pfInterest = document.getElementById('pfInterest');
    const pfDate = document.getElementById('pfDate');
    const pfDateDisplay = document.getElementById('pfDateDisplay');
    const pfShopId = document.getElementById('pfShopId');
    const pfYear = document.getElementById('pfYear');
    const monthCheckboxContainer = document.getElementById('monthCheckboxContainer');
    const pfSelectedMonthsCount = document.getElementById('pfSelectedMonthsCount');
    const pfSelectedMonthValues = document.getElementById('pfSelectedMonthValues');
    const pfSelectedDueMonths = document.getElementById('pfSelectedDueMonths');
    const pfYearInput = document.getElementById('pfYearInput');
    const pfRentTotal = document.getElementById('pfRentTotal');
    const pfDuePaymentView = document.getElementById('pfDuePaymentView');
    const pfDueInterestView = document.getElementById('pfDueInterestView');
    const pfCalculatedAmount = document.getElementById('pfCalculatedAmount');
    const pfSelectedMonthBalance = document.getElementById('pfSelectedMonthBalance');
    const pfSubmit = document.getElementById('pfSubmit');
    const pfCancel = document.getElementById('pfCancel');
    const pfEditMode = document.getElementById('pfEditMode');
    const btnCheckAll = document.getElementById('btnCheckAll');

    // Edit mode handling - NOTE: Paid months remain permanently disabled regardless of edit mode
    if (pfEditMode) {
        pfEditMode.addEventListener('change', function() {
            const now = new Date();
            if (pfEditMode.checked) {
                pfDateDisplay.removeAttribute('readonly');
                pfDateDisplay.removeAttribute('disabled');
                pfDate.value = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
            } else {
                pfDateDisplay.setAttribute('readonly', 'readonly');
                pfDateDisplay.setAttribute('disabled', 'disabled');
                pfDate.value = new Date().toISOString().split('T')[0];
            }
            pfDateDisplay.value = formatDateDisplay(pfDate.value);
            // Re-render checkboxes when edit mode changes
            // IMPORTANT: Paid months will ALWAYS remain disabled, even in edit mode
            if (paymentModalShop) {
                renderMonthCheckboxes(paymentModalShop);
            }
        });
    }

    pfDateDisplay.addEventListener('input', function() {
        if (pfEditMode && pfEditMode.checked) {
            pfDate.value = this.value.replace(/\//g, '-');
        }
    });

    // Check Selected button - lock only currently selected months
    btnCheckAll.addEventListener('click', function(e) {
        e.preventDefault();
        const selectedCheckboxes = monthCheckboxContainer.querySelectorAll('.month-checkbox:not(:disabled):checked');

        if (selectedCheckboxes.length === 0) {
            alert('⚠️ Please select at least one month before clicking "Check Selected Months".');
            return;
        }

        selectedCheckboxes.forEach(checkbox => {
            checkbox.disabled = true; // Make read-only after checking
            const month = parseInt(checkbox.dataset.month, 10);
            const dueBox = monthCheckboxContainer.querySelector(`.month-due-checkbox[data-month="${month}"]`);
            if (dueBox && !dueBox.disabled) {
                dueBox.disabled = true; // Lock due checkbox too
            }
            const itemLabel = checkbox.closest('.month-item');
            if (itemLabel && !itemLabel.classList.contains('paid')) {
                itemLabel.classList.add('checked');
            }
        });

        isCheckSelectedClicked = true; // Set flag to true
        btnCheckAll.disabled = true; // Disable the button itself
        if (paymentModalShop) {
            updateMonthlyPayment(paymentModalShop);
        }
    });

    pfYear.addEventListener('change', () => {
        if (!paymentModalShop) return;
        isCheckSelectedClicked = false; // Reset flag when year changes
        renderMonthCheckboxes(paymentModalShop);
        btnCheckAll.disabled = false; // Re-enable Check Selected Months button when year changes
    });

    // pfIncludeDue event listener removed as checkbox is gone
    const paymentBackdrop = document.getElementById('paymentBackdrop');
    const btnPay = document.getElementById('btnPay');
    const btnPayMain = document.getElementById('btnPayMain');
    const btnHistoricalEditMode = document.getElementById('btnHistoricalEditMode');

    async function openPaymentModal() {
        if (!currentShop) {
            alert('Please search and select a shop first.');
            return;
        }
        pfShopId.value = currentShop.id;
        document.getElementById('pfShop').textContent = currentShop.shop_number;
        document.getElementById('pfOwner').textContent = currentShop.shop_owner_name;
        pfReceiptNo.value = '';
        if (!pfEditMode || !pfEditMode.checked) {
            pfDate.value = new Date().toISOString().split('T')[0];
            pfDateDisplay.value = formatDateDisplay(pfDate.value);
        }

        try {
            await setupPaymentModal(currentShop);
        } catch (error) {
            alert('Error loading payment form: ' + error.message);
            return;
        }

        paymentBackdrop.style.display = 'block';
        paymentForm.style.display = 'block';
    }

    if (btnPay) {
        btnPay.addEventListener('click', openPaymentModal);
    }
    if (btnPayMain) {
        btnPayMain.addEventListener('click', openPaymentModal);
    }
    if (btnHistoricalEditMode) {
        btnHistoricalEditMode.addEventListener('click', async function() {
            if (!currentShop) {
                alert('Please search and select a shop first.');
                return;
            }

            if (pfEditMode) {
                pfEditMode.checked = true;
                pfDateDisplay.removeAttribute('readonly');
                pfDateDisplay.removeAttribute('disabled');
                pfDate.value = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
                pfDateDisplay.value = formatDateDisplay(pfDate.value);
            }

            await openPaymentModal();
        });
    }

    pfCancel.addEventListener('click', () => {
        paymentForm.style.display = 'none';
        paymentBackdrop.style.display = 'none';
        if (pfEditMode) {
            pfEditMode.checked = false; // Reset edit mode when closing
        }
        isCheckSelectedClicked = false; // Reset flag when closing
        btnCheckAll.disabled = false; // Re-enable button when form closes
    });

    pfSubmit.addEventListener('click', async function(e){
        e.preventDefault();
        
        // Check if "Check Selected Months" button was clicked
        if (!isCheckSelectedClicked) {
            alert('⚠️ Please click "Check Selected Months" button to confirm selected months are locked before submitting payment.');
            return;
        }
        
        const amountVal = parseFloat(pfAmount.value) || 0;
        const interestVal = parseFloat(pfInterest.value) || 0;
        const receiptNoVal = pfReceiptNo.value.trim();

        if (!receiptNoVal) { alert('Receipt Number is required'); return; }
        if (amountVal <= 0 && interestVal <= 0) { alert('Please enter amount or interest'); return; }

        try {
            const fd = new FormData();
            fd.append('shop_id', pfShopId.value);
            fd.append('receipt_number', receiptNoVal);
            fd.append('amount', amountVal);
            fd.append('interest', interestVal);
            fd.append('paid_at', pfDate.value);
            if (document.getElementById('pfSlip').files[0]) fd.append('slip', document.getElementById('pfSlip').files[0]);

            fd.append('selected_months', pfSelectedMonthValues.value || '[]');
            fd.append('selected_dues', pfSelectedDueMonths.value || '[]');
            fd.append('year', pfYear.value);

            const res = await fetch(recordUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: fd
            });

            if (!res.ok) throw await res.json();

            alert('✅ Payment recorded successfully! Selected months are now permanently locked and cannot be edited.');

            // Refresh latest shop due/interest values from server, then re-render.
            if (paymentModalShop) {
                await loadShopById(paymentModalShop.id);
                await loadPaymentHistory(paymentModalShop.id);
                paymentModalShop = currentShop;

                // Re-render checkboxes with updated history - PAID MONTHS WILL BE PERMANENTLY DISABLED
                if (paymentModalShop) {
                    renderMonthCheckboxes(paymentModalShop);
                }

                // Reset form for potential next payment
                pfReceiptNo.value = '';
                pfAmount.value = '0.00';
                pfInterest.value = '0.00';
                pfSelectedMonthValues.value = '[]';
                pfSelectedDueMonths.value = '[]';
                if (pfEditMode) {
                    pfEditMode.checked = false; // Disable edit mode so paid months stay locked
                }
                pfDate.value = new Date().toISOString().split('T')[0];
                pfDateDisplay.value = formatDateDisplay(pfDate.value);
                isCheckSelectedClicked = false; // Reset flag for next transaction
                btnCheckAll.disabled = false; // Reset Check Selected Months button for next transaction

                if (paymentModalShop) {
                    updateMonthlyPayment(paymentModalShop);
                }
            } else {
                // If modal closed, close and refresh search
                paymentForm.style.display = 'none';
                paymentBackdrop.style.display = 'none';
                isCheckSelectedClicked = false; // Reset flag
                btnCheckAll.disabled = false; // Reset button
                performSearch();
            }
        } catch (err) {
            alert(err.message || 'Payment recording failed.');
        }
    });

    // History Logic
    const btnHistory = document.getElementById('btnHistory');
    const historyModal = document.getElementById('paymentHistoryModal');
    const historyBackdrop = document.getElementById('paymentHistoryBackdrop');
    const historyTableBody = document.getElementById('historyTableBody');
    const historyClose = document.getElementById('historyClose');

    btnHistory.addEventListener('click', async function(){
        if (!currentShop) return;
        historyTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">Loading history...</td></tr>';
        historyBackdrop.style.display = 'block';
        historyModal.style.display = 'block';

        try {
            const res = await fetch(historyUrl + '?shop_id=' + currentShop.id);
            const data = await res.json();
            historyTableBody.innerHTML = '';
            if (!data.length) {
                historyTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">No payment history found.</td></tr>';
                return;
            }
            data.forEach(p => {
                const row = document.createElement('tr');
                const cellStyle = 'padding:10px; border-bottom:1px solid #f1f5f9;';
                const values = [
                    `Rs. ${formatMoney(p.amount)}`,
                    `Rs. ${formatMoney(p.interest)}`,
                    p.payable_months_display || p.payable_months || '-',
                    p.paid_at ? p.paid_at.split('T')[0] : '-',
                    p.receipt_number || '-'
                ];

                values.forEach(value => {
                    const cell = document.createElement('td');
                    cell.style.cssText = cellStyle;
                    cell.textContent = value;
                    row.appendChild(cell);
                });

                const receiptCell = document.createElement('td');
                receiptCell.style.cssText = cellStyle;
                if (p.slips && p.slips.length && p.slips[0].url) {
                    const receiptButton = document.createElement('a');
                    receiptButton.href = p.slips[0].url;
                    receiptButton.target = '_blank';
                    receiptButton.rel = 'noopener';
                    receiptButton.textContent = 'View Receipt';
                    receiptButton.className = 'btn btn-primary';
                    receiptButton.style.cssText = 'display:inline-block; text-decoration:none; padding:6px 10px; font-size:12px;';
                    receiptCell.appendChild(receiptButton);
                } else {
                    receiptCell.textContent = '-';
                }
                row.appendChild(receiptCell);
                historyTableBody.appendChild(row);
            });
        } catch (err) {
            historyTableBody.innerHTML = '<tr><td colspan="6" style="color:red; text-align:center;">Error loading history</td></tr>';
        }
    });

    historyClose.addEventListener('click', () => {
        historyModal.style.display = 'none';
        historyBackdrop.style.display = 'none';
    });
});
</script>
@endpush
