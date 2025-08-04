<x-layout-dashboard title="Rest Api">
    <div class="app-content">
        <div class="content-wrapper">
            <div class="container">



                <!--breadcrumb-->
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">User</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!--end breadcrumb-->

                <div class="row">
                    @if (session()->has('alert'))
                        <x-alert>
                            @slot('type', session('alert')['type'])
                            @slot('msg', session('alert')['msg'])
                        </x-alert>
                    @endif
                    <div class="card">
                        <div class="card-body">
                            <h5 class="">Settings</h5>
                            <div class="row col-md-6 mx-auto">
                                <div class="col-md-12">
                                    <form action="{{ route('generateNewApiKey') }}" method="POST">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">API Key</span>

                                            <input type="text" class="form-control"
                                                value="{{ Auth::user()->api_key }}" aria-label="Username"
                                                aria-describedby="basic-addon1" readonly>
                                            <button type="submit" name="api_key" class="btn btn-primary">Generate
                                                New</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6 mx-auto">

                                    <form action="{{ route('changePassword') }}" method="POST">
                                        @csrf

                                        <div class="form-group">
                                            <label for="settingsCurrentPassword" class="form-label">Current
                                                Password</label>
                                            <input type="password" name="current"
                                                class="form-control {{ $errors->has('current') ? 'is-invalid' : '' }} "
                                                aria-describedby="settingsCurrentPassword"
                                                placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">
                                            @if ($errors->has('current'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('current') }}
                                                </div>
                                            @endif


                                        </div>

                                        <div class="col-md-12">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" name="password"
                                                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                                aria-describedby="password"
                                                placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">
                                            @if ($errors->has('password'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('password') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-12">
                                            <label for="settingsConfirmPassword" class="form-label">Confirm
                                                Password</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                aria-describedby="settingsConfirmPassword"
                                                placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">
                                        </div>

                                        <div class="row m-t-lg mt-4">
                                            <div class="col mt-2">

                                                <button type="submit" class="btn btn-info text-white m-t-sm">Change
                                                    Password</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-layout-dashboard>
