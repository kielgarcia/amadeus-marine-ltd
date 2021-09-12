var WipDrawingHasChanged = false;
var WipDrawingUpdateFile = false;
var initial_wip_drawing_hull_no;
var initial_wip_drawing_no;
var initial_wip_drawing_title;

var finalizeWipDrawingId;
var finalizeWipDrawingNo;

$(document).ready(function(){
    $('#wip_drawings_tbl').DataTable({
        "order": [[ 4, 'desc' ]],
        "columnDefs": [{ "orderable": false, "targets": 6 }],
    });
    
});

// Start New WIP Drawing Functions
$('#new_wip_drawing_btn').on('click',function(){
    $('#new_wip_drawing_file_validation_lbl').prop('hidden',true);
    $('#new_wip_drawing_modal').modal('show');
});

$('#save_wip_drawing_btn').on('click',function(){
    var new_wip_drawing_hull_no = $('#new_wip_drawing_hull_no').val();
    var new_wip_drawing_no = $('#new_wip_drawing_no').val();
    var new_wip_drawing_title = $('#new_wip_drawing_title').val();
    var new_wip_drawing_dwf = $('#new_wip_drawing_dwf').val();
    var new_wip_drawing_dwg = $('#new_wip_drawing_dwg').val();

    if(
        ( new_wip_drawing_hull_no != null && new_wip_drawing_hull_no.trim() != "" ) &&
        ( new_wip_drawing_no != null && new_wip_drawing_no.trim() != "" ) &&
        ( new_wip_drawing_title != null && new_wip_drawing_title.trim() != "" ) &&
        ( 
            (new_wip_drawing_dwf != null && new_wip_drawing_dwf.trim() != "") || (new_wip_drawing_dwg != null && new_wip_drawing_dwg.trim() != "")
        )
    ){
        $('#new_wip_drawing_form').submit();
    }else{
        setEmptyClassInNewWipDrawingForm(new_wip_drawing_hull_no, new_wip_drawing_no, new_wip_drawing_title, new_wip_drawing_dwf, new_wip_drawing_dwg);

    }
});

function setEmptyClassInNewWipDrawingForm(new_wip_drawing_hull_no, new_wip_drawing_no, new_wip_drawing_title, new_wip_drawing_dwf, new_wip_drawing_dwg){

    if( new_wip_drawing_hull_no == null || new_wip_drawing_hull_no.trim() == ""){
        $('#new_wip_drawing_hull_no').addClass('emptyField');
    }else{
        $('#new_wip_drawing_hull_no').removeClass('emptyField');
    }

    if( new_wip_drawing_no == null || new_wip_drawing_no.trim() == ""){
        $('#new_wip_drawing_no').addClass('emptyField');
    }else{
        $('#new_wip_drawing_no').removeClass('emptyField');
    }

    if( new_wip_drawing_title == null || new_wip_drawing_title.trim() == ""){
        $('#new_wip_drawing_title').addClass('emptyField');
    }else{
        $('#new_wip_drawing_title').removeClass('emptyField');
    }

    if( (new_wip_drawing_dwf == null || new_wip_drawing_dwf.trim() == "") && (new_wip_drawing_dwg == null || new_wip_drawing_dwg.trim() == "") ){
        $('#new_wip_drawing_dwf').addClass('emptyField');
        $('#new_wip_drawing_dwg').addClass('emptyField');
        $('#new_wip_drawing_file_validation_lbl').prop('hidden',false);

    }
    else if( (new_wip_drawing_dwf != null || new_wip_drawing_dwf.trim() != "") || (new_wip_drawing_dwg != null || new_wip_drawing_dwg.trim() != "") ){
        $('#new_wip_drawing_dwf').removeClass('emptyField');
        $('#new_wip_drawing_dwg').removeClass('emptyField');
        $('#new_wip_drawing_file_validation_lbl').prop('hidden',true);
    }
};

$('#new_wip_drawing_modal').on('hide.bs.modal',function(){
    $('#new_wip_drawing_form').trigger('reset');

    $('#new_wip_drawing_hull_no').removeClass('emptyField');
    $('#new_wip_drawing_no').removeClass('emptyField');
    $('#new_wip_drawing_title').removeClass('emptyField');
    $('#new_wip_drawing_dwf').removeClass('emptyField');
    $('#new_wip_drawing_dwg').removeClass('emptyField');
});
// End New WIP Drawing Functions

// Start Edit WIP Drawing Functions
$('.edit_wip_drawing_btn').on('click',function(){
    $('#edit_wip_drawing_file_div').prop('hidden',true);

    finalizeWipDrawingId = $(this).data('edit_wip_drawing_id');
    finalizeWipDrawingNo = $(this).data('edit_wip_drawing_no');
    
    $('#edit_wip_drawing_id').val($(this).data('edit_wip_drawing_id'));
    $('#edit_wip_drawing_hull_no').val($(this).data('edit_wip_drawing_hull_id'));
    $('#edit_wip_drawing_no').val($(this).data('edit_wip_drawing_no'));
    $('#edit_wip_drawing_title').val($(this).data('edit_wip_drawing_title'));

    $('#update_wip_drawing_btn').prop('disabled', true);
    $('#edit_wip_drawing_modal').modal('show');
});

