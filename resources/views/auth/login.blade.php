<x-layout-auth>
    @slot('title', 'Login')

    <main class="authentication-content mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-4 mx-auto">
                    <div class="card shadow rounded-5 overflow-hidden">
                        <div class="card-body p-4 p-sm-5">
                            @if (session()->has('alert'))
                                <x-alert>
                                    @slot('type', session('alert')['type'])
                                    @slot('msg', session('alert')['msg'])
                                </x-alert>
                            @endif
                            <h5 class="card-title">Sign In</h5>
                            <p class="card-text mb-5">Hi, welcome to MPWA MD version.</p>
                            <form class="form-body" action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="username" class="form-label">Username</label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <input type="text"
                                                class="form-control radius-30 ps-5 {{ $errors->has('username') ? 'is-invalid' : '' }}"
                                                id="username" name="username" placeholder="Username" required>
                                        </div>
                                        <p class="text-danger">
                                            @error('username')
                                                {{ $message }}
                                            @enderror
                                        </p>
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label">Enter Password</label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                    class="bi bi-lock-fill"></i></div>
                                            <input type="password" name="password" class="form-control radius-30 ps-5"
                                                id="password" placeholder="Enter Password" required>

                                        </div>

                                    </div>

                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary radius-30">Sign In</button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p class="mb-0">Don't have an account yet? <a
                                                href="{{ route('register') }}">Sign up here</a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout-auth>
