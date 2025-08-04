<x-layout-dashboard title="Device Settings">
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
                                <li class="breadcrumb-item active" aria-current="page">Device Settings</li>
                                <li class="breadcrumb-item active" aria-current="page">{{$number->body}}</li>
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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                    <div class="card">
                        <div class="card-body">
                            <div class="row mt-4">
                                <div class="col-md-12 mx-auto">
                                    <form action="{{ route('deviceSettings',$number->body.'')}}" method="POST">
                                        @csrf
                                        <h5>AI Bot Reply Settings (Google Gemini)</h5>
                                        <div class="row mt-3">
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-control" name="gemini_status">
                                                    <option value="disabled" {{($number->gemini_status == 'disabled' ? 'selected':'')}} >Disabled</option>
                                                    <option value="enabled"{{($number->gemini_status == 'enabled' ? 'selected':'')}}>Enabled</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Gemini Model</label>
                                                <select class="form-control" name="gemini_model">
                                                    <option value="gemini-1.5-pro" {{($number->gemini_model == 'gemini-1.5-pro' ? 'selected':'')}} >1.5 Pro</option>
                                                    <option value="gemini-1.5-flash" {{($number->gemini_model == 'gemini-1.5-flash' ? 'selected':'')}} >1.5 Flash</option>
                                                    <option value="gemini-1.5-flash-8b" {{($number->gemini_model == 'gemini-1.5-flash-8b' ? 'selected':'')}} >1.5 Flash-8B</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="form-label">Gemini API Key</label>
                                                <input class="form-control" name="gemini_api_key"  placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxx"  value="{{$number->gemini_api_key}}">
                                            </div>
                                            <div class="form-group col-md-12 mt-3">
                                                <label class="form-label">Model Instructions</label>
                                                <textarea class="form-control" name="gemini_instructions" rows="5" style="width:100%" placeholder="Your role is .............">{{$number->gemini_instructions}}</textarea>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="mt-3">Auto Audio Transcription/Translation (Whisper)</h5>
                                        <div class="row mt-3">
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-control" name="transcription_status">
                                                    <option value="disabled" {{($number->transcription_status == 'disabled' ? 'selected':'')}}>Disabled</option>
                                                    <option value="enabled" {{($number->transcription_status == 'enabled' ? 'selected':'')}}>Enabled</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Whisper Model</label>
                                                <select class="form-control" name="transcription_model">
                                                    <option value="whisper-large-v3-turbo" {{($number->transcription_model == 'whisper-large-v3-turbo' ? 'selected':'')}} >Whisper Large v3 Turbo</option>
                                                    <option value="whisper-large-v3" {{($number->transcription_model == 'whisper-large-v3' ? 'selected':'')}} >Whisper Large v3</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="form-label">Huggingface API Key</label>
                                                <input class="form-control" name="huggingface_api_key" placeholder="hf_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" value="{{$number->huggingface_api_key}}">
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="mt-3">Status/Chat Settings</h5>
                                        <div class="row mt-3">
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Auto Save Status</label>
                                                <select class="form-control" name="auto_status_save">
                                                    <option value="disabled" {{($number->auto_status_save == 'disabled' ? 'selected':'')}} >Disabled</option>
                                                    <option value="enabled" {{($number->auto_status_save == 'enabled' ? 'selected':'')}} >Enabled</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Auto Forward Status</label>
                                                <select class="form-control" name="auto_status_forward">
                                                    <option value="disabled" {{($number->auto_status_forward == 'disabled' ? 'selected':'')}} >Disabled</option>
                                                    <option value="enabled" {{($number->auto_status_forward == 'enabled' ? 'selected':'')}} >Enabled</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Status Nudity Detection</label>
                                                <select class="form-control" name="status_nudity_detection">
                                                    <option value="disabled" {{($number->status_nudity_detection == 'disabled' ? 'selected':'')}} >Disabled</option>
                                                    <option value="enabled" {{($number->status_nudity_detection == 'enabled' ? 'selected':'')}} >Enabled</option>
                                                </select>
                                            </div> 
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Chat Nudity Detection</label>
                                                <select class="form-control" name="chat_nudity_detection">
                                                    <option value="disabled" {{($number->chat_nudity_detection == 'disabled' ? 'selected':'')}} >Disabled</option>
                                                    <option value="enabled" {{($number->chat_nudity_detection == 'enabled' ? 'selected':'')}} >Enabled</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-3" style="font-size:10px">
                                                <b>Note:</b>
                                                <ul>
                                                    <li><b>Auto Save Status</b>: All statuses of favorite contacts will be saved in Me Chat.</li>
                                                    <li><b>Auto Forward Status</b>: Status will be forwarded automatically if someone says Send, Send me, please send etc.</li>
                                                    <li><b>Status Nudity Detection</b>: Status Videos/Images will be analyzed to check nudity, if found, will reply that you content may be unsave.</li>
                                                    <li><b>Chat Nudity Detection</b>: Received Images will be analyzed to check nudity, if found, will reply that you content may be unsave.</li>
                                                </ul>
                                                <b>Motivation:</b>
                                                <ul>
                                                    <li>Nudity detection feature is useful to protect your Contacts, as sometime people mistakenly post unsave content on status.</li>
                                                </ul>
                                            </div>
                                            
                                        </div>
                                        <div class="row m-t-lg mt-4">
                                            <div class="col mt-2">
                                                <button type="submit" name='saveSettings' class="btn btn-info text-white m-t-sm">Save Settings</button>
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
