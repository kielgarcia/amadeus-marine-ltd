// Global Variables
var HullHasChanged = false;
var initial_hull_no;
var initial_hull_description;


$(document).ready(function(){
    $('#hulls_tbl').DataTable({
        autoWidth: false,
        "order": [[ 5, 'desc' ]],
        "columnDefs": [{ "orderable": false, "targets": 5 }],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "pageLength": -1
    });
});


// Start New Hull JS Functions 
$('#new_hull_btn').on('click',function(){
    $('#new_hull_modal').modal('show');
});

$('#save_hull_btn').on('click',function(){
    var new_hull_no = $('#new_hull_no').val();
    var new_hull_description = $('#new_hull_description').val();

    if(
        (new_hull_no != null && new_hull_no.trim() != "") &&
        (new_hull_description != null && new_hull_description.trim() != "")
    ){
        $('#new_hull_form').submit();
    }else{
        setEmptyClassInNewHullForm();
    }
});

function setEmptyClassInNewHullForm(){
    var new_hull_no = $('#new_hull_no').val();
    var new_hull_description = $('#new_hull_description').val();

    if( new_hull_no == null || new_hull_no.trim() == "" ){
        $('#new_hull_no').addClass('emptyField');
    }else{
        $('#new_hull_no').removeClass('emptyField');
    }

    if( new_hull_description == null || new_hull_description.trim() == "" ){
        $('#new_hull_description').addClass('emptyField');
    }else{
        $('#new_hull_description').removeClass('emptyField');
    }
};

$('#new_hull_form').on('submit',function(){
    $('#save_hull_btn').prop('disabled',true).text('...');
});

$('#new_hull_modal').on('hide.bs.modal',function(){
    $('#new_hull_form').trigger('reset');
    $('#new_hull_no').removeClass('emptyField');
    $('#new_hull_description').removeClass('emptyField');
});
// End New Hull JS Functions 

// Start Delete Hull JS Functions
$('.delete_hull_btn').on('click',function(){
    $('#delete_hull_id').val($(this).data('delete_hull_id'));
    $('#delete_hull_description').text($(this).data('delete_hull_no'));
    $('#delete_hull_modal').modal('show'); 
});

$('#confirm_delete_hull_btn').on('click',function(){
    $('#delete_hull_form').submit();
});

$('#delete_hull_form').on('submit',function(){
    $('#delete_hull_btn').prop('disabled',true);
    $('#delete_hull_btn').text('...');
});
// End Delete Hull JS Functions

// Start Edit Hull JS Functions
$('.edit_hull_btn').on('click',function(){
    initial_hull_no = $(this).data('edit_hull_no');
    initial_hull_description = $(this).data('edit_hull_description');

    $('#edit_hull_id').val($(this).data('edit_hull_id'));
    $('#edit_hull_no').val($(this).data('edit_hull_no'));
    $('#edit_hull_description').val($(this).data('edit_hull_description'));

    $('#update_hull_btn').prop('disabled', true);
    setEmptyClassInEditHullForm();
    $('#edit_hull_modal').modal('show');
});

$('#edit_hull_form').on('keyup change',function(){
    checkIfEditHullFormHasChanged();
    checkIfEditHullFormHasFilled();
});

function checkIfEditHullFormHasChanged(){
    var edited_hull_no = $('#edit_hull_no').val();
    var edited_hull_description = $('#edit_hull_description').val();

    if(
        (edited_hull_no != initial_hull_no) ||
        (edited_hull_description != initial_hull_description)
    ){
        HullHasChanged = true;
    }else{
        HullHasChanged = false;
    }

    return HullHasChanged;
}

function checkIfEditHullFormHasFilled(){
    var edit_hull_no = $('#edit_hull_no').val();
    var edit_hull_description = $('#edit_hull_description').val();

    if(
        (edit_hull_no != null && edit_hull_no.trim() != "") &&
        (edit_hull_description != null && edit_hull_description.trim() != "") &&
        (HullHasChanged == true)
    ){
        $('#update_hull_btn').prop('disabled',false);
    }else{
        $('#update_hull_btn').prop('disabled',true);
        setEmptyClassInEditHullForm();
    }
};

function setEmptyClassInEditHullForm(){
    var edit_hull_no = $('#edit_hull_no').val();
    var edit_hull_description = $('#edit_hull_description').val();

    if( edit_hull_no == null || edit_hull_no.trim() == "" ){
        $('#edit_hull_no').addClass('emptyField');
    }else{
        $('#edit_hull_no').removeClass('emptyField');
    }

    if( edit_hull_description == null || edit_hull_description.trim() == "" ){
        $('#edit_hull_description').addClass('emptyField');
    }else{
        $('#edit_hull_description').removeClass('emptyField');
    }
};

$('#update_hull_btn').on('click', function(){
    $('#edit_hull_form').submit();
});

$('#edit_hull_form').on('submit',function(){
    $('#update_hull_btn').prop('disabled',true).text('...');
});
// End Edit Hull JS Functions


