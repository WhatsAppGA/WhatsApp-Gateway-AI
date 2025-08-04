<x-layout-dashboard title="Test Messages">

    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Message</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Test</li>
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
        <div class="alert border-0 bg-light-danger alert-dismissible fade show py-2">
            <div class="d-flex align-items-center">
                <div class="fs-3 text-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>

                </div>
                <div class="ms-3">
                    <p>The given data was invalid.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        </div>
    @endif
    {{-- form --}}
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header py-3 bg-transparent">
                    <div class="d-sm-flex align-items-center">
                        <h5 class="mb-2 mb-sm-0">Test Message</h5>
                    </div>
                </div>
                @if (!session()->has('selectedDevice') || !session()->has('selectedDevice'))
                    <div class="alert alert-danger">
                        <ul>
                            <li>Please select a device and a message to test</li>
                        </ul>
                    </div>
                @else
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" action="{{ route('messagetest') }}" method="POST">
                                        @csrf
                                        <div class="col-12">
                                            <label class="form-label">Sender</label>
                                            <input name="sender"
                                                value="{{ session()->get('selectedDevice')['device_body'] }}"
                                                type="text" class="form-control" readonly>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Receiver Number</label>
                                            <textarea placeholder="628xxx|628xxx|628xxx" class="form-control" name="number" id="" cols="20"
                                                rows="2"></textarea>
                                            {{-- <input name="number" value="" type="number" class="form-control"
                                                required> --}}
                                        </div>
                                        <div class="col-12">
                                            <label for="type" class="form-label">Type Message</label>
                                            <select name="type" id="type" class="js-states form-control"
                                                tabindex="-1" required>
                                                <option value="" selected disabled>Select One</option>
                                                <option value="text">Text Message</option>
                                                <option value="media">Media Message</option>
                                                <option value="poll">Poll Message</option>
                                                {{-- vcard,location,sticker --}}
                                                <option value="vcard">Vcard Message</option>
                                                <option value="location">Location Message</option>
                                                <option value="sticker">Sticker Message</option>
                                                <option value="list">List Message (Unstable,must with image)</option>
                                                <option value="button">Button Message ( Unstable,must with image )</option>


                                            </select>
                                        </div>
                                        <div class="col-12 ajaxplace ">

                                        </div>
                                        {{-- button --}}
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-info btn-sm text-white px-5">Send
                                                Message</button>
                                        </div>
                                    </form>
                                </div>
                            </div>


                        </div>

                    </div>
                @endif
                <!--end row-->
            </div>
        </div>
    </div>
    </div>
    {{-- end form --}}

</x-layout-dashboard>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.3.3/dist/leaflet.js"></script>
<script src="https://woody180.github.io/vanilla-javascript-emoji-picker/vanillaEmojiPicker.js"></script>
<script>
    $('#type').on('change', () => {
        const type = $('#type').val();
        $.ajax({
            url: `/form-message/${type}`,

            type: "GET",
            dataType: "html",
            success: (result) => {
                $(".ajaxplace").html(result);
            },
            error: (error) => {
                console.log(error);
            },
        });
    })
</script>
