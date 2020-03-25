<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Arabic: The :attribute must be accepted.',
    'active_url' => 'Arabic: The :attribute is not a valid URL.',
    'after' => 'Arabic: The :attribute must be a date after :date.',
    'after_or_equal' => 'Arabic: The :attribute must be a date after or equal to :date.',
    'alpha' => 'Arabic: The :attribute may only contain letters.',
    'alpha_dash' => 'Arabic: The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'Arabic: The :attribute may only contain letters and numbers.',
    'array' => 'Arabic: The :attribute must be an array.',
    'before' => 'Arabic: The :attribute must be a date before :date.',
    'before_or_equal' => 'Arabic: The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'Arabic: The :attribute must be between :min and :max.',
        'file' => 'Arabic: The :attribute must be between :min and :max kilobytes.',
        'string' => 'Arabic: The :attribute must be between :min and :max characters.',
        'array' => 'Arabic: The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'Arabic: The :attribute field must be true or false.',
    'confirmed' => 'Arabic: The :attribute confirmation does not match.',
    'date' => 'Arabic: The :attribute is not a valid date.',
    'date_equals' => 'Arabic: The :attribute must be a date equal to :date.',
    'date_format' => 'Arabic: The :attribute does not match the format :format.',
    'different' => 'Arabic: The :attribute and :other must be different.',
    'digits' => 'Arabic: The :attribute must be :digits digits.',
    'digits_between' => 'Arabic: The :attribute must be between :min and :max digits.',
    'dimensions' => 'Arabic: The :attribute has invalid image dimensions.',
    'distinct' => 'Arabic: The :attribute field has a duplicate value.',
    'email' => 'Arabic: The :attribute must be a valid email address.',
    'ends_with' => 'Arabic: The :attribute must end with one of the following: :values',
    'exists' => 'Arabic: The selected :attribute is invalid.',
    'file' => 'Arabic: The :attribute must be a file.',
    'filled' => 'Arabic: The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'Arabic: The :attribute must be greater than :value.',
        'file' => 'Arabic: The :attribute must be greater than :value kilobytes.',
        'string' => 'Arabic: The :attribute must be greater than :value characters.',
        'array' => 'Arabic: The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'Arabic: The :attribute must be greater than or equal :value.',
        'file' => 'Arabic: The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'Arabic: The :attribute must be greater than or equal :value characters.',
        'array' => 'Arabic: The :attribute must have :value items or more.',
    ],
    'image' => 'Arabic: The :attribute must be an image.',
    'in' => 'Arabic: The selected :attribute is invalid.',
    'in_array' => 'Arabic: The :attribute field does not exist in :other.',
    'integer' => 'Arabic: The :attribute must be an integer.',
    'ip' => 'Arabic: The :attribute must be a valid IP address.',
    'ipv4' => 'Arabic: The :attribute must be a valid IPv4 address.',
    'ipv6' => 'Arabic: The :attribute must be a valid IPv6 address.',
    'json' => 'Arabic: The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'Arabic: The :attribute must be less than :value.',
        'file' => 'Arabic: The :attribute must be less than :value kilobytes.',
        'string' => 'Arabic: The :attribute must be less than :value characters.',
        'array' => 'Arabic: The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'Arabic: The :attribute must be less than or equal :value.',
        'file' => 'Arabic: The :attribute must be less than or equal :value kilobytes.',
        'string' => 'Arabic: The :attribute must be less than or equal :value characters.',
        'array' => 'Arabic: The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'Arabic: The :attribute may not be greater than :max.',
        'file' => 'Arabic: The :attribute may not be greater than :max kilobytes.',
        'string' => 'Arabic: The :attribute may not be greater than :max characters.',
        'array' => 'Arabic: The :attribute may not have more than :max items.',
    ],
    'mimes' => 'Arabic: The :attribute must be a file of type: :values.',
    'mimetypes' => 'Arabic: The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'Arabic: The :attribute must be at least :min.',
        'file' => 'Arabic: The :attribute must be at least :min kilobytes.',
        'string' => 'Arabic: The :attribute must be at least :min characters.',
        'array' => 'Arabic: The :attribute must have at least :min items.',
    ],
    'not_in' => 'Arabic: The selected :attribute is invalid.',
    'not_regex' => 'Arabic: The :attribute format is invalid.',
    'numeric' => 'Arabic: The :attribute must be a number.',
    'present' => 'Arabic: The :attribute field must be present.',
    'regex' => 'Arabic: The :attribute format is invalid.',
    'required' => 'Arabic: The :attribute field is required.',
    'required_if' => 'Arabic: The :attribute field is required when :other is :value.',
    'required_unless' => 'Arabic: The :attribute field is required unless :other is in :values.',
    'required_with' => 'Arabic: The :attribute field is required when :values is present.',
    'required_with_all' => 'Arabic: The :attribute field is required when :values are present.',
    'required_without' => 'Arabic: The :attribute field is required when :values is not present.',
    'required_without_all' => 'Arabic: The :attribute field is required when none of :values are present.',
    'same' => 'Arabic: The :attribute and :other must match.',
    'size' => [
        'numeric' => 'Arabic: The :attribute must be :size.',
        'file' => 'Arabic: The :attribute must be :size kilobytes.',
        'string' => 'Arabic: The :attribute must be :size characters.',
        'array' => 'Arabic: The :attribute must contain :size items.',
    ],
    'starts_with' => 'Arabic: The :attribute must start with one of the following: :values',
    'string' => 'Arabic: The :attribute must be a string.',
    'timezone' => 'Arabic: The :attribute must be a valid zone.',
    'unique' => 'Arabic: The :attribute has already been taken.',
    'uploaded' => 'Arabic: The :attribute failed to upload.',
    'url' => 'Arabic: The :attribute format is invalid.',
    'uuid' => 'Arabic: The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'Arabic: custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
