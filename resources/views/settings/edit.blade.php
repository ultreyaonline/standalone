@extends('layouts.app')

@section('title', 'Configuration Settings')

@section('content')
    <style>
        .col-form-label { color: #3490dc;}
    </style>
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="card">
                    <div class="card-header">Site Settings</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('admin-settings-update') }}">
                            @csrf @method('patch')

        @foreach($settings as $setting)
            @php
                $rules = collect($setting->rules);
                $required = $rules->contains('required');
                $type = $rules->contains('email') ? 'email' : 'text';
                if ($rules->contains('numeric') || $setting->cast === 'integer') $type = 'number';
                if ($rules->contains('date')) $type = 'date';
                if ($setting->cast === 'boolean') $type = 'checkbox';
                if (Str::startsWith($rules->first(), 'in:')) {
                    $type = 'radio';
                    $options = explode(',', substr($rules->first(), 3)); // 3 = first char after 'in:'
                }
                if ($setting->encrypt === true) $type = 'password';
                // @TODO - find a way to detect textarea relevance, and create a field type for it
            @endphp

            @if ($type === 'text' || $type === 'number' || $type === 'email' || $type === 'date' || $type === 'password')

            <div class="form-group row{{ $errors->has($setting->id) ? ' is-invalid' : '' }}">

            @if($setting->label)
                <label class="col-lg-5 text-lg-right col-form-label" for="input_{{ $setting->id }}">{{ $setting->label }}</label>
            @endif

                <div class="col-lg-7">
                    <input type="{{ $type }}" name="{{ $setting->id }}" value="{{ old($setting->id) ?: $setting->getValue() }}" id="input_{{ $setting->id }}" class="form-control" maxlength="255" {{ $required }} autocomplete="false">
                    <p class="small">{{ $setting->hint }}</p>
                    @if ($errors->has($setting->id))
                    <span class="form-text text-danger"> <strong>{{ $errors->first($setting->id) }}</strong> </span>
                    @endif
                </div>
            </div>
            @endif


            @if ($type === 'checkbox')
            <div class="form-group row">
                <label class="col-lg-5 text-lg-right col-form-label">{{ $setting->label }}</label>
                <div class="checkbox col-lg-7">
                    @php
                    $checked = old($setting->id) == true || $setting->getValue() == true;
                    @endphp
                    <input type="radio" name="{{ $setting->id }}" value="1" {{ $checked ? 'checked' : '' }} id="input_{{ $setting->id }}-true">
                    <label for="input_{{ $setting->id }}-true">Yes</label>
                    <input type="radio" name="{{ $setting->id }}" value="0" {{ $checked === false ? 'checked' : '' }} id="input_{{ $setting->id }}-false">
                    <label for="input_{{ $setting->id }}-false">No</label>
                    <p class="small">{{ $setting->hint }}</p>
                </div>
            </div>
            @endif

            @if ($type === 'radio')
            <div class="form-group row">
                <label class="col-lg-5 text-lg-right col-form-label">{{ $setting->label }}</label>
                <div class="radio col-lg-7">
                    @foreach($options as $option)
                    <input type="radio" name="{{ $setting->id }}" value="{{ $option }}" {{ old($setting->id) === $option || $setting->getValue() === $option ? 'checked' : '' }} id="input_{{ $setting->id . '-' . $option }}">
                        <label for="input_{{ $setting->id . '-' . $option }}">{{ $option }}</label> <br>
                    @endforeach
                    <p class="small">{{ $setting->hint }}</p>
                </div>
            </div>
            @endif

            <hr>

        @endforeach


                            <div class="form-group row">
                                <div class="col-12">
                                    <button type="submit" class="float-sm-right btn btn-primary"><i class="fa fa-btn fa-save"></i> Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

