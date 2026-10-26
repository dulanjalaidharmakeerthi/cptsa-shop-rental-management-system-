@php
    $layout = 'layouts.app';
    if (auth()->check() && auth()->user()->position === 'Cashier') {
        $layout = 'layouts.chap';
    }
@endphp
@extends($layout)

@section('title', 'Create Shop')

@push('head')
<style>
  /* Advanced Modern UI Styles */
  * {
    box-sizing: border-box;
  }

  .shop-form-wrapper {
    max-width: 1100px;
    margin: 30px auto;
    padding: 0;
  }

  .shop-form-header {
    background: linear-gradient(135deg, #1f4e79 0%, #00a3ad 100%);
    color: white;
    padding: 40px;
    border-radius: 12px 12px 0 0;
    text-align: center;
    box-shadow: 0 4px 20px rgba(31, 78, 121, 0.15);
  }

  .shop-form-header h1 {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    letter-spacing: -0.5px;
  }

  .shop-form-header p {
    margin: 8px 0 0 0;
    font-size: 15px;
    opacity: 0.95;
    font-weight: 300;
  }

  .form-container {
    background: white;
    border-radius: 0 0 12px 12px;
    padding: 50px 45px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
  }

  .step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 45px;
    position: relative;
  }

  .step-indicator::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e5e7eb;
    z-index: 0;
  }

  .step-item {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
  }

  .step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #f3f4f6;
    color: #6b7280;
    font-weight: 700;
    font-size: 16px;
    margin: 0 auto 10px;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
  }

  .step-item.active .step-number {
    background: linear-gradient(135deg, #1f4e79 0%, #00a3ad 100%);
    color: white;
    border-color: #1f4e79;
    transform: scale(1.1);
  }

  .step-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
    margin-top: 8px;
    display: block;
  }

  .step-item.active .step-label {
    color: #1f4e79;
    font-weight: 600;
  }

  .form-section {
    display: none;
    animation: fadeIn 0.3s ease;
  }

  .form-section.active {
    display: block;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .section-header {
    margin-bottom: 30px;
  }

  .section-header h2 {
    font-size: 22px;
    color: var(--primary-color);
    margin: 0 0 8px 0;
    font-weight: 700;
  }

  .section-header p {
    color: #6b7280;
    font-size: 14px;
    margin: 0;
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 30px;
  }

  .form-grid.full-width {
    grid-column: 1 / -1;
  }

  .form-group {
    display: flex;
    flex-direction: column;
  }

  .form-group.full-span {
    grid-column: 1 / -1;
  }

  .form-label {
    font-weight: 600;
    color: #1f4e79;
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    letter-spacing: 0.3px;
  }

  .form-label .required {
    color: #ef4444;
  }

  .form-control {
    padding: 12px 15px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: inherit;
    background: #fafafa;
  }

  .form-control:focus {
    outline: none;
    border-color: #00a3ad;
    background: white;
    box-shadow: 0 0 0 3px rgba(0, 163, 173, 0.1);
  }

  .form-control::placeholder {
    color: #9ca3af;
  }

  textarea.form-control {
    resize: vertical;
    min-height: 100px;
  }

  .field-help {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 6px;
    font-style: italic;
  }

  .alert {
    padding: 15px 18px;
    border-radius: 8px;
    margin-bottom: 24px;
    animation: slideDown 0.3s ease;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border: 1px solid #6ee7b7;
    color: #065f46;
  }

  .alert-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border: 1px solid #fca5a5;
    color: #7f1d1d;
  }

  .alert-danger ul {
    margin: 8px 0 0 0;
    padding-left: 20px;
  }

  .alert-danger li {
    margin: 4px 0;
  }

  .button-group {
    display: flex;
    gap: 12px;
    margin-top: 40px;
    justify-content: flex-end;
  }

  .btn-primary {
    padding: 12px 32px;
    background: linear-gradient(135deg, #1f4e79 0%, #00a3ad 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(31, 78, 121, 0.2);
    letter-spacing: 0.3px;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(31, 78, 121, 0.3);
  }

  .btn-primary:active {
    transform: translateY(0);
  }

  .btn-secondary {
    padding: 12px 32px;
    background: #f3f4f6;
    color: #1f4e79;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    letter-spacing: 0.3px;
  }

  .btn-secondary:hover {
    background: #e5e7eb;
    border-color: #d1d5db;
  }

  .btn-nav {
    padding: 10px 24px;
    background: transparent;
    color: #1f4e79;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-nav:hover {
    border-color: #00a3ad;
    color: #00a3ad;
  }

  .btn-nav:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .image-upload {
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fafafa;
  }

  .image-upload:hover {
    border-color: #00a3ad;
    background: #f0fffe;
  }

  .image-upload input[type="file"] {
    display: none;
  }

  .image-upload-text {
    color: #6b7280;
    font-size: 14px;
  }

  .image-upload-icon {
    font-size: 32px;
    margin-bottom: 10px;
    display: block;
  }

  .divider {
    height: 1px;
    background: #e5e7eb;
    margin: 30px 0;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
  }

  .form-row.three {
    grid-template-columns: 1fr 1fr 1fr;
  }

  @media (max-width: 900px) {
    .form-container {
      padding: 30px 25px;
    }

    .form-grid,
    .form-row {
      grid-template-columns: 1fr;
    }

    .form-row.three {
      grid-template-columns: 1fr;
    }

    .button-group {
      flex-direction: column;
      justify-content: stretch;
    }

    .btn-primary,
    .btn-secondary {
      width: 100%;
      justify-content: center;
    }

    .step-indicator {
      margin-bottom: 30px;
    }

    .step-label {
      font-size: 11px;
    }

    .shop-form-header {
      padding: 30px 20px;
    }

    .shop-form-header h1 {
      font-size: 24px;
    }
  }

  @media (max-width: 600px) {
    .shop-form-wrapper {
      margin: 15px;
    }

    .form-container {
      padding: 20px 15px;
    }

    .form-grid,
    .form-row {
      gap: 15px;
    }

    .step-indicator::before {
      height: 1px;
    }

    .step-number {
      width: 36px;
      height: 36px;
      font-size: 14px;
    }
  }

  input[type="file"]::file-selector-button {
    background: #1f4e79;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    margin-right: 10px;
    transition: all 0.3s ease;
  }

  input[type="file"]::file-selector-button:hover {
    background: #00a3ad;
  }

  input[type="number"]::-webkit-outer-spin-button,
  input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  input[type="number"] {
    -moz-appearance: textfield;
  }
</style>
@endpush

@section('content')
<div class="shop-form-wrapper">
  <div class="shop-form-header">
    <h1>✨ Create New Shop</h1>
    <p>Complete the form below to register a new shop in the system</p>
  </div>

  <div class="form-container">
    <!-- Alert Messages -->
    @if(session('success'))
      <div class="alert alert-success">
        <strong>✓ Success!</strong> {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
        <strong>⚠ Please fix the following errors:</strong>
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Step Indicator -->
    <div class="step-indicator">
      <div class="step-item active" data-step="1">
        <div class="step-number">1</div>
        <span class="step-label">Bus Stand</span>
      </div>
      <div class="step-item" data-step="2">
        <div class="step-number">2</div>
        <span class="step-label">Basic Info</span>
      </div>
      <div class="step-item" data-step="3">
        <div class="step-number">3</div>
        <span class="step-label">Owner Details</span>
      </div>
      <div class="step-item" data-step="4">
        <div class="step-number">4</div>
        <span class="step-label">Financial</span>
      </div>
      <div class="step-item" data-step="5">
        <div class="step-number">5</div>
        <span class="step-label">Agreement</span>
      </div>
    </div>

    <form action="{{ route('shops.store') }}" method="POST" enctype="multipart/form-data" id="shopForm">
      @csrf

      <!-- Step 1: Bus Stand Selection -->
      <div class="form-section active" data-section="1">
        <div class="section-header">
          <h2>Select Bus Stand</h2>
          <p>Choose the bus stand where this shop is located</p>
        </div>

        <div class="form-grid">
          <div class="form-group full-span">
            <label class="form-label" for="bus_stand_id">
              Bus Stand <span class="required">*</span>
            </label>
            <select name="bus_stand_id" id="bus_stand_id" class="form-control" required>
              <option value="">-- Select Bus Stand --</option>
              @foreach($busStands ?? [] as $bs)
                <option value="{{ $bs->id }}" {{ (old('bus_stand_id') == $bs->id || (isset($preselect) && $preselect == $bs->id)) ? 'selected' : '' }}>{{ $bs->name }}</option>
              @endforeach
            </select>
            <div class="field-help">Select the bus stand location for this shop</div>
          </div>
        </div>
      </div>

      <!-- Step 2: Basic Information -->
      <div class="form-section" data-section="2">
        <div class="section-header">
          <h2>Shop Basic Information</h2>
          <p>Enter the basic details of the shop</p>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="shop_number">
              Shop Number <span class="required">*</span>
            </label>
            <input type="text" name="shop_number" id="shop_number" class="form-control" value="{{ old('shop_number') }}" placeholder="e.g., SH-001" required>
            <div class="field-help">Unique identifier for the shop</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="shop_name">
              Shop Name <span class="required">*</span>
            </label>
            <input type="text" name="shop_name" id="shop_name" class="form-control" value="{{ old('shop_name') }}" placeholder="e.g., Main Store" required>
            <div class="field-help">Public-facing name (max 150 characters)</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="location">Location</label>
            <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" placeholder="e.g., Ground Floor">
            <div class="field-help">Physical location within the complex</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="shop_address">Shop Address</label>
            <input type="text" name="shop_address" id="shop_address" class="form-control" value="{{ old('shop_address') }}" placeholder="e.g., Street name, area">
            <div class="field-help">Complete address of the shop</div>
          </div>

          <div class="form-group full-span">
            <label class="form-label" for="image">Shop Image</label>
            <div class="image-upload" onclick="document.getElementById('image').click()">
              <div class="image-upload-icon">📸</div>
              <input type="file" name="image" id="image" accept="image/*">
              <div class="image-upload-text">Click to upload shop image (Max 2MB)</div>
            </div>
            <div class="field-help">Upload a photo of the shop for reference</div>
          </div>
        </div>
      </div>

      <!-- Step 3: Owner Details -->
      <div class="form-section" data-section="3">
        <div class="section-header">
          <h2>Owner Information</h2>
          <p>Provide details about the shop owner</p>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="shop_owner_name">
              Owner Name <span class="required">*</span>
            </label>
            <input type="text" name="shop_owner_name" id="shop_owner_name" class="form-control" value="{{ old('shop_owner_name') }}" placeholder="Full name" required>
            <div class="field-help">Full name of the shop owner</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="shop_owner_nic">NIC Number</label>
            <input type="text" name="shop_owner_nic" id="shop_owner_nic" class="form-control" value="{{ old('shop_owner_nic') }}" placeholder="e.g., 123456789V">
            <div class="field-help">NIC Number</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="shop_owner_phone">Owner Phone</label>
            <input type="text" name="shop_owner_phone" id="shop_owner_phone" class="form-control" value="{{ old('shop_owner_phone') }}" placeholder="e.g., +94 77 123 4567">
            <div class="field-help">Contact number of the owner</div>
          </div>

          <div class="form-group full-span">
            <label class="form-label" for="shop_owner_address">Owner Address</label>
            <textarea name="shop_owner_address" id="shop_owner_address" class="form-control" placeholder="Street address, city, postal code...">{{ old('shop_owner_address') }}</textarea>
            <div class="field-help">Residential or postal address of the owner</div>
          </div>
        </div>
      </div>

      <!-- Step 4: Financial Details -->
      <div class="form-section" data-section="4">
        <div class="section-header">
          <h2>Financial Information</h2>
          <p>Enter rent, rates, and payment details</p>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="monthly_rent">
              Monthly Rent <span class="required">*</span>
            </label>
            <input type="number" step="0.01" name="monthly_rent" id="monthly_rent" class="form-control" value="{{ old('monthly_rent') }}" placeholder="0.00" required>
            <div class="field-help">Monthly rent amount in local currency</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="rate">Rate</label>
            <input type="number" step="0.01" name="rate" id="rate" class="form-control" value="{{ old('rate', 0) }}" placeholder="0.00">
            <div class="field-help">Customer rate per unit</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="advance_payment">Advance Payment</label>
            <input type="number" step="0.01" name="advance_payment" id="advance_payment" class="form-control" value="{{ old('advance_payment', 0) }}" placeholder="0.00">
            <div class="field-help">Advance payment collected</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="opening_balance">Opening Balance</label>
            <input type="number" step="0.01" name="opening_balance" id="opening_balance" class="form-control" value="{{ old('opening_balance', 0) }}" placeholder="0.00">
            <div class="field-help">Initial account balance</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="record_year">Record Year</label>
            <input type="number" name="record_year" id="record_year" class="form-control" value="{{ old('record_year', now()->year) }}" required>
            <div class="field-help">Year for which records are being maintained</div>
          </div>
        </div>
      </div>

      <!-- Step 5: Agreement Details -->
      <div class="form-section" data-section="5">
        <div class="section-header">
          <h2>Agreement Information</h2>
          <p>Set the agreement period and add any notes</p>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="agreement_start">Agreement Start Date</label>
            <input type="date" name="agreement_start" id="agreement_start" class="form-control" value="{{ old('agreement_start') }}">
            <div class="field-help">When does the agreement begin?</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="agreement_end">Agreement End Date</label>
            <input type="date" name="agreement_end" id="agreement_end" class="form-control" value="{{ old('agreement_end') }}">
            <div class="field-help">When does the agreement expire?</div>
          </div>

          <div class="form-group full-span">
            <label class="form-label" for="remarks">Additional Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control" placeholder="Any special notes or conditions...">{{ old('remarks') }}</textarea>
            <div class="field-help">Enter any additional information about the shop</div>
          </div>
        </div>
      </div>

      <!-- Navigation and Submit Buttons -->
      <div class="button-group">
        <button type="button" class="btn-nav" id="prevBtn" onclick="changeStep(-1)">← Previous</button>
        <button type="button" class="btn-nav" id="nextBtn" onclick="changeStep(1)">Next →</button>
        <button type="submit" class="btn-primary" id="submitBtn" style="display: none;">✓ Create Shop</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  let currentStep = 1;
  const totalSteps = 5;

  document.addEventListener('DOMContentLoaded', function() {
    updateStepDisplay();
    setupImageUpload();
    setupAutoFillShopName();
  });

  function changeStep(direction) {
    const newStep = currentStep + direction;
    
    if (newStep >= 1 && newStep <= totalSteps) {
      currentStep = newStep;
      updateStepDisplay();
      document.querySelector(`[data-section="${currentStep}"]`).scrollIntoView({ behavior: 'smooth' });
    }
  }

  function updateStepDisplay() {
    // Update sections
    document.querySelectorAll('.form-section').forEach(section => {
      section.classList.remove('active');
    });
    document.querySelector(`[data-section="${currentStep}"]`).classList.add('active');

    // Update step indicators
    document.querySelectorAll('.step-item').forEach((item, index) => {
      item.classList.remove('active');
      if (parseInt(item.getAttribute('data-step')) === currentStep) {
        item.classList.add('active');
      }
    });

    // Update button states
    document.getElementById('prevBtn').disabled = currentStep === 1;
    document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'block';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';
  }

  function setupImageUpload() {
    const imageInput = document.getElementById('image');
    const imageUpload = document.querySelector('.image-upload');

    if (imageInput && imageUpload) {
      imageUpload.addEventListener('dragover', (e) => {
        e.preventDefault();
        imageUpload.style.borderColor = '#00a3ad';
        imageUpload.style.background = '#f0fffe';
      });

      imageUpload.addEventListener('dragleave', () => {
        imageUpload.style.borderColor = '#d1d5db';
        imageUpload.style.background = '#fafafa';
      });

      imageUpload.addEventListener('drop', (e) => {
        e.preventDefault();
        imageUpload.style.borderColor = '#d1d5db';
        imageUpload.style.background = '#fafafa';
        if (e.dataTransfer.files.length) {
          imageInput.files = e.dataTransfer.files;
        }
      });

      imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          const fileName = this.files[0].name;
          const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
          document.querySelector('.image-upload-text').textContent = `✓ ${fileName} (${fileSize}MB)`;
        }
      });
    }
  }

  function setupAutoFillShopName() {
    const busSelect = document.getElementById('bus_stand_id');
    const shopName = document.getElementById('shop_name');

    if (busSelect && shopName) {
      busSelect.addEventListener('change', function() {
        if (this.value) {
          const selectedOption = this.options[this.selectedIndex];
          const busStandName = selectedOption.text.trim();
          if (busStandName && busStandName !== '-- Select Bus Stand --') {
            shopName.value = busStandName;
          }
        }
      });
    }
  }
</script>
@endpush
