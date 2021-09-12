// Global Variables
var SbHullHasChanged = false;
var initial_sb_hull_no;
var initial_sb_hull_description;

var selectedSbHullId;
var selectedSbHullNo;

var revisionSbDrawingId;
var revisionSbDrawingNo;

var SbDrawingHasChanged = false;
var SbDrawingUpdateFile = false;
var initial_sb_drawing_no;
var initial_sb_drawing_title;

$(document).ready(function(){
    $('#new_sb_drawing_btn').prop('hidden',true);
    $('#selected_sb_hull_lbl').text('Drawings');

    $('#ship_bldg_hulls_tbl').DataTable({
        autoWidth: false,
        "order": [[ 4, 'desc' ]],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "pageLength": -1
    });

    $('#ship_bldg_drawings_tbl').DataTable({
        ordering: false,
        language: {
            emptyTable: "Select a hull first"
          }
    });
    
});

$('#ship_bldg_hulls_tbl_body').on('click', 'tr', function() {
    $('#ship_bldg_hulls_tbl_body > tr').removeClass('selected');
    $(this).addClass('selected');
});

$('.view_drawings').on('click',function(){
    selectedSbHullId = $(this).data('selected_sb_hull_id');
    selectedSbHullNo = $(this).data('selected_sb_hull_no');
    $('#selected_sb_hull_lbl').text($(this).data('selected_sb_hull_no')+" - Drawings");

    if( selectedSbHullId != 0 ){
        $('#new_sb_drawing_btn').prop('hidden',false);
    }else{
        $('#new_sb_drawing_btn').prop('hidden',true);
    }

    $('#ship_bldg_drawings_tbl').DataTable().clear();

    $('#ship_bldg_drawings_tbl').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        columnDefs: [{ orderable: false, "targets": 4 }],
        language: {
            searchPlaceholder: "Enter drawing no."
        },
        ajax: "/drawings/getSbHullDrawings/"+selectedSbHullId,
        columns: [
            { data: 'drawing_no' },
            { data: 'drawing_title' },
            { data: 'revision_no' },
            { data: 'date_published' },
            { data: 'uploaded_by' },
            {
                data: null,
                bSortable: false,
                "mRender": function (data) { 
                    return '<div class="btn-group"><a href="/download/'+ data.pdf+
                            '" class="download-btn btn btn-outline-primary" style="text-decoration:none; border-top-left-radius: 10px; border-bottom-left-radius: 10px; padding:0px 10px 0px 10px;">PDF</a><a href="/download/'+ data.dwf+
                            '" class="download-btn btn btn-outline-primary" style="text-decoration:none; padding:0px 10px 0px 10px;">DWF</a><a href="/download/'+ data.dwg+
                            '" class="download-btn btn btn-outline-primary" style="text-decoration:none; border-top-right-radius: 10px; border-bottom-right-radius: 10px; padding:0px 10px 0px 10px;">DWG</a></div>'; 
                }
            },
            {
                data: null,
                bSortable: false,
                "mRender": function (data) { 
                    return '<div class="btn-group float-right"><button class="edit_sb_drawing_btn btn" title="Edit Drawing" data-edit_sb_drawing_id="'+data.id+
                    '"data-edit_sb_drawing_no="'+data.drawing_no+
                    '"data-edit_sb_drawing_title="'+data.drawing_title+
                    '"data-edit_sb_drawing_revision_no="'+data.revision_no+
                    '"><i class="fas fa-pen"></i></button><button class="delete_sb_drawing_btn btn" title="Delete Drawing" data-delete_sb_drawing_id="'+data.id+
                    '" data-delete_sb_drawing_no="'+data.drawing_no+
                    '" data-delete_sb_drawing_title="'+data.drawing_title+
                    '"><i class="far fa-trash-alt"></i></button></div>'; 
                }
            }
        ]
    });
    
    $('html, body').animate({ scrollTop: $('#sb_drawings_div').offset().top }, 'slow');

});

$('#ship_bldg_drawings_tbl').on('click','#edit_sb_drawing_btn',function(){
    alert($(this).data('id'));
});

// Start New Hull JS Functions 
$('#new_sb_hull_btn').on('click',function(){
    $('#new_sb_hull_modal').modal('show');
});

