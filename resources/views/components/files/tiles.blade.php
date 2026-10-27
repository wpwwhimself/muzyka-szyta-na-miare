@props([
    "groupedFiles",
    "editable" => false,
    "highlightForClientId" => null,
    "canDownloadFiles" => false,
])

<div class="flex down">
    @forelse ($groupedFiles as $variant_name => $versions)
    <x-shipyard::app.section
        :title="'wariant: '.$variant_name"
        :title-lvl="3"
        :icon="model_field_icon('files', 'variant_name')"
        inner-class="flex down"
    >
        @if ($versions->first()->first()->variant_description)
        <div class="var_desc">
            {{ Illuminate\Mail\Markdown::parse($versions->first()->first()->variant_description ?? "") }}
        </div>
        @endif

        @foreach ($versions as $version_name => $transpositions)
        @php
        $version = $transpositions->firstWhere(fn ($v) => $v->description) ?? $transpositions->first();
        @endphp

        <x-shipyard::app.section
            :title="$version_name"
            :title-lvl="4"
            :icon="model_field_icon('files', 'version_name')"
            inner-class="flex down"
        >
            <x-slot:actions>
                @foreach ($transpositions->first()->version_tags ?? [] as $tag)
                <x-file-tag :tag="$tag" />
                @endforeach
            </x-slot:actions>

            @if ($version->base_file_id || $version->description != $version->variant_description)
            <div class="ver_desc">
                @if ($version->base_file_id)
                <span>
                    Plik oparty na wersji <strong>{{ $version->base_file->version_name }}</strong>
                    @if ($version->description != $version->variant_description)
                    z uwzględnieniem poniższych zmian:
                    @endif
                </span>
                @endif

                @if ($version->description != $version->variant_description)
                {{ Illuminate\Mail\Markdown::parse($version->description ?? "") }}
                @endif
            </div>
            @endif

            @foreach ($transpositions as $file)
            @unless (!$file->exclusiveClients->contains(Auth::user()) && !is_archmage())
            <x-shipyard::app.card @class([
                'ghost' => is_archmage() && !$file->exclusiveClients?->contains(App\Models\User::find($highlightForClientId)),
            ])
                inner-class="flex down"
            >
                <div class="flex right middle spread">
                    <div class="flex right middle" role="tp_tags">
                        <x-file-tag :transpose="$file->transposition" />
                        @if ($file->needs_replacing)
                        <x-file-tag tag="needs_replacing" />
                        @endif
                        @if ($version->exclusiveClients && is_archmage())
                        <div class="file-tag flex right center middle"
                            style="background-color: white;"
                            {{ Popper::pop("Widoczny dla: ".$version->exclusiveClients->pluck("display_name")->join(", ")) }}
                        >
                            <x-shipyard::app.icon name="eye" />
                        </div>
                        @endif
                    </div>
                    <small class="grayed-out" {{ Popper::pop($version->updated_at) }}>
                        {{ $version->updated_at->diffForHumans() }}
                    </small>
                </div>

                @if ($file->missing_files)
                <x-shipyard::app.h lvl="4" class="accent danger" icon="alert">Sejf jest niekompletny. Napisz do mnie, żeby dodać pliki.</x-shipyard::app.h>
                @endif

                <div class="flex right but-mobile-down center middle">
                    <div role="preview" class="flex right center middle">
                    @foreach ($file->file_paths as $extension => $f)
                        @switch ($extension)
                            @case ("mp4")
                            <video controls>
                                <source src="{{ route('safe-show', ["id" => $file->song_id, "filename" => basename($f)]) }}"
                                    type="video/mp4"
                                />
                            </video>
                            @break

                            @case ("mp3")
                            @case ("ogg")
                            <x-file-player
                                :song-id="$file->song_id"
                                :file="$file"
                                :type="$extension"
                            />
                            @break

                            @case ("pdf")
                                <span class="ghost">Podgląd PDF<br>niedostępny</span>
                            @break

                            @case ("jpg")
                            <img src="{{ route('safe-show', ["id" => $file->song_id, "filename" => basename($f)]) }}"
                                alt="Mapa utworu"
                                class="thumbnail interactive"
                                @popper(Mapa utworu: kliknij, aby powiększyć)
                                onclick="window.open('{{ route('safe-show', ['id' => $file->song_id, 'filename' => basename($f)]) }}');"
                            />
                            @break
                        @endswitch
                    @endforeach
                    </div>

                    <div role="tiles" class="flex right center middle no-gap">
                    @foreach ($file->file_paths as $extension => $f)
                        @continue (!$canDownloadFiles && $extension != "pdf")
                        @continue ($extension == "jpg")
                        <x-file-tile :id="$file->song_id" :file="$f" />
                    @endforeach
                    </div>
                </div>

                @if ($editable)
                <div class="flex right center middle nowrap">
                    @if ($highlightForClientId != "null" && !$file->exclusiveClients?->contains(App\Models\User::find($highlightForClientId)))
                    <x-shipyard::ui.button
                        :icon="model_icon('users')"
                        pop="Przypisz aktualnemu klientowi"
                        action="none"
                        onclick="assignFileToUser({{ $file->id }}, {{ $highlightForClientId }})"
                        class="tertiary"
                    />
                    @endif
                    <x-shipyard::ui.button
                        icon="pencil"
                        pop="Edytuj"
                        :action="route('files-edit', ['id' => $file->id])"
                        target="_blank"
                    />
                </div>
                @endif
            </x-shipyard::app.card>
            @endunless
            @endforeach
        </x-shipyard::app.section>
        @endforeach
    </x-shipyard::app.section>
    @empty
    @endforelse
</div>
