<x-layout-dashboard title="Phone book">
    @if (session()->has('alert'))
        <x-alert>
            @slot('type', session('alert')['type'])
            @slot('msg', session('alert')['msg'])
        </x-alert>
    @endif
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Data Campaign</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$campaign_name}}</li>
                </ol>
            </nav>
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
                                    <th>Receiver</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                  
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($blasts as $blast)
                                    <tr>
                                        <td>{{ $blast->receiver }}</td>
                                      
                                       
                                      
                                       
                                        <td>
                                            @if ($blast->status == 'success')
                                                <span class="badge rounded-pill bg-success">Sent</span>
                                            @elseif ($blast->status == 'pending')
                                                <span class="badge rounded-pill bg-warning">Pending</span>
                                            @else
                                                <span
                                                    class="badge rounded-pill bg-danger">{{ $blast->status }}</span>
                                            @endif
                                        </td>
                                         <td>{{ $blast->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item {{ $blasts->currentPage() == 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $blasts->previousPageUrl() }}">Previous</a>
                            </li>

                            @for ($i = 1; $i <= $blasts->lastPage(); $i++)
                                <li class="page-item {{ $blasts->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $blasts->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            <li
                                class="page-item {{ $blasts->currentPage() == $blasts->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $blasts->nextPageUrl() }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
     
    </div>
    {{-- end table --}}

</x-layout-dashboard>

