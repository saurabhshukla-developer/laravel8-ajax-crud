<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <!-- header -->
        <header class="bg-light text-center p-3">
            <h4>Task 2 - Employee CRUD Using Ajax</h4>
        </header>
    
        <section class="container my-5">
            <div class="table-responsive">
                <div style="display:flex;">
                    <h2 class="mb-0">Employee List</h2>
                    <div class="ml-auto">
                        <a class="btn btn-primary" id="btn-add-employee-modal" href="#add_employee" data-toggle="modal">
                            Add Employee
                        </a>
                        <a class="btn btn-primary" href="{{ url('api/export') }}">
                            Export To .CSV
                            {{-- URL::to('/'); --}}
                        </a>
                    </div>
                </div>
                <hr class="mt-2">
                <table class="table table-striped table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Designation</th>
                            <th>Salary(INR)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="employee-data-tbody">
                        
                    </tbody>
                </table>
            </div>
        </section>
    
        <div class="modal fade" id="add_employee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" style="overflow-y: initial !important">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_label">New Employee Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="max-height:400px; overflow-y:auto;">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 pt-2 mb-1" style="height:fit-content;">
                                    <form action="" method="" id="add-employee-form" accept-charset="utf-8">
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="name">Name</label><span class="text-danger">*</span>
                                                <input name="name" type="text" class="form-control" placeholder="Employee Name" required>
                                                <span id="name_error" class="text-danger"></span>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="email">Email</label><span class="text-danger">*</span>
                                                <input name="email" type="email" class="form-control" placeholder="employee@company.com" required>
                                                <span id="email_error" class="text-danger"></span>
                                            </div>      
                                            <div class="col-md-4 form-group">
                                                <label for="mobile">Mobile</label><span class="text-danger">*</span>
                                                <input name="mobile" type="text" class="form-control" placeholder="+91" required>
                                                <span id="mobile_error" class="text-danger"></span>
                                            </div>              
                                            <div class="col-md-4 form-group">
                                                <label for="designation">Designation</label><span class="text-danger">*</span>
                                                <select class="form-control" name="designation" required>
                                                    <option value="manager">Manager</option>
                                                    <option value="team lead">Team Lead</option>
                                                    <option value="senior developer">Senior Developer</option>
                                                    <option value="junior developer">Junior Developer</option>
                                                    <option value="intern" selected>Intern</option>
                                                </select>
                                                <span id="designation_error" class="text-danger"></span>
                                            </div>         
                                            <div class="col-md-4 form-group">
                                                <label for="salary">Salary</label><span class="text-danger">*</span>
                                                <input name="salary" type="number" class="form-control" placeholder="6" required>
                                                <small class="font-italic">Lakhs Per Anum(INR)</small>
                                                <span id="salary_error" class="text-danger"></span>
                                            </div>                        
                                        </div>
                                        <input type="hidden" name="operation">
                                        <input type="hidden" name="emp_id">
                                    </form>
                                </div>
                            </div> <!--row end-->
                        </div> <!--container end-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="close-modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary button-add-edit">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            $(document).ready(function()
            {
                var baseURL = {!! json_encode(url('/')) !!} + "/";

                // var baseURL = "http://localhost/personal/interview/laravel8-ajax-crud/public/";
                var all_employee_details = '';
                showAllEmployee();
                
                $('#btn-add-employee-modal').click(function()
                {
                    $('#add-employee-form').trigger("reset");
                    $('.modal-title').text('Add New Employee');
                    $('.button-add-edit').text('Save');
                    $('#add-employee-form').attr('action', baseURL+"api/employee");
                    $('input[name=operation]').val('add');
                    $('input[name=id]').val('');
                });

                $(document).on("click",".edit-employee",function()
                {
                    $('#add-employee-form').trigger("reset");
                    $('.modal-title').text('Edit Employee Details');
                    $('.button-add-edit').text('Update');
                    $('#add-employee-form').attr('action', baseURL+"api/update/employee/");
                    $('input[name=operation]').val('update');

                    // Add values in form
                    index = $(this).attr('data-index');
                    $('input[name=emp_id]').val(all_employee_details[index].id);
                    $('input[name=name]').val(capitalizeFirstLetter(all_employee_details[index].name));
                    $('input[name=email]').val(all_employee_details[index].email);
                    $('input[name=mobile]').val(all_employee_details[index].mobile);
                    $('input[name=salary]').val(all_employee_details[index].salary);
                    $('select[name=designation]').val(all_employee_details[index].designation);
                });

                $(document).on('click','.button-add-edit', function(e)
                {
                    e.preventDefault();

                    var name = $.trim($('input[name=name]').val());
                    var email = $('input[name=email]').val();
                    var mobile = $('input[name=mobile]').val();
                    var salary = $('input[name=salary]').val();
                    var designation = $('select[name=designation]').val();
                    var errors = {};
                    error_display = ['name_error', 'email_error', 'salary_error', 'mobile_error', 'designation_error'];
                    for(let i=0; i<error_display.length; i++)
                    {
                        $('#'+error_display[i]).text('');
                    }

                    if(!name)
                    {
                        errors['name_error'] = 'Please Enter Valid Name';
                    }
                    if(!email)
                    {
                        errors['email_error'] = 'Please Enter Valid Email';
                    }
                    if(mobile.length == 0 || mobile.length != 10)
                    {
                        errors['mobile_error'] = 'Mobile Number should be 10 digits long';
                    }
                    if(!salary)
                    {
                        errors['salary_error'] = 'Please Enter Valid Salary';
                    }
                    if(!designation)
                    {
                        errors['designation_error'] = 'Please Enter Valid Designation';
                    }
                    if(!$.isEmptyObject(errors))
                    {
                        $.each(errors, function (i){
                            $('#'+i).text(errors[i]);
                        });
                        errors = {};
                        return;
                    }

                    var operation = $('input[name=operation]').val();
                    var url = $('#add-employee-form').attr('action');
                    if(operation == 'update')
                    {
                        var emp_id = $('input[name=emp_id]').val();
                        url = url+''+emp_id;
                    }

                    var form_id = $('#add-employee-form');
                    var data =  $('#add-employee-form').serialize();
                    $.ajax({
                        method: "post",
                        url: url,
                        data: data,
                        async: false,
                        dataType: 'json',
                        success: function(response)
                        {
                            $( "#close-modal" ).trigger( "click" );
                            showAllEmployee();
                            if(operation == 'update')
                            {
                                swal("Poof!", "Employee has been updated successfully", "success");
                            }
                            else{
                                swal("Poof!", "Employee has been added successfully", "success");
                            }
                        },
                        error: function()
                        {
                            $( "#close-modal" ).trigger( "click" );
                            if(operation == 'update')
                            {
                                swal("oops!", "Could not update Employee, try adding again", "error");
                            }
                            else
                            {
                                swal("oops!", "Could not add Employee, try adding again", "error");
                            }
                        } 
                    });
                });

                $(document).on('click','.delete-btn', function()
                {
                    var employee_id = $(this).attr('data-id');				
                    url = baseURL+"api/delete/employee/"+employee_id;
                    swal({
                        title: "Are you sure?",
                        text: "Once deleted, you will not be able to recover Employee!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => 
                    {
                        if (willDelete) 
                        {
                            $.ajax({
                                method: 'get',
                                url: url,
                                success: function(response)
                                {
                                    swal("Poof!", "Employee has been deleted successfully", "success");
                                    showAllEmployee();	
                                },
                                error: function()
                                {
                                    swal("oops!", "Could not delete Employee data", "error");
                                } 
                            });
                        }
                    });
                });

                function showAllEmployee()
                {
                    url = baseURL+'api/employees';
                    $.ajax({
                        type: 'get',
                        url:url,
                        async: false,
                        dataType: 'json',
                        success: function(response){
                            if(response.data == '')
                            {
                                $('#employee-data-tbody').html('<tr class="text-center"><td colspan="7">No data found</td></tr>');
                            }
                            else {
                                var employee_detail = '';
                                all_employee_details = employee_details = response.data;
                                for(i=0; i<employee_details.length; i++)
                                {
                                    employee_detail += 
                                            `<tr>
                                                <td>`+(i+1)+`</td>
                                                <td>`+capitalizeFirstLetter(employee_details[i].name)+`</td>
                                                <td>`+employee_details[i].email+`</td>
                                                <td>`+employee_details[i].mobile+`</td>
                                                <td>`+capitalizeFirstLetter(employee_details[i].designation)+`</td>
                                                <td>`+employee_details[i].salary+` LPA</td>
                                                <td>
                                                    <a data-id="`+employee_details[i].id+`" data-index="`+i+`" class="btn btn-sm btn-info edit-employee" href="#add_employee" data-toggle="modal">
                                                        Edit
                                                    </a>
                                                    <button data-id="`+employee_details[i].id+`"  data-index="`+i+`" class="btn btn-sm btn-danger delete-btn">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>`;
                                }
                                $('#employee-data-tbody').html(employee_detail);
                            }           
                        },
                        error: function(){
                            $('#employee-data-tbody').html('Some error occurred, please try again in some time');
                        }
                    });
                }

                function capitalizeFirstLetter(string){
                    return string.charAt(0).toUpperCase() + string.slice(1);
                }
            });
        </script>
    </body>
</html>
