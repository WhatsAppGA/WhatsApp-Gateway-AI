<label for="message" class="form-label">Message</label>
<textarea type="text" value="" name="message" class="form-control" id="message" required> </textarea>

<label for="footer" class="form-label">Footer message *optional</label>
<input type="text" name="footer" class="form-control" id="footer" >

 <label class="form-label">Image <span class="text-sm text-warbubg">*OPTIONAL</span></label>
                   <div class="input-group">
                     <span class="input-group-btn">
                       <a id="image" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                         <i class="fa fa-picture-o"></i> Choose
                       </a>
                     </span>
                     <input id="thumbnail" class="form-control"  type="text" name="image" >
                   </div>
                   <label class="form-label">Template Button <span class="text-sm text-warbubg">*Minimum 1 template button</span></label><br>
<button type="button" id="addbutton" class="btn btn-primary btn-sm">Add Button</button>
<button type="button" id="reduceButton" class="btn btn-danger btn-sm">Reduce Button</button>
<div id="emailHelp" class="form-text">Template = Button Link or button call message,<br>
    if you want to send Call button you can fill like this : call|YourText|Yournumber<br>
    if you want to send Link button, you can fill like this : url|YourText|YourLink<br>

    type only have two value, url and call !
</div>

<label for="template1" class="form-label">Template 1</label>
<input type="text" name="template[1]" value="type|text|linkornumber" class="form-control" id="template[1]" required>

<div class="template-area">

</div>

<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    // add button when click add button maximal 3 button
    $(document).ready(function() {
        $('#image').filemanager('file')
       
        var max_fields = 3; //maximum input boxes allowed
        var wrapper = $(".template-area"); //Fields wrapper
        var add_button = $("#addbutton"); //Add button ID
        var x = 1; //initlal text box count
        $(add_button).click(function(e) { //on add input button click
            e.preventDefault();
            if (x < max_fields) { //max input box allowed
                x++; //text box increment
              //  $(wrapper).append('<div class="form-group templateinput"><label for="template' + x + '" class="form-label">Template ' + x + '</label><input type="text" name="template' + x + '" class="form-control" id="template' + x + '" required></div>'); //add input box
                // change append above from template1,template2,template3 to template[1] template[2] template[3]
                $(wrapper).append('<div class="form-group templateinput"><label for="template[' + x + ']" class="form-label">Template ' + x + '</label><input type="text" name="template[' + x + ']" class="form-control" id="template[' + x + ']" required></div>'); //add input box
            } else {
                toastr['warning'](`Maximum template button is ${max_fields} !`);
            }
        });
        // reduce button when click
        $(document).on('click', '#reduceButton', function(e) {
            e.preventDefault();
            if(x > 1){
                $('.templateinput').last().remove();
                x--;
            }
        });
       
    });
</script>