$('#save_sb_hull_btn').on('click',function(){
    var new_sb_hull_no = $('#new_sb_hull_no').val();
    var new_sb_hull_description = $('#new_sb_hull_description').val();

    if(
        (new_sb_hull_no != null && new_sb_hull_no.trim() != "") &&
        (new_sb_hull_description != null && new_sb_hull_description.trim() != "")
    ){
        $('#new_sb_hull_form').submit();
    }else{
        setEmptyClassInNewSbHullForm();
    }
});

function setEmptyClassInNewSbHullForm(){
    var new_sb_hull_no = $('#new_sb_hull_no').val();
    var new_sb_hull_description = $('#new_sb_hull_description').val();

    if( new_sb_hull_no == null || new_sb_hull_no.trim() == "" ){
        $('#new_sb_hull_no').addClass('emptyField');
    }else{
        $('#new_sb_hull_no').removeClass('emptyField');
    }

    if( new_sb_hull_description == null || new_sb_hull_description.trim() == "" ){
        $('#new_sb_hull_description').addClass('emptyField');
    }else{
        $('#new_sb_hull_description').removeClass('emptyField');
    }
};

$('#new_sb_hull_form').on('submit',function(){
    $('#save_sb_hull_btn').prop('disabled',true).text('...');
});

$('#new_sb_hull_modal').on('hide.bs.modal',function(){
    $('#new_sb_hull_form').trigger('reset');
    $('#new_sb_hull_no').removeClass('emptyField');
    $('#new_sb_hull_description').removeClass('emptyField');
});
// End New Hull JS Functions 

// Start Delete Hull JS Functions
$('.delete_sb_hull_btn').on('click',function(){
    $('#delete_sb_hull_id').val($(this).data('delete_sb_hull_id'));
    $('#delete_sb_hull_description').text($(this).data('delete_sb_hull_no'));
    $('#delete_sb_hull_modal').modal('show'); 
});

$('#confirm_delete_sb_hull_btn').on('click',function(){
    $('#delete_sb_hull_form').submit();
});

$('#delete_sb_hull_form').on('submit',function(){
    $('#delete_sb_hull_btn').prop('disabled',true);
    $('#delete_sb_hull_btn').text('...');
});
// End Delete Hull JS Functions

// Start Edit Hull JS Functions
$('.edit_sb_hull_btn').on('click',function(){
    initial_sb_hull_no = $(this).data('edit_sb_hull_no');
    initial_sb_hull_description = $(this).data('edit_sb_hull_description');

    $('#edit_sb_hull_id').val($(this).data('edit_sb_hull_id'));
    $('#edit_sb_hull_no').val($(this).data('edit_sb_hull_no'));
    $('#edit_sb_hull_description').val($(this).data('edit_sb_hull_description'));

    $('#update_sb_hull_btn').prop('disabled', true);
    setEmptyClassInEditSbHullForm();
    $('#edit_sb_hull_modal').modal('show');
});

$('#edit_sb_hull_form').on('keyup change',function(){
    checkIfEditSbHullFormHasChanged();
    checkIfEditSbHullFormHasFilled();
});

function checkIfEditSbHullFormHasChanged(){
    var edited_sb_hull_no = $('#edit_sb_hull_no').val();
    var edited_sb_hull_description = $('#edit_sb_hull_description').val();

    if(
        (edited_sb_hull_no != initial_sb_hull_no) ||
        (edited_sb_hull_description != initial_sb_hull_description)
    ){
        SbHullHasChanged = true;
    }else{
        SbHullHasChanged = false;
    }

    return SbHullHasChanged;
}

function checkIfEditSbHullFormHasFilled(){
    var edit_sb_hull_no = $('#edit_sb_hull_no').val();
    var edit_sb_hull_description = $('#edit_sb_hull_description').val();

    if(
        (edit_sb_hull_no != null && edit_sb_hull_no.trim() != "") &&
        (edit_sb_hull_description != null && edit_sb_hull_description.trim() != "") &&
        (SbHullHasChanged == true)
    ){
        $('#update_sb_hull_btn').prop('disabled',false);
    }else{
        $('#update_sb_hull_btn').prop('disabled',true);
        setEmptyClassInEditSbHullForm();
    }
};

