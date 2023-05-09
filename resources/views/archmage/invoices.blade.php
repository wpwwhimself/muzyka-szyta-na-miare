@extends('layouts.app', compact("title"))

@section('content')

<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-file-invoice"></i>
            Lista faktur
        </h1>
    </div>
    <table>
        <thead>
            <tr>
                <th>Faktura</th>
                <th>Dotyczy</th>
                <th>Kwota</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($invoices as $invoice)
            <tr>
                <td>
                    <a href="{{ route('invoice', ['id' => $invoice->id]) }}">
                        <i class="fa-solid fa-{{ $invoice->visible ? 'file-invoice' : 'eye-slash' }}"></i>
                        {{ $invoice->fullCode }}
                    </a>
                </td>
                <td>
                @foreach ($invoice->quests as $quest)
                    <a href="{{ route('quest', ['id' => $quest->id]) }}">{{ $quest->id }}</a>
                @endforeach
                </td>
                <td class="{{ $invoice->isPaid ? '' : 'error' }}">{{ as_pln($invoice->amount) }}</td>
            </tr>
        @empty

        @endforelse
        </tbody>
    </table>
</section>

@endsection