$('#view_edit_wip_drawing_file_cb').on('click',function(){
    if ($('#view_edit_wip_drawing_file_cb').is(':checked')){
        $('#edit_wip_drawing_file_div').prop('hidden',false);
        $('#view_edit_wip_drawing_file_cb').val(1);
    }else{
        $('#edit_wip_drawing_file_div').prop('hidden',true);
        $('#view_edit_wip_drawing_file_cb').val(0);
        $('#edit_wip_drawing_dwf').val("");
        $('#edit_wip_drawing_dwg').val("");
    }
});


$('#edit_wip_drawing_form').on('keyup change',function(){
    if( $('#view_edit_wip_drawing_file_cb').is(':checked') ){
        checkIfEditWipDrawingFileFormHasFilled();
        checkIfEditWipDrawingFormHasFilled();
    }else{
        checkIfEditWipDrawingFormHasChanged();
        checkIfEditWipDrawingFormHasFilled();
    }
});

function checkIfEditWipDrawingFormHasChanged(){
    var edited_wip_drawing_hull_no = $('#edit_wip_drawing_hull_no').val();
    var edited_wip_drawing_no = $('#edit_wip_drawing_no').val();
    var edited_wip_drawing_title = $('#edit_wip_drawing_title').val();
    
    if(
        (edited_wip_drawing_hull_no != initial_wip_drawing_hull_no) ||
        (edited_wip_drawing_no != initial_wip_drawing_no) ||
        (edited_wip_drawing_title != initial_wip_drawing_title)
    ){
        WipDrawingHasChanged = true;
    }else{
        WipDrawingHasChanged = false;
    }

    return WipDrawingHasChanged;
};

function checkIfEditWipDrawingFileFormHasFilled(){
    var edited_wip_drawing_dwf = $('#edit_wip_drawing_dwf').val();
    var edited_wip_drawing_dwg = $('#edit_wip_drawing_dwg').val();

    if(
        (edited_wip_drawing_dwf != null && edited_wip_drawing_dwf.trim() != "") ||
        (edited_wip_drawing_dwg != null && edited_wip_drawing_dwg.trim() != "")
    ){
        WipDrawingUpdateFile = true;
    }else{
        WipDrawingUpdateFile = false;
    }

    return WipDrawingUpdateFile;
};

function checkIfEditWipDrawingFormHasFilled(){
    var edit_wip_drawing_hull_no = $('#edit_wip_drawing_hull_no').val();
    var edit_wip_drawing_no = $('#edit_wip_drawing_no').val();
    var edit_wip_drawing_title = $('#edit_wip_drawing_title').val();
    
        if( $('#view_edit_wip_drawing_file_cb').is(':checked') ){ // If Checkbox is checked, required drawing files
            
            if( 
                (edit_wip_drawing_hull_no != null && edit_wip_drawing_hull_no.trim() != "") &&
                (edit_wip_drawing_no != null && edit_wip_drawing_no.trim() != "") &&
                (edit_wip_drawing_title != null && edit_wip_drawing_title.trim() != "") &&
                (WipDrawingUpdateFile == true)
                 
            ){
                $('#update_wip_drawing_btn').prop('disabled',false);
            }else{
                setEmptyClassInEditWipDrawingForm();
                $('#update_wip_drawing_btn').prop('disabled',true);
            }   

        }else{

            if( 
                (edit_wip_drawing_hull_no != null && edit_wip_drawing_hull_no.trim() != "") &&
                (edit_wip_drawing_no != null && edit_wip_drawing_no.trim() != "") &&
                (edit_wip_drawing_title != null && edit_wip_drawing_title.trim() != "") &&
                (WipDrawingHasChanged == true)
            ){
                $('#update_wip_drawing_btn').prop('disabled',false);
            }else{
                setEmptyClassInEditWipDrawingForm();
                $('#update_wip_drawing_btn').prop('disabled',true);
            }
        }
};

