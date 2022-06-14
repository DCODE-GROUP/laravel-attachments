<attachments
    :model="{{ $model->toJson() }}"
    model-class="{{ get_class($model) }}"
    upload-endpoint="{{ $uploadEndpoint ?? route('frontend.admin.media.upload') }}"
    model-existing-endpoint="{{ $existingEndpoint ?? route('frontend.admin.media.existing', ['modelClass' => get_class($model), 'modelId' => $model->id]) }}"
    category-options-endpoint="{{ $categoryOptionEndpoint ?? route('frontend.admin.categories.options', [
        'type' => \Dcodegroup\LaravelAttachments\Models\Category::TYPE_ATTACHMENT
    ]) }}"
    categories-endpoint="{{ $categoryEndpoint ?? route('frontend.admin.categories', [
        'type' => \Dcodegroup\LaravelAttachments\Models\Category::TYPE_ATTACHMENT
    ]) }}"
    @isset($displayStyle)
    display-style="{{ $displayStyle }}"
    @endisset
    @isset($selectExisting)
    add-existing-endpoint="{{ route('frontend.admin.media.existing', ['modelClass' => get_class($selectExisting), 'modelId' => $selectExisting->id]) }}"
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
