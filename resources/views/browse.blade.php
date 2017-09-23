@extends('voyager::master')

@section('css')
	<meta name="csrf-token" content="{{ csrf_token() }}">

@stop


@section('page_title','edit')

@section('page_header')
	<h1 class="page-title">
		<i class="voyager-lock"></i>
		Blocker
	</h1>
@stop


@section('content')

	<div class="page-content edit-add container-fluid">
		<div class="row">
			<div class="col-md-12">

                <div class="alert alert-info">
                    <strong>{{ __('voyager.generic.how_to_use') }}:</strong>
                    <p>Create an JSON object with a key whitelist and set ip values that can visit admin side. Example: <code>{ "whitelist": ["127.0.0.1"] }</code> Be careful, don't block yourself. Update and clear code soon</p>
                </div>
				<div class="panel panel-bordered">

					<form role="form" class="form-edit-add" action="{{ route('voyager.blocker.update') }}" method="POST">
						{{ method_field("PUT") }}
						{{ csrf_field() }}

						<div class="panel-body">

							@if (count($errors) > 0)
								<div class="alert alert-danger">
									<ul>
										@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							@endif

							<div class="form-group">
								<label for="editor">Set whitelist ip's</label>

                                <div id="editor" data-editor="json"></div>

                                <textarea name="ips" style="display: none">@if( isset($blocker->ips) )
                                        {{ $blocker->ips }}
                                    @endif</textarea>
							</div>

						</div>

						<div class="panel-footer">
							<button type="submit" class="btn btn-primary save">{{ __('voyager.generic.save') }}</button>
						</div>

					</form>


				</div>


			</div>
		</div>
	</div>

@endsection

@section('javascript')
	<script>

        var textarea = $('textarea[name="ips"]');


        /*{
            "whitelist": ["127.0.0.1"]
        }*/
        var editor = ace.edit("editor");
        editor.setOption("maxLines", 30);
        editor.setOption("minLines", 10);
        editor.setTheme("ace/theme/github");
        editor.resize();
        editor.getSession().setMode("ace/mode/json");

        if (textarea.val()) {
            editor.setValue(JSON.stringify(JSON.parse(textarea.val()), null, 4));
        }

        window.onload = function() {
            textarea.val(editor.getSession().getValue());
        };

        editor.getSession().on("change", function () {
            textarea.val(editor.getSession().getValue());
        });

	</script>
@endsection