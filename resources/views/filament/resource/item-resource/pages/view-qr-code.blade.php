<x-filament::page>
    <img class="border-white border-2" src="data:image/png;base64, {!! base64_encode(
        QrCode::format('png')->size(256)->generate('Item-' . $record->id),
    ) !!} ">
</x-filament::page>
