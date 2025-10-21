@extends('layout.superAdminDashboard')
@section('body')
<section id="DB-container" class="container mt-4">
    <ul class="nav nav-tabs" id="apiTabs" role="tablist" style="border: none;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                type="button" role="tab" aria-controls="overview" aria-selected="true">
                <i class="fas fa-code me-2 text-gray-500"></i> DealerKIT
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings"
                type="button" role="tab" aria-controls="settings" aria-selected="false">
                <i class="fas fa-code me-2 text-gray-500"></i> Click Dealers
            </button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="apiTabsContent">
  
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">DealerKIT API Overview</h5>
                    <p class="mb-0">Only APIs provided by DealerKIT can be connected and integrated here.</p>
                </div>
                <div>
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#connectDealerModal"
                        data-source-type="api">
                        Connect Dealer
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Dealer Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Dealer ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apiusers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->dealer_id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No users found from API source.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Click Dealers Overview</h5>
                    <p class="mb-0">Only FTP feeds provided by Click Dealers and Spidersnet can be connected here.</p>
                </div>
                <div>
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#connectDealerModal"
                        data-source-type="ftp_feed">
                        Connect Dealer
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Dealer Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Dealer ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clickdealers as $clickdealer)
                            <tr>
                                <td>{{ $clickdealer->name }}</td>
                                <td>{{ $clickdealer->email }}</td>
                                <td>{{ $clickdealer->dealer_id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No users found from FTP feed source.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="connectDealerModal" tabindex="-1" aria-labelledby="connectDealerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.api.connectDealer') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="connectDealerModalLabel">Connect Dealer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Dealer Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="dealer_id" class="form-label">Dealer ID</label>
                        <input type="text" name="dealer_id" class="form-control" required>
                    </div>
                    <input type="hidden" name="source_type" id="source_type">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark">Save Dealer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var connectDealerModal = document.getElementById('connectDealerModal');
        connectDealerModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var sourceType = button.getAttribute('data-source-type'); 
            var sourceTypeInput = connectDealerModal.querySelector('#source_type');
            sourceTypeInput.value = sourceType; 
        });
    });
</script>
@endsection