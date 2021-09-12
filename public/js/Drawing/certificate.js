// Global Variables
var CertHullHasChanged = false;

var selectedCertHullId;
var selectedCertHullNo;

var CertHasChanged = false;
var CertUpdateFile = false;
var initial_certificate_no;
var initial_certificate_title;

$(document).ready(function(){
    $('#new_certificate_btn').prop('hidden',true);
    $('#selected_certificate_hull_lbl').text('Certificates');

    $('#certificate_hulls_tbl').DataTable({
        autoWidth: false,
        "order": [[ 4, 'desc' ]],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "pageLength": -1
    });

    $('#certificates_tbl').DataTable({
        ordering: false,
        language: {
            emptyTable: "Select a hull first"
          }
    });
    
});

$('#certificate_hulls_tbl_body').on('click', 'tr', function() {
    $('#certificate_hulls_tbl_body > tr').removeClass('selected');
    $(this).addClass('selected');
});

$('.view_certificates').on('click',function(){
    selectedCertHullId = $(this).data('selected_certificate_hull_id');
    selectedCertHullNo = $(this).data('selected_certificate_hull_no');
    $('#selected_certificate_hull_lbl').text($(this).data('selected_certificate_hull_no')+" - Certificates");

    if( selectedCertHullId != 0 ){
        $('#new_certificate_btn').prop('hidden',false);
    }else{
        $('#new_certificate_btn').prop('hidden',true);
    }

    $('#certificates_tbl').DataTable().clear();

    $('#certificates_tbl').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        columnDefs: [{ orderable: false, "targets": 4 }],
        language: {
            searchPlaceholder: "Enter drawing no."
        },
        ajax: "/certificates/getHullCertificates/"+selectedCertHullId,
        columns: [
            { data: 'drawing_no' },
            { data: 'drawing_title' },
            { data: 'date_published' },
            { data: 'uploaded_by' },
            {
                data: null,
                bSortable: false,
                "mRender": function (data) { 
                    return '<a href="/download/'+ data.pdf+'" class="download-btn btn btn-outline-primary" style="text-decoration:none; border-radius:10px; padding:0px 15px 0px 15px;">PDF</a>'; 
                }
            },
            {
                data: null,
                bSortable: false,
                "mRender": function (data) { 
                    return '<div class="btn-group float-right"><button class="edit_certificate_btn btn" title="Edit Certificate" data-edit_certificate_id="'+data.id+
                    '"data-edit_certificate_no="'+data.drawing_no+
                    '"data-edit_certificate_title="'+data.drawing_title+
                    '"data-edit_certificate_revision_no="'+data.revision_no+
                    '"><i class="fas fa-pen"></i></button><button class="delete_certificate_btn btn" title="Delete Certificate" data-delete_certificate_id="'+data.id+
                    '" data-delete_certificate_no="'+data.drawing_no+
                    '" data-delete_certificate_title="'+data.drawing_title+
                    '"><i class="far fa-trash-alt"></i></button></div>'; 
                }
            }
        ]
    });
    
    $('html, body').animate({ scrollTop: $('#certificates_div').offset().top }, 'slow');

});


// Start New Certificate Functions
$('#new_certificate_btn').on('click',function(){
    $('#new_certificate_hull_id').val(selectedCertHullId);
    $('#new_certificate_hull_no').val(selectedCertHullNo);
    
    $('#new_certificate_no').removeClass('emptyField');
    $('#new_certificate_title').removeClass('emptyField');
    $('#new_certificate_pdf').removeClass('emptyField');

    $('#new_certificate_modal').modal('show');
});

$('#save_certificate_btn').on('click', function(){
    var new_certificate_no = $('#new_certificate_no').val();
    var new_certificate_title = $('#new_certificate_title').val();
    var new_certificate_pdf = $('#new_certificate_pdf').val();

    if(
        (new_certificate_no != null && new_certificate_no.trim() != "") &&
        (new_certificate_title != null && new_certificate_title.trim() != "") &&
        (new_certificate_pdf != null && new_certificate_pdf.trim() != "")
    ){
        $('#new_certificate_form').submit();
    }else{
        setEmptyClassInNewCertificateForm();
    }
});

$('#new_certificate_form').on('submit',function(){
    $('#save_certificate_btn').prop('disabled',true).text('...');
});

function setEmptyClassInNewCertificateForm(){
    var new_certificate_no = $('#new_certificate_no').val();
    var new_certificate_title = $('#new_certificate_title').val();
    var new_certificate_pdf = $('#new_certificate_pdf').val();

    if(new_certificate_no == null || new_certificate_no.trim() == ""){
        $('#new_certificate_no').addClass('emptyField');
    }else{
        $('#new_certificate_no').removeClass('emptyField');
    }

    if(new_certificate_title == null || new_certificate_title.trim() == ""){
        $('#new_certificate_title').addClass('emptyField');
    }else{
        $('#new_certificate_title').removeClass('emptyField');
    }

    if(new_certificate_pdf == null || new_certificate_pdf.trim() == ""){
        $('#new_certificate_pdf').addClass('emptyField');
    }else{
        $('#new_certificate_pdf').removeClass('emptyField');
    }
};
// End New Certificate Functions

