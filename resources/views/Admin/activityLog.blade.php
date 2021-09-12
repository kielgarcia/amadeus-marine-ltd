@extends('layouts.app')
@auth
@section('content')
@section('title', 'Activity Logs - ')
@include('layouts/loader')
<div id="contents" class="container" style="display:none;">
    <div class="row justify-content-center">
        

        <div class="col-md-10">

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
                <div class="card-header"><h5>Activity Logs</h5></div>

                <div class="card-body">
                    <table id="activity_log_tbl" class="table-hover table responsive" style="width:100%; font-size:12px;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>By</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach( $activity_logs as $log )
                            <tr>
                                <td>{{ $log->created_at }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->action_by }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

$(document).ready(function(){
    $('#activity_log_tbl').DataTable({
        "order": [[ 0, 'desc' ]],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "pageLength": -1
    });
});
</script>
@endsection
@endauth
