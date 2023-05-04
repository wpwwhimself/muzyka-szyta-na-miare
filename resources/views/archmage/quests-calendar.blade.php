@extends("layouts.app")

@section('content')

<section>
  <div class="section-header">
    <h1>
      <i class="fa-solid fa-calendar"></i>
      Grafik nadchodzących zleceń
    </h1>
  </div>
  <x-calendar :click-days="false" :with-today="true" :length="$calendar_length" />
</section>
@endsection