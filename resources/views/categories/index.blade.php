@extends('layouts.app')

@section('title', 'Expense Categories')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Categories</li>
@endsection

@section('content')
    <div class="row">
        <!-- Categories List -->
        <div class="col-md-8">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-tags-fill me-2 text-primary"></i> Category List
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 80px;">ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th class="text-center" style="width: 120px;">Expenses</th>
                                    <th class="text-end pe-3" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $category->id }}</td>
                                        <td class="fw-bold">{{ $category->name }}</td>
                                        <td class="text-muted text-truncate" style="max-width: 250px;">
                                            {{ $category->description ?? 'No description provided' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-white rounded-pill px-3">
                                                {{ $category->expenses_count }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <!-- Edit button triggers Bootstrap modal -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editCategoryModal"
                                                    data-id="{{ $category->id }}"
                                                    data-name="{{ $category->name }}"
                                                    data-description="{{ $category->description }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            
                                            <!-- Delete button -->
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $category->expenses_count > 0 ? 'disabled title=Cannot_delete_category_with_expenses' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No categories defined yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($categories->hasPages() || $categories->total() > 0)
                    <div class="card-footer bg-transparent py-3 border-top">
                        <div class="row align-items-center">
                            <div class="col-sm-5">
                                <p class="mb-0 text-muted small">
                                    Showing
                                    <strong>{{ $categories->firstItem() ?? 0 }}</strong>
                                    to
                                    <strong>{{ $categories->lastItem() ?? 0 }}</strong>
                                    of
                                    <strong>{{ $categories->total() }}</strong>
                                    categories
                                </p>
                            </div>
                            <div class="col-sm-7 d-flex justify-content-sm-end">
                                {{ $categories->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Category Form -->
        <div class="col-md-4">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom-0">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-plus-circle-fill me-2 text-success"></i> Add Category
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Cement, Bricks" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Brief description of category items...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-save me-1"></i> Save Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="POST" id="editCategoryForm">
                @csrf
                @method('PUT')
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editCategoryModalLabel">
                            <i class="bi bi-pencil-square me-2"></i> Edit Category
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editCategoryModal = document.getElementById('editCategoryModal');
            if (editCategoryModal) {
                editCategoryModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const description = button.getAttribute('data-description');
                    
                    const form = editCategoryModal.querySelector('#editCategoryForm');
                    form.action = "{{ url('categories') }}/" + id;
                    
                    editCategoryModal.querySelector('#edit_name').value = name;
                    editCategoryModal.querySelector('#edit_description').value = description;
                });
            }
        });
    </script>
@endpush
