<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = ExpenseCategory::orderBy('name', 'asc')->get();

        // Build query
        $query = Expense::with('category', 'creator');

        // Apply Date Filters
        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->input('end_date'));
        }

        // Apply Category Filter
        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->input('category_id'));
        }

        // Apply Keyword Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('paid_to', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Apply Sorting
        $sortBy = $request->input('sort_by', 'expense_date');
        $sortOrder = $request->input('sort_order', 'desc');
        
        // Validate sorting fields
        $allowedSorts = ['expense_date', 'amount', 'description', 'paid_to'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('expense_date', 'desc');
        }

        // Calculate total BEFORE paginating (clone ensures the original query is reusable)
        $filteredTotal = (clone $query)->sum('amount');

        $expenses = $query->paginate(20)->withQueryString();

        return view('expenses.index', compact('expenses', 'categories', 'filteredTotal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ExpenseCategory::orderBy('name', 'asc')->get();
        return view('expenses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'quantity' => 'nullable|numeric|min:0.01',
            'unit' => 'nullable|string|max:50',
            'rate' => 'nullable|numeric|min:0.01',
            'amount' => 'nullable|numeric|min:0.01',
            'paid_to' => 'required|string|max:255',
            'payment_method' => 'required|in:Cash,Bank,Cheque,Online Transfer,Other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $qty = $request->input('quantity');
            $rate = $request->input('rate');
            $amt = $request->input('amount');

            if ($qty && !$rate) {
                $validator->errors()->add('rate', 'The rate field is required when quantity is provided.');
            }
            if ($rate && !$qty) {
                $validator->errors()->add('quantity', 'The quantity field is required when rate is provided.');
            }
            if (!$qty && !$rate && !$amt) {
                $validator->errors()->add('amount', 'The amount field is required when quantity and rate are not provided.');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Calculate amount if qty and rate exist
        if ($request->filled('quantity') && $request->filled('rate')) {
            $validated['amount'] = (float)$request->input('quantity') * (float)$request->input('rate');
        }

        // Upload attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('attachments', $filename, 'public');
            $validated['attachment'] = $path;
        }

        $validated['created_by'] = Auth::id();

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $expense->load('category', 'creator');
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::orderBy('name', 'asc')->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $rules = [
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'quantity' => 'nullable|numeric|min:0.01',
            'unit' => 'nullable|string|max:50',
            'rate' => 'nullable|numeric|min:0.01',
            'amount' => 'nullable|numeric|min:0.01',
            'paid_to' => 'required|string|max:255',
            'payment_method' => 'required|in:Cash,Bank,Cheque,Online Transfer,Other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $qty = $request->input('quantity');
            $rate = $request->input('rate');
            $amt = $request->input('amount');

            if ($qty && !$rate) {
                $validator->errors()->add('rate', 'The rate field is required when quantity is provided.');
            }
            if ($rate && !$qty) {
                $validator->errors()->add('quantity', 'The quantity field is required when rate is provided.');
            }
            if (!$qty && !$rate && !$amt) {
                $validator->errors()->add('amount', 'The amount field is required when quantity and rate are not provided.');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Calculate amount if qty and rate exist
        if ($request->filled('quantity') && $request->filled('rate')) {
            $validated['amount'] = (float)$request->input('quantity') * (float)$request->input('rate');
        }

        // Upload attachment
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($expense->attachment) {
                Storage::disk('public')->delete($expense->attachment);
            }
            
            $file = $request->file('attachment');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('attachments', $filename, 'public');
            $validated['attachment'] = $path;
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // Delete attachment from storage
        if ($expense->attachment) {
            Storage::disk('public')->delete($expense->attachment);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Generate simple Expense Report
     */
    public function report(Request $request)
    {
        $categories = ExpenseCategory::orderBy('name', 'asc')->get();
        
        // Get unique Paid To values for filter dropdown
        $suppliers = Expense::select('paid_to')->distinct()->orderBy('paid_to', 'asc')->pluck('paid_to');

        // Build query
        $query = Expense::with('category');

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->input('end_date'));
        }
        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->input('category_id'));
        }
        if ($request->filled('paid_to')) {
            $query->where('paid_to', $request->input('paid_to'));
        }

        // Compute total across ALL filtered records (regardless of pagination page)
        $totalExpense = (clone $query)->sum('amount');
        $totalCount   = (clone $query)->count();

        // Paginate for screen view; print/export mode uses ?all=1 to load everything
        if ($request->boolean('all')) {
            $expenses = $query->orderBy('expense_date', 'asc')->get();
        } else {
            $expenses = $query->orderBy('expense_date', 'asc')->paginate(25)->withQueryString();
        }

        // ── Server-side CSV Export ─────────────────────────────────────────────
        if ($request->input('export') === 'csv') {
            $allExpenses = (clone $query)->orderBy('expense_date', 'asc')->get();
            $filename    = 'Masjid_Expense_Report_' . now()->format('Y-m-d') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            $callback = function () use ($allExpenses, $totalExpense, $totalCount) {
                $handle = fopen('php://output', 'w');

                // UTF-8 BOM for Excel compatibility
                fputs($handle, "\xEF\xBB\xBF");

                // Header row
                fputcsv($handle, ['Date', 'Category', 'Description', 'Quantity', 'Unit', 'Rate (Rs.)', 'Amount (Rs.)', 'Paid To', 'Payment Method', 'Reference No.', 'Notes']);

                // Data rows
                foreach ($allExpenses as $expense) {
                    fputcsv($handle, [
                        $expense->expense_date->format('d-M-Y'),
                        $expense->category->name,
                        $expense->description,
                        $expense->quantity ? (float) $expense->quantity : '',
                        $expense->unit ?? '',
                        $expense->rate ? (float) $expense->rate : '',
                        (float) $expense->amount,
                        $expense->paid_to,
                        $expense->payment_method,
                        $expense->reference_number ?? '',
                        $expense->notes ?? '',
                    ]);
                }

                // Total row
                fputcsv($handle, ['', '', '', '', '', "TOTAL ({$totalCount} records)", number_format($totalExpense, 2), '', '', '', '']);

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        // ── Normal view (paginated for screen, all records for print/all mode) ──
        return view('expenses.report', compact('expenses', 'categories', 'suppliers', 'totalExpense', 'totalCount'));
    }
}
