<div class="form-group">
    <label for="keyword">Keyword</label>
    <input type="text" class="form-control" id="keyword" name="keyword" value="{{ $keyword }}" readonly>
</div>

<div class="form-group">
    <label for="message">Message</label>
    <textarea class="form-control" id="message" name="message" rows="3">{{ $message }}</textarea>
</div>

<div class="form-group">
    <label for="footer">Footer</label>
    <input type="text" class="form-control" id="footer" name="footer" value="{{ $footer }}">
</div>


<label for="image">Image</label>
<div class="input-group">
    <span class="input-group-btn">
        <a id="image" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
            <i class="fa fa-picture-o"></i> {{ __('Choose') }}
        </a>
    </span>
    <input id="thumbnail" class="form-control" type="text" name="image" value="{{ $image ?? '' }}">
</div>

<h5>Buttons</h5>
<div id="buttons-container">
    @foreach ($buttons as $index => $button)
        <div class="button-item" data-index="{{ $index }}">
            <h6>Button {{ $index + 1 }}</h6>
            <div class="form-group">
                <label for="buttons[{{ $index }}][displayText]">Display Text</label>
                <input type="text" class="form-control" name="button[{{ $index }}][displayText]"
                    value="{{ $button['displayText'] }}">
            </div>

            <div class="form-group">
                <label for="buttons[{{ $index }}][type]">Type</label>
                <select class="form-control button-type" name="buttons[{{ $index }}][type]">
                    <option value="call" {{ $button['type'] == 'call' ? 'selected' : '' }}>Call</option>
                    <option value="url" {{ $button['type'] == 'url' ? 'selected' : '' }}>URL</option>
                    <option value="copy" {{ $button['type'] == 'copy' ? 'selected' : '' }}>Copy</option>
                </select>
            </div>

            <div class="form-group call-options" style="display: {{ $button['type'] == 'call' ? 'block' : 'none' }}">
                <label for="buttons[{{ $index }}][phoneNumber]">Phone Number</label>
                <input type="text" class="form-control" name="buttons[{{ $index }}][phoneNumber]"
                    value="{{ $button['phoneNumber'] ?? '' }}">
            </div>

            <div class="form-group url-options" style="display: {{ $button['type'] == 'url' ? 'block' : 'none' }}">
                <label for="buttons[{{ $index }}][url]">URL</label>
                <input type="text" class="form-control" name="buttons[{{ $index }}][url]"
                    value="{{ $button['url'] ?? '' }}">
            </div>

            <div class="form-group copy-options" style="display: {{ $button['type'] == 'copy' ? 'block' : 'none' }}">
                <label for="buttons[{{ $index }}][copyText]">Copy Text</label>
                <input type="text" class="form-control" name="buttons[{{ $index }}][copyText]"
                    value="{{ $button['copyText'] ?? '' }}">
            </div>

            <button type="button" class="btn btn-danger remove-button" data-index="{{ $index }}">Remove
                Button</button>
            <hr>
        </div>
    @endforeach
</div>

<button type="button" id="add-button" class="btn btn-primary">Add Button</button>
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    // Fungsi untuk menambah button baru

    $('#image').filemanager('file');
    document.getElementById('add-button').addEventListener('click', function() {
        const index = document.querySelectorAll('.button-item').length;
        const buttonHTML = `
            <div class="button-item" data-index="${index}">
                <h6>Button ${index + 1}</h6>
                <div class="form-group">
                    <label for="buttons[${index}][displayText]">Display Text</label>
                    <input type="text" class="form-control" name="buttons[${index}][displayText]" value="">
                </div>

                <div class="form-group">
                    <label for="buttons[${index}][type]">Type</label>
                    <select class="form-control button-type" name="buttons[${index}][type]">
                        <option value="reply">Reply</option>
                        <option value="call">Call</option>
                        <option value="url">URL</option>
                        <option value="copy">Copy</option>
                    </select>
                </div>

                <div class="form-group call-options" style="display: none;">
                    <label for="buttons[${index}][phoneNumber]">Phone Number</label>
                    <input type="text" class="form-control" name="buttons[${index}][phoneNumber]" value="">
                </div>

                <div class="form-group url-options" style="display: none;">
                    <label for="buttons[${index}][url]">URL</label>
                    <input type="text" class="form-control" name="buttons[${index}][url]" value="">
                </div>

                <div class="form-group copy-options" style="display: none;">
                    <label for="buttons[${index}][copyText]">Copy Text</label>
                    <input type="text" class="form-control" name="buttons[${index}][copyText]" value="">
                </div>

                <button type="button" class="btn btn-danger remove-button" data-index="${index}">Remove Button</button>
                <hr>
            </div>
        `;
        document.getElementById('buttons-container').insertAdjacentHTML('beforeend', buttonHTML);
    });

    // Fungsi untuk menghapus button
    document.addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('remove-button')) {
            const buttonItem = event.target.closest('.button-item');
            buttonItem.remove();
        }
    });

    // Fungsi untuk toggle visibility of options based on selected type
    document.addEventListener('change', function(event) {
        if (event.target && event.target.classList.contains('button-type')) {
            const buttonItem = event.target.closest('.button-item');
            const type = event.target.value;

            // Sembunyikan semua opsi
            buttonItem.querySelector('.call-options').style.display = 'none';
            buttonItem.querySelector('.url-options').style.display = 'none';
            buttonItem.querySelector('.copy-options').style.display = 'none';

            // Tampilkan opsi yang sesuai dengan type
            if (type === 'call') {
                buttonItem.querySelector('.call-options').style.display = 'block';
            } else if (type === 'url') {
                buttonItem.querySelector('.url-options').style.display = 'block';
            } else if (type === 'copy') {
                buttonItem.querySelector('.copy-options').style.display = 'block';
            }
        }
    });
</script>
