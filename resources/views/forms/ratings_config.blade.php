@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="page-header">
                    <h4>Ratings Config</h4>
                </div>

                <dl class="dl-horizontal">
                    <dt>Form</dt>
                    <dd><a href="/form/{{ $form->id }}">{{ $form->title }}</a></dd>
                    <dt>Description</dt>
                    <dd>{!! str_limit(nl2br($linkify->process($form->description)), 100) !!}</dd>
                </dl>

                <hr/>

                <p class="text-center" id="no-config" style="display: none;">
                    <em>There is nothing here yet. <a href="javascript:addConfig({});">Add</a> a new config to get started.</em>
                </p>

                <div class="configs"></div>

                <p class="text-center">
                    <a href="javascript:addConfig({});" class="btn btn-primary btn-sm"><i class="fa fa-btn fa-plus"></i> Add</a>
                </p>

                <hr/>

                <p class="text-right">
                    <a href="javascript:updateConfigs();" class="btn btn-wide btn-primary">Save</a>
                    <a href="/form/{{ $form->id }}" class="btn btn-wide btn-default">Cancel</a>
                </p>

            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js"></script>

    <script type="text/x-handlebars-template" id="template-config">
        <div class="panel panel-default config" id="config-@{{ id }}">

            <input name="id" type="hidden" value="@{{ id }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-11 form-horizontal">
                        <div class="form-group">
                            <label for="label" class="col-sm-3 control-label">Label</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="title" placeholder="Label" value="@{{ title }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="label" class="col-sm-3 control-label">Type</label>
                            <div class="col-sm-9">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-inverse @{{ type_text }}">
                                        <input type="radio" name="type" id="option1" autocomplete="off" value="text"> Free Text
                                    </label>
                                    <label class="btn btn-inverse @{{ type_numeric }}">
                                        <input type="radio" name="type" id="option2" autocomplete="off" value="numeric"> Numeric
                                    </label>
                                    <label class="btn btn-inverse @{{ type_yes_no }}">
                                        <input type="radio" name="type" id="option3" autocomplete="off" value="yes_no"> Yes/No
                                    </label>
                                    <label class="btn btn-inverse @{{ type_stars }}">
                                        <input type="radio" name="type" id="option4" autocomplete="off" value="stars"> Stars
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="label" class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="description" placeholder="Description" rows="2">@{{ description }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="label" class="col-sm-3 control-label">Required</label>
                            <div class="col-sm-9">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-inverse @{{ required_yes }}">
                                        <input type="radio" name="required" id="required_yes" autocomplete="off" value="yes"> Yes
                                    </label>
                                    <label class="btn btn-inverse @{{ required_no }}">
                                        <input type="radio" name="required" id="required_no" autocomplete="off" value="no"> No
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-1 text-right">
                        <button class="close" aria-label="Close" onclick="javascript:removeConfig('@{{ id }}')"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>

            </div>
        </div>
    </script>

    <script type="text/javascript">
        var source;
        var template;

        var new_id = 0;

        function updateConfigs() {
            var payload = {
                '_token': '{{ csrf_token() }}',
                'configs': {}
            };

            $('.config').each(function ( index ) {
                payload.configs[index] = {
                    'title': $( this ).find('input[name="title"]').val(),
                    'type': $( this ).find('label.active input[name="type"]').val(),
                    'description': $( this ).find('textarea[name="description"]').val(),
                    'required': $( this ).find('label.active input[name="required"]').val()
                }
            });

            $.ajax({
                type: "PATCH",
                data: payload,
                url: '/form/{{ $form->id }}/ratings/config',
                success: function(worked) {
                    if (worked > 0) window.location = '/form/{{ $form->id }}';
                }
            });
        }

        function addConfig(config) {
            if ($.isEmptyObject(config)) {
                config = { id: 'new-'+new_id, type: 'text' };
                new_id =+ 1;
            }

            switch (config.type) {
                case 'text':
                    config.type_text = 'active';
                    break;
                case 'numeric':
                    config.type_numeric = 'active';
                    break;
                case 'yes_no':
                    config.type_yes_no = 'active';
                    break;
                case 'stars':
                    config.type_stars = 'active';
                    break;
                default:
                    config.type_text = 'active';
            }
            switch (config.required){
                case 'yes':
                    config.required_yes = 'active';
                    break;
                case 'no':
                    config.required_no = 'active';
                    break;
                default:
                    config.required_yes = 'active';
            }

            var html = template(config);

            $('#no-config').hide();
            $('.configs').append(html);

            $('.configs').sortable({
                items: ' .panel'
            });
            $('select').select2({dropdownCssClass: 'dropdown-inverse'});
        }

        function removeConfig(id) {
            $('#config-'+id).remove();
            $( ".configs" ).sortable();
            if ($('.configs').html().trim() == '') {
                $('#no-config').show();
            }
        }


        $( document ).ready(function() {
            source   = $('#template-config').html();
            template = Handlebars.compile(source);

            @if(count($form->ratings_config) == 0)
                $('#no-config').show();
            @else
                @foreach($form->ratings_config as $config)
                    addConfig( {!! json_encode($config) !!} );
                @endforeach
            @endif

        });

    </script>
@endsection