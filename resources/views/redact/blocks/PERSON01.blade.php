@if ($edit_mode || (isset($person_name) && $person_name != null && $person_name != ''))
    <div class="block block-PERSON01">
        <div class="person-photo-wrapper">
            @include('redact.atoms.image', [
                'edit_mode' => $edit_mode,
                'class' => 'person-photo',
                'html_data' => [
                    'id' => $person_image ?? '',
                    'width' => 256,
                    'height' => 256,
                ],
                'small_photo' => true,
            ])
        </div>
        <div class="person-details">
            @include('redact.atoms.textline', [
                'edit_mode' => $edit_mode,
                'class' => 'person-name',
                'html_data' => $person_name ?? '',
                'placeholder' => 'Имя сотрудника',
            ])
            @include('redact.atoms.textarea', [
                'edit_mode' => $edit_mode,
                'class' => 'person-bio',
                'html_data' => $text ?? '',
            ])
        </div>
    </div>
@endif