function setEmptyClassInEditSbHullForm(){
    var edit_sb_hull_no = $('#edit_sb_hull_no').val();
    var edit_sb_hull_description = $('#edit_sb_hull_description').val();

    if( edit_sb_hull_no == null || edit_sb_hull_no.trim() == "" ){
        $('#edit_sb_hull_no').addClass('emptyField');
    }else{
        $('#edit_sb_hull_no').removeClass('emptyField');
    }

    if( edit_sb_hull_description == null || edit_sb_hull_description.trim() == "" ){
        $('#edit_sb_hull_description').addClass('emptyField');
    }else{
        $('#edit_sb_hull_description').removeClass('emptyField');
    }
};

$('#update_sb_hull_btn').on('click', function(){
    $('#edit_sb_hull_form').submit();
});

$('#edit_sb_hull_form').on('submit',function(){
    $('#update_sb_hull_btn').prop('disabled',true).text('...');
});
// End Edit Hull JS Functions

// Start New Drawing Functions
$('#new_sb_drawing_btn').on('click',function(){
    $('#new_sb_drawing_hull_id').val(selectedSbHullId);
    $('#new_sb_drawing_hull_no').val(selectedSbHullNo);

    
    $('#new_sb_drawing_no').removeClass('emptyField');
    $('#new_sb_drawing_title').removeClass('emptyField');
    $('#new_sb_drawing_pdf').removeClass('emptyField');
    $('#new_sb_drawing_dwf').removeClass('emptyField');
    $('#new_sb_drawing_dwg').removeClass('emptyField');

    $('#new_sb_drawing_modal').modal('show');
});

$('#save_sb_drawing_btn').on('click', function(){
    var new_sb_drawing_no = $('#new_sb_drawing_no').val();
    var new_sb_drawing_title = $('#new_sb_drawing_title').val();
    var new_sb_drawing_pdf = $('#new_sb_drawing_pdf').val();
    var new_sb_drawing_dwf = $('#new_sb_drawing_dwf').val();
    var new_sb_drawing_dwg = $('#new_sb_drawing_dwg').val();

    if(
        (new_sb_drawing_no != null && new_sb_drawing_no.trim() != "") &&
        (new_sb_drawing_title != null && new_sb_drawing_title.trim() != "") &&
        (new_sb_drawing_pdf != null && new_sb_drawing_pdf.trim() != "") &&
        (new_sb_drawing_dwf != null && new_sb_drawing_dwf.trim() != "") &&
        (new_sb_drawing_dwg != null && new_sb_drawing_dwg.trim() != "")
    ){
        $('#new_sb_drawing_form').submit();
    }else{
        setEmptyClassInNewSbDrawingForm();
    }
});

$('#new_sb_drawing_form').on('submit',function(){
    $('#save_sb_drawing_btn').prop('disabled',true).text('...');
});

function setEmptyClassInNewSbDrawingForm(){
    var new_sb_drawing_no = $('#new_sb_drawing_no').val();
    var new_sb_drawing_title = $('#new_sb_drawing_title').val();
    var new_sb_drawing_pdf = $('#new_sb_drawing_pdf').val();
    var new_sb_drawing_dwf = $('#new_sb_drawing_dwf').val();
    var new_sb_drawing_dwg = $('#new_sb_drawing_dwg').val();

    if(new_sb_drawing_no == null || new_sb_drawing_no.trim() == ""){
        $('#new_sb_drawing_no').addClass('emptyField');
    }else{
        $('#new_sb_drawing_no').removeClass('emptyField');
    }

    if(new_sb_drawing_title == null || new_sb_drawing_title.trim() == ""){
        $('#new_sb_drawing_title').addClass('emptyField');
    }else{
        $('#new_sb_drawing_title').removeClass('emptyField');
    }

    if(new_sb_drawing_pdf == null || new_sb_drawing_pdf.trim() == ""){
        $('#new_sb_drawing_pdf').addClass('emptyField');
    }else{
        $('#new_sb_drawing_pdf').removeClass('emptyField');
    }
    if(new_sb_drawing_dwf == null || new_sb_drawing_dwf.trim() == ""){
        $('#new_sb_drawing_dwf').addClass('emptyField');
    }else{
        $('#new_sb_drawing_dwf').removeClass('emptyField');
    }

    if(new_sb_drawing_dwg == null || new_sb_drawing_dwg.trim() == ""){
        $('#new_sb_drawing_dwg').addClass('emptyField');
    }else{
        $('#new_sb_drawing_dwg').removeClass('emptyField');
    }
};
// End New Drawing Functions

// Start Edit Drawing JS Functions 
$('#view_edit_sb_drawing_file_cb').on('click',function(){
    if ($('#view_edit_sb_drawing_file_cb').is(':checked')){
        $('#edit_sb_drawing_file_div').prop('hidden',false);
        $('#view_edit_sb_drawing_file_cb').val(1);
    }else{
        $('#edit_sb_drawing_file_div').prop('hidden',true);
        $('#view_edit_sb_drawing_file_cb').val(0);
        $('#edit_sb_drawing_pdf').val("");
        $('#edit_sb_drawing_dwf').val("");
        $('#edit_sb_drawing_dwg').val("");
    }
});


$('#ship_bldg_drawings_tbl').on('click','.edit_sb_drawing_btn',function(){
    revisionSbDrawingId = $(this).data('edit_sb_drawing_id');
    revisionSbDrawingNo = $(this).data('edit_sb_drawing_no');

    initial_sb_drawing_no = $(this).data('edit_sb_drawing_no');
    initial_sb_drawing_title = $(this).data('edit_sb_drawing_title');

    var drawingId = $(this).data('edit_sb_drawing_id');
    $('#edit_sb_drawing_id').val($(this).data('edit_sb_drawing_id'));
    $('#edit_sb_drawing_hull_no').val(selectedSbHullNo);
    $('#edit_sb_drawing_no').val($(this).data('edit_sb_drawing_no'));
    $('#edit_sb_drawing_title').val($(this).data('edit_sb_drawing_title'));
    $('#edit_sb_drawing_revision_no').val($(this).data('edit_sb_drawing_revision_no'));
    $('#update_sb_drawing_btn').prop('disabled',true);
    $('#revised_sb_drawing_file_div').prop('hidden',true);

    if( $(this).data('edit_sb_drawing_revision_no') >= 1 ){
        $('#sb_drawing_revision_history').prop('hidden',false);
        viewSbDrawingRevisionHistory(drawingId);
    }else{
        $('#sb_drawing_revision_history').prop('hidden',true);
    }
    
    setEmptyClassInEditSbDrawingForm();

    $('#view_edit_sb_drawing_file_cb').prop('checked',false);
    $('#edit_sb_drawing_file_div').prop('hidden',true);

    $('#edit_sb_drawing_modal').modal('show');

    
});

function viewSbDrawingRevisionHistory(drawingId){
    $.ajax({
        url: '/drawings/getRevisionHistory/' + drawingId,
        type: 'get',
        data: {},
        success: function(data){

                var tr = '<tr>';
                                for(i in data.revisionHistory) {

                                        tr += ('<tr>');
                                        tr += ('<td style="text-align:center;">' + data.revisionHistory[i].revision_no + '</td>');
                                        tr += ('<td>' + data.revisionHistory[i].date_published + '</td>');
                                        tr += ('<td>' + data.revisionHistory[i].name + '</td>');

                                        tr += ('<td style="text-align:right; width:20px;"><div class="btn-group"><a href="/download_revised/'+ data.revisionHistory[i].pdf+
                                        '" class="download-btn btn btn-outline-primary" style="text-decoration:none; border-top-left-radius: 10px; border-bottom-left-radius: 10px; padding:0px 5px 0px 5px;">PDF</a><a href="/download_revised/'+ data.revisionHistory[i].dwf+
                                        '" class="download-btn btn btn-outline-primary" style="text-decoration:none; padding:0px 5px 0px 5px;">DWF</a><a href="/download_revised/'+ data.revisionHistory[i].dwg+
                                        '" class="download-btn btn btn-outline-primary" style="text-decoration:none; border-top-right-radius: 10px; border-bottom-right-radius: 10px; padding:0px 5px 0px 5px;">DWG</a></div></td>');
                                        
                                        tr += ('</tr>');
                                };
                    tr += '</tr>'; 
                    $('#sb_drawing_revision_history_tbl_body').html(tr)
        },

            error: function(XMLHttpRequest, textStatus, errorThrown) { // IF NOT SUCCESS
                            toastrMessage = "Unable to fetch records from database.";
                            showErrorToast(toastrMessage);  
        } // END FUNCTION

    }); // END AJAX
};

$('#edit_sb_drawing_form').on('keyup change',function(){
    if( $('#view_edit_sb_drawing_file_cb').is(':checked') ){
        checkIfEditSbDrawingFileFormHasFilled();
        checkIfEditSbDrawingFormHasFilled();
    }else{
        checkIfEditSbDrawingFormHasChanged();
        checkIfEditSbDrawingFormHasFilled();
    }
});