function setEmptyClassInEditWipDrawingForm(){
    var edit_wip_drawing_hull_no = $('#edit_wip_drawing_hull_no').val();
    var edit_wip_drawing_no = $('#edit_wip_drawing_no').val();
    var edit_wip_drawing_title = $('#edit_wip_drawing_title').val();
    var edit_wip_drawing_dwf = $('#edit_wip_drawing_dwf').val();
    var edit_wip_drawing_dwg = $('#edit_wip_drawing_dwg').val();

    if(edit_wip_drawing_hull_no == null || edit_wip_drawing_hull_no.trim() == ""){
        $('#edit_wip_drawing_hull_no').addClass('emptyField');
    }else{
        $('#edit_wip_drawing_hull_no').removeClass('emptyField');
    }

    if(edit_wip_drawing_no == null || edit_wip_drawing_no.trim() == ""){
        $('#edit_wip_drawing_no').addClass('emptyField');
    }else{
        $('#edit_wip_drawing_no').removeClass('emptyField');
    }

    if(edit_wip_drawing_title == null || edit_wip_drawing_title.trim() == ""){
        $('#edit_wip_drawing_title').addClass('emptyField');
    }else{
        $('#edit_wip_drawing_title').removeClass('emptyField');
    }

    if( 
        ( edit_wip_drawing_dwf == null || edit_wip_drawing_dwf.trim() == "") &&
        ( edit_wip_drawing_dwg == null || edit_wip_drawing_dwg.trim() == "") 
    ){
        $('#edit_wip_drawing_dwf').addClass('emptyField');
        $('#edit_wip_drawing_dwg').addClass('emptyField');
        $('#edit_wip_drawing_file_validation_lbl').prop('hidden',false);

    }
    else if(
        (( edit_wip_drawing_dwf != null || edit_wip_drawing_dwf.trim() != "") && ( edit_wip_drawing_dwg == null || edit_wip_drawing_dwg.trim() == "")) ||
        (( edit_wip_drawing_dwf == null || edit_wip_drawing_dwf.trim() == "") && ( edit_wip_drawing_dwg != null || edit_wip_drawing_dwg.trim() != ""))
    )
    {
        $('#edit_wip_drawing_dwf').removeClass('emptyField');
        $('#edit_wip_drawing_dwg').removeClass('emptyField');
    }
};

$('#update_wip_drawing_btn').on('click',function(){
    $('#edit_wip_drawing_form').submit();
});

$('#edit_wip_drawing_form').on('submit',function(){
    $('#update_wip_drawing_btn').prop('disabled',true).text('...');
});

$('#edit_wip_drawing_modal').on('hide.bs.modal',function(){
    $('#edit_wip_drawing_form').trigger('reset');
});

// End Edit WIP Drawing Functions

// Start Finalize WIP drawing JS Functions
$('#finalize_wip_drawing_btn').on('click',function(){
    $('#finalize_wip_drawing_title').text(finalizeWipDrawingNo+" - Finalization");

    $('#finalize_wip_drawing_id').val(finalizeWipDrawingId);
    $('#finalize_wip_drawing_modal').modal('show');
    $('#edit_wip_drawing_modal').modal('hide');
});

$('#save_finalize_wip_drawing_btn').on('click',function(){
    var finalize_wip_drawing_pdf = $('#finalize_wip_drawing_pdf').val();
    var finalize_wip_drawing_dwf = $('#finalize_wip_drawing_dwf').val();
    var finalize_wip_drawing_dwg = $('#finalize_wip_drawing_dwg').val();

    if(
        (finalize_wip_drawing_pdf != null && finalize_wip_drawing_pdf.trim() != "") &&
        (finalize_wip_drawing_dwf != null && finalize_wip_drawing_dwf.trim() != "") &&
        (finalize_wip_drawing_dwg != null && finalize_wip_drawing_dwg.trim() != "")
    ){
        $('#finalize_wip_drawing_form').submit();
    }else{
        setEmptyClassInFinalizeWipDrawingForm(finalize_wip_drawing_pdf,finalize_wip_drawing_dwf,finalize_wip_drawing_dwg);
    }
});

function setEmptyClassInFinalizeWipDrawingForm(finalize_wip_drawing_pdf,finalize_wip_drawing_dwf,finalize_wip_drawing_dwg){
    if(finalize_wip_drawing_pdf == null || finalize_wip_drawing_pdf.trim() == ""){
        $('#finalize_wip_drawing_pdf').addClass('emptyField');
    }else{
        $('#finalize_wip_drawing_pdf').removeClass('emptyField');
    }

    if(finalize_wip_drawing_dwf == null || finalize_wip_drawing_dwf.trim() == ""){
        $('#finalize_wip_drawing_dwf').addClass('emptyField');
    }else{
        $('#finalize_wip_drawing_dwf').removeClass('emptyField');
    }

    if(finalize_wip_drawing_dwg == null || finalize_wip_drawing_dwg.trim() == ""){
        $('#finalize_wip_drawing_dwg').addClass('emptyField');
    }else{
        $('#finalize_wip_drawing_dwg').removeClass('emptyField');
    }
};

$('#finalize_wip_drawing_form').on('submit',function(){
    $('#finalize_wip_drawing_btn').prop('disabled',true).text('...');
});

$('#finalize_wip_drawing_modal').on('hide.bs.modal',function(){
    $('#finalize_wip_drawing_form').trigger('reset');
    $('#finalize_wip_drawing_pdf').removeClass('emptyField');
    $('#finalize_wip_drawing_dwf').removeClass('emptyField');
    $('#finalize_wip_drawing_dwg').removeClass('emptyField');
});

// End Finalize WIP drawing JS Functions

// Start Delete WIP Drawing JS Functions
$('.delete_wip_drawing_btn').on('click',function(){
    $('#delete_wip_drawing_id').val($(this).data('delete_wip_drawing_id'));
    $('#delete_wip_drawing_description').text($(this).data('delete_wip_drawing_no')+" - "+$(this).data('delete_wip_drawing_title'));
    $('#delete_wip_drawing_hull_no').text($(this).data('delete_wip_drawing_hull_no'));
    $('#delete_wip_drawing_modal').modal('show');
});


// End Delete WIP Drawing JS Functions
