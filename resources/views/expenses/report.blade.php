@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'Expense Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Expense Report</li>
@endsection

@push('styles')
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
                font-size: 12px !important;
            }
            /* Hide sidebar, navbar, footer, buttons, and filter card */
            .app-header,
            .app-sidebar,
            .app-footer,
            .breadcrumb,
            .app-content-header,
            #filters-card,
            .action-buttons,
            .no-print {
                display: none !important;
            }
            .app-main {
                margin-left: 0 !important;
                padding: 0 !important;
                margin-top: 0 !important;
            }
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
            }
            .card {
                border: 0 !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .card-body {
                padding: 0 !important;
            }
            /* Show printable title/header */
            #print-header {
                display: block !important;
                margin-bottom: 25px;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            table th, table td {
                border: 1px solid #000 !important;
                padding: 6px !important;
            }
        }
        /* Header is hidden by default in normal browser view */
        #print-header {
            display: none;
        }
    </style>
@endpush

@section('content')
    <!-- Print Header -->
    <div id="print-header" class="text-center">
        <h2 class="fw-bold mb-1">Masjid Construction Project</h2>
        <h4 class="text-secondary mb-3">Mosque Construction Expense Report</h4>
        <p class="mb-0">
            <strong>Period:</strong> 
            {{ request('start_date') ? Carbon\Carbon::parse(request('start_date'))->format('d-M-Y') : 'Beginning' }} 
            to 
            {{ request('end_date') ? Carbon\Carbon::parse(request('end_date'))->format('d-M-Y') : 'Today' }}
        </p>
        @if (request('category_id') || request('paid_to'))
            <p class="mb-0 small text-muted">
                @if (request('category_id') && $categories->firstWhere('id', request('category_id')))
                    <strong>Category:</strong> {{ $categories->firstWhere('id', request('category_id'))->name }} |
                @endif
                @if (request('paid_to'))
                    <strong>Paid To:</strong> {{ request('paid_to') }}
                @endif
            </p>
        @endif
        <hr style="border-top: 2px solid #000; margin-top: 15px;">
    </div>

    <!-- Filters Card -->
    <div class="card mb-4 border-0 shadow-sm" id="filters-card">
        <div class="card-header bg-transparent py-3 border-bottom-0">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-funnel-fill me-2 text-primary"></i> Report Filters
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('expenses.report') }}" method="GET" class="row g-3">
                <!-- Date Range -->
                <div class="col-md-3">
                    <label for="start_date" class="form-label small fw-bold text-muted">From Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label small fw-bold text-muted">To Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>

                <!-- Category -->
                <div class="col-md-3">
                    <label for="category_id" class="form-label small fw-bold text-muted">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Paid To / Supplier -->
                <div class="col-md-3">
                    <label for="paid_to" class="form-label small fw-bold text-muted">Paid To / Payee</label>
                    <select class="form-select" id="paid_to" name="paid_to">
                        <option value="">All Suppliers/Contractors</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier }}" {{ request('paid_to') == $supplier ? 'selected' : '' }}>
                                {{ $supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Form Buttons -->
                <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
                    @if (request()->anyFilled(['start_date', 'end_date', 'category_id', 'paid_to']))
                        <a href="{{ route('expenses.report') }}" class="btn btn-outline-secondary">
                            Reset Filters
                        </a>
                    @endif
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-filter"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Output Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-3 border-bottom-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i> Generated Report
            </h5>
            <div class="action-buttons d-flex gap-2 flex-wrap">
                {{-- Print: open all records in a new window and trigger print dialog --}}
                <a href="{{ request()->fullUrlWithQuery(['all' => '1']) }}" 
                   target="_blank" 
                   onclick="setTimeout(() => { window.frames[this.target] ? window.frames[this.target].print() : ''; }, 800); return true;"
                   class="btn btn-sm btn-outline-dark"
                   id="print-btn">
                    <i class="bi bi-printer me-1"></i> Print Report
                </a>
                {{-- Export CSV: loads all records then triggers download --}}
                <a href="{{ request()->fullUrlWithQuery(['all' => '1', 'export' => 'csv']) }}"
                   class="btn btn-sm btn-outline-success"
                   id="export-btn">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel Export
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="report-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                            <th class="pe-3">Paid To</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="ps-3">{{ $expense->expense_date->format('d-M-Y') }}</td>
                                <td>{{ $expense->category->name }}</td>
                                <td>{{ $expense->description }}</td>
                                <td class="text-center">
                                    {{ $expense->quantity ? (float) $expense->quantity . ' ' . $expense->unit : '-' }}
                                </td>
                                <td class="text-end">
                                    {{ $expense->rate ? 'Rs. ' . number_format($expense->rate, 2) : '-' }}
                                </td>
                                <td class="text-end fw-bold text-dark">Rs. {{ number_format($expense->amount, 2) }}</td>
                                <td class="pe-3">{{ $expense->paid_to }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-light"></i>
                                    No records found matching report criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($totalCount > 0)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="5" class="text-end ps-3">Total Construction Expense ({{ $totalCount }} records):</td>
                                <td class="text-end text-primary fs-5">Rs. {{ number_format($totalExpense, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
        {{-- Pagination footer (only shown in normal paginated mode, not in ?all=1 mode) --}}
        @if (!request()->boolean('all') && $expenses instanceof \Illuminate\Pagination\LengthAwarePaginator && ($expenses->hasPages() || $expenses->total() > 0))
            <div class="card-footer bg-transparent py-3 border-top no-print">
                <div class="row align-items-center">
                    <div class="col-sm-5">
                        <p class="mb-0 text-muted small">
                            Showing
                            <strong>{{ $expenses->firstItem() ?? 0 }}</strong>
                            to
                            <strong>{{ $expenses->lastItem() ?? 0 }}</strong>
                            of
                            <strong>{{ $totalCount }}</strong>
                            records.
                            <span class="text-warning fw-semibold">
                                Total across all pages: Rs. {{ number_format($totalExpense, 2) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-sm-7 d-flex justify-content-sm-end">
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

{{-- No JavaScript export needed - CSV export is now handled server-side via ?export=csv --}}
