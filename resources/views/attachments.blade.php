<attachments
        :model="{{ $model->toJson() }}"
        model-class="{{ get_class($model) }}"
        upload-endpoint="{{ $uploadEndpoint ?? route(config('attachments.route_name_prefix').'.media.upload') }}"
        model-existing-endpoint="{{ $existingEndpoint ?? route(config('attachments.route_name_prefix').'.media.existing', ['modelClass' => get_class($model), 'modelId' => $model->id]) }}"
        category-options-endpoint="{{ $categoryOptionEndpoint ?? route(config('attachments.route_name_prefix').'.categories.options', [
        'type' => \Dcodegroup\LaravelAttachments\Models\MediaCategory::TYPE_ATTACHMENT
    ]) }}"
        categories-endpoint="{{ $categoryEndpoint ?? route(config('attachments.route_name_prefix').'.categories', [
        'type' => \Dcodegroup\LaravelAttachments\Models\MediaCategory::TYPE_ATTACHMENT
    ]) }}"
        @isset($displayStyle)
            display-style="{{ $displayStyle }}"
        @endisset
        @isset($selectExisting)
            add-existing-endpoint="{{ route(config('attachments.route_name_prefix').'.media.existing', ['modelClass' => get_class($selectExisting), 'modelId' => $selectExisting->id]) }}"
        @endisset
        @hasanyrole('super admin|admin')
:permit-delete="true"
{{-- Permit edit needs to set to true, roles are not set correctly on my local --}}
:permit-edit="true"
@else
    :permit-delete="false"
    {{-- Permit edit needs to set to false, roles are not set correctly on my local --}}
    :permit-edit="false"
    @endhasanyrole
    @isset($showOnly)
        :show-only="true"
    @endisset
    >
    </attachments>
