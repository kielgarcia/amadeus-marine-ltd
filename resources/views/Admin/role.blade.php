<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <h5>Roles</h5>
        </div>

        <div class="col-sm-6">
            <div class="btn-group" style="float:right;">
                <form action="{{ route('RolePermissionRefresh') }}" method="GET">
                    {{ csrf_field() }} 
                    <button class="btn btn-outline-primary" title="Refresh Role-Permission" style="float:right;">Refresh Role-Permission</button>
                </form>
                <button id="new_role_btn" class="btn btn-primary" title="New Role" style="float:right;">New Role</button>
            </div>
        </div>
    </div>
    
    
    <br>

    <table id="roles_tbl" class="table table-hover table-striped responsive" style="width:100%;">
        <thead>
            <tr>
                <th class="all">Role</th>
                <th>Permissions</th>
                <th class="all"></th>
            </tr>
        </thead>

        <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td>
                    @foreach($role_has_permissions as $role_permission)
                        @if($role->id == $role_permission->role_id)
                            <span class="badge badge-pill badge-primary">{{$role_permission->permission}}</span>
                        @else
                            @continue
                        @endif
                    @endforeach
                </td>

                <td style="text-align:right;">
                    <button class="edit_role_btn btn" title="Edit Role"
                        data-role_id="{{ $role->id }}"
                        data-role_name="{{ $role->name }}">
                        <i class="fas fa-pen"></i>
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


    <!-- New Role Modal  -->
    <div class="modal fade" id="new_role_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>New Role</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="new_role_form" action="{{ route('saveRole') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_role_name">Role</label>

                        <div class="col-md-8">
                            <input class="form-control" id="new_role_name" name="new_role_name" placeholder="Enter role name" />
                        </div>
                    </div>

                    <div>
                        

                    </div>
                    
                    <div class="form-group row">
                        
                        <label class="col-md-3 col-form-label text-md-right" for="new_role_permissions">Permissions</label>
                        
                        <div class="col-md-8">
                        
                            <span class="emptyField">
                                <select class="js-example-basic-multiple form-control" name="new_role_permissions[]" id="new_role_permissions" multiple="multiple" style="width:100%; border-color:red !important;" required>
                                @foreach($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                                </select>
                            </span>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="save_role_btn" class="btn btn-primary" style="width:100%;" type="button">Save</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>
                </form>
            </div>
        </div>
    </div>

     <!-- Edit Modal -->
    <div class="modal fade" id="edit_role_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Edit Role</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit_role_form" action="{{ route('updateRole') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input class="form-control" id="edit_role_id" name="edit_role_id" type="hidden" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_role_name">Role</label>

                        <div class="col-md-8">
                            <input class="form-control" id="edit_role_name" name="edit_role_name" placeholder="Enter role name" />
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_role_permissions">Permissions</label>

                        <div class="col-md-8">
                            <span class="emptyField"><select class="js-example-basic-multiple form-control" name="edit_role_permissions[]" id="edit_role_permissions" multiple="multiple" style="width:100%; border-color:red !important;" required>
                                @foreach($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select></span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="update_role_btn" class="btn btn-primary" style="width:100%;" type="button">Update</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/Admin/role.js') }}"></script>
    




