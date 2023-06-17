@props(['raw', 'editable' => null])

<div class="quest-links {{ $editable ? 'flex-down center' : '' }}">
    @foreach (explode(",", $raw) as $link)
    @if (filter_var($link, FILTER_VALIDATE_URL))
    <x-button action="{{ $link }}" target="_blank" icon="up-right-from-square" label="Link" :small="true" />
    @endif
    @endforeach
    @if($editable)
    <x-button action="#/" id="link-edit-trigger" icon="pencil" label="" :small="true" />
    <div id="link-edit-field">
        <x-input type="text" name="link" label="Linki" :value="$link" :small="true" />
    </div>
    <script>
    $(document).ready(() => {
        $("#link-edit-field").hide();
        $("#link-edit-trigger").click(() => {
            $("#link-edit-field").show();
        });

        $("#link").change((e) => {
            $.ajax({
                type: "post",
                url: "/song_link_change",
                data: {
                    id: "{{ $editable }}",
                    link: e.target.value,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    window.location.reload();
                }
            });
        });
    });
    </script>
    @endif
</div>
