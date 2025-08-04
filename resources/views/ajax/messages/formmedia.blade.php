<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

<label class="form-label">Media Url </label>
<div class="input-group media-area">
    <span class="input-group-btn">
        <a id="image" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
            <i class="fa fa-picture-o"></i> Choose
        </a>
    </span>
    <input id="thumbnail" class="form-control" type="text" name="url">
</div>
{{-- optional --}}
<div class="form-group mt-4">
    <label class="form-label">Media Type</label>
    {{-- dif flex gap 4 --}}

    <div class="d-flex ">
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline1" name="media_type" class="custom-control-input" value="image"
                >
            <label class="custom-control-label" for="customRadioInline1">Image</label>
        </div>

        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline1" name="media_type" class="custom-control-input"
                value="document" checked>
            <label class="custom-control-label" for="customRadioInline1">Document</label>
        </div>

        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline2" name="media_type" class="custom-control-input" value="video">
            <label class="custom-control-label" for="customRadioInline2">Video</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline3" name="media_type" class="custom-control-input" value="audio">
            <label class="custom-control-label" for="customRadioInline3">Voice Note</label>
        </div>
        {{-- pdf,xls,xlsx,doc,docx,zip,mp3 --}}
        {{-- <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline4" name="media_type" class="custom-control-input" value="pdf">
            <label class="custom-control-label" for="customRadioInline4">pdf</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline5" name="media_type" class="custom-control-input" value="xls">
            <label class="custom-control-label" for="customRadioInline5">xls</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline6" name="media_type" class="custom-control-input" value="xlsx">
            <label class="custom-control-label" for="customRadioInline6">xlsx</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline7" name="media_type" class="custom-control-input" value="doc">
            <label class="custom-control-label" for="customRadioInline7">doc</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline8" name="media_type" class="custom-control-input" value="docx">
            <label class="custom-control-label" for="customRadioInline8">docx</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline9" name="media_type" class="custom-control-input" value="zip">
            <label class="custom-control-label" for="customRadioInline9">zip</label>
        </div> --}}





    </div>
</div>
{{-- end optional --}}
{{-- <div class="type-audio form-group mt-4">
    <label for="" class="form-label">Audio Type</label>
    <select name="ptt" id="" class="form-control">
        <option value="audio">Audio</option>
        <option value="vn">VN ( Voice Note )</option>
    </select>
</div> --}}
<div class="form-group caption-area mt-4">

    <label for="caption" class="form-label">Caption</label>
    <textarea type="text" name="caption" class="form-control" id="caption" required> </textarea>
</div>

<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $('#image').filemanager('file')

    $('.type-audio').hide()


    // on media_type change
    // $('input[name="media_type"]').on('change', function() {
    //     let type = $(this).val()
    //     if (type == 'audio') {
    //         $('.type-audio').show()
    //     } else {
    //         $('.type-audio').hide()
    //     }

    //     if (type == 'image' || type == 'video' || type == 'pdf' || type == 'xls' || type == 'xlsx' || type ==
    //         'doc' || type == 'docx' || type == 'zip') {
    //         $('.caption-area').show()
    //     } else {
    //         $('.caption-area').hide()
    //     }
    // })
</script>
