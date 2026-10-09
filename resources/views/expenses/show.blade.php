@extends('layouts.app')

@section('title', 'Expense Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}" class="text-decoration-none">Expenses</a></li>
    <li class="breadcrumb-item active" aria-current="page">Details</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="row g-4">
                <!-- Details Card -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm mb-4 h-100">
                        <div class="card-header bg-transparent py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="bi bi-info-circle-fill me-2 text-info"></i> Expense Attributes
                            </h5>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fs-6">
                                ID: #{{ $expense->id }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <tbody>
                                        <tr>
                                            <th class="bg-light" style="width: 35%;">Expense Date</th>
                                            <td class="fw-semibold text-dark">{{ $expense->expense_date->format('d-F-Y (l)') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Category</th>
                                            <td>
                                                <span class="badge bg-primary px-3 py-2 fs-6">
                                                    {{ $expense->category->name }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Item / Description</th>
                                            <td class="text-wrap">{{ $expense->description }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Quantity / Unit</th>
                                            <td>
                                                @if ($expense->quantity)
                                                    {{ (float) $expense->quantity }} {{ $expense->unit }}
                                                @else
                                                    <span class="text-muted">Not applicable (Fixed amount)</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Rate / Unit Price</th>
                                            <td>
                                                @if ($expense->rate)
                                                    Rs. {{ number_format($expense->rate, 2) }}
                                                @else
                                                    <span class="text-muted">Not applicable</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light text-primary">Total Amount Paid</th>
                                            <td class="fs-5 fw-bold text-primary">Rs. {{ number_format($expense->amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Paid To / Supplier</th>
                                            <td class="fw-semibold">{{ $expense->paid_to }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Payment Method</th>
                                            <td>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                                                    {{ $expense->payment_method }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Reference Number</th>
                                            <td>{{ $expense->reference_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Recorded By</th>
                                            <td>
                                                <span class="text-muted">{{ $expense->creator->name }}</span>
                                                <small class="d-block text-black-50">on {{ $expense->created_at->format('d-M-Y H:i A') }}</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if ($expense->notes)
                                <div class="mt-4">
                                    <h6 class="fw-bold text-muted mb-2">
                                        <i class="bi bi-chat-left-text-fill me-1"></i> Construction Notes:
                                    </h6>
                                    <div class="p-3 bg-light border rounded text-wrap text-secondary" style="white-space: pre-wrap;">{{ $expense->notes }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent py-3 border-top d-flex justify-content-between">
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back to List
                            </a>
                            <div>
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-primary me-2">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>
                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this expense record? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attachment Card -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm mb-4 h-100">
                        <div class="card-header bg-transparent py-3 border-bottom-0">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="bi bi-paperclip me-2 text-primary"></i> Receipt / Bill Attachment
                            </h5>
                        </div>
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                            @if ($expense->attachment)
                                @php
                                    $extension = pathinfo($expense->attachment, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                                    $attachmentUrl = asset('storage/' . $expense->attachment);
                                @endphp

                                @if ($isImage)
                                    <div class="mb-3 w-100">
                                        <img src="{{ $attachmentUrl }}" class="img-fluid rounded border shadow-sm mx-auto d-block" style="max-height: 400px; object-fit: contain;" alt="Receipt Attachment">
                                    </div>
                                    <a href="{{ $attachmentUrl }}" target="_blank" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-fullscreen me-1"></i> View Full Screen
                                    </a>
                                @else
                                    <div class="p-5 border rounded bg-light mb-3 w-100">
                                        <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3 fw-bold">PDF Document</h5>
                                        <p class="text-muted small">{{ basename($expense->attachment) }}</p>
                                    </div>
                                    <a href="{{ $attachmentUrl }}" target="_blank" class="btn btn-danger w-100">
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Open & Download PDF
                                    </a>
                                @endif
                            @else
                                <div class="p-5 border border-dashed rounded bg-light text-muted w-100 text-center">
                                    <i class="bi bi-file-earmark-x fs-1 d-block mb-3 text-light"></i>
                                    No receipt or bill image uploaded for this expense.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
