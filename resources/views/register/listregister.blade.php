<style>
    .table-responsive {
        overflow: visible;
    }
    #userTable {
        width: 100% !important;
    }
</style>
<div class="container mt-2">
    <div id="formtable">
        <h5>User Table</h5>
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" id="startDate" class="form-control" placeholder="Start Date">
            </div>
            <div class="col-md-3">
                <input type="date" id="endDate" class="form-control" placeholder="End Date">
            </div>
            <div class="col-md-3">
                <input type="text" id="searchBox" class="form-control" placeholder="Search">
            </div>
            <div class="col-md-3">
                <button id="filterBtn" class="btn btn-primary">Filter</button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="userTable" class="display table table-bordered mb-2">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Roles</th>
                        <th>Joined At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi oleh DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    <div id="formedit" class="d-none">
        <div class="container master-edit-register"><br>
            <div class="col-md-6 col-md-offset-3">
                <h2 class="text-center">FORM EDIT USER</h2>
                <hr>
                {{-- @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
                @endif --}}
                <h3 id="message"></h3>
                <form action="" id="editListRegisterForm" method="post">
                    @csrf
                    <input type="text" name="id" id="id" class="form-control" value="" required="" readonly>
                    <div class="form-group">
                        <label><i class="fa fa-envelope"></i> Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="" required="">
                    </div>
                    <div class="form-group">
                        <label><i class="fa fa-user"></i> Username</label>
                        <input type="text" name="name" id="name" class="form-control" value="" required="">
                    </div>
                    <div class="form-group">
                        <label><i class="fa fa-key"></i> Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>
                    <div class="form-group">
                        <label><i class="fa fa-address-book"></i> Role</label>
                        <input type="text" name="roles" id="roles" class="form-control" value="" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" id="but_edit_list_register"><i class="fa fa-user"></i> Update</button>
                    <hr>
                    <p class="text-center">Kembali ke <a href="javascript:void(0);">Dashboard</a></p>
                </form>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // ========================== menapilkan list user ===============================
    loadListRegisterForm();
    function loadListRegisterForm() {
        let table = $('#userTable').DataTable({
            ajax: {
                url: '{{ route("filter_register") }}',
                data: function(d) {
                    d.startDate = $('#startDate').val();
                    d.endDate = $('#endDate').val();
                    d.searchText = $('#searchBox').val();
                }
            },
            columns:[
                { data: 'email' },
                { data: 'name' },
                { data: 'roles' },
                { data: 'created_at' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return '<button class="btn btn-primary btn-sm editBtn" data-id="' + row.id + '">' + '<i class="fas fa-pencil-alt"></i>' + '</button> ' + '<button class="btn btn-danger btn-sm deleteBtn" data-id="' + row.id + '">' + '<i class="fas fa-trash"></i>' + '</button>';
                    }
                }
            ],
            searching: false,
            paging: true,
            info: false,
            scrollY: '50vh',  // Menambahkan scrolling vertikal
        scrollCollapse: true,
        scrollX: true,
        fixedHeader: {
            header: true,
            footer: false
        }
        });
            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });
    }
    // ========================== end of menapilkan list user ===============================

    // ============================ edit list user =================================
    $(document).on('click', '.editBtn', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = '{{ route("edit_list_register", ":id") }}';
        url = url.replace(':id', id);
        $.ajax({
            url: url, // Route to load the form
            type: 'GET',
            success: function(data) {
                $('#id').val(data.id);
                $('#email').val(data.email);
                $('#name').val(data.name);
                $('#roles').val(data.roles);

                // Tampilkan form
                $('#formtable').hide();
                $('#formedit').removeClass('d-none');
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    });
    // ========================== end of edit list user ===============================
    // ========================== update list user ===============================
    $(document).off('submit', '#editListRegisterForm');

    $(document).on('submit', '#editListRegisterForm', function(e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();
        e.stopPropagation();
        $.ajax({
            url: '{{ route('update_list_register') }}', // Route to handle form submission
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#formedit').addClass('d-none');
                $('#formtable').show();

                $('#userTable').DataTable().ajax.reload();
                Swal.fire({
                    title: 'Sukses!',
                    text: 'Data berhasil diupdate!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            },
            error: function(response) {
                $('#message').html('<p>' + response.responseJSON.pesan + '</p>');
            }
        });
    });
    // ========================== end of update list user ===============================
    // ============================= delete list user ==================================
    $(document).on('click','.deleteBtn', function(e){
        e.preventDefault();
        let row = $(this).closest('tr');
        let id = $(this).data('id');
        let url = '{{ route("delete_list_register", ":id") }}';
        url = url.replace(':id', id);

        Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#userTable').DataTable().row(row).remove().draw(false);

                        Swal.fire(
                            'Terhapus!',
                            'Data telah berhasil dihapus.',
                            'success'
                        );
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat menghapus data.',
                            'error'
                        );
                    }
                });
            }
        });
    })
    // ========================== end of delete list user ===============================

});


</script>
