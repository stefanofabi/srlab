@extends('patients/default-template')

@section('title')
{{ trans('protocols.show_protocol') }} #{{ $protocol->id }}
@endsection

@section('active_results', 'active')

@section('menu-title')
{{ trans('forms.menu') }}
@endsection

@section('js')
    <script type="text/javascript">
        function print_selection() {
            $('#print_selection').submit();
        }
    </script>
@endsection

@section('menu')
<ul class="nav flex-column">
	<li class="nav-item">
		<a class="nav-link" target="_blank" href="{{ route('patients/protocols/print', $protocol->id) }}"> <img src="{{ asset('img/drop.png') }}" width="25" height="25"> {{ trans('protocols.print_report') }} </a>
	</li>
</ul>
@endsection

@section('content-title')
<i class="fas fa-file-medical"></i> {{ trans('protocols.show_protocol') }} #{{ $protocol->id }}
@endsection


@section('content')

	<div class="input-group mt-2 mb-1 col-md-9 input-form">
		<div class="input-group-prepend">
			<span class="input-group-text"> {{ trans('patients.patient') }} </span>
		</div>

		<input type="text" class="form-control" value="{{ $patient->full_name }}" disabled>
	</div>

	<div class="input-group mt-2 mb-1 col-md-9 input-form">
		<div class="input-group-prepend">
			<span class="input-group-text"> {{ trans('social_works.social_work') }} </span>
		</div>

		<input type="text" class="form-control" value="{{ $social_work->name }} {{ $plan->name }}" disabled>
	</div>

	<div class="input-group mt-2 mb-1 col-md-9 input-form">
		<div class="input-group-prepend">
			<span class="input-group-text"> {{ trans('prescribers.prescriber') }} </span>
		</div>

		<input type="text" class="form-control" value="{{ $prescriber->full_name }}" disabled>
	</div>

	<div class="input-group mt-2 mb-1 col-md-9 input-form">
		<div class="input-group-prepend">
			<span class="input-group-text"> {{ trans('protocols.completion_date') }} </span>
		</div>

		<input type="date" class="form-control" value="{{ $protocol->completion_date }}" disabled>
	</div>

	<div class="input-group mt-2 mb-1 col-md-9 input-form">
		<div class="input-group-prepend">
			<span class="input-group-text"> {{ trans('protocols.diagnostic') }} </span>
		</div>

		<input type="text" class="form-control" value="{{ $protocol->diagnostic }}" disabled>
	</div>
@endsection

@section('extra-content')

<div class="card mt-3 mb-4">
	<div class="card-header">
        <div class="btn-group float-right">
            <button type="button" class="btn btn-primary" onclick="print_selection()">{{ trans('protocols.print_selected') }}</button>
        </div>

        <h4> <span class="fas fa-syringe" ></span> {{ trans('determinations.determinations')}} </h4>
    </div>

    <div class="table-responsive">
		<table class="table table-striped">
				<tr class="info">
                    <th>  </th>
					<th> {{ trans('determinations.code') }} </th>
					<th> {{ trans('determinations.determination') }} </th>
					<th> {{ trans('determinations.amount') }} </th>
					<th> {{ trans('determinations.informed') }} </th>
					<th class="text-right"> {{ trans('forms.actions') }}</th>
				</tr>

                <form id="print_selection" action="{{ route('patients/protocols/print_selection') }}" method="post" target="_blank">
                   @csrf

                    @foreach ($practices as $practice)
                        <tr>
                            <td style="width: 50px"> <input type="checkbox" name="to_print[]" value="{{ $practice->id }}"> </td>
                            <td> {{ $practice->report->determination->code }} </td>
                            <td> {{ $practice->report->determination->name }} </td>
                            <td> $ {{ number_format($practice->amount, 2, ",", ".") }} </td>
                            <td>
                                @if (empty($practice->results->first()))
                                    <span class="badge badge-primary"> {{ trans('forms.no') }} </span>
                                @else
                                    <span class="badge badge-success"> {{ trans('forms.yes') }} </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('patients/protocols/practices/show', $practice->id) }}" class="btn btn-info btn-sm" title="{{ trans('protocols.show_practice') }}"> <i class="fas fa-eye fa-sm"></i> </a>
                            </td>
                        </tr>
                    @endforeach
                </form>
		</table>
	</div>
</div>
@endsection
