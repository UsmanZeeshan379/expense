@extends('layouts.app')

@section('title', 'All Construction Expenses')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Expenses</li>
@endsection

@section('content')
    <!-- Filters & Search Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-transparent py-3 border-bottom-0">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-filter-left me-2 text-primary"></i> Search & Filters
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('expenses.index') }}" method="GET" class="row g-3">
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
                <div class="col-md-2">
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

                <!-- Keyword Search -->
                <div class="col-md-4">
                    <label for="search" class="form-label small fw-bold text-muted">Search Keyword</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search description, supplier, notes...">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        @if (request()->anyFilled(['start_date', 'end_date', 'category_id', 'search']))
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Sorting Options -->
                <div class="col-12 mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small fw-bold text-muted">Sort By:</span>
                        <select name="sort_by" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                            <option value="expense_date" {{ request('sort_by') == 'expense_date' ? 'selected' : '' }}>Expense Date</option>
                            <option value="amount" {{ request('sort_by') == 'amount' ? 'selected' : '' }}>Amount</option>
                            <option value="description" {{ request('sort_by') == 'description' ? 'selected' : '' }}>Description</option>
                            <option value="paid_to" {{ request('sort_by') == 'paid_to' ? 'selected' : '' }}>Supplier/Payee</option>
                        </select>
                        <select name="sort_order" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                            <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                    <div class="text-muted small">
                        Showing
                        <strong>{{ $expenses->firstItem() ?? 0 }}</strong>
                        –
                        <strong>{{ $expenses->lastItem() ?? 0 }}</strong>
                        of
                        <strong>{{ $expenses->total() }}</strong>
                        records
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-3 border-bottom-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-wallet2 me-2 text-primary"></i> Expense Register
            </h5>
            <div class="d-flex align-items-center gap-3">
                <span class="fs-6 text-muted">Filtered Total: <strong class="text-primary">Rs. {{ number_format($filteredTotal, 2) }}</strong></span>
                <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg me-1"></i> Add Expense
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-center">Qty / Unit</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Total Amount</th>
                            <th>Paid To</th>
                            <th class="text-end pe-3" style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="ps-3 text-nowrap fw-semibold">
                                    {{ $expense->expense_date->format('d-M-Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        {{ $expense->category->name }}
                                    </span>
                                </td>
                                <td class="text-truncate" style="max-width: 200px;" title="{{ $expense->description }}">
                                    {{ $expense->description }}
                                </td>
                                <td class="text-center text-nowrap">
                                    @if ($expense->quantity)
                                        {{ (float) $expense->quantity }} <span class="small text-muted">{{ $expense->unit }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    @if ($expense->rate)
                                        Rs. {{ number_format($expense->rate, 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap fw-bold text-dark">
                                    Rs. {{ number_format($expense->amount, 2) }}
                                </td>
                                <td class="text-truncate" style="max-width: 150px;" title="{{ $expense->paid_to }}">
                                    {{ $expense->paid_to }}
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <!-- View button -->
                                    <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-sm btn-outline-info me-1" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <!-- Edit button -->
                                    <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Expense">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <!-- Delete button -->
                                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this expense record? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Expense">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-light"></i>
                                    No expense records found matching filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($expenses->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="ps-3 fw-bold text-end">Grand Total (Filtered):</td>
                                <td class="text-end fw-bold text-primary fs-5">Rs. {{ number_format($filteredTotal, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
        @if ($expenses->hasPages() || $expenses->total() > 0)
            <div class="card-footer bg-transparent py-3 border-top">
                <div class="row align-items-center">
                    <div class="col-sm-5">
                        <p class="mb-0 text-muted small">
                            Showing
                            <strong>{{ $expenses->firstItem() ?? 0 }}</strong>
                            to
                            <strong>{{ $expenses->lastItem() ?? 0 }}</strong>
                            of
                            <strong>{{ $expenses->total() }}</strong>
                            expense records
                            @if (request()->anyFilled(['start_date','end_date','category_id','search']))
                                <span class="text-info">(filtered)</span>
                            @endif
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
