<x-layout-dashboard title="Messages History">
    @if (session()->has('alert'))
        <x-alert>
            @slot('type', session('alert')['type'])
            @slot('msg', session('alert')['msg'])
        </x-alert>
    @endif
    <!--breadcrumb-->

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Messages</div>

        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Messages</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <button onclick="clearAll()" type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                data-bs-target="#deleteAllModal">
                <i class="bi bi-trash-fill"></i> Clear all
        </div>
    </div>
    {{-- end breadcrumb --}}

    {{-- table --}}
    <div class="row">
        <div class="col-12 col-lg-12 d-flex">
            <div class="card w-100">
                <div class="card-header py-3">
                    <div class="row g-3">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Sender</th>
                                    <th>Number</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Via</th>
                                    <th>Last Updated</th>
                                    <th>Action </th>

                                </tr>
                            </thead>

                            <tbody>
                                @if ($messages->total() == 0)
                                    <x-no-data colspan="6" text="No Messages History" />
                                @endif
                                @foreach ($messages as $msg)
                                    <tr>
                                        <td>{{ $msg->id }}</td>
                                        <td>{{ $msg->device->body ?? 'NA/Deleted' }}</td>
                                        <td>{{ $msg->number }}</td>
                                        <td>
                                            <span class="text-info">{{ $msg->type }} </span>:
                                            {{ substr($msg->message, 0, 20) }}
                                            {{ strlen($msg->message) > 20 ? '...' : '' }}
                                        <td>
                                            @if ($msg->status == 'success')
                                                <span class="badge rounded-pill bg-success">Sent</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($msg->send_by == 'web')
                                                <span class="badge rounded-pill bg-primary">Web</span>
                                            @else
                                                <span class="badge rounded-pill bg-warning">API</span>
                                            @endif
                                        </td>
                                        <td>{{ date('d M Y', strtotime($msg->updated_at)) }}</td>
                                        <td>

                                            <a onclick="resend({{ $msg->id }}, '{{ $msg->status }}')"
                                                class="btn btn-sm btn-primary">
                                                <i class="bx bx-refresh"></i> Resend
                                            </a>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item {{ $messages->currentPage() == 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $messages->previousPageUrl() }}">Previous</a>
                            </li>

                            @for ($i = 1; $i <= $messages->lastPage(); $i++)
                                <li class="page-item {{ $messages->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $messages->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            <li
                                class="page-item {{ $messages->currentPage() == $messages->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $messages->nextPageUrl() }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
    {{-- end table --}}

</x-layout-dashboard>
<script>
    function clearAll(id) {
        if (!confirm('Are you sure you want to clear all messages?')) {
            toastr['error']('Cancel clearing messages')
            return;
        }
        $.ajax({
            url: `/messages/clear`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'DELETE',
            dataType: 'json',
            success: (result) => {
                location.reload();
            },
            error: (error) => {
                toastr['error']('something went wrong when clearing messages ')
            }
        })
    }

    function resend(id, status) {

        if (status == 'success') {
            toastr.info('Message already sent');
            return;
        }

        $.ajax({
            url: '/resend-message',
            type: 'POST',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if (res.error) {
                    toastr.error(res.msg);
                    return;
                } else {
                    toastr.success(res.msg);
                    return;
                }
            },
            error: function(err) {
                toastr.error('Something went wrong');
            }
        });

    }
</script>
