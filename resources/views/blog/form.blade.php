@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Blogs List') }}</div>

                <div class="card-body">
                @if(isset($blog))
                    <form name="blogForm" id="blogForm" method="post" action="{{ route('blog.update',$blog->id) }}" enctype="multipart/form-data">
                    <input name="_method" type="hidden" value="PUT">
                @else
                    <form name="blogForm" id="blogForm" method="POST" action="{{ route('blog.store') }}" enctype="multipart/form-data">
                @endif
                        @csrf
                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>

                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ isset($blog) ? $blog->title : '' }}" autocomplete="title" autofocus>

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                            <div class="col-md-6">
                                <textarea id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description"> {{isset($blog) ? $blog->description : ''}}</textarea>

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __('Start Date') }}</label>

                            <div class="col-md-6">
                                <input id="start_date" type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ isset($blog) ? $blog->start_date : '' }}"  autocomplete="start_date" autofocus>

                                @error('start_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="end_date" class="col-md-4 col-form-label text-md-right">{{ __('End Date') }}</label>

                            <div class="col-md-6">
                                <input id="end_date" type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{isset($blog) ? $blog->end_date : '' }}"  autocomplete="end_date" autofocus>

                                @error('end_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Image') }}</label>
                            @if(isset($blog))
                            <div class="col-md-3">
                                <img src="{{$blog->image}}" alt="{{$blog->title}}" class='w-100 m-1' />
                            </div>
                            @endif
                            <div class="col-md-3">
                                <input id="image" type="file" class="form-control @error('image') is-invalid @enderror" name="image" value="{{ old('image') }}"  autocomplete="image" autofocus>

                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="is_active" class="col-md-4 col-form-label text-md-right">{{ __('Is Active') }}</label>

                            <div class="col-md-6">
                                <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror">
                                        <option value="1" {!! (isset($blog) && $blog->is_active == 1) ? "selected" : "" !!}> Active </option>
                                        <option value="0" {!! (isset($blog) && $blog->is_active == 0) ? "selected" : "" !!}> In Active </option>
                                </select>

                                @error('is_active')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{!! asset('js/jquery.validate.js') !!}"></script>
<script src="{!! asset('js/additional-methods.js') !!}"></script>
<script type="text/javascript">
 $(document).ready(function () {
     var isEdit = "{{isset($blog) ? false : true}}";
    $('#blogForm').validate({
        rules: {
            'title': {
                required: true
            },
            'description' :{
                required: true
            },
            'start_date' :{
                required: true
            },
            'end_date' :{
                required: true,
            },
            'image': {
                required: isEdit,
                accept: "image/jpg,image/jpeg,image/png,image/gif"                
            },
        },
        highlight: function (input) {
            $(input).parents('.form-line').addClass('error');
        },
        unhighlight: function (input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function (error, element) {
            $(element).parents('.form-group').append(error);
        },
    });
 });
</script>
@endsection