function checkIfEditSbDrawingFormHasChanged(){
    var edited_sb_drawing_no = $('#edit_sb_drawing_no').val();
    var edited_sb_drawing_title = $('#edit_sb_drawing_title').val();
    
    if(
        (edited_sb_drawing_no != initial_sb_drawing_no) ||
        (edited_sb_drawing_title != initial_sb_drawing_title)
    ){
        SbDrawingHasChanged = true;
    }else{
        SbDrawingHasChanged = false;
    }

    return SbDrawingHasChanged;
};

function checkIfEditSbDrawingFileFormHasFilled(){
    var edited_sb_drawing_pdf = $('#edit_sb_drawing_pdf').val();
    var edited_sb_drawing_dwf = $('#edit_sb_drawing_dwf').val();
    var edited_sb_drawing_dwg = $('#edit_sb_drawing_dwg').val();

    if(
        (edited_sb_drawing_pdf != null && edited_sb_drawing_pdf.trim() != "") &&
        (edited_sb_drawing_dwf != null && edited_sb_drawing_dwf.trim() != "") &&
        (edited_sb_drawing_dwg != null && edited_sb_drawing_dwg.trim() != "")
    ){
        SbDrawingUpdateFile = true;
    }else{
        SbDrawingUpdateFile = false;
    }

    return SbDrawingUpdateFile;
};

function checkIfEditSbDrawingFormHasFilled(){
    var edit_sb_drawing_no = $('#edit_sb_drawing_no').val();
    var edit_sb_drawing_title = $('#edit_sb_drawing_title').val();
    
        if( $('#view_edit_sb_drawing_file_cb').is(':checked') ){ // If Checkbox is checked, required drawing files
            
            if( 
                (edit_sb_drawing_no != null && edit_sb_drawing_no.trim() != "") &&
                (edit_sb_drawing_title != null && edit_sb_drawing_title.trim() != "") &&
                (SbDrawingUpdateFile == true)
                 
            ){
                $('#update_sb_drawing_btn').prop('disabled',false);
            }else{
                setEmptyClassInEditSbDrawingForm();
                $('#update_sb_drawing_btn').prop('disabled',true);
            }   

        }else{

            if( 
                (edit_sb_drawing_no != null && edit_sb_drawing_no.trim() != "") &&
                (edit_sb_drawing_title != null && edit_sb_drawing_title.trim() != "") &&
                (SbDrawingHasChanged == true)
            ){
                $('#update_sb_drawing_btn').prop('disabled',false);
            }else{
                setEmptyClassInEditSbDrawingForm();
                $('#update_sb_drawing_btn').prop('disabled',true);
            }
        }
};

function setEmptyClassInEditSbDrawingForm(){
    var edit_sb_drawing_no = $('#edit_sb_drawing_no').val();
    var edit_sb_drawing_title = $('#edit_sb_drawing_title').val();
    var edit_sb_drawing_pdf = $('#edit_sb_drawing_pdf').val();
    var edit_sb_drawing_dwf = $('#edit_sb_drawing_dwf').val();
    var edit_sb_drawing_dwg = $('#edit_sb_drawing_dwg').val();

    if(edit_sb_drawing_no == null || edit_sb_drawing_no.trim() == ""){
        $('#edit_sb_drawing_no').addClass('emptyField');
    }else{
        $('#edit_sb_drawing_no').removeClass('emptyField');
    }

    if(edit_sb_drawing_title == null || edit_sb_drawing_title.trim() == ""){
        $('#edit_sb_drawing_title').addClass('emptyField');
    }else{
        $('#edit_sb_drawing_title').removeClass('emptyField');
    }

    if(edit_sb_drawing_pdf == null || edit_sb_drawing_pdf.trim() == ""){
        $('#edit_sb_drawing_pdf').addClass('emptyField');
    }else{
        $('#edit_sb_drawing_pdf').removeClass('emptyField');
    }

    if(edit_sb_drawing_dwf == null || edit_sb_drawing_dwf.trim() == ""){
        $('#edit_sb_drawing_dwf').addClass('emptyField');
    }else{
        $('#edit_sb_drawing_dwf').removeClass('emptyField');
    }

    if(edit_sb_drawing_dwg == null || edit_sb_drawing_dwg.trim() == ""){
        $('#edit_sb_drawing_dwg').addClass('emptyField');
    }else{
        $('#edit_sb_drawing_dwg').removeClass('emptyField');
    }
};

