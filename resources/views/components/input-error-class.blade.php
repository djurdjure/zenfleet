@props(['field'])
{{ isset($errors) && $errors->has($field) ? 'border-red-300' : '' }}
