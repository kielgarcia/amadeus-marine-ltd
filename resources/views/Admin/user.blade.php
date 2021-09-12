<div class="container-fluid">
    <h5>Users
        <button id="new_user_btn" class="btn btn-primary" style="float:right;">New User</button>
    </h5>

    <br>

        <table id="users_tbl" class="table table-hover table-striped responsive" style="width:100%;">
            <thead>
                <tr>
                    <th class="all">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="all"></th>
                </tr>
            </thead>

            <tbody style="font-size:12px;">
            @foreach($users as $user)
                <tr role="row">
                    <td> {{$user->user_name}} </td>
                    <td> {{$user->email}} </td>
                    <td> {{$user->role_name}} </td>
                    <td> {{$user->status}} </td>
                    <td style="text-align:right;">
                        <button class="edit_user_btn btn" title="Edit User"
                            data-user_id="{{ $user->user_id }}"
                            data-user_name="{{ $user->user_name }}"
                            data-user_email="{{ $user->email }}"
                            data-user_role="{{ $user->role_id }}"
                            data-user_status="{{ $user->status }}">
                            <i class="fas fa-pen"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>                
        </table>
            

    <!-- New User Modal -->
    <div class="modal fade" id="new_user_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>New User</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="new_user_form" action="{{ route('saveUser') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_user_name">Name</label>

                        <div class="col-md-8">
                            <input class="form-control" id="new_user_name" name="new_user_name" placeholder="Enter fullname" />
                        </div>
                    </div>
                    

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_user_email">Email</label>

                        <div class="col-md-8">
                            <input type="email" class="form-control" id="new_user_email" name="new_user_email" placeholder="Enter valid email" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_user_role">Role</label>

                        <div class="col-md-8">
                            <select class="form-control" id="new_user_role" name="new_user_role">
                                <option value="" selected disabled style="display:none;">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select> 
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="save_user_btn" type="button" class="btn btn-primary" style="width:100%;">Save</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>
                
            </div>
        </div>
    </div>

     <!-- Edit User Modal -->
     <div class="modal fade" id="edit_user_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Update User</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="reset_user_password_form" action="{{ route('resetUserPassword') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                    {{ csrf_field() }}
                    <input type="hidden" class="form-control" id="reset_password_user_id" name="reset_password_user_id" readonly />
                </form>

                <form id="edit_user_form" action="{{ route('updateUser') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" class="form-control" id="edit_user_id" name="edit_user_id" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_user_name">Name</label>

                        <div class="col-md-8">
                            <input class="form-control" id="edit_user_name" name="edit_user_name" placeholder="Enter fullname" />
                        </div>
                    </div>
                    

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_user_email">Email</label>

                        <div class="col-md-8">
                            <input type="email" class="form-control" id="edit_user_email" name="edit_user_email" placeholder="Enter valid email" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_user_role">Role</label>

                        <div class="col-md-8">
                            <select class="form-control" id="edit_user_role" name="edit_user_role">
                                <option value="" selected disabled style="display:none;">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select> 
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_user_status">Status</label>

                        <div class="col-md-8">
                            <select class="form-control" id="edit_user_status" name="edit_user_status">
                                <option value="" selected disabled style="display:none;">- Select Status -</option>
                                <option value="Active">Active</option>
                                <option value="Deactivated">Deactivated</option>
                            </select>
                        </div>
                    </div>
                </div>

                </form>

                <div class="modal-footer">   
                    <button id="update_user_btn" class="btn btn-primary" style="width:100%;" type="button">Update</button>
                    <button id="reset_user_password_btn" class="btn btn-outline-secondary" style="width:100%;" type="button">Reset Password</button>
                </div>
                
                

            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/Admin/user.js') }}"></script>





