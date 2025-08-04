<x-layout-dashboard title="Settings Server ">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Admin</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Setting Server</li>
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
    <div class="row">

        <div class="col">
            <div class="page-description page-description-tabbed">


                <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="account-tab" data-bs-toggle="tab" data-bs-target="#server"
                            type="button" role="tab" aria-controls="hoaccountme"
                            aria-selected="true">Server</button>
                    </li>


                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="server" role="tabpanel" aria-labelledby="account-tab">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row m-t-lg">
                                        <form action="{{ route('setServer') }}" method="POST">
                                            @csrf
                                            <div class="col-md-12">
                                                <label for="typeServer"
                                                    class="form-label">{{ __('Server Type') }}</label>
                                                <select name="typeServer" class="form-control" id="server" required>

                                                    @if (env('TYPE_SERVER') === 'localhost')
                                                        <option value="localhost" selected>{{ __('Localhost') }}
                                                        </option>
                                                        <option value="hosting">{{ __('Hosting Shared') }}</option>
                                                        <option value="other">{{ __('Other') }}</option>
                                                    @elseif(env('TYPE_SERVER') === 'hosting')
                                                        <option value="localhost">{{ __('Localhost') }}</option>
                                                        <option value="hosting" selected>{{ __('Hosting Shared') }}
                                                        </option>
                                                        <option value="other">{{ __('Other') }}</option>
                                                    @else
                                                        <option value="other" required>{{ __('Other') }}</option>
                                                        <option value="localhost">{{ __('Localhost') }}</option>
                                                        <option value="hosting">{{ __('Hosting Shared') }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="Port"
                                                    class="form-label">{{ __('Port Node JS') }}</label>
                                                <input type="number" name="portnode" class="form-control"
                                                    id="Port" value="{{ env('PORT_NODE') }}" required>
                                            </div>
                                    </div>
                                    <div
                                        class="row m-t-lg {{ env('TYPE_SERVER') === 'other' ? 'd-block' : 'd-none' }} formUrlNode">
                                        <div class="col-md-12">
                                            <label for="settingsInputUserName "
                                                class="form-label">{{ __('URL Node') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"
                                                    id="settingsInputUserName-add">{{ __('URL') }}</span>
                                                <input type="text" class="form-control"
                                                    value="{{ env('WA_URL_SERVER') }}" name="urlnode"
                                                    id="settingsInputUserName"
                                                    aria-describedby="settingsInputUserName-add">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row m-t-lg ">
                                        <div class="col mt-4">

                                            <button type="submit"
                                                class="btn btn-primary btn-sm">{{ __('Update') }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="col-md-6 mt-3 p-2 border rounded d-flex align-items-center justify-content-center flex-column">
                                    <div>
                                        <h4>{{ __('Port (:port) Is', ['port' => $port]) }}
                                            {{ $isConnected ? __('Connected') : __('Disconnected') }}</h4>
                                    </div>
                                    <div>
                                        <h1>{{ $isConnected ? '✅' : '❌' }}</h1>
                                    </div>

                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-center">{{ __('Generate SSL For Your NodeJS') }}</h5>
                                    <div class="text-center">
                                        <form action="{{ route('generateSsl') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="settingsInputUserName "
                                                        class="form-label">{{ __('Domain') }}</label>
                                                    <input type="text" name="domain" class="form-control"
                                                        id="domain" value="{{ $host }}" required readonly
                                                        @if ($host === 'localhost') disabled @endif>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="settingsInputUserName "
                                                        class="form-label">{{ __('Email') }}</label>
                                                    <input type="email" name="email" class="form-control"
                                                        id="email" value="" required
                                                        @if ($host === 'localhost') readonly disabled @endif>
                                                </div>
                                            </div>
                                            @if ($host == 'localhost' || $host == 'hosting')
                                                <button type="submit" class="btn btn-danger btn-sm mt-3"
                                                    disabled>{{ __('Ssl only required in vps if you want to access via ssl') }}</button>
                                            @else
                                                <button type="submit"
                                                    class="btn btn-primary btn-sm mt-3">{{ __('Generate SSL Certificate') }}</button>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>


    <script>
        $('#server').on('change', function() {
            let type = $('#server :selected').val();
            console.log(type);
            if (type === 'other') {
                $('.formUrlNode').removeClass('d-none')
            } else {
                $('.formUrlNode').addClass('d-none')

            }
        })
    </script>
</x-layout-dashboard>
