@extends('layouts.admin')

@section('title', 'Manage / Edit Shops')

@section('content')

<div style="max-width:1100px;margin:0 auto;">
    <h2 style="margin-bottom:12px">Search and Edit Shops</h2>

    <div style="margin-bottom:18px">
        <input id="searchInput" placeholder="Search by shop number or owner name" style="width:70%;padding:10px;border:1px solid #ddd;border-radius:6px">
        <button id="searchBtn" class="btn btn-primary">Search</button>
    </div>

    <div id="results" style="margin-bottom:20px"></div>

    <form id="editForm" method="POST" style="background:white;padding:20px;border-radius:8px;display:none;" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" id="shop_id" name="id">

        <div class="form-grid">
            <!-- basic identifiers -->
            <div class="form-group">
                <label>Shop Number</label>
                <input type="text" id="shop_number" name="shop_number" required>
            </div>
            <div class="form-group">
                <label>Shop Name</label>
                <input type="text" id="shop_name" name="shop_name" required>
            </div>
            <div class="form-group">
                <label>Owner Name</label>
                <input type="text" id="shop_owner_name" name="shop_owner_name" required>
            </div>
            <div class="form-group">
                <label>Owner NIC</label>
                <input type="text" id="shop_owner_nic" name="shop_owner_nic">
            </div>
            <div class="form-group">
                <label>Owner Phone</label>
                <input type="text" id="shop_owner_phone" name="shop_owner_phone">
            </div>
            <div class="form-group">
                <label>Owner Address</label>
                <textarea id="shop_owner_address" name="shop_owner_address" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label>Shop Address</label>
                <input type="text" id="shop_address" name="shop_address">
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" id="location" name="location">
            </div>

            <!-- financial fields -->
            <div class="form-group">
                <label>Monthly Rent</label>
                <input type="number" step="0.01" id="monthly_rent" name="monthly_rent">
            </div>
            <div class="form-group">
                <label>Shop Value</label>
                <input type="number" step="0.01" id="shop_value" name="shop_value">
            </div>
            <div class="form-group">
                <label>Rate</label>
                <input type="number" step="0.01" id="rate" name="rate">
            </div>
            <div class="form-group">
                <label>Advance Payment</label>
                <input type="number" step="0.01" id="advance_payment" name="advance_payment">
            </div>
            <div class="form-group">
                <label>Opening Balance</label>
                <input type="number" step="0.01" id="opening_balance" name="opening_balance">
            </div>
            <div class="form-group">
                <label>Due Payment</label>
                <input type="number" step="0.01" id="due_payment" name="due_payment">
            </div>
            <div class="form-group">
                <label>Due Interest</label>
                <input type="number" step="0.01" id="due_interest" name="due_interest">
            </div>
            <div class="form-group">
                <label>Year Charge</label>
                <input type="number" step="0.01" id="year_charge" name="year_charge">
            </div>
            <div class="form-group">
                <label>Year Interest</label>
                <input type="number" step="0.01" id="year_interest" name="year_interest">
            </div>
            <div class="form-group">
                <label>Year Total Taken</label>
                <input type="number" step="0.01" id="year_total_taken" name="year_total_taken">
            </div>
            <div class="form-group">
                <label>Deduct Payment</label>
                <input type="number" step="0.01" id="deduct_payment" name="deduct_payment">
            </div>
            <div class="form-group">
                <label>Deduct Interest</label>
                <input type="number" step="0.01" id="deduct_interest" name="deduct_interest">
            </div>
            <div class="form-group">
                <label>Agreement Duration</label>
                <input type="text" id="agreement_duration" name="agreement_duration">
            </div>
            <div class="form-group">
                <label>Total Paid</label>
                <input type="number" step="0.01" id="total_paid" name="total_paid">
            </div>
            <div class="form-group">
                <label>Total Due</label>
                <input type="number" step="0.01" id="total_due" name="total_due">
            </div>
            <div class="form-group">
                <label>Balance Amount</label>
                <input type="number" step="0.01" id="balance_amount" name="balance_amount">
            </div>
            <div class="form-group">
                <label>Record Year</label>
                <input type="number" id="record_year" name="record_year" min="1900" max="2100">
            </div>

            <!-- agreement dates -->
            <div class="form-group">
                <label>Agreement Start</label>
                <input type="date" id="agreement_start" name="agreement_start">
            </div>
            <div class="form-group">
                <label>Agreement End</label>
                <input type="date" id="agreement_end" name="agreement_end">
            </div>

            <!-- bus stand -->
            <div class="form-group">
                <label>Bus Stand</label>
                <select id="bus_stand_id" name="bus_stand_id">
                    <option value="">-- none --</option>
                    @foreach($busStands as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- monthly rents removed (DB columns not present) -->

            <!-- remarks -->
            <div class="form-group" style="grid-column:1 / -1;">
                <label>Remarks</label>
                <textarea id="remarks" name="remarks" rows="3"></textarea>
            </div>
        </div>

        <div style="margin-top:12px;display:flex;gap:8px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <button type="button" id="clearBtn" class="btn btn-secondary">Clear</button>
        </div>
    </form>
</div>

<style>
    .form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px}
    .form-group label{display:block;font-weight:600;margin-bottom:6px}
    .form-group input,.form-group select,.form-group textarea{width:100%;padding:8px;border:1px solid #e5e7eb;border-radius:6px}
</style>

@push('scripts')
<script>
    const searchBtn = document.getElementById('searchBtn');
    const searchInput = document.getElementById('searchInput');
    const results = document.getElementById('results');
    const editForm = document.getElementById('editForm');

    function renderResults(items){
        if(!items.length){
            results.innerHTML = '<div>No results</div>';
            return;
        }
        let html = '<ul style="list-style:none;padding:0">';
        items.forEach(i=>{
            html += `<li style="padding:8px;border:1px solid #eee;margin-bottom:6px;cursor:pointer" data-id="${i.id}"><strong>${i.shop_number}</strong> — ${i.shop_name} — ${i.shop_owner_name}</li>`;
        });
        html += '</ul>';
        results.innerHTML = html;

        results.querySelectorAll('li').forEach(li=>{
            li.addEventListener('click', ()=>{
                loadShop(li.getAttribute('data-id'));
            });
        });
    }

    async function loadShop(id){
        // fetch single record from show endpoint
        const res = await fetch(`/admin/shops/${id}`);
        if(!res.ok){
            return alert('Failed to load shop');
        }
        const shop = await res.json();

        document.getElementById('shop_id').value = shop.id;

        // populate any form field whose name matches a property
        editForm.querySelectorAll('[name]').forEach(el=>{
            const name = el.getAttribute('name');
            if(shop.hasOwnProperty(name)){
                let val = shop[name] === null ? '' : shop[name];
                if(el.type === 'date' && val){
                    val = val.split('T')[0];
                }
                el.value = val;
            }
        });

        editForm.action = `/admin/shops/${shop.id}`;
        editForm.style.display = 'block';
        window.scrollTo({top: editForm.offsetTop - 20, behavior: 'smooth'});
    }

    searchBtn.addEventListener('click', async ()=>{
        const q = encodeURIComponent(searchInput.value.trim());
        if(!q) return;
        const res = await fetch(`/admin/shops/search?q=${q}`);
        const data = await res.json();
        renderResults(data);
    });

    document.getElementById('clearBtn').addEventListener('click', ()=>{
        editForm.reset(); editForm.style.display = 'none'; results.innerHTML = '';
    });
</script>
@endpush

@endsection
