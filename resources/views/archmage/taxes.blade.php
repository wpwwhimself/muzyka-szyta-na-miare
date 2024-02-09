@extends("layouts.app", compact("title"))

@section("content")

<div>
    <x-button action="{{ route('finance') }}" label="Wróć" icon="angles-right" />
</div>

<section>
    <div class="section-header">
        <h1>
            <i class="fas fa-cash-register"></i>
            Podliczenie podatków za rok {{ $fiscal_year }}
        </h1>
        @if (request()->get("fiscalYear"))
            <x-a href="{{ route('taxes') }}">{{ date("Y") - 1 }}</x-a>
        @else
            <x-a href="{{ route('taxes', ['fiscalYear' => date('Y')]) }}">{{ date("Y") }}</x-a>
        @endif
    </div>
    <x-stats-highlight-h title="Kwoty" :data="$money" :all-pln="true" />
</section>

@endsection
