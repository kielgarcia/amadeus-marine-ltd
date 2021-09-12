// Global Variables
var HasChanged = false;
var initial_role_name;
var initial_role_permissions = [];

$(document).ready(function(){
    $('#roles_tbl').DataTable({
        "order": [[ 0, 'asc' ]],
        "columnDefs": [{ "orderable": false, "targets": 2 }]
    });

    $('.js-example-basic-multiple').select2({
        placeholder: "Select Permission",
        allowClear: true
    });
});

// Start New Role JS Functions
$('#new_role_btn').on('click',function(){
    $('#new_role_modal').modal('show');
});

$('#save_role_btn').on('click',function(){
    var role_name = $('#new_role_name').val();
    var role_permissions = $('#new_role_permissions').val();

    if(
        (role_name != null && role_name.trim() != "") &&
        (role_permissions != null)
    ){
        $('#new_role_form').submit();
    }else{
        setEmptyClassInNewRoleForm();
    }
});

function setEmptyClassInNewRoleForm(){
    var new_role_name = $('#new_role_name').val();
    var new_role_permissions = $('#new_role_permissions').val();

    if(new_role_name == null || new_role_name.trim() == ""){
        $('#new_role_name').addClass('emptyField');
    }else{
        $('#new_role_name').removeClass('emptyField');
    }

    if(new_role_permissions == null){
        $($('#new_role_permissions').select2("container")).addClass('error');
    }else{
        $($('#new_role_permissions').select2("container")).removeClass('error');

    }
};

$('#new_role_form').on('submit',function(){
    $('#save_role_btn').prop('disabled',true);
    $('#save_role_btn').text('...');
});

$('#new_role_modal').on('hide.bs.modal',function(){
    $('#new_role_form').trigger('reset');
    $('#new_role_name').removeClass('emptyField');
});
// End New Role JS Functions

// Start Edit Role JS Functions 
$('.edit_role_btn').on('click',function(){
    $('#edit_role_id').val($(this).data('role_id'));
    $('#edit_role_name').val($(this).data('role_name'));

    var role_id = $(this).data('role_id');

    $.ajax({
        url: '/get_permissions/' + role_id,
        type: 'get',
        dataType: 'json',
        success: function(data){
            var role_permissions = [];
            for (i in data.permissions){
                role_permissions.push(data.permissions[i].permission_id);
            }

            initial_role_name = $('#edit_role_name').val();
            initial_role_permissions = role_permissions;

            $('#edit_role_permissions').val(role_permissions).trigger('change');
            
        },
        error: function(jqXHR, textStatus, errorThrown) {}

    }); //AJAX END
    $('#update_role_btn').prop('disabled', true);
    $('#edit_role_modal').modal('show');
});

$('#edit_role_form').on('keyup change',function(){
    checkIfEditRoleFormHasChanged();
    checkIfEditRoleFormHasFilled();
});

function checkIfEditRoleFormHasChanged(){
    var edited_role_name = $('#edit_role_name').val();
    
    var edited_role_permissions = $('#edit_role_permissions').val();
    var array_difference = [];
    initial_role_permissions = initial_role_permissions.toString().split(',').map(Number);
    edited_role_permissions = edited_role_permissions.toString().split(',').map(Number);

    for (var i in initial_role_permissions){
        if(edited_role_permissions.indexOf(initial_role_permissions[i]) === -1){
            array_difference.push(initial_role_permissions[i]);
        } 
    }
    for(i in edited_role_permissions){
        if(initial_role_permissions.indexOf(edited_role_permissions[i]) === -1){
            array_difference.push(edited_role_permissions[i]);
        } 
    }
  
    if(
        (edited_role_name.toLowerCase() != initial_role_name.toLowerCase()) ||
        (array_difference.length > 0)
    ){
        HasChanged = true;
    }
    else{
        HasChanged = false;
    }
    return HasChanged;
};

function checkIfEditRoleFormHasFilled(){
    var role = $('#edit_role_name').val();
    var permissions = $('#edit_role_permissions').val();
    var substring = permissions.slice(0,1);
      
    if(
        (role != null && role.trim() != "") &&
        (substring != "") &&
        (HasChanged == true)
    ){
        $('#update_role_btn').prop('disabled',false);
    }
    else{
        $('#update_role_btn').prop('disabled',true);
        setEmptyClassInEditRoleForm();
    }
};

function setEmptyClassInEditRoleForm(){
    var edit_role_name = $('#edit_role_name').val();

    if(edit_role_name == null || edit_role_name.trim() == ""){
        $('#edit_role_name').addClass('emptyField');
    }else{
        $('#edit_role_name').removeClass('emptyField');
    }
};

$('#update_role_btn').on('click',function(){
    $('#edit_role_form').submit();
});

$('#edit_role_form').on('submit',function(){
    $('#update_role_btn').prop('disabled',true);
    $('#update_role_btn').text('...');
});
// End Edit Role JS Functions 
