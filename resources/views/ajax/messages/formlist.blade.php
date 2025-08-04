<label for="message" class="form-label">Message</label>
<textarea type="text" name="message" class="form-control" id="message" required> </textarea>
<label for="buttontext" class="form-label">Button </label>
<input type="text" name="buttontext" class="form-control" id="buttonlist">
<label for="footer" class="form-label">Footer </label>
<input type="text" name="footer" class="form-control" id="footer" required>
{{-- create input section and each section have list menu --}}
<label for="ttile" class="form-label">Title List</label>
<input type="text" name="title" class="form-control" id="titlelist" required>
<div class="input-group">
    <span class="input-group-btn">
        <a id="image" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
            <i class="fa fa-picture-o"></i> Choose
        </a>
    </span>
    <input id="thumbnail" class="form-control" type="text" name="image">
</div>
<button type="button" id="addlist" class="btn btn-primary btn-sm mt-4">Add List</button>
<button type="button" id="reducelist" class="btn btn-danger btn-sm mt-4">Reduce List</button><br>

<div class="area-list">

</div>
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    // add list when click,maximal 5 list
    $(document).ready(function() {
        $('#image').filemanager('file');



        var max_fields = 5; //maximum input boxes allowed
        var wrapper = $(".area-list"); //Fields wrapper
        var add_button = $("#addlist"); //Add button ID
        var x = 0; //initlal text box count
        $(add_button).click(function(e) { //on add input button click
            e.preventDefault();
            if (x < max_fields) { //max input box allowed
                x++; //text box increment
                $(wrapper).append('<div class="form-group listinput"><label for="list' + x +
                    '" class="form-label">List ' + x + '</label><input type="text" name="list[' +
                    x + ']" class="form-control" id="list' + x + '" required></div>'); //add input box
            } else {
                toastr['warning']('Maximal 5 list');
            }
        });
        // reduce list when click
        $(document).on('click', '#reducelist', function(e) {
            e.preventDefault();
            if (x > 0) {
                $('.listinput').last().remove();
                x--;
            }
        });
    });
</script>