$('#update_sb_drawing_btn').on('click',function(){
    $('#edit_sb_drawing_form').submit();
});

$('#edit_sb_drawing_form').on('submit',function(){
    $('#update_sb_drawing_btn').prop('disabled',true).text('...');
});

$('#edit_sb_drawing_modal').on('hide.bs.modal',function(){
    $('#edit_sb_drawing_form').trigger('reset');
});

// end edit Drawing JS Functions 

// Start Upload Drawing Revision JS Functions
$('#new_revision_sb_drawing_btn').on('click',function(){
    $('#new_revision_sb_drawing_title').text(revisionSbDrawingNo+" - Upload Revision");
    $('#new_revision_sb_drawing_id').val(revisionSbDrawingId);
    $('#edit_sb_drawing_modal').modal('hide');
    $('#new_revision_sb_drawing_modal').modal('show');
});

$('#upload_revision_sb_drawing_btn').on('click',function(){
    var revised_sb_drawing_pdf = $('#new_revision_sb_drawing_pdf').val();
    var revised_sb_drawing_dwf = $('#new_revision_sb_drawing_dwf').val();
    var revised_sb_drawing_dwg = $('#new_revision_sb_drawing_dwg').val();

    if(
    (revised_sb_drawing_pdf != null && revised_sb_drawing_pdf.trim() != "") &&
    (revised_sb_drawing_dwf != null && revised_sb_drawing_dwf.trim() != "") &&
    (revised_sb_drawing_dwg != null && revised_sb_drawing_dwg.trim() != "")
    ){
        $('#new_revision_sb_drawing_form').submit();
    }else{
        setEmptyClassInNewRevisionSbDrawingForm(revised_sb_drawing_pdf, revised_sb_drawing_dwf, revised_sb_drawing_dwg);
    }
});

$('#new_revision_sb_drawing_form').on('submit', function(){
    $('#upload_revision_sb_drawing_btn').prop('disabled',true).text('...');
});

$('#new_revision_sb_drawing_modal').on('hide.bs.modal',function(){
    $('#new_revision_sb_drawing_form').trigger('reset');
    $('#new_revision_sb_drawing_pdf').removeClass('emptyField');
    $('#new_revision_sb_drawing_pdf').removeClass('emptyField');
    $('#new_revision_sb_drawing_pdf').removeClass('emptyField');
});

function setEmptyClassInNewRevisionSbDrawingForm(revised_sb_drawing_pdf, revised_sb_drawing_dwf, revised_sb_drawing_dwg){

    if( revised_sb_drawing_pdf == null || revised_sb_drawing_pdf.trim() == ""){
        $('#new_revision_sb_drawing_pdf').addClass('emptyField');
    }else{
        $('#new_revision_sb_drawing_pdf').removeClass('emptyField');
    }

    if( revised_sb_drawing_dwf == null || revised_sb_drawing_dwf.trim() == ""){
        $('#new_revision_sb_drawing_dwf').addClass('emptyField');
    }else{
        $('#new_revision_sb_drawing_dwf').removeClass('emptyField');
    }

    if( revised_sb_drawing_dwg == null || revised_sb_drawing_dwg.trim() == ""){
        $('#new_revision_sb_drawing_dwg').addClass('emptyField');
    }else{
        $('#new_revision_sb_drawing_dwg').removeClass('emptyField');
    }
};
// End Upload Drawing Revision JS Functions

// Start Delete Drawing JS Functions
$('#ship_bldg_drawings_tbl').on('click','.delete_sb_drawing_btn',function(){

    $('#delete_drawing_id').val($(this).data('delete_sb_drawing_id'));
    $('#delete_sb_drawing_description').text($(this).data('delete_sb_drawing_no')+" - "+$(this).data('delete_sb_drawing_title'));
    $('#delete_sb_drawing_hull_no').text(selectedSbHullNo);
    $('#delete_sb_drawing_modal').modal('show');
});

$('#confirm_delete_sb_drawing_btn').on('click',function(){
    $('#delete_sb_drawing_form').submit();
});

$('#deletes_sb_drawing_btn').on('submit',function(){
    $('#confirm_delete_sb_drawing_btn').prop('disabled',true).text('...');
});
// End Delete Drawing JS Functions

