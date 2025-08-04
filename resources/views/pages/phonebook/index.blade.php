<x-layout-dashboard title="Phone book">

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
        <div class="breadcrumb-title pe-3">Phonebook</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Contact</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="ms-auto my-4 items-end">
        <div class="btn-group">
            <form action="{{ route('fetch.groups') }}" method="post">
                @csrf
                <input type="hidden" name="device"
                    value="{{ Session::has('selectedDevice') ? Session::get('selectedDevice')['device_id'] : '' }}">

                <button type="submit" class="btn btn-info btn-sm text-white">
                    Fetch From Selected Device <i class="bi bi-whatsapp"></i>
                </button>
            </form>


            <button type="submit" class="btn btn-secondary btn-sm mx-2" onclick="clearPhonebook()">
                Clear Phonebook <i class="bi bi-trash"></i>
            </button>



        </div>
    </div>
    <!--end breadcrumb-->
    <div class="email-wrapper">
        <div class="email-sidebar">
            <div class="email-sidebar-header d-grid"> <button data-bs-toggle="modal" data-bs-target="#addTag"
                    class="btn btn-primary compose-mail-btn"><i class="bi bi-plus-lg me-2"></i>Phonebook</button>
                <input type="text" class="form-control mt-2 search-phonebook" placeholder="Search phonebook">
            </div>
            <div class="email-sidebar-content">
                <div class="email-navigation">
                    <div class="list-group list-group-flush phone-book-list"
                        style="overflow-y: scroll !important; height : 140% ;">
                        <div class="d-flex justify-content-center align-items-center load-phonebook text-danger">

                        </div>
                    </div>
                </div>
            </div>
            <div class="email-meeting">
                <div class="list-group list-group-flush">
                    <button class="btn  load-more" data-page="1">
                        Load More
                    </button>
                </div>
            </div>
        </div>


        <div class="email-header d-xl-flex align-items-center">
            <div class="d-flex align-items-center">
                <div class="email-toggle-btn"><i class='bx bx-menu'></i>
                </div>
                <button onclick="deleteAllContact()" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i> All
                </button>

            </div>
            <div class="flex-grow-1 mx-xl-2 my-2 my-xl-0">
                <div class="input-group"> <span class="input-group-text bg-transparent"><i
                            class="bi bi-search"></i></span>
                    <input type="text" class="form-control search-contact" placeholder="Search contacts">
                </div>
            </div>
            <div>
                <button class="btn btn-primary btn-sm add-contact" onclick="addContact()">
                    Add Contact
                </button>
                <button class="btn btn-success btn-sm import-contact" onclick="importContact()">
                    <i class="bi bi-upload"></i> Import
                </button>
                <button class="btn btn-warning btn-sm export-contact" onclick="exportContact()">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>

        </div>
        <div class="email-content">

            <div class="contacts-list email-list">

            </div>
            {{-- spinner --}}
            <div class="d-flex justify-content-center align-items-center mt-4 process-get-contact">
                Please select phonebook to show contact
            </div>
        </div>

    </div>
    <!--start compose mail-->

    <!--end compose mail-->
    <!--start email overlay-->
    <div class="overlay email-toggle-btn-mobile">Click to close tab</div>


    {{-- modal add phonebook --}}
    <div class="modal fade" id="addTag" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tag.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- modal add contact -->
    <div class="modal fade" id="addContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="add-contact-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control contact-name" id="name"
                            required>
                        <label for="number" class="form-label">Number</label>
                        <input type="number" name="number" class="form-control contact-number" id="number"
                            required>
                        <label for="is_favorite" class="form-label">Is Favorite</label>
                        <select name="is_favorite" class="form-control contact-is-favorite" id="is_favorite" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                        <input type="hidden" class="input_phonebookid" name="tag_id" value=" ">


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary add-contact">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal add contact -->
    <!-- modal import contact -->
    <div class="modal fade" id="importContacts" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="import-contact-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="fileContacts" class="form-label">File (xlsx )</label>
                        <input {{-- accept xlsx and csv --}} accept=".xlsx" type="file" name="fileContacts"
                            class="form-control file-import" id="fileContacts" required>

                        <input type="hidden" name="tag_id" value="" class="import_phonebookid">


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary">Import</button>
                    </form>
                </div>
            </div>
        </div>
        {{-- end modal import contact --}}


    </div>
    <!-- end modal import contact -->

    </div>
    <script src="{{ asset('js/phonebook.js') }}"></script>
</x-layout-dashboard>
