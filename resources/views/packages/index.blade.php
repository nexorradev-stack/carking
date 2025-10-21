@extends('layout.superAdminDashboard')

@section('body')
<div class="container mt-4">
    <h1>Packages</h1>
    <div class="mt-3 d-flex justify-content-end">
        <a href="{{ route('packages.create') }}" style="background-color:black; color:white; text-decoration:none; padding:10px 12px; border-radius:10px;">
            <i class="fas fa-plus me-2"></i> Add Package
        </a>
    </div>

    <div class="mt-5">
        @if($packages->isEmpty())
            <div class="d-flex justify-content-center align-items-center" style="height: 50vh;">
                <h3 class="text-muted">No package available</h3>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Features</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Subscription</th>
                            <th>Status</th>
                            <th>Recommend</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $package->title }}</td>
                                <td>{{ $package->description }}</td>
                                <td>{{ $package->features }}</td>
                                <td>${{ number_format($package->price, 2) }}</td>
                                <td>{{ $package->duration }} days</td>
                                <td>{{ $package->recovery_payment }}</td>
                                <td>
                                    <div class="Recomended">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle"
                                                   type="checkbox"
                                                   role="switch"
                                                   id="flexSwitchCheck{{ $package->id }}"
                                                   data-package-id="{{ $package->id }}"
                                                   {{ $package->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label" for="flexSwitchCheck{{ $package->id }}">
                                                {{ $package->is_active ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="Recomended">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input featurestatus-toggle"
                                                   type="checkbox"
                                                   role="switch"
                                                   id="featureflexSwitchCheck{{ $package->id }}"
                                                   data-package-id="{{ $package->id }}"
                                                   {{ $package->is_featured ? 'checked' : '' }}>
                                            <label class="form-check-label" for="featureflexSwitchCheck{{ $package->id }}">
                                                {{ $package->is_featured ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="gap-2 d-flex">
                                        <a href="{{ route('packages.edit', $package->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('packages.destroy', $package->id) }}" method="POST" class="d-inline" id="delete-form-{{ $package->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $package->id }})">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.13/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.13/dist/sweetalert2.all.min.js"></script>

<script>
    $(document).ready(function() {
        // Status toggle
        $('.status-toggle').change(function() {
            var packageId = $(this).data('package-id');
            var isActive = $(this).prop('checked') ? 1 : 0;
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to change the status of this package?",
                showCancelButton: true,
                confirmButtonText: 'Yes, change it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/packages/' + packageId + '/update-status',
                        method: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            is_active: isActive
                        },
                        success: function(response) {
                            Swal.fire(
                                'Updated!',
                                response.message,
                                'success'
                            );
                            var label = isActive ? 'Active' : 'Inactive';
                            $('label[for="flexSwitchCheck' + packageId + '"]').text(label);
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                'Failed to update status.',
                                'error'
                            );
                        }
                    });
                } else {
                    $(this).prop('checked', !isActive);
                }
            });
        });

        // Featured status toggle
        $('.featurestatus-toggle').change(function() {
            var packageId = $(this).data('package-id');
            var isFeatured = $(this).prop('checked') ? 1 : 0;
            var $label = $('label[for="featureflexSwitchCheck' + packageId + '"]');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to change the featured status of this package?",
                showCancelButton: true,
                confirmButtonText: 'Yes, change it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/packages/' + packageId + '/feature-update-status',
                        method: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            is_featured: isFeatured
                        },
                        success: function(response) {
                            Swal.fire(
                                'Updated!',
                                response.message,
                                'success'
                            );
                            var label = isFeatured ? 'Active' : 'Inactive';
                            $label.text(label);
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                'Failed to update featured status.',
                                'error'
                            );
                            $(this).prop('checked', !isFeatured); // Revert toggle state
                        }
                    });
                } else {
                    $(this).prop('checked', !isFeatured); // Revert toggle state
                }
            });
        });
    });
    // Confirm delete
    function confirmDelete(packageId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + packageId).submit();
            }
        });
    }
</script>

<style>
    table, th, td {
        border: 1px solid gray;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .btn-outline-primary {
        border-color: #0d6efd;
        color: #0d6efd;
    }
    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: #fff;
    }
    .btn-outline-danger {
        border-color: #dc3545;
        color: #dc3545;
    }
    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: #fff;
    }
    .Recomended div{
        display: flex;
        align-content: center;
        justify-content: center;
        gap: 4px;
    }
    .d-flex.gap-2 {
        gap: 8px;
    }
</style>
@endsection
