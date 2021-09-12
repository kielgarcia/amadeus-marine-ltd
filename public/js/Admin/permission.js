// Global Variables
var HasChanged = false;
var initial_permission_name;
var initial_permission_descriptions;

$(document).ready(function(){
    $('#permissions_tbl').DataTable({
        "order": [[ 0, 'asc' ]],
        "columnDefs": [{ "orderable": false, "targets": 2 }]
    });
});

// Start New Permission JS Functions
$('#new_permission_btn').on('click',function(){
    $('#new_permission_modal').modal('show');
});

$('#save_permission_btn').on('click',function(){
    var new_permission_name = $('#new_permission_name').val();
    var new_permission_description = $('#new_permission_description').val();

    if(
        (new_permission_name != null && new_permission_name.trim() != "") &&
        (new_permission_description != null && new_permission_description.trim() != "")
    ){
        $('#new_permission_form').submit();
    }else{
        setEmptyClassInNewPermissionForm();
    }
});

function setEmptyClassInNewPermissionForm() {
    var new_permission_name = $('#new_permission_name').val();
    var new_permission_description = $('#new_permission_description').val();

    if( new_permission_name == null || new_permission_name.trim() == "" ){
        $('#new_permission_name').addClass('emptyField');
    }else{
        $('#new_permission_name').removeClass('emptyField');
    }

    if( new_permission_description == null || new_permission_description.trim() == "" ){
        $('#new_permission_description').addClass('emptyField');
    }else{
        $('#new_permission_description').removeClass('emptyField');
    }
};

$('#new_permission_form').on('submit', function(){
    $('#save_permission_btn').prop('disabled',true);
    $('#save_permission_btn').text('...');
});

$('#new_permission_modal').on('hide.bs.modal',function(){
    $('#new_permission_form').trigger('reset');
    $('#new_permission_name').removeClass('emptyField');
    $('#new_permission_description').removeClass('emptyField');
});
// End New Permission JS Functions

// Start Edit Permission JS Functions
$('.edit_permission_btn').on('click', function(){
    initial_permission_name = $(this).data('permission_name');
    initial_permission_description = $(this).data('permission_description');

    $('#edit_permission_id').val($(this).data('permission_id'));
    $('#edit_permission_name').val($(this).data('permission_name'));
    $('#edit_permission_description').val($(this).data('permission_description'));
    $('#update_permission_btn').prop('disabled', true);
    setEmptyClassInEditPermissionForm();
    $('#edit_permission_modal').modal('show');
});

$('#edit_permission_form').on('keyup change',function(){
    checkIfEditPermissionFormHasChanged();
    checkIfEditPermissionFormHasFilled();
});

function checkIfEditPermissionFormHasChanged(){
    var edited_permission_name = $('#edit_permission_name').val();
    var edited_permission_description = $('#edit_permission_description').val();

    if(
        (edited_permission_name != initial_permission_name) ||
        (edited_permission_description != initial_permission_description)
    ){
        HasChanged = true;
    }else{
        HasChanged = false;
    }

    return HasChanged;
};

function checkIfEditPermissionFormHasFilled(){
    var permission_name = $('#edit_permission_name').val();
    var permission_description = $('#edit_permission_description').val();

    if(
        (permission_name != null && permission_name.trim() != "") &&
        (permission_description != null && permission_description.trim() != "") &&
        (HasChanged == true)
    ){
        $('#update_permission_btn').prop('disabled',false);
    }else{
        $('#update_permission_btn').prop('disabled',true);
        setEmptyClassInEditPermissionForm();
    }
};

function setEmptyClassInEditPermissionForm() {
    var edit_permission_name = $('#edit_permission_name').val();
    var edit_permission_description = $('#edit_permission_description').val();

    if( edit_permission_name == null || edit_permission_name.trim() == "" ){
        $('#edit_permission_name').addClass('emptyField');
    }else{
        $('#edit_permission_name').removeClass('emptyField');
    }

    if( edit_permission_description == null || edit_permission_description.trim() == "" ){
        $('#edit_permission_description').addClass('emptyField');
    }else{
        $('#edit_permission_description').removeClass('emptyField');
    }
};

$('#update_permission_btn').on('click', function(){
    $('#edit_permission_form').submit();
});

$('#edit_permission_form').on('submit', function(){
    $('#update_permission_btn').prop('disabled',true);
    $('#update_permission_btn').text('...');
});
// End Edit Permission JS Functions