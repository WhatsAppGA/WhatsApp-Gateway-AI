<label for="message" class="form-label">{{ __('Message') }}</label>
<textarea type="text" name="message" class="form-control" id="message" required>{{ $message ?? '' }}</textarea>

<label for="buttontext" class="form-label">{{ __('Button') }}</label>
<input type="text" name="buttontext" class="form-control" id="buttonlist" value="{{ $buttontext ?? '' }}">

<label for="footer" class="form-label">{{ __('Footer') }}</label>
<input type="text" name="footer" class="form-control" id="footer" value="{{ $footer ?? '' }}" required>

{{-- Section for title list --}}
<label for="titlelist" class="form-label">{{ __('Title List') }}</label>
<input type="text" name="title" class="form-control" id="titlelist" value="{{ $title ?? '' }}"
    required>

<div class="input-group">
    <span class="input-group-btn">
        <a id="image" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
            <i class="fa fa-picture-o"></i> {{ __('Choose') }}
        </a>
    </span>
    <input id="thumbnail" class="form-control" type="text" name="image" value="{{ $image ?? '' }}">
</div>

<button type="button" id="addlist{{ $id }}"
    class="btn btn-primary btn-sm mt-4">{{ __('Add List') }}</button>
<button type="button" id="reducelist{{ $id }}"
    class="btn btn-danger btn-sm mt-4">{{ __('Reduce List') }}</button><br>

<div class="area-list{{ $id }}">
    @if (isset($list))
        @foreach ($list as $index => $l)
            <div class="form-group listinput{{ $id }}">
                <label for="list{{ $index + 1 }}" class="form-label">{{ __('List') }}
                    {{ $index + 1 }}</label>
                <input type="text" name="list[{{ $index + 1 }}]" class="form-control"
                    id="list{{ $index + 1 }}" value="{{ $l }}" required>
            </div>
        @endforeach
    @endif
</div>

<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $(document).ready(function() {
        $('#image').filemanager('file');

        var max_fields = 5; // Maximum input boxes allowed
        var wrapper = $(".area-list{{ $id }}"); // Fields wrapper
        var add_button = $("#addlist{{ $id }}"); // Add button ID

        @if (isset($sections[0]->rows))
            var x = {{ count($sections[0]->rows) ?? 0 }};
        @else
            var x = 0;
        @endif

        // Add list when clicked, maximum 5 lists
        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                $(wrapper).append(
                    '<div class="form-group listinput{{ $id }}"><label for="list' + x +
                    '" class="form-label">{{ __('List') }} ' + x +
                    '</label><input type="text" name="list[' +
                    x + ']" class="form-control" id="list' + x + '" required></div>'); // Add input box
            } else {
                toastr['warning']('{{ __('Maximal 5 list') }}');
            }
        });

        // Reduce list when clicked
        $(document).on('click', '#reducelist{{ $id }}', function(e) {
            e.preventDefault();
            var lastButton = wrapper.find('.listinput{{ $id }}:last');
            if (lastButton.length > 0) {
                lastButton.remove();
                x--;
            }
        });
    });
</script>
