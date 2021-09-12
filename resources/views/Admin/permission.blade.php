<div class="container-fluid">
     <h5>Permissions
    <button id="new_permission_btn" class="btn btn-primary" style="float:right;">New Permission</button>
    </h5>

    <br>
                       
    <table id="permissions_tbl" class="table-hover  table-striped" style="width:100%;">
        <thead>
            <tr>
                <th class="all">Permission</th>
                <th>Description</th>
                <th class="all"></th>
            </tr>
        </thead>

        <tbody>
            @foreach($permissions as $permission)
            <tr>
                <td>{{ $permission->name }}</td>
                <td>{{ $permission->description }}</td>
                <td style="text-align:right;">
                    <button class="edit_permission_btn btn" title="Edit Permission"
                        data-permission_id="{{ $permission->id }}"
                        data-permission_name="{{ $permission->name }}"
                        data-permission_description="{{ $permission->description }}">
                        <i class="fas fa-pen"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

        
        

    <!-- New Permission Modal -->
    <div class="modal fade" id="new_permission_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>New Permission</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="new_permission_form" action="{{ route('savePermission') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_permission_name">Permission</label>

                        <div class="col-md-8">
                            <input class="form-control" id="new_permission_name" name="new_permission_name" placeholder="Enter permission name" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_permission_description">Description</label>

                        <div class="col-md-8">
                            <input class="form-control" id="new_permission_description" name="new_permission_description" placeholder="Enter permission description" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="save_permission_btn" class="btn btn-primary" style="width:100%;" type="button">Save</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>

     <!-- Edit Permission Modal -->
     <div class="modal fade" id="edit_permission_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Edit Permission</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit_permission_form" action="{{ route('updatePermission') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input class="form-control" id="edit_permission_id" name="edit_permission_id" type="hidden" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_permission_name">Permission</label>

                        <div class="col-md-8">
                            <input class="form-control" id="edit_permission_name" name="edit_permission_name" placeholder="Enter permission name" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_permission_description">Description</label>

                        <div class="col-md-8">
                            <input class="form-control" id="edit_permission_description" name="edit_permission_description" placeholder="Enter permission description" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="update_permission_btn" class="btn btn-primary" style="width:100%;" type="button">Update</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/Admin/permission.js') }}"></script>


