@extends('layouts.app')

@section('title', 'Add Construction Expense')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}" class="text-decoration-none">Expenses</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Expense</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent py-3 border-bottom-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-plus-circle-fill me-2 text-success"></i> Record New Expense
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3 mb-4">
                            <!-- Expense Date -->
                            <div class="col-md-4">
                                <label for="expense_date" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="col-md-4">
                                <label for="expense_category_id" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('expense_category_id') is-invalid @enderror" id="expense_category_id" name="expense_category_id" required>
                                    <option value="" selected disabled>Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
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
                                <input type="text" class="form-control @error('paid_to') is-invalid @enderror" id="paid_to" name="paid_to" value="{{ old('paid_to') }}" placeholder="e.g. ABC Steel Corp, Mason Akram" required>
                                @error('paid_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Description / Item -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Item / Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" placeholder="What was purchased or work done? e.g. 5000 bricks, foundation demolition work" required>
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
                                        <input type="number" step="0.01" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" placeholder="e.g. 100, 5.5">
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Unit -->
                                    <div class="col-md-4">
                                        <label for="unit" class="form-label small fw-semibold text-muted">Unit</label>
                                        <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit') }}" placeholder="e.g. Bags, Tons, Pieces, Trolley, KG">
                                        @error('unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Rate -->
                                    <div class="col-md-4">
                                        <label for="rate" class="form-label small fw-semibold text-muted">Rate / Unit Price (Rs.)</label>
                                        <input type="number" step="0.01" class="form-control @error('rate') is-invalid @enderror" id="rate" name="rate" value="{{ old('rate') }}" placeholder="e.g. 1500, 18">
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
                                    <input type="number" step="0.01" class="form-control fw-bold @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Enter total amount" required>
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
                                    <option value="Cash" {{ old('payment_method', 'Cash') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Bank" {{ old('payment_method') == 'Bank' ? 'selected' : '' }}>Bank</option>
                                    <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="Online Transfer" {{ old('payment_method') == 'Online Transfer' ? 'selected' : '' }}>Online Transfer</option>
                                    <option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Reference Number -->
                            <div class="col-md-4">
                                <label for="reference_number" class="form-label fw-semibold">Reference No. (Cheque / Slip No.)</label>
                                <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g. Cheque # 120489">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Notes -->
                            <div class="col-md-8">
                                <label for="notes" class="form-label fw-semibold">Construction-related Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Any specific details, contractor terms, structural use remarks...">{{ old('notes') }}</textarea>
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
                            </div>
                        </div>

                        <div class="row g-3 pt-3 border-top">
                            <div class="col-12 d-flex justify-content-between">
                                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-save me-1"></i> Save Expense
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

            // Run once on load (if form is validation-failed with old input)
            calculateAmount();
        });
    </script>
@endpush
