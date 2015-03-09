@extends('admin.layouts.modal')

@section('content')
<div class="col-md-12 main middleWrap">
    <div class="row">      
        <div class="row">
            <form class="form-horizontal" enctype="multipart/form-data" method="post">
                <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                <div class="form-group">
                    <label for="inputName" class="control-label col-xs-2">Word</label>
                    <div class="col-xs-10">
                        <input type="hidden" class="form-control" id="inputWordType" name="word_type_id" value='{{ $type }}'>
                        <input type="text" class="form-control" id="inputName" name="word" placeholder="Word">
                        {{ $errors->first('word', '<span class="help-inline error-message">:message</span>') }}
                    </div>
                </div>
                @if($type == 1)
                <div class="form-group">
                    <label for="inputImg" class="control-label col-xs-2">Sentiment Category</label>
                    <div class="col-xs-10">
                       <select class="form-control" name="category_id" id="category_id">
                            
                                 @foreach ($categories as $category)
                                    <option value="{{{ $category->id }}}" {{{ ( $category->id == Input::old('category_id')  ? ' selected="selected"' : '') }}} >{{{ $category->meaning }}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="form-group">
                    <div class="col-xs-offset-2 col-xs-10">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
