@extends('layouts.admin')

@section('title', ($record ? 'Edit ' : 'Tambah ') . $resourceTitle)
@section('page-title', ($record ? 'Edit ' : 'Tambah ') . $resourceTitle)

@section('content')
<div class="max-w-3xl space-y-5 animate-fade-in-up">
    <div class="card p-5">
        <form method="POST" action="{{ $formAction }}" class="space-y-5">
            @csrf
            @if($record)
                @method('PUT')
            @endif

            @foreach($fields as $field)
                @php
                    $fieldName = $field['name'];
                    $fieldValue = old($fieldName, $recordValues[$fieldName] ?? ($field['multiple'] ?? false ? [] : ''));
                @endphp
                <div>
                    @if($field['type'] === 'checkbox')
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="{{ $fieldName }}" value="1" class="form-checkbox" @checked(old($fieldName, $recordValues[$fieldName] ?? false))>
                            {{ $field['label'] }}
                        </label>
                    @else
                        <label for="field-{{ $fieldName }}" class="mb-1.5 block text-xs font-semibold text-slate-600">{{ $field['label'] }}</label>
                        @if($field['type'] === 'select')
                            <select id="field-{{ $fieldName }}" name="{{ $fieldName }}{{ ($field['multiple'] ?? false) ? '[]' : '' }}" class="form-select w-full" @if($field['multiple'] ?? false) multiple size="6" @endif @required($field['required'] ?? false)>
                                @unless($field['multiple'] ?? false)
                                    <option value="">Pilih {{ strtolower($field['label']) }}</option>
                                @endunless
                                @foreach($field['options'] as $option)
                                    <option value="{{ $option['value'] }}" @selected(($field['multiple'] ?? false) ? in_array($option['value'], (array) $fieldValue) : (string) $fieldValue === (string) $option['value'])>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        @elseif($field['type'] === 'textarea')
                            <textarea id="field-{{ $fieldName }}" name="{{ $fieldName }}" rows="4" class="form-input w-full" @required($field['required'] ?? false)>{{ $fieldValue }}</textarea>
                        @else
                            <input id="field-{{ $fieldName }}" type="{{ $field['type'] }}" name="{{ $fieldName }}" value="{{ $field['type'] === 'password' ? '' : $fieldValue }}" class="form-input w-full" @if(isset($field['step'])) step="{{ $field['step'] }}" @endif @required($field['required'] ?? false)>
                        @endif
                    @endif
                    @error($fieldName)
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            @if($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">Periksa kembali data yang ditandai.</div>
            @endif

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ url('/admin/'.$resource) }}" class="btn btn-secondary text-xs">Batal</a>
                <button type="submit" class="btn btn-primary text-xs">{{ $record ? 'Simpan Perubahan' : 'Simpan' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection