@extends('administrators/default-template')

@section('title')
    {{ trans('settings.settings') }}
@endsection

@section('menu-title')
    {{ trans('forms.menu') }}
@endsection

@section('menu')
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('administrators/settings/nomenclators/index') }}"> <img src="{{ asset('images/drop.png') }}" width="25" height="25"> {{ trans('social_works.nomenclators') }} </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('administrators/settings/social_works/billing_periods/index') }}"> <img src="{{ asset('images/drop.png') }}" width="25" height="25"> {{ trans('billing_periods.billing_periods') }} </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('administrators/settings/social_works/index') }}"> <img src="{{ asset('images/drop.png') }}" width="25" height="25"> {{ trans('social_works.social_works') }} </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('administrators/settings/generate_reports') }}"> <img src="{{ asset('images/drop.png') }}" width="25" height="25"> {{ trans('settings.generate_reports') }} </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('administrators/settings/activity_logs') }}"> <img src="{{ asset('images/drop.png') }}" width="25" height="25"> {{ trans('settings.activity_logs') }} </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('administrators/settings/system_logs') }}"> <img src="{{ asset('images/drop.png') }}" width="25" height="25"> {{ trans('settings.system_logs') }} </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('administrators/home') }}"> <img src="{{ asset('images/drop.png') }}" width="25" height="25"> {{ trans('forms.go_back') }} </a>
        </li>
    </ul>
@endsection

@section('content-title')
    <i class="fas fa-cogs"></i> {{ trans('settings.settings') }}
@endsection

@section('content')

@endsection
