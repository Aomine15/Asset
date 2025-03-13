@extends('Navigation.head')

@section('users')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><a href="/dashboard">Dashboard</a> / <span> Users</h4>

        <div class="card">
            <div class="card-header">
                <div style="font-size: 1.5em">User information
                    <a href="#" data-bs-toggle="modal" data-bs-target="#addUser" class="btn btn-primary float-end">Add User</a>
                </div>
            </div>
            <div class="card-body">
                <div id="successmessage">
                </div>
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registration Date</th>
                        <th>Action</th>
                    </thead>
                    <tbody class="table-group-divider">

                    </tbody>
                </table>
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
                            <input type="text" name = "full_name" class="name form-control" value="{{old('full_name')}}" placeholder = "Enter your username">
                        </div>

                        <div class="form-group mb-3">
                            <label for="">Email</label>
                            <input type="email" name = "email" class="email form-control" value="{{old('email')}}" placeholder = "Enter your email address">
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
                    <button type="button" class="btn btn-primary add_user">Save</button>
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
                                <tr>
                                    <td>${item.name}</td>
                                    <td>${item.email}</td>
                                    <td>${item.created_at}</td>
                                    <td coldspan="2">
                                        <button class="btn btn-primary btn-sm" value="${item.id}">Edit</button>
                                        <button class="btn btn-danger btn-sm" value="${item.id}">Delete</button>
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


            $(document).on('click', '.add_user', function (e) {
                e.preventDefault();

                var data = {
                    'full_name': $('.name').val(),
                    'email': $('.email').val(),
                    'password': $('.password').val(),
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
                            $('#errorlist').html("");
                            $('#errorlist').addClass("alert alert-danger");
                            $.each(response.errors, function (key, err_values) {
                                $('#errorlist').append('<li>' + err_values + '</li>')
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


        });
    </script>

@endsection
