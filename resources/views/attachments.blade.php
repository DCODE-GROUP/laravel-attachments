<attachments
        :model="{{ $model->toJson() }}"
        model-class="{{ get_class($model) }}"
        upload-endpoint="{{ $uploadEndpoint ?? route(config('attachments.route_name_prefix').'.media.upload') }}"
        model-existing-endpoint="{{ $existingEndpoint ?? route(config('attachments.route_name_prefix').'.media.existing', ['modelClass' => get_class($model), 'modelId' => $model->id, 'category_id' => $preSelectedCategoryId ?? null]) }}"
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
        @isset($permitDelete)
            :permit-delete="{{ $permitDelete }}"
        @endisset
        {{-- Permit edit needs to set to true, roles are not set correctly on my local --}}
        @isset($permitEdit)
            :permit-edit="{{ $permitEdit }}"
        @endisset
        @isset($permitUpload)
            :permit-upload="{{ $permitUpload }}"
        @endisset
        @isset($showOnly)
            :show-only="true"
        @endisset
        @isset($preSelectedCategoryId)
            :pre-selected-category-id="{{ $preSelectedCategoryId }}"
        @endisset
        @isset($headingText)
            heading-text="{{ $headingText }}"
        @endisset
        @isset($showTitle)
            :show-title="{{ $showTitle }}"
        @endisset
        @isset($showAltText)
            :show-alt-text="{{ $showAltText }}"
        @endisset
        @isset($showFileSize)
            :show-file-size="{{ $showFileSize }}"
        @endisset
        @isset($showUploadedBy)
            :show-uploaded-by="{{ $showUploadedBy }}"
        @endisset
        @isset($showCreatedAt)
            :show-created-at="{{ $showCreatedAt }}"
        @endisset
        @isset($showDownload)
            :show-download="{{ $showDownload }}"
        @endisset
        >
    </attachments>
