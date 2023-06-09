@extends('layouts.app')

@section('content')
<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-clipboard-user"></i> Dane osobowe</h1>
    </div>
    <form action="">
        <x-input type="text" name="client_name" label="Nazwisko" :value="$client->client_name" />
        <div class="grid-3">
            <x-input type="email" name="email" label="Email" :value="$client->email" :small="true" />
            <x-input type="phone" name="phone" label="Telefon" :value="$client->phone" :small="true" />
            <x-input type="text" name="email" label="Inna droga kontaktu" :value="$client->other_medium" :small="true" />
        </div>
    </form>
</section>
<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-address-card"></i> Dane użytkownika</h1>
    </div>
    <form action="">
        <x-input type="text" name="password" label="Hasło" :value="$client->user->password" :small="true" />
    </form>
</section>
<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-suitcase"></i> Dane klienta</h1>
    </div>
    wszystko inne
</section>
@endsection
