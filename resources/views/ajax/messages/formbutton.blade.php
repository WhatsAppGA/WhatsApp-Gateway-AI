<label for="message" class="form-label">Message</label>
<textarea type="text" name="message" class="form-control" id="message" required> </textarea>
<label for="footer" class="form-label">Footer message *optional</label>
<input type="text" name="footer" class="form-control" id="footer">
<label class="form-label">Image <span class="text-sm text-warbubg">*OPTIONAL</span></label>
<div class="input-group">
    <span class="input-group-btn">
        <a id="image" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
            <i class="fa fa-picture-o"></i> Choose
        </a>
    </span>
    <input id="thumbnail" class="form-control" type="text" name="image">
</div>
<button type="button" id="addbutton" class="btn btn-primary btn-sm mr-2 mt-4">Add Button</button>
<button type="button" id="reduceButton" class="btn btn-danger btn-sm ml-2 mt-4">Reduce Button</button>
<div class="button-area">

</div>



<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    // add button when click add button maximal 3 button
    $(document).ready(function() {
        $('#image').filemanager('file');
        var max_fields = 3; // Maximum number of buttons allowed
        var wrapper = $(".button-area"); // Wrapper for button forms
        var add_button = $("#addbutton"); // Add button ID
        var x = 0; // Initial button count

        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++; // Increment button count

                var buttonForm = `
                <div class="form-group buttoninput mt-3" id="buttonGroup${x}">
                    <label for="buttonType${x}" class="form-label">Button ${x} Type</label>
                    <select name="button[${x}][type]" class="form-control buttonType" id="buttonType${x}" data-index="${x}" required>
                        <option value="reply">Reply</option>
                        <option value="call">Call</option>
                        <option value="url">URL</option>
                        <option value="copy">Copy</option>
                    </select>
                    
                    <label for="buttonDisplayText${x}" class="form-label mt-2">Display Text</label>
                    <input type="text" name="button[${x}][displayText]" class="form-control" id="buttonDisplayText${x}" required>

                    <div class="additionalFields mt-2" id="additionalFields${x}"></div>
                    
                    <button type="button" class="btn btn-danger btn-sm mt-2 removeButton" data-index="${x}">Remove</button>
                </div>
            `;
                $(wrapper).append(buttonForm);
            } else {
                toastr['warning']('Maximum of 3 buttons allowed');
            }
        });

        // Handle button type change to display relevant additional fields
        $(document).on('change', '.buttonType', function() {
            var index = $(this).data('index');
            var selectedType = $(this).val();
            var additionalFields = $(`#additionalFields${index}`);

            additionalFields.empty(); // Clear existing fields

            if (selectedType === 'call') {
                additionalFields.append(`
                <label for="phoneNumber${index}" class="form-label">Phone Number</label>
                <input type="text" name="button[${index}][phoneNumber]" class="form-control" id="phoneNumber${index}" required>
            `);
            } else if (selectedType === 'url') {
                additionalFields.append(`
                <label for="url${index}" class="form-label">URL</label>
                <input type="text" name="button[${index}][url]" class="form-control" id="url${index}" required>
            `);
            } else if (selectedType === 'copy') {
                additionalFields.append(`
                <label for="copyText${index}" class="form-label">Copy Text</label>
                <input type="text" name="button[${index}][copyCode]" class="form-control" id="copyText${index}" required>
            `);
            }
        });

        // Remove button form
        $(document).on('click', '.removeButton', function(e) {
            e.preventDefault();
            var index = $(this).data('index');
            $(`#buttonGroup${index}`).remove();
            x--; // Decrement button count
        });
    });
</script>
