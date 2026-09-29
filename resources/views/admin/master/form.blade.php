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
                        <label id="field-{{ $fieldName }}-label" for="field-{{ $fieldName }}" class="mb-1.5 block text-xs font-semibold text-slate-600">{{ $field['label'] }}</label>
                        @if($field['type'] === 'select')
                            @if($field['multiple'] ?? false)
                                <div data-multi-select data-multi-select-name="{{ strtolower($field['label']) }}" class="relative">
                                    <details data-multi-select-dropdown>
                                        <summary id="field-{{ $fieldName }}" aria-labelledby="field-{{ $fieldName }}-label" class="form-input flex w-full cursor-pointer list-none items-center justify-between gap-3">
                                            <span data-multi-select-label aria-live="polite">Pilih {{ strtolower($field['label']) }}</span>
                                            <svg class="h-4 w-4 shrink-0 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                        </summary>
                                        <div class="absolute left-0 right-0 top-full z-30 mt-1 space-y-2 rounded-md border border-slate-200 bg-white p-2 shadow-lg">
                                            <input id="field-{{ $fieldName }}-search" type="search" data-multi-select-search="{{ $fieldName }}" class="form-input w-full" placeholder="Cari {{ strtolower($field['label']) }}..." aria-label="Cari {{ strtolower($field['label']) }}">
                                            <div class="max-h-56 overflow-y-auto" role="group" aria-labelledby="field-{{ $fieldName }}-label">
                                                @forelse($field['options'] as $option)
                                                    <label data-multi-select-option class="flex cursor-pointer items-center gap-3 rounded px-2 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                                        <input type="checkbox" name="{{ $fieldName }}[]" value="{{ $option['value'] }}" class="form-checkbox" @checked(in_array($option['value'], (array) $fieldValue))>
                                                        <span>{{ $option['label'] }}</span>
                                                    </label>
                                                @empty
                                                    <p class="px-2 py-3 text-xs text-slate-500">Belum ada pilihan {{ strtolower($field['label']) }}.</p>
                                                @endforelse
                                            </div>
                                            <p data-multi-select-count class="border-t border-slate-100 px-2 pt-2 text-xs text-slate-500" aria-live="polite"></p>
                                        </div>
                                    </details>
                                    <button type="button" data-multi-select-dismiss hidden class="fixed inset-0 z-20 cursor-default" aria-label="Tutup pilihan"></button>
                                </div>
                            @else
                            <select id="field-{{ $fieldName }}" name="{{ $fieldName }}" class="form-select w-full" @required($field['required'] ?? false)>
                                <option value="">Pilih {{ strtolower($field['label']) }}</option>
                                @foreach($field['options'] as $option)
                                    <option value="{{ $option['value'] }}" @selected((string) $fieldValue === (string) $option['value'])>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @endif
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

@section('scripts')
<script>
    document.querySelectorAll('[data-multi-select]').forEach((select) => {
        const dropdown = select.querySelector('[data-multi-select-dropdown]');
        const search = select.querySelector('[data-multi-select-search]');
        const options = [...select.querySelectorAll('[data-multi-select-option]')];
        const count = select.querySelector('[data-multi-select-count]');
        const label = select.querySelector('[data-multi-select-label]');
        const dismiss = select.querySelector('[data-multi-select-dismiss]');

        const updateOptions = () => {
            const query = search.value.trim().toLocaleLowerCase();
            let selectedCount = 0;

            options.forEach((option) => {
                const checkbox = option.querySelector('input');
                option.hidden = !option.textContent.toLocaleLowerCase().includes(query);
                selectedCount += checkbox.checked ? 1 : 0;
            });

            count.textContent = `${selectedCount} dipilih`;
            const name = select.dataset.multiSelectName;
            label.textContent = selectedCount === 0 ? `Pilih ${name}` : `${selectedCount} ${name} dipilih`;
        };

        search.addEventListener('input', updateOptions);
        select.addEventListener('change', updateOptions);
        dropdown.addEventListener('toggle', () => {
            dismiss.hidden = !dropdown.open;
            if (dropdown.open) {
                search.focus();
            }
        });
        dismiss.addEventListener('click', () => {
            dropdown.open = false;
        });
        updateOptions();
    });
</script>
@endsection