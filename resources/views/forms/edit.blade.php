@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="page-header">
                    <h1>{{ $page['title'] }}</h1>
                </div>
                <form action="/form" method="POST">
                    <div class="form-group">
                        <input type="text" class="form-control input-hg" name="title" placeholder="Form Title">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="description" placeholder="Form Description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="url">Responses URL (Google Sheets):</label>
                        <input type="text" class="form-control" name="responses_url" placeholder="https://docs.google.com/spreadsheets/d/13Wxta0N3WrKMUp2XeVj0L6l_V6beGo4OQ8PSM_SHCU4/edit#gid=0">
                    </div>
                    <hr/>
                    <div class="text-left">
                        <button type="submit" class="btn btn-primary btn-wide mrm">Save</button>
                        <a href="/home" class="btn btn-default btn-wide">Cancel</a>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div> <!-- /.col-md-8 -->
        </div>
    </div>
@endsection