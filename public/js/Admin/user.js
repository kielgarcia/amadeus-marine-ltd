// Global Variables
var HasChanged = false;
var initial_user_name;
var initial_user_email;
var initial_user_role;
var initial_user_status;

$(document).ready(function(){

    $('#users_tbl').DataTable({
        "order": [[ 0, 'asc' ]],
        "columnDefs": [{ "orderable": false, "targets": 4 }]
    });
});

// Start New User JS Functions 
$('#new_user_btn').on('click',function(){
    $('#new_user_modal').modal('show');
});

$('#save_user_btn').on('click',function(){
    
    var user_name = $('#new_user_name').val();
    var user_email = $('#new_user_email').val();
    var user_role = $('#new_user_role').val();

    if( 
        (user_name != null && user_name.trim() != "") &&
        (user_email != null && user_email.trim() != "") &&
        (user_role != null && user_role.trim() != "")
    ){
        $('#new_user_form').submit();
    }else{
        setEmptyClassInNewUserForm();
    }
});

function setEmptyClassInNewUserForm(){
    var new_user_name = $('#new_user_name').val();
    var new_user_email = $('#new_user_email').val();
    var new_user_role = $('#new_user_role').val();

    if( new_user_name == null || new_user_name.trim() == "" ){
        $('#new_user_name').addClass('emptyField');
    }else{
        $('#new_user_name').removeClass('emptyField');
    }

    if( new_user_email == null || new_user_email.trim() == "" ){
        $('#new_user_email').addClass('emptyField');
    }else{
        $('#new_user_email').removeClass('emptyField');
    }

    if( new_user_role == null || new_user_role.trim() == ""){
        $('#new_user_role').addClass('emptyField');
    }else{
        $('#new_user_role').removeClass('emptyField');
    }
};

$('#new_user_form').submit( function(){
    $('#save_user_btn').prop('disabled',true);
    $('#save_user_btn').text('...');
});

$('#new_user_modal').on('hide.bs.modal',function(){
    $('#new_user_form').trigger('reset');
    $('#new_user_name').removeClass('emptyField');
    $('#new_user_email').removeClass('emptyField');
    $('#new_user_role').removeClass('emptyField');
});
// End New User JS Functions

// Start Edit User JS Functions
$('.edit_user_btn').on('click',function(){
    var user_id = $(this).data('user_id');
    initial_user_name = $(this).data('user_name');
    initial_user_email = $(this).data('user_email');
    initial_user_role = $(this).data('user_role');
    initial_user_status = $(this).data('user_status');

    $('#reset_password_user_id').val(user_id);
    $('#edit_user_id').val(user_id);
    $('#edit_user_name').val(initial_user_name);
    $('#edit_user_email').val(initial_user_email);
    $('#edit_user_role').val(initial_user_role);
    $('#edit_user_status').val(initial_user_status);

    $('#update_user_btn').prop('disabled', true);
    setEmptyClassInEditUserForm();
    $('#edit_user_modal').modal('show');
});

$('#edit_user_form').on('keyup change',function(){
    checkIfEditUserFormHasChanged();
    checkIfEditUserFormHasFilled();
});

function checkIfEditUserFormHasChanged(){
    var edited_user_name = $('#edit_user_name').val();
    var edited_user_email = $('#edit_user_email').val();
    var edited_user_role = $('#edit_user_role').val();
    var edited_user_status = $('#edit_user_status').val();

    if(
        (edited_user_name != initial_user_name) ||
        (edited_user_email != initial_user_email) ||
        (edited_user_role != initial_user_role) ||
        (edited_user_status != initial_user_status)
     ){
         HasChanged = true;
     }else{
         HasChanged = false;
     }

     return HasChanged;
};

function checkIfEditUserFormHasFilled(){
    var user_name = $('#edit_user_name').val();
    var user_email = $('#edit_user_email').val();
    var user_role = $('#edit_user_role').val();
    var user_status = $('#edit_user_status').val();

    if(
        (user_name != null && user_name.trim() != "") &&
        (user_email != null && user_email.trim() != "") &&
        (user_role != null && user_role.trim() != "") &&
        (user_status != null && user_status.trim() != "") &&
        (HasChanged == true)
    ){
        $('#update_user_btn').prop('disabled',false);
    }else{
        $('#update_user_btn').prop('disabled',true);
        setEmptyClassInEditUserForm();
    }
};

function setEmptyClassInEditUserForm(){
    var edit_user_name = $('#edit_user_name').val();
    var edit_user_email = $('#edit_user_email').val();
    var edit_user_role = $('#edit_user_role').val();

    if( edit_user_name == null || edit_user_name.trim() == "" ){
        $('#edit_user_name').addClass('emptyField');
    }else{
        $('#edit_user_name').removeClass('emptyField');
    }

    if( edit_user_email == null || edit_user_email.trim() == "" ){
        $('#edit_user_email').addClass('emptyField');
    }else{
        $('#edit_user_email').removeClass('emptyField');
    }

    if( edit_user_role == null || edit_user_role.trim() == ""){
        $('#edit_user_role').addClass('emptyField');
    }else{
        $('#edit_user_role').removeClass('emptyField');
    }
};

$('#update_user_btn').on('click', function(){
    $('#edit_user_form').submit();
});

$('#edit_user_form').on('submit', function(){
    $('#update_user_btn').prop('disabled',true);
    $('#reset_user_password_btn').prop('disabled',true);
    $('#update_user_btn').text('...');
    $('#reset_user_password_btn').text('...');
});
// End Edit User JS Functions

// Start Reset Password JS Functions 
$('#reset_user_password_btn').on('click', function(){
    $('#reset_user_password_form').submit();
});

$('#reset_user_password_form').on('submit',function(){
    $('#update_user_btn').prop('disabled',true);
    $('#reset_user_password_btn').prop('disabled',true);
    $('#update_user_btn').text('...');
    $('#reset_user_password_btn').text('...');
});
// End Reset Password JS Functions 

