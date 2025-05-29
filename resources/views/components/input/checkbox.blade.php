{{-- 
Contoh penggunaan:
parent:
<x-input.checkbox class="form-check-input" id="checkbox-all" 
    :checked="count($columns) == count($selectedColumns)"
    :parent="true" />

child:
<x-input.checkbox class="form-check-input" id="checkbox-{{ $column }}"
    name="col[]" value="{{ $column }}" :checked="in_array($column, $selectedColumns)"
    parentId="checkbox-all" />
--}}

@props([
    'id' => '',
    'class' => '',
    'checked' => false,
    'parent' => false,
    'parentId' => '',
])

<input type="checkbox" {{ $attributes->merge(['class' => $class . ($parent ? ' parent-checkbox' : ' child-checkbox')]) }}
    id="{{ $id }}" {{ $checked ? 'checked' : '' }}
    @if (!$parent && $parentId) data-parent-id="{{ $parentId }}" @endif>

@pushOnce('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Handle parent checkbox toggle
            document.querySelectorAll('.parent-checkbox').forEach(parentCheckbox => {
                parentCheckbox.addEventListener('change', function() {
                    const parentId = this.id;
                    const isChecked = this.checked;

                    // Update all child checkboxes linked to this parent
                    document.querySelectorAll(`.child-checkbox[data-parent-id="${parentId}"]`)
                        .forEach(childCheckbox => {
                            childCheckbox.checked = isChecked;
                        });
                });
            });

            // Handle child checkbox toggle to update parent status
            document.querySelectorAll('.child-checkbox').forEach(childCheckbox => {
                childCheckbox.addEventListener('change', function() {
                    const parentId = this.dataset.parentId;

                    const children = document.querySelectorAll(
                        `.child-checkbox[data-parent-id="${parentId}"]`);
                    const allChecked = Array.from(children).every(child => child.checked);

                    const parentCheckbox = document.getElementById(parentId);
                    if (parentCheckbox) {
                        parentCheckbox.checked = allChecked;
                    }
                });
            });
        });
    </script>
@endPushOnce
