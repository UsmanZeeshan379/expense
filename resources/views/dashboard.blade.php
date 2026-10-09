@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <!-- Total Construction Expense -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary shadow-sm p-3 mb-4 rounded position-relative overflow-hidden">
                <div class="inner">
                    <h3 class="fw-bold mb-1">Rs. {{ number_format($totalExpenses, 2) }}</h3>
                    <p class="mb-0 text-white-50">Total Construction Expense</p>
                </div>
                <div class="icon position-absolute top-0 end-0 p-3 fs-1 opacity-25">
                    <i class="bi bi-bank"></i>
                </div>
            </div>
        </div>

        <!-- Today's Expense -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success shadow-sm p-3 mb-4 rounded position-relative overflow-hidden">
                <div class="inner">
                    <h3 class="fw-bold mb-1">Rs. {{ number_format($todayExpenses, 2) }}</h3>
                    <p class="mb-0 text-white-50">Today's Expense</p>
                </div>
                <div class="icon position-absolute top-0 end-0 p-3 fs-1 opacity-25">
                    <i class="bi bi-calendar-event"></i>
                </div>
            </div>
        </div>

        <!-- This Month's Expense -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning text-white shadow-sm p-3 mb-4 rounded position-relative overflow-hidden">
                <div class="inner text-white">
                    <h3 class="fw-bold mb-1 text-white">Rs. {{ number_format($monthExpenses, 2) }}</h3>
                    <p class="mb-0 text-white-50 text-white">This Month's Expense</p>
                </div>
                <div class="icon position-absolute top-0 end-0 p-3 fs-1 opacity-25 text-white">
                    <i class="bi bi-calendar3"></i>
                </div>
            </div>
        </div>

        <!-- Number of Expenses -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-info text-white shadow-sm p-3 mb-4 rounded position-relative overflow-hidden">
                <div class="inner text-white">
                    <h3 class="fw-bold mb-1 text-white">{{ $expenseCount }}</h3>
                    <p class="mb-0 text-white-50 text-white">Total Expenses Recorded</p>
                </div>
                <div class="icon position-absolute top-0 end-0 p-3 fs-1 opacity-25 text-white">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main row -->
    <div class="row">
        <!-- Monthly Expense Chart -->
        <div class="col-lg-7">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header border-bottom-0 bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-bar-chart-fill me-2 text-info"></i> Monthly Construction Expense
                    </h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="col-lg-5">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header border-bottom-0 bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-clock-history me-2 text-primary"></i> Recent Expenses
                    </h5>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Category</th>
                                    <th class="text-end pe-3">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentExpenses as $expense)
                                    <tr>
                                        <td class="ps-3">{{ $expense->expense_date->format('d-M-Y') }}</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                {{ $expense->category->name }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3 fw-semibold">Rs. {{ number_format($expense->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No expenses recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Expense Amount (Rs.)',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: 'rgba(13, 110, 253, 0.75)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rs. ' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rs. ' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