// Start Edit Certificate JS Functions 
$('#view_edit_certificate_file_cb').on('click',function(){
    if ($('#view_edit_certificate_file_cb').is(':checked')){
        $('#edit_certificate_file_div').prop('hidden',false);
        $('#view_edit_certificate_file_cb').val(1);
    }else{
        $('#edit_certificate_file_div').prop('hidden',true);
        $('#view_edit_certificate_file_cb').val(0);
        $('#edit_certificate_pdf').val("");
    }
});


$('#certificates_tbl').on('click','.edit_certificate_btn',function(){
    initial_certificate_no = $(this).data('edit_certificate_no');   
    initial_certificate_title = $(this).data('edit_certificate_title');

    $('#edit_certificate_id').val($(this).data('edit_certificate_id'));
    $('#edit_certificate_hull_no').val(selectedCertHullNo);
    $('#edit_certificate_no').val($(this).data('edit_certificate_no'));
    $('#edit_certificate_title').val($(this).data('edit_certificate_title'));
    $('#update_certificate_btn').prop('disabled',true);

    
    setEmptyClassInEditCertificateForm();

    $('#view_edit_certificate_file_cb').prop('checked',false);
    $('#edit_certificate_file_div').prop('hidden',true);

    $('#edit_certificate_modal').modal('show');

    
});

$('#edit_certificate_form').on('keyup change',function(){
    if( $('#view_edit_certificate_file_cb').is(':checked') ){
        checkIfEditCertificateFileFormHasFilled();
        checkIfEditCertificateFormHasFilled();
    }else{
        checkIfEditCertificateFormHasChanged();
        checkIfEditCertificateFormHasFilled();
    }
});

function checkIfEditCertificateFormHasChanged(){
    var edited_certificate_no = $('#edit_certificate_no').val();
    var edited_certificate_title = $('#edit_certificate_title').val();
    
    if(
        (edited_certificate_no != initial_certificate_no) ||
        (edited_certificate_title != initial_certificate_title)
    ){
        CertHasChanged = true;
    }else{
        CertHasChanged = false;
    }

    return CertHasChanged;
};

function checkIfEditCertificateFileFormHasFilled(){
    var edited_certificate_pdf = $('#edit_certificate_pdf').val();

    if(
        (edited_certificate_pdf != null && edited_certificate_pdf.trim() != "")
    ){
        CertUpdateFile = true;
    }else{
        CertUpdateFile = false;
    }

    return CertUpdateFile;
};

function checkIfEditCertificateFormHasFilled(){
    var edit_certificate_no = $('#edit_certificate_no').val();
    var edit_certificate_title = $('#edit_certificate_title').val();
    
        if( $('#view_edit_certificate_file_cb').is(':checked') ){ // If Checkbox is checked, required drawing files
            
            if( 
                (edit_certificate_no != null && edit_certificate_no.trim() != "") &&
                (edit_certificate_title != null && edit_certificate_title.trim() != "") &&
                (CertUpdateFile == true)
                 
            ){
                $('#update_certificate_btn').prop('disabled',false);
            }else{
                setEmptyClassInEditCertificateForm();
                $('#update_certificate_btn').prop('disabled',true);
            }   

        }else{

            if( 
                (edit_certificate_no != null && edit_certificate_no.trim() != "") &&
                (edit_certificate_title != null && edit_certificate_title.trim() != "") &&
                (CertHasChanged == true)
            ){
                $('#update_certificate_btn').prop('disabled',false);
            }else{
                setEmptyClassInEditCertificateForm();
                $('#update_certificate_btn').prop('disabled',true);
            }
        }
};

function setEmptyClassInEditCertificateForm(){
    var edit_certificate_no = $('#edit_certificate_no').val();
    var edit_certificate_title = $('#edit_certificate_title').val();
    var edit_certificate_pdf = $('#edit_certificate_pdf').val();

    if(edit_certificate_no == null || edit_certificate_no.trim() == ""){
        $('#edit_certificate_no').addClass('emptyField');
    }else{
        $('#edit_certificate_no').removeClass('emptyField');
    }

    if(edit_certificate_title == null || edit_certificate_title.trim() == ""){
        $('#edit_certificate_title').addClass('emptyField');
    }else{
        $('#edit_certificate_title').removeClass('emptyField');
    }

    if(edit_certificate_pdf == null || edit_certificate_pdf.trim() == ""){
        $('#edit_certificate_pdf').addClass('emptyField');
    }else{
        $('#edit_certificate_pdf').removeClass('emptyField');
    }
};

$('#update_certificate_btn').on('click',function(){
    $('#edit_certificate_form').submit();
});

$('#edit_certificate_form').on('submit',function(){
    $('#update_certificate_btn').prop('disabled',true).text('...');
});

$('#edit_certificate_modal').on('hide.bs.modal',function(){
    $('#edit_certificate_form').trigger('reset');
});

// end edit Certificate JS Functions 

// Start Delete Certificate JS Functions
$('#certificates_tbl').on('click','.delete_certificate_btn',function(){

    $('#delete_certificate_id').val($(this).data('delete_certificate_id'));
    $('#delete_certificate_description').text($(this).data('delete_certificate_no')+" - "+$(this).data('delete_certificate_title'));
    $('#delete_certificate_hull_no').text(selectedCertHullNo);
    $('#delete_certificate_modal').modal('show');
});

$('#confirm_delete_certificate_btn').on('click',function(){
    $('#delete_certificate_form').submit();
});

$('#deletes_certificate_btn').on('submit',function(){
    $('#confirm_delete_certificate_btn').prop('disabled',true).text('...');
});
// End Delete Certificate JS Functions

