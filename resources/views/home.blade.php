<x-layout-dashboard title="Home">
    
    <div class="app-content">
        <div class="content-wrapper">
            <div class="container">

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
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4">
                    <div class="col">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1">Total Devices</p>
                                        <h4 class="mb-0">{{ $user->devices_count }}</h4>
                                        <p class="mb-0 mt-2 font-13">
                                            <span>Your limit device : {{ $user->limit_device }}</span>
                                        </p>
                                    </div>
                                    <div class="ms-auto widget-icon bg-primary text-white">
                                        <i class="bi bi-whatsapp"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1">Blast/Bulk</p>
                                        <p class="mb-0 badge bg-warning">{{ $user->blasts_pending }} Wait </p>
                                        <p class="mb-0 badge bg-success">{{ $user->blasts_success }} Sent </p>
                                        <p class="mb-0 badge bg-danger">{{ $user->blasts_failed }} Fail </p>
                                        <p class="mb-0 mt-2 font-13">
                                            From {{ $user->campaigns_count }} Campaigns
                                            </span></p>
                                    </div>
                                    <div class="ms-auto widget-icon bg-success text-white">
                                        <i class="bi bi-broadcast"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1">Subscription Status</p>
                                        <h4 class="mb-0">{{ $user->subscription_status }}</h4>
                                        <p class="mb-0 mt-2 font-13"><span>
                                                Expired : {{ $user->expired_subscription_status }}
                                            </span></p>
                                    </div>
                                    <div class="ms-auto widget-icon bg-orange text-white">
                                        <i class="bi bi-emoji-heart-eyes"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1">All Messages Sent</p>
                                        <h4 class="mb-0">{{ $user->message_histories_count }}</h4>
                                        <p class="mb-0 mt-2 font-13"><span>From messages histories</span></p>
                                    </div>
                                    <div class="ms-auto widget-icon bg-info text-white">
                                        <i class="bi bi-chat-left-text"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end row-->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0">Whatsapp Account</h5>
                            <form class="ms-auto position-relative">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addDevice"><i class="bi bi-plus"></i> Add Device</button>

                            </form>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-responsive align-middle">
                                <thead>
                                    <th>{{ __('Number') }}</th>
                                    <th class="text-nowrap">{{ __('Webhook URL') }}</th>
                                    <th>{{ __('Read') }}</th>
                                    <th class="text-nowrap">{{ __('Reject Call') }}</th>
                                    <th>{{ __('Online') }}</th>
                                    <th>{{ __('Typing') }} (WH)</th>

                                    <th>{{ __('Sent') }}</th>
                                    <th>{{ __('status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </thead>
                                <tbody>
                                    @if ($numbers->total() == 0)
                                        <x-no-data colspan="9" text="No Device added yet" />
                                    @endif
                                    @foreach ($numbers as $number)
                                        <tr>

                                            <td>{{ $number['body'] }}</td>
                                            <td>
                                                <form action="" method="post">
                                                    @csrf
                                                    <input type="text"
                                                        class="form-control form-control-solid-bordered webhook-url-form"
                                                        data-id="{{ $number['body'] }}" name=""
                                                        value="{{ $number['webhook'] }}" id="">
                                                </form>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input data-url="{{ route('setHookRead') }}"
                                                        class="form-check-input toggle-read" type="checkbox"
                                                        data-id="{{ $number['body'] }}"
                                                        {{ $number['wh_read'] ? 'checked' : '' }} />
                                                    <label class="form-check-label"
                                                        for="toggle-switch">{{ $number['webhook_read'] ? 'Yes' : 'No' }}</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input data-url="{{ route('setHookReject') }}"
                                                        class="form-check-input toggle-reject" type="checkbox"
                                                        data-id="{{ $number['body'] }}"
                                                        {{ $number['reject_call'] ? 'checked' : '' }} />
                                                    <label class="form-check-label"
                                                        for="toggle-switch">{{ $number['reject_call'] ? 'Yes' : 'No' }}</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input data-url="{{ route('setAvailable') }}"
                                                        class="form-check-input toggle-available" type="checkbox"
                                                        data-id="{{ $number['body'] }}"
                                                        {{ $number['set_available'] ? 'checked' : '' }} />
                                                    <label class="form-check-label"
                                                        for="toggle-switch">{{ $number['set_available'] ? 'Yes' : 'No' }}</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input data-url="{{ route('setHookTyping') }}"
                                                        class="form-check-input toggle-typing" type="checkbox"
                                                        data-id="{{ $number['body'] }}"
                                                        {{ $number['wh_typing'] ? 'checked' : '' }} />
                                                    <label class="form-check-label"
                                                        for="toggle-switch">{{ $number['webhook_typing'] ? 'Yes' : 'No' }}</label>
                                                </div>
                                            </td>

                                            <td>{{ $number['message_sent'] }}</td>
                                            <td><span
                                                    class="badge bg-{{ $number['status'] == 'Connected' ? 'success' : 'danger' }}">
                                                    {{ $number['status'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('connect-via-code', $number->body) }}"
                                                        class="btn btn-primary shadow btn-xs sharp me-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="{{ __('Connect Via Code') }}"><i
                                                            class="bi bi-phone"  style="margin-left:0px !important"></i></a>
                                                    <a href="{{ route('scan', $number->body) }}"
                                                        class="btn btn-primary shadow btn-xs sharp me-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="{{ __('connect Via QR') }}"  style="margin-left:0px !important"><i
                                                            class="bi bi-qr-code"  style="margin-left:0px !important"></i></a>
                                                    <a href="{{ route('deviceSettings', $number->body) }}" class="btn btn-primary shadow btn-xs sharp me-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Device Settings">
                                                        <i class="bi bi-gear-fill" style="margin-left:0px !important"></i>
                                                    </a>
                                                    <form action="{{ route('deleteDevice') }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <input name="deviceId" type="hidden"
                                                            value="{{ $number['id'] }}">
                                                        <button type="submit" name="delete"
                                                            class="btn btn-danger shadow btn-xs sharp"  onclick="return confirm('Are you sure?');"><i
                                                                class="bi bi-trash"  style="margin-left:0px !important"></i></button>
                                                    </form>
                                                </div>


                                            </td>
                                        </tr>
                                    @endforeach


                                </tbody>

                            </table>
                        </div>

                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li class="page-item {{ $numbers->currentPage() == 1 ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $numbers->previousPageUrl() }}">Previous</a>
                                </li>

                                @for ($i = 1; $i <= $numbers->lastPage(); $i++)
                                    <li class="page-item {{ $numbers->currentPage() == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $numbers->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                <li
                                    class="page-item {{ $numbers->currentPage() == $numbers->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $numbers->nextPageUrl() }}">Next</a>
                                </li>
                            </ul>
                        </nav>

                    </div>
                </div>


            </div>
        </div>
    </div>





    <div class="modal fade" id="addDevice" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('addDevice') }}" method="POST">
                        @csrf
                        <label for="sender" class="form-label">Number</label>
                        <input type="number" name="sender" class="form-control" id="nomor" required>
                        <p class="text-small text-danger">*Use Country Code ( without + )</p>
                        <label for="urlwebhook" class="form-label">Link webhook</label>
                        <input type="text" name="urlwebhook" class="form-control" id="urlwebhook">
                        <p class="text-small text-danger">*Optional</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layout-dashboard>
<script>
    var typingTimer; //timer identifier
    var doneTypingInterval = 1000;

    $('.webhook-url-form').on('keyup', function() {
        clearTimeout(typingTimer);
        let value = $(this).val();
        let number = $(this).data('id');

        typingTimer = setTimeout(function() {

            $.ajax({
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('setHook') }}',
                data: {
                    csrf: $('meta[name="csrf-token"]').attr('content'),
                    number: number,
                    webhook: value
                },
                dataType: 'json',
                success: (result) => {
                    toastr.success('Webhook URL has been updated');
                },
                error: (err) => {
                    console.log(err);
                }
            })
        }, doneTypingInterval);
    })


    $('.delay-url-form').on('keyup', function() {
        clearTimeout(typingTimer);
        let value = $(this).val();
        let number = $(this).data('id');

        typingTimer = setTimeout(function() {

            $.ajax({
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('setDelay') }}',
                data: {
                    csrf: $('meta[name="csrf-token"]').attr('content'),
                    number: number,
                    delay: value
                },
                dataType: 'json',
                success: (result) => {
                    toastr.success('{{ __('Delay has been updated') }}');
                },
                error: (err) => {
                    console.log(err);
                }
            })
        }, doneTypingInterval);
    });

    $(".toggle-read").on("click", function() {
        let dataId = $(this).data("id");
        let isChecked = $(this).is(":checked");
        let url = $(this).data("url");
        $.ajax({
            url: url,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                webhook_read: isChecked ? "1" : "0",
                id: dataId,
            },
            success: function(result) {
                if (!result.error) {
                    // find label
                    let label = $(`.toggle-read[data-id=${dataId}]`)
                        .parent()
                        .find("label");
                    // change label
                    if (isChecked) {
                        label.text("{{ __('Yes') }}");
                    } else {
                        label.text("{{ __('No') }}");
                    }
                    toastr['success'](result.msg);
                }
            },
        });
    });

    $(".toggle-reject").on("click", function() {
        let dataId = $(this).data("id");
        let isChecked = $(this).is(":checked");
        let url = $(this).data("url");
        $.ajax({
            url: url,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                webhook_reject_call: isChecked ? "1" : "0",
                id: dataId,
            },
            success: function(result) {
                if (!result.error) {
                    // find label
                    let label = $(`.toggle-reject[data-id=${dataId}]`)
                        .parent()
                        .find("label");
                    // change label
                    if (isChecked) {
                        label.text("{{ __('Yes') }}");
                    } else {
                        label.text("{{ __('No') }}");
                    }
                    toastr['success'](result.msg);
                }
            },
        });
    });

    $(".toggle-typing").on("click", function() {
        let dataId = $(this).data("id");
        let isChecked = $(this).is(":checked");
        let url = $(this).data("url");
        $.ajax({
            url: url,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                webhook_typing: isChecked ? "1" : "0",
                id: dataId,
            },
            success: function(result) {
                if (!result.error) {
                    // find label
                    let label = $(`.toggle-typing[data-id=${dataId}]`)
                        .parent()
                        .find("label");
                    // change label
                    if (isChecked) {
                        label.text("{{ __('Yes') }}");
                    } else {
                        label.text("{{ __('No') }}");
                    }
                    toastr['success'](result.msg);
                }
            },
        });
    });

    $(".toggle-available").on("click", function() {
        let dataId = $(this).data("id");
        let isChecked = $(this).is(":checked");
        let url = $(this).data("url");
        $.ajax({
            url: url,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                set_available: isChecked ? "1" : "0",
                id: dataId,
            },
            success: function(result) {
                if (!result.error) {
                    // find label
                    let label = $(`.toggle-available[data-id=${dataId}]`)
                        .parent()
                        .find("label");
                    // change label
                    if (isChecked) {
                        label.text("{{ __('Yes') }}");
                    } else {
                        label.text("{{ __('No') }}");
                    }
                    toastr['success'](result.msg);
                }
            },
        });
    });
</script>
