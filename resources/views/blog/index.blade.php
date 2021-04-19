@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Blogs List') }}</div>

                <div class="card-body">
                    <table class="table" id="blog_table">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
@endsection
@section('scripts')
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script type="text/javascript">
        var pageModel = $("#deleteModal");
        var csrfToken = "{{csrf_token()}}";    
        var baseUrl = "{{ url('/') }}";
        var tableUrl = "{{ Auth::user() ? route('blog.table') : route('blog.list.table') }}";
        function deleteBlog(id) {
            $.get(baseUrl + '/blog/delete/' + id, function(data, status) {
                pageModel.html('');
                pageModel.html(data);
                pageModel.modal('show');
            });
        }
 $(document).ready(function () {

    var dataTable = $('#blog_table').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "deferRender": true,
        "ajax": {
            "url": tableUrl,
            "dataType": "json",
            "type": "POST",
            "data": { _token: csrfToken }
        },
        "columns": [
            { "data": "id", orderable: true },
            { "data": "title", orderable: true },
            { "data": "image", orderable: false },
            { "data": "start_date", orderable: true },
            { "data": "end_date", orderable: true },
            { "data": "actions", orderable: false }
        ]
    });
 });
</script>
@endsection
