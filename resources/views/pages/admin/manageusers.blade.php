<x-layout-dashboard title="Auto Replies">



    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Admin</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>

    </div>
    <!--end breadcrumb-->
    @if (session()->has('alert'))
        <x-alert>
            @slot('type', session('alert')['type'])
            @slot('msg', session('alert')['msg'])
        </x-alert>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif




    <div class="row mt-4">
        <div class="col">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title">Users</h5>


                    <button type="button" class="btn btn-primary" onclick="addUser()">
                        Add User
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive mt-3">
                        <table class="table align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Total Device</th>
                                    <th>Limit Device</th>
                                    <th>Subscription</th>
                                    <th>Expired subscription</th>
                                    <th>Action</th>
                                    {{-- <th class="d-flex justify-content-center">Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->total_device }}</td>
                                        <td>{{ $user->limit_device }}</td>
                                        <td>
                                            @php
                                                if ($user->is_expired_subscription) {
                                                    $badge = 'danger';
                                                } else {
                                                    $badge = 'success';
                                                }
                                            @endphp
                                            <span
                                                class="badge bg-{{ $badge }}">{{ $user->active_subscription }}</span>
                                        </td>

                                        <td>
                                            @php
                                                if ($user->is_expired_subscription) {
                                                    echo '<span class="badge bg-danger">-</span>';
                                                } else {
                                                    if ($user->active_subscription == 'active') {
                                                        echo $user->subscription_expired;
                                                    } else {
                                                        echo '<span class="badge bg-danger">-</span>';
                                                    }
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                                <a onclick="editUser({{ $user->id }})" href="javascript:;"
                                                    class="text-primary" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" title="Edit user"><i
                                                        class="bx bxs-edit"></i></a>

                                                <form action="{{ route('user.delete', $user->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure will delete this user ? all data user also will deleted')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                                    <button type="submit" name="delete"
                                                        class="btn text-sm btn-sm text-danger"><i
                                                            class="bi bi-trash-fill"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            <tfoot></tfoot>
                        </table>
                    </div>
                      <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item {{ $users->currentPage() == 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->previousPageUrl() }}">Previous</a>
                            </li>

                            @for ($i = 1; $i <= $users->lastPage(); $i++)
                                <li class="page-item {{ $users->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            <li
                                class="page-item {{ $users->currentPage() == $users->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->nextPageUrl() }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>


        <!-- Modal -->
        <div class="modal fade" id="modalUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST" enctype="multipart/form-data" id="formUser">
                            @csrf
                            <input type="hidden" id="iduser" name="id">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="">
                            <label for="password" class="form-label" id="labelpassword">Password</label>
                            <input type="password" name="password" id="password" class="form-control" value="">
                            <label for="limit_device" class="form-label">Limit Device</label>
                            <input type="number" name="limit_device" id="limit_device" class="form-control"
                                value="">
                            <label for="active_subscription" class="form-label">Active Subscription</label><br>
                            <select name="active_subscription" id="active_subscription" class="form-control">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="lifetime">Lifetime</option>
                            </select><br>
                            <label for="subscription_expired" class="form-label">Subscription Expired</label>
                            <input type="date" name="subscription_expired" id="subscription_expired"
                                class="form-control" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="modalButton" name="submit" class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>







        <script>
            function addUser() {
                $('#modalLabel').html('Add User');
                $('#modalButton').html('Add');
                $('#formUser').attr('action', '{{ route('user.store') }}');
                $('#modalUser').modal('show');
            }

            function editUser(id) {

                // return;
                $('#modalLabel').html('Edit User');
                $('#modalButton').html('Edit');
                $('#formUser').attr('action', '{{ route('user.update') }}');
                $('#modalUser').modal('show');
                $.ajax({
                    url: "{{ route('user.edit') }}",
                    type: "GET",
                    data: {
                        id: id
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $('#labelpassword').html('Password *(leave blank if not change)');
                        $('#username').val(data.username);
                        $('#email').val(data.email);
                        $('#password').val(data.password);
                        $('#limit_device').val(data.limit_device);
                        $('#active_subscription').val(data.active_subscription);
                        $('#subscription_expired').val(data.subscription_expired);
                        $('#iduser').val(data.id);
                    }
                });
            }
        </script>
</x-layout-dashboard>
