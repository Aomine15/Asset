@extends('Navigation.head')

@section('users')

    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/dashboard">Dashboad</a>x`
                </li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">

                <h5 class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h2>User information</h2>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-m" data-bs-toggle="modal" data-bs-target="#addUser">
                            Add User
                        </button>
                    </div>
                </h5>
            </div>
            <div class="card-body">
                    <div id="successmessage">

                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Registration Date</th>
                                <th>Action</th>
                            </thead>
                            <tbody class="table-border-bottom-0">

                            </tbody>
                        </table>
                    </div>
                <div class="pagination-links">
                    {!! $all_users->links() !!}
                </div>
            </div>
        </div>

        {{-- Add User Modal --}}
        <div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addUserLabel">Add User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body">

                        <ul id="errorlist"></ul>

                        <div class="form-group mb-3">
                            <label for="">Username</label>
                            <input type="text" name = "username" class="username form-control" value="{{old('username')}}" placeholder = "Enter your username">
                        </div>

                        <div class="form-group mb-3">
                            <label for="">Email</label>
                            <input type="email" name = "email" class="email form-control" value="{{old('email')}}" placeholder = "example@gmail.com">
                        </div>

                        <div class="form-group mb-3">
                            <label for="">Password</label>
                            <input type="password" name = "password" class="password form-control" placeholder = "Enter your password">
                        </div>

                        <div class="form-group mb-3">
                            <label for="">Confirm Password</label>
                            <input type="password" name = "password_confirmation" class="password_confirmation form-control" placeholder = "Confirm your password">
                        </div>

                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary add_user">Create</button>
                </div>
                </div>
            </div>
        </div>
        {{-- End-Add User Modal --}}
    </div>



@endsection


@section('scripts')

    <script>
        $(document).ready(function () {

            fetchData();

            $('#addUser').on('show.bs.modal', function () {
                setTimeout(function () {
                    $('.modal-backdrop').css('background-color', 'rgba(255, 255, 255, 0.1)');
                }, 1);
            });


            $(document).on("click", ".pagination a", function (e) {
                e.preventDefault();
                let page = $(this).attr("href").split("page=")[1];
                fetchData(page);
            });

            function fetchData(page = 1) {

                $.ajax({
                    type: "GET",
                    url: "/users?page=" + page,
                    dataType: "json",
                    success: function (response) {
                        $("tbody").html("");
                        $.each(response.all_users.data, function (key, item) {
                            $("tbody").append(`
                                <tr class="table-row row-${item.id}">
                                    <td>${item.name}</td>
                                    <td>${item.email}</td>
                                    <td>${item.created_at}</td>
                                    <td coldspan="2">
                                        <a class="bx bx-edit-alt text-primary me-2" type="button"
                                        </a>
                                        <a class="bx bx-trash text-danger" type="button"
                                        </a>
                                    </td>
                                </tr>
                            `);
                        });

                        // Update pagination links
                        $(".pagination-links").html(response.pagination);
                        $('.modal-backdrop').remove();
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching data:", error);
                    }
                });
            }

            // Add user
            $(document).on('click', '.add_user', function (e) {
                e.preventDefault();

                var data = {
                    'username': $('.username').val(),
                    'email': $('.email').val(),
                    'password': $('.password').val(),
                    'password_confirmation': $('.password_confirmation').val(),
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/users",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 400) {
                            $('#errorlist').html("").addClass("alert alert-danger");

                            // Clear only the invalid input fields
                            $('.form-control').each(function () {
                                let inputName = $(this).attr("name");
                                if (response.errors[inputName]) {
                                    $(this).val("");

                                    if (inputName === "password") {
                                        $('.password_confirmation').val("");
                                    }
                                }


                            });

                            $.each(response.errors, function (key, err_values) {
                                $('#errorlist').append('<li>' + err_values + '</li>');
                            });

                        } else {
                            $('#errorlist').html("");
                            $('#successmessage').html(
                                `<div class="alert alert-success alert-dismissible fade show p-2" role="alert"> ` +
                                response.message +
                                `<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`
                            );
                            $('#addUser').modal('hide');
                            $('.modal-backdrop').remove();
                            $('#addUser').find('input').val("");
                            fetchData();
                        }
                    }
                });
            });

            $('#addUser').on('shown.bs.modal', function () {
                $('.modal-backdrop').css('background-color', 'rgba(255, 255, 255, 0.1)'); // White with 80% transparency
            });

            $('#addUser').on('hidden.bs.modal', function () {
                $(this).find('input').val("");
                $('#errorlist').html("").removeClass("alert alert-danger");;
            });


        });
    </script>

@endsection
