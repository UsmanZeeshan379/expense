@extends('layouts.app')

@section('title', 'Edit Construction Expense')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}" class="text-decoration-none">Expenses</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Expense</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent py-3 border-bottom-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-pencil-square me-2 text-primary"></i> Edit Expense Record
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3 mb-4">
                            <!-- Expense Date -->
                            <div class="col-md-4">
                                <label for="expense_date" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="col-md-4">
                                <label for="expense_category_id" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('expense_category_id') is-invalid @enderror" id="expense_category_id" name="expense_category_id" required>
                                    <option value="" disabled>Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Paid To / Supplier -->
                            <div class="col-md-4">
                                <label for="paid_to" class="form-label fw-semibold">Paid To / Supplier / Contractor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('paid_to') is-invalid @enderror" id="paid_to" name="paid_to" value="{{ old('paid_to', $expense->paid_to) }}" placeholder="e.g. ABC Steel Corp, Mason Akram" required>
                                @error('paid_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Description / Item -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Item / Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $expense->description) }}" placeholder="What was purchased or work done? e.g. 5000 bricks, demolition" required>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Quantity and Rate Row -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <h6 class="fw-bold mb-2 text-secondary">
                                            <i class="bi bi-calculator me-1"></i> Quantity-based Pricing (Optional)
                                        </h6>
                                        <p class="text-muted small mb-3">If you leave quantity and rate empty, you can enter the fixed Total Amount directly below.</p>
                                    </div>
                                    
                                    <!-- Quantity -->
                                    <div class="col-md-4">
                                        <label for="quantity" class="form-label small fw-semibold text-muted">Quantity</label>
                                        <input type="number" step="0.01" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', (float) $expense->quantity ?: '') }}" placeholder="e.g. 100, 5.5">
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Unit -->
                                    <div class="col-md-4">
                                        <label for="unit" class="form-label small fw-semibold text-muted">Unit</label>
                                        <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', $expense->unit) }}" placeholder="e.g. Bags, Tons, Pieces, Trolley, KG">
                                        @error('unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Rate -->
                                    <div class="col-md-4">
                                        <label for="rate" class="form-label small fw-semibold text-muted">Rate / Unit Price (Rs.)</label>
                                        <input type="number" step="0.01" class="form-control @error('rate') is-invalid @enderror" id="rate" name="rate" value="{{ old('rate', (float) $expense->rate ?: '') }}" placeholder="e.g. 1500, 18">
                                        @error('rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Total Amount -->
                            <div class="col-md-4">
                                <label for="amount" class="form-label fw-semibold">Total Amount (Rs.) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" step="0.01" class="form-control fw-bold @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $expense->amount) }}" placeholder="Enter total amount" required>
                                </div>
                                <span class="small text-info mt-1 d-block" id="amount_calc_help" style="display: none;">
                                    <i class="bi bi-info-circle-fill"></i> Auto-calculated (Qty × Rate). Clear Qty or Rate to edit.
                                </span>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div class="col-md-4">
                                <label for="payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                    <option value="Cash" {{ old('payment_method', $expense->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Bank" {{ old('payment_method', $expense->payment_method) == 'Bank' ? 'selected' : '' }}>Bank</option>
                                    <option value="Cheque" {{ old('payment_method', $expense->payment_method) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="Online Transfer" {{ old('payment_method', $expense->payment_method) == 'Online Transfer' ? 'selected' : '' }}>Online Transfer</option>
                                    <option value="Other" {{ old('payment_method', $expense->payment_method) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Reference Number -->
                            <div class="col-md-4">
                                <label for="reference_number" class="form-label fw-semibold">Reference No. (Cheque / Slip No.)</label>
                                <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" name="reference_number" value="{{ old('reference_number', $expense->reference_number) }}" placeholder="e.g. Cheque # 120489">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Notes -->
                            <div class="col-md-8">
                                <label for="notes" class="form-label fw-semibold">Construction-related Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Any specific details, contractor terms, structural use remarks...">{{ old('notes', $expense->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Attachment -->
                            <div class="col-md-4">
                                <label for="attachment" class="form-label fw-semibold">Receipt / Bill Attachment</label>
                                <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf">
                                <span class="small text-muted mt-1 d-block">Supported: JPG, JPEG, PNG, PDF. Max Size: 5MB</span>
                                @error('attachment')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @if ($expense->attachment)
                                    <div class="mt-3 p-2 border border-info-subtle bg-info-subtle rounded d-flex align-items-center justify-content-between">
                                        <span class="small text-truncate w-75 text-info-emphasis fw-semibold">
                                            <i class="bi bi-file-earmark-check-fill me-1 text-info"></i> {{ basename($expense->attachment) }}
                                        </span>
                                        <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye-fill"></i> View
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row g-3 pt-3 border-top">
                            <div class="col-12 d-flex justify-content-between">
                                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Update Expense
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const qtyInput = document.getElementById('quantity');
            const rateInput = document.getElementById('rate');
            const amountInput = document.getElementById('amount');
            const calcHelp = document.getElementById('amount_calc_help');

            function calculateAmount() {
                const qty = parseFloat(qtyInput.value);
                const rate = parseFloat(rateInput.value);

                if (!isNaN(qty) && !isNaN(rate) && qty > 0 && rate > 0) {
                    amountInput.value = (qty * rate).toFixed(2);
                    amountInput.readOnly = true;
                    amountInput.classList.add('bg-light');
                    calcHelp.style.display = 'block';
                } else {
                    amountInput.readOnly = false;
                    amountInput.classList.remove('bg-light');
                    calcHelp.style.display = 'none';
                }
            }

            qtyInput.addEventListener('input', calculateAmount);
            qtyInput.addEventListener('change', calculateAmount);
            rateInput.addEventListener('input', calculateAmount);
            rateInput.addEventListener('change', calculateAmount);

            // Run once on load (to setup initial input locks if populated)
            calculateAmount();
        });
    </script>
@endpush
