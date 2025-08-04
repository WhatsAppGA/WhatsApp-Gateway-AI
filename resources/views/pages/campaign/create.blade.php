<x-layout-dashboard title="Phone book">

    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
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
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Campaign</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>

    </div>
    <!--end breadcrumb-->

    {{-- wizard --}}
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <h6 class="mb-0 text-uppercase">Create Your Campaign </h6>
            <hr />
            <div class="card">
                @if (!session()->has('selectedDevice'))
                    <div class="alert alert-danger"> Please select a device first </div>
                @else
                    <div class="card-body">

                        <!-- SmartWizard html -->
                        <div id="smartwizard" style="height: 100%;">
                            <ul class="nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="#step-1">
                                        <strong>Step 1</strong> <br />Create name and preview the sender.</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#step-2">
                                        <strong>Step 2</strong> <br />Set message and destination </a>
                                </li>
                                <li class="nav-item">
                                    <a onclick="return false;" class="nav-link" href="#step-3">
                                        <strong>Step 3</strong> <br />Delay and Campaign type</a>
                                </li>

                            </ul>
                            <div class="tab-content col-md-10 offset-md-1 mt-4">
                                <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                                    <div class="form-group">
                                        <label class="form-label" for="campaignName">Sender Number / Device</label>
                                        <input type="text" class="form-control" id="campaignName" name="sender"
                                            placeholder="Enter campaign name"
                                            value="{{ session('selectedDevice')['device_body'] }}" disabled>
                                        <input type="hidden" name="device_id" id="device_id"
                                            value="{{ session('selectedDevice')['device_id'] }}">

                                    </div>
                                    <div class="form-group mt-4">
                                        <label class="form-label" for="campaign_name">Campaign Name</label>
                                        <input type="text" class="form-control" id="campaign_name"
                                            name="campaign_name" placeholder="Enter campaign name">
                                    </div>
                                </div>
                                <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2"
                                    style="height: 400px; overflow-y: scroll;">

                                    <div class="mb-3 form-group">
                                        <label class="form-label">Select PhoneBook</label>
                                        <select id="phonebook_id" name="phonebook_id"
                                            class="single-select phonebook-option">
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="type" class="form-label">Type Message</label>
                                        <select name="type" id="type" class="js-states form-control"
                                            tabindex="-1" required>
                                            <option value="" selected disabled>Select One</option>
                                            <option value="text">Text Message</option>
                                            <option value="media">Media Message</option>
                                           
                                            <option value="list">List Message (Unstable,must with image) </option>
                                            {{-- <option value="image">Image Message</option> --}}
                                            <option value="button">Button Message ( Unstable,must with image )</option>



                                        </select>
                                    </div>
                                    <div class=" form-group ajaxplace ">

                                    </div>
                                </div>
                                <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3"
                                    style="height : 100%;">
                                    <div class="form-group mt-2">
                                        <label for="" class="form-label">Delay Per Message (Second)</label>
                                        <input type="number" name="delay" id="delay" class="form-control"
                                            placeholder="Delay Per Message (Second)" value="10" min="1"
                                            max="60">

                                    </div>
                                    <div class="form-group">
                                        <label for="tipe" class="form-label">Type</label>
                                        <select name="tipe" id="tipe" class="js-states form-control">
                                            <option value="immediately">Immediately</option>
                                            <option value="schedule">Schedule</option>

                                        </select>
                                    </div>
                                    <div class="form-group d-none" id="datetime">
                                        <label for="datetime" class="form-label">Date Time</label>
                                        <input type="datetime-local" id="datetime2" name="datetime"
                                            class="form-control">
                                    </div>
                                </div>

                            </div>

                        </div>
                        {{-- prev and next button --}}


                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button class="btn btn-info text-white" id="prev-btn" type="button">Previous</button>
                            <button class="btn btn-info text-white" id="next-btn" type="button">Next</button>
                            <button class="btn btn-info text-white d-none buttonsubmit" id="finish-btn"
                                type="button">Create Campaign</button>
                        </div>


                    </div>
            </div>
            @endif
        </div>
    </div>
    </div>
    {{-- end wizard --}}
    <script src="{{ asset('js/autoreply.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/form-select2.js') }}"></script>
    <script>
        $(document).ready(function() {

            // Smart Wizard
            // if url have #step-2 or above.. replace with #step-1
            if (window.location.hash === '#step-2' || window.location.hash === '#step-3') {
                window.location.hash = '#step-1';
            }


            $("#smartwizard").smartWizard({
                selected: 0,
                theme: "dots",
                transition: {
                    animation: "slide-horizontal", // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
                },
                toolbarSettings: {
                    toolbarPosition: "none",
                },
                // disable all anchor
                anchorSettings: {
                    anchorClickable: false,
                },

            });

            // load phonebook when step 2 is selected
            const loadPhonebook = (page = 1, search = '') => {
                let option = $('.phonebook-option');
                option.html('<option value="" selected disabled>Loading...</option>');
                $.ajax({
                    url: "/get-phonebook-list" + "?page=" + page + "&search=" + search,
                    method: 'GET',
                    dataType: 'html',
                    success: function(data) {
                        option.html(data);
                        $('#smartwizard').smartWizard("fixHeight");
                        return true;
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });


            }
            loadPhonebook();

            // External Button Events
            $("#prev-btn").on("click", function() {
                $("#smartwizard").smartWizard("prev");
                return true;
            });
            $("#next-btn").on("click", function() {
                let nextSelected = $("#smartwizard").smartWizard("getStepIndex");
                if (validation(nextSelected)) {
                    $("#smartwizard").smartWizard("next");
                    return true;
                }

            });
            // validation 
            function requiredInput(id) {
                const value = $(`#${id}`).val();
                if (value === ' ' || value === undefined || value.length === 0) {
                    toastr['warning'](`${id} must be filled`);
                    return false;
                }
                return true;
            }


            function checkMultipleForm(type, count = 3, template = false) {
                let success = false;
                let firstElement = $(`input[name='${type}[1]']`).val();
                // Periksa apakah ada elemen pertama yang diisi
                if (firstElement === undefined) {
                    toastr['warning'](`Please add at least one ${type} input`);
                } else {
                    // Periksa apakah semua elemen diisi
                    let isAllFilled = true;
                    for (let i = 1; i <= count; i++) {
                        let element = $(`input[name='${type}[${i}]']`).val();
                        if (element !== undefined && element === '') {
                            isAllFilled = false;
                            break;
                        }
                        if (template) {
                            try {
                                let format = element.split('|');

                                if (format.length < 3 || (format[0] !== 'call' && format[0] !== 'url')) {
                                    toastr['warning'](
                                        `Invalid ${type} ${i} format, format must be like this: call|number|text or url|url|text, first element must be call or url`
                                    );
                                    return false;
                                    break;
                                }
                            } catch (e) {

                            }
                        }
                    }

                    if (isAllFilled) {
                        success = true;
                    } else {
                        toastr['warning'](`All ${type} inputs must be filled`);
                    }
                }

                return success;
            }



            function validation(step) {
                if (step == 0) {
                    return requiredInput('campaign_name');
                }
                if (step == 1) {
                    let phonebook = $('.phonebook-option').val();
                    const type = $('#type').val();
                    if (phonebook == null) {
                        toastr['warning']('Please select phonebook');
                        return false;
                    }
                    if (type == 'text') {
                        return requiredInput('message');
                    } else if (type == 'media') {

                        let image = $('#thumbnail').val();
                        let caption = $('#caption').val();
                        if (image.length < 5) {
                            toastr['warning']('Please fill all field needed');
                            return false;
                        }
                        return true;
                    } else if (type == 'button') {
                        if (!requiredInput('message')) {
                            return false;
                        }

                        // Manual validation for buttons
                        let isValid = true;
                        let buttonCount = $('.buttoninput').length;
                        if (buttonCount < 1 || buttonCount > 5) {
                            toastr['warning']('You must have between 1 and 5 buttons.');
                            return false;
                        }

                        $('.buttoninput').each(function(index) {
                            let buttonType = $(this).find('.buttonType').val();
                            let displayText = $(this).find(`#buttonDisplayText${index + 1}`).val();

                            if (!buttonType || ['reply', 'call', 'url', 'copy'].indexOf(buttonType) === -
                                1) {
                                toastr['warning'](
                                    `Button ${index + 1} must have a valid type (reply, call, url, copy).`
                                );
                                isValid = false;
                                return false;
                            }

                            if (!displayText) {
                                toastr['warning'](`Button ${index + 1} must have display text.`);
                                isValid = false;
                                return false;
                            }

                            if (buttonType === 'call') {
                                let phoneNumber = $(this).find(`#phoneNumber${index + 1}`).val();
                                if (!phoneNumber) {
                                    toastr['warning'](
                                        `Button ${index + 1} with type 'call' must have a phone number.`
                                    );
                                    isValid = false;
                                    return false;
                                }
                            }

                            if (buttonType === 'url') {
                                let url = $(this).find(`#url${index + 1}`).val();
                                if (!url) {
                                    toastr['warning'](
                                        `Button ${index + 1} with type 'url' must have a valid URL.`);
                                    isValid = false;
                                    return false;
                                }
                            }

                            if (buttonType === 'copy') {
                                let copyText = $(this).find(`#copyText${index + 1}`).val();
                                if (!copyText) {
                                    toastr['warning'](
                                        `Button ${index + 1} with type 'copy' must have copy text.`);
                                    isValid = false;
                                    return false;
                                }
                            }
                        });

                        return isValid;

                    } else if (type == 'list') {
                        return requiredInput('message') && requiredInput('buttonlist') && requiredInput(
                            'namelist') && requiredInput('titlelist') && checkMultipleForm('list', 5);
                    } else {
                        toastr['warning']('Please select message type');
                        return false;
                    }

                }
            }

            // handle change tipe campaign
            $('#tipe').change(function() {
                if ($(this).val() == 'schedule') {
                    $('#datetime').removeClass('d-none');
                } else {
                    $('#datetime').addClass('d-none');
                }
            });

            // on wizard step change
            $("#smartwizard").on(
                "showStep",
                function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
                    $("#prev-btn").removeClass("disabled");
                    $("#next-btn").removeClass("disabled");
                    if (stepPosition === "first") {
                        $("#prev-btn").addClass("disabled");
                    } else if (stepPosition === "last") {
                        $('.buttonsubmit').removeClass('d-none');
                        $("#next-btn").addClass("disabled");
                    } else {
                        $('.buttonsubmit').addClass('d-none');
                        $("#prev-btn").removeClass("disabled");
                        $("#next-btn").removeClass("disabled");
                    }
                }
            );

            $('.buttonsubmit').click(function() {
                if (!requiredInput('delay')) {
                    return false;
                }
                if ($('#tipe').val() == 'schedule') {
                    if (!requiredInput('datetime2')) {
                        return false;
                    }
                }


                // find input and select in tab-content
                const input = $('.tab-content').find('input');
                const select = $('.tab-content').find('select');
                const textarea = $('.tab-content').find('textarea');
                let data = {};
                var datetimeInput = document.getElementById('datetime2');
                var datetimeValue = datetimeInput.value;
                data['device_id'] = $('#device_id').val();
                data['delay'] = $('#delay').val();
                data['datetime'] = datetimeValue;
                // get name and value from input and select
                $('input[type="text"]').each(function() {
                    data[$(this).attr('name')] = $(this).val();
                });

                $('input[type="radio"]:checked').each(function() {
                    data[$(this).attr('name')] = $(this).val();
                });
                select.each(function() {
                    data[$(this).attr('name')] = $(this).val();
                });
                textarea.each(function() {
                    data[$(this).attr('name')] = $(this).val();
                });

                const formData = new FormData();
                for (const key in data) {
                    if (key == 'thumbnail')
                        formData.append('image', data[key]);
                    else {
                        formData.append(key, data[key]);
                    }

                }








                $.ajax({
                    url: "/campaign/store",
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        if (result.error) {
                            toastr['error'](result.message);
                        } else {
                            toastr['success'](result.message);
                            // reset wizard form
                            $('#smartwizard').smartWizard("reset");
                            input.each(function() {
                                $(this).val('');
                            });
                            select.each(function() {
                                $(this).val('');
                            });
                            textarea.each(function() {
                                $(this).val('');
                            });
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });




            });







        });
    </script>
</x-layout-dashboard>
