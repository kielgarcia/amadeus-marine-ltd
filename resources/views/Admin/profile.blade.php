@extends('layouts.app')
@auth
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
        @if ($message = Session::get('success'))
                <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ $message }}
                </div>
            @endif

            @if ($message = Session::get('failed'))
                <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ $message }}
                </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <ul class="list-group" style="padding:10px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="card">
                <div class="card-header"><b>My Profile</b></div>
                
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <img src="../images/favicon.png" style="width:100px;" />
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">Name:</label>
                            <label class="col-md-10"><b>{{ $user->name }}</b></label><br>
                            <label class="form-label">Email:</label>
                            <label class="col-md-10"><b>{{ $user->email }}</b></label>

                            <button id="edit_btn" data-name="{{ $user->name }}" data-email="{{ $user->email }}" class="btn btn-primary" style="width:50%; border-radius: 500px; padding: 0px; font-size:12px;">Update</button>
                            <button class="btn btn-primary" style="float:right; width:50%; border-radius: 500px; padding: 0px; font-size:12px;" data-toggle="modal" data-target="#edit_password_modal">Change Password</button><br><br>
                        </div>

                        <div class="container">
                            <div class="card" style="width:100%;">
                                <div class="card-header" style="background-color:#ffffff; color:#000000;">Category <span class="badge badge-primary" style="border-radius:10px;">Total</span> <i style="float:right;">Draft/Approved</i></div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Ship Building <span class="badge badge-primary" style="border-radius:10px;">{{ $total_sb + $total_sb_draft }}</span></label> <i style="float:right; right:0;">{{ $total_sb_draft}}/{{ $total_sb }}</i><br>
                                    
                                        <label>Ship Repair <span class="badge badge-primary"  style="border-radius:10px;">{{ $total_sr + $total_sr_draft }}</span></label> <i style="float:right;">{{ $total_sr_draft}}/{{ $total_sr }}</i><br>
                                    
                                        <label>Special Project <span class="badge badge-primary"  style="border-radius:10px;">{{ $total_sp + $total_sp_draft }}</span></label> <i style="float:right;">{{ $total_sp_draft}}/{{ $total_sp }}</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                            
                            <input id="sb" type="hidden" value="{{ $total_sb }}" />
                            <input id="sb_draft" type="hidden" value="{{ $total_sb_draft }}" />
                            <input id="sr" type="hidden" value="{{ $total_sr }}" />
                            <input id="sr_draft" type="hidden" value="{{ $total_sr_draft }}" />
                            <input id="sp" type="hidden" value="{{ $total_sp }}" />
                            <input id="sp_draft" type="hidden" value="{{ $total_sp_draft }}" />
                            <input id="total_drawings" type="hidden" value="{{ $drawings }}" />
                        
                    </div>

                    <div class="form-group">
                        <div class="card shadow-sm">
                            <div class="card-header" style="background: #ffffff; color: #000000;">My Uploaded Drawings</div>
                            <div id="chart_div"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Update Details  -->
    <div class="modal fade" id="edit_my_profile_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <b>Update Details</b>
                    <button type="button" class="fas btn" data-dismiss="modal" style="font-size:26px; color:white; background:#003366;">&#xf00d;</button>
                </div>

                <form id="edit_my_details_form" action="{{ route('update_my_details') }}" method="POST">
                {{ csrf_field() }}

                <div class="modal-body">

                        <input type="hidden" id="my_id" name="my_id" value="{{ Auth::user()->id }}" />

                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_my_name" name="edit_my_name" />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_my_email" name="edit_my_email" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" style="width:100%;" type="submit">Save Changes</button>
                    <button class="btn" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>


    <!-- Update Password  -->
    <div class="modal fade" id="edit_password_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <b>Update Password</b>
                    <button type="button" class="fas btn" data-dismiss="modal" style="font-size:26px; color:white; background:#003366;">&#xf00d;</button>
                </div>

                <form id="edit_password_form" action="{{ route('edit_password') }}" method="POST">
                {{ csrf_field() }}

                <div class="modal-body">

                        <input type="hidden" id="edit_password_user_id" name="edit_password_user_id" value="{{ Auth::user()->id }}" />

                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="edit_current_password" name="edit_current_password" />
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" id="edit_new_password" name="edit_new_password" />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="edit_confirm_password" name="edit_confirm_password" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" style="width:100%;" type="submit">Update Password</button>
                    <button class="btn" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>

$(document).ready( function() {
   
});

$(document).on('click','#edit_btn', function(){
    $('#edit_my_name').val($(this).data('name'));
    $('#edit_my_email').val($(this).data('email'));
    $('#edit_my_profile_modal').modal('show');
});


window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 5000);

    

   // Load the Visualization API and the corechart package.
   google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

// Draw the chart and set the chart values
function drawChart() {
    var sb = parseInt(document.getElementById('sb').value);
    var sb_draft = parseInt(document.getElementById('sb_draft').value);

    var sr = parseInt(document.getElementById('sr').value);
    var sr_draft = parseInt(document.getElementById('sr_draft').value);

    var sp = parseInt(document.getElementById('sp').value);
    var sp_draft = parseInt(document.getElementById('sp_draft').value);

    var total_drawings = parseInt(document.getElementById('total_drawings').value);

    var data = google.visualization.arrayToDataTable([
        ['Category', 'Draft', 'Approved'],
        ['Ship Building', sb_draft, sb],
        ['Ship Repair', sr_draft, sr],
        ['Special Projects', sp_draft, sp]
      ]);

      var options = {
        title: 'My uploaded drawings',
        chartArea: {width: '50%'},
        isStacked: true,
        hAxis: {
          title: 'Total Drawings',
          minValue: total_drawings,
        },
        vAxis: {
          title: 'Category'
        }
      };

  // Display the chart inside the <div> element with id="piechart"
  var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
  chart.draw(data, options);
}
</script>

@endsection
@endauth