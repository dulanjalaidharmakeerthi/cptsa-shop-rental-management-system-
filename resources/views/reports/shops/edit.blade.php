@extends('layouts.app')

@section('content')
<div class="edit-shop-container">
    <div class="edit-header">
        <a href="{{ route('reports.shops.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Shops
        </a>
        <h1><i class="fas fa-edit"></i> Edit Shop Details</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('reports.shops.update', $shop->id) }}" method="POST" class="edit-form" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="form-section">
                <h2 class="section-title"><i class="fas fa-store"></i> Shop Information</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="shop_number">Shop Number</label>
                        <input type="text" id="shop_number" class="form-control" 
                               value="{{ $shop->shop_number }}" disabled>
                        <small class="text-muted">Cannot be edited</small>
                    </div>

                    <div class="form-group">
                        <label for="shop_name">Shop Name *</label>
                        <input type="text" id="shop_name" name="shop_name" class="form-control @error('shop_name') is-invalid @enderror" 
                               value="{{ old('shop_name', $shop->shop_name) }}" required>
                        @error('shop_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title"><i class="fas fa-user-circle"></i> Owner Information</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="shop_owner_name">Owner Name *</label>
                        <input type="text" id="shop_owner_name" name="shop_owner_name" class="form-control @error('shop_owner_name') is-invalid @enderror" 
                               value="{{ old('shop_owner_name', $shop->shop_owner_name) }}" required>
                        @error('shop_owner_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="shop_owner_nic">Owner NIC *</label>
                        <input type="text" id="shop_owner_nic" name="shop_owner_nic" class="form-control @error('shop_owner_nic') is-invalid @enderror" 
                               value="{{ old('shop_owner_nic', $shop->shop_owner_nic) }}" required>
                        @error('shop_owner_nic')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="shop_owner_phone">Phone Number *</label>
                        <input type="tel" id="shop_owner_phone" name="shop_owner_phone" class="form-control @error('shop_owner_phone') is-invalid @enderror" 
                               value="{{ old('shop_owner_phone', $shop->shop_owner_phone) }}" required>
                        @error('shop_owner_phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="shop_owner_address">Address *</label>
                        <textarea id="shop_owner_address" name="shop_owner_address" class="form-control @error('shop_owner_address') is-invalid @enderror" 
                                  rows="2" required>{{ old('shop_owner_address', $shop->shop_owner_address) }}</textarea>
                        @error('shop_owner_address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="owner_change_reason">Owner Change Reason</label>
                        <textarea id="owner_change_reason" name="owner_change_reason" class="form-control @error('owner_change_reason') is-invalid @enderror" rows="2" placeholder="Expired agreement or special reason (optional)">{{ old('owner_change_reason') }}</textarea>
                        @error('owner_change_reason')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Location Information</h2>
                
                <div class="location-instructions" style="background: linear-gradient(135deg, #e3f2fd, #f3e5f5); border-left: 5px solid #667eea; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="margin: 0 0 10px 0; color: #667eea;">📍 Location Options:</h4>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #555;">
                        <li><strong>Regular Address:</strong> Enter a street address (e.g., "123 Main St, Colombo")</li>
                        <li><strong>KML/Google Maps Link:</strong> Paste a Google My Maps link or KML URL directly</li>
                        <li>Examples: <code style="background: white; padding: 2px 6px; border-radius: 4px;">https://mymaps.google.com/maps/d/...</code></li>
                    </ul>
                </div>

                <div class="form-row">
                    <div class="form-group full-span">
                        <label for="location">Location (Address or KML Link) <span style="color: #667eea; font-weight: bold;">🗺️</span></label>
                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" 
                               placeholder="e.g., Bogambara Shops or https://mymaps.google.com/maps/d/..."
                               value="{{ old('location', $shop->location) }}">
                        <small class="text-muted" style="display: block; margin-top: 8px;">
                            💡 Tip: Paste a Google My Maps KML link to show location on interactive map
                        </small>
                        @error('location')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="shop_address">Shop Address</label>
                        <textarea id="shop_address" name="shop_address" class="form-control @error('shop_address') is-invalid @enderror" 
                                  placeholder="Complete shop address"
                                  rows="2">{{ old('shop_address', $shop->shop_address) }}</textarea>
                        @error('shop_address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="image_path">Main Image</label>
                        <input type="file" id="image_path" name="image_path" accept="image/*" class="form-control @error('image_path') is-invalid @enderror" />
                        @if($shop->image_path)
                            <div style="margin-top:6px;">
                                <img src="{{ $shop->getAttachmentUrl($shop->image_path) }}" style="max-width:120px;height:auto;" />
                                <div style="margin-top:6px;">
                                    <label style="font-size:13px;line-height:1.3;"><input type="checkbox" name="remove_image_path" value="1"> Remove this image</label>
                                </div>
                            </div>
                        @endif
                        @error('image_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image1_path">Additional Image 1</label>
                        <input type="file" id="image1_path" name="image1_path" accept="image/*" class="form-control @error('image1_path') is-invalid @enderror" />
                        @if($shop->image1_path)
                            <div style="margin-top:6px;">
                                <img src="{{ $shop->getAttachmentUrl($shop->image1_path) }}" style="max-width:120px;height:auto;" />
                                <div style="margin-top:6px;">
                                    <label style="font-size:13px;line-height:1.3;"><input type="checkbox" name="remove_image1_path" value="1"> Remove this image</label>
                                </div>
                            </div>
                        @endif
                        @error('image1_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="image2_path">Additional Image 2</label>
                        <input type="file" id="image2_path" name="image2_path" accept="image/*" class="form-control @error('image2_path') is-invalid @enderror" />
                        @if($shop->image2_path)
                            <div style="margin-top:6px;">
                                <img src="{{ $shop->getAttachmentUrl($shop->image2_path) }}" style="max-width:120px;height:auto;" />
                                <div style="margin-top:6px;">
                                    <label style="font-size:13px;line-height:1.3;"><input type="checkbox" name="remove_image2_path" value="1"> Remove this image</label>
                                </div>
                            </div>
                        @endif
                        @error('image2_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image3_path">Additional Image 3</label>
                        <input type="file" id="image3_path" name="image3_path" accept="image/*" class="form-control @error('image3_path') is-invalid @enderror" />
                        @if($shop->image3_path)
                            <div style="margin-top:6px;">
                                <img src="{{ $shop->getAttachmentUrl($shop->image3_path) }}" style="max-width:120px;height:auto;" />
                                <div style="margin-top:6px;">
                                    <label style="font-size:13px;line-height:1.3;"><input type="checkbox" name="remove_image3_path" value="1"> Remove this image</label>
                                </div>
                            </div>
                        @endif
                        @error('image3_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-span">
                        <label for="document_path">Documentation File</label>
                        <input type="file" id="document_path" name="document_path" accept=".pdf,.doc,.docx,image/*" class="form-control @error('document_path') is-invalid @enderror" />
                        @if($shop->document_path)
                            <div style="margin-top:6px;">
                                <a href="{{ $shop->getAttachmentUrl($shop->document_path) }}" target="_blank" rel="noopener noreferrer">View current document</a>
                                <div style="margin-top:6px;">
                                    <label style="font-size:13px;line-height:1.3;"><input type="checkbox" name="remove_document_path" value="1"> Delete current document</label>
                                </div>
                            </div>
                        @endif
                        @error('document_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="bus_stand_id">Bus Stand</label>
                        <select id="bus_stand_id" name="bus_stand_id" class="form-control @error('bus_stand_id') is-invalid @enderror">
                            <option value="">-- Select Bus Stand --</option>
                            @foreach($busStands as $busStand)
                                <option value="{{ $busStand->id }}" 
                                        {{ old('bus_stand_id', $shop->bus_stand_id) == $busStand->id ? 'selected' : '' }}>
                                    {{ $busStand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bus_stand_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title"><i class="fas fa-money-bill-wave"></i> Financial Information</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="monthly_rent">Monthly Rent (Rs) *</label>
                        <input type="number" id="monthly_rent" name="monthly_rent" class="form-control @error('monthly_rent') is-invalid @enderror" 
                               step="0.01" value="{{ old('monthly_rent', $shop->monthly_rent) }}" required>
                        @error('monthly_rent')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="rate">Rate (%)</label>
                        <input type="number" id="rate" name="rate" class="form-control @error('rate') is-invalid @enderror" 
                               step="0.01" value="{{ old('rate', $shop->rate) }}">
                        @error('rate')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="advance_payment">Advance Payment (Rs)</label>
                        <input type="number" id="advance_payment" name="advance_payment" class="form-control @error('advance_payment') is-invalid @enderror" 
                               step="0.01" value="{{ old('advance_payment', $shop->advance_payment) }}">
                        @error('advance_payment')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="opening_balance">Opening Balance (Rs)</label>
                        <input type="number" id="opening_balance" name="opening_balance" class="form-control @error('opening_balance') is-invalid @enderror" 
                               step="0.01" value="{{ old('opening_balance', $shop->opening_balance) }}">
                        @error('opening_balance')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title"><i class="fas fa-handshake"></i> Agreement Information</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="agreement_duration">Agreement Duration (months)</label>
                        <input type="number" id="agreement_duration" name="agreement_duration" class="form-control @error('agreement_duration') is-invalid @enderror" 
                               value="{{ old('agreement_duration', $shop->agreement_duration) }}">
                        @error('agreement_duration')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="agreement_start">Agreement Start Date</label>
                        <input type="date" id="agreement_start" name="agreement_start" class="form-control @error('agreement_start') is-invalid @enderror" 
                               value="{{ old('agreement_start', $shop->agreement_start?->format('Y-m-d')) }}">
                        @error('agreement_start')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="agreement_end">Agreement End Date</label>
                        <input type="date" id="agreement_end" name="agreement_end" class="form-control @error('agreement_end') is-invalid @enderror" 
                               value="{{ old('agreement_end', $shop->agreement_end?->format('Y-m-d')) }}">
                        @error('agreement_end')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title"><i class="fas fa-sticky-note"></i> Remarks</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="remarks">Remarks (Optional)</label>
                        <textarea id="remarks" name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                  rows="4" placeholder="Add any additional remarks or notes about this shop">{{ old('remarks', $shop->remarks) }}</textarea>
                        @error('remarks')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('reports.shops.index') }}" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .edit-shop-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #fff 100%);
        min-height: 100vh;
        padding: 30px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .edit-header {
        margin-bottom: 30px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        color: #764ba2;
        gap: 12px;
    }

    .edit-header h1 {
        font-size: 28px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .edit-header h1 i {
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Alert */
    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        gap: 12px;
        animation: slideDown 0.3s ease;
        border: none;
    }

    .alert-danger {
        background: #fee;
        color: #721c24;
    }

    .alert-danger i {
        margin-top: 2px;
    }

    .alert ul {
        margin: 10px 0 0 0;
        padding-left: 20px;
    }

    .alert li {
        margin-bottom: 5px;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Form Card */
    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .edit-form {
        padding: 30px;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section:last-of-type {
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #667eea;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group .form-control {
        padding: 12px 14px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control:disabled {
        background: #f5f7fa;
        color: #999;
        cursor: not-allowed;
    }

    .form-control textarea {
        resize: vertical;
        font-family: inherit;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .error-message::before {
        content: '⚠';
    }

    .text-muted {
        color: #999;
        font-size: 12px;
        margin-top: 6px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 25px;
        border-top: 1px solid #e9ecef;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-cancel {
        background: #f0f2f5;
        color: #666;
    }

    .btn-cancel:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }

    .btn-save {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .edit-shop-container {
            padding: 15px;
        }

        .edit-form {
            padding: 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection
