@props([
    'primaryName',
    'secondaryName',
    'primaryToSecondaryMap',
    'secondaryToPrimaryMap',
    'filterSecondaryFn',
    'filterPrimaryFn'
])

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.cascadingMaps = window.cascadingMaps || {};
        window.cascadingMaps['{{ $primaryName }}_{{ $secondaryName }}'] = {
            primaryMap: @json($primaryToSecondaryMap ?? []),
            secondaryMap: @json($secondaryToPrimaryMap ?? [])
        };
        
        window['{{ $filterSecondaryFn }}'] = function(primaryId) {
            setTimeout(() => {
                const map = window.cascadingMaps['{{ $primaryName }}_{{ $secondaryName }}'].primaryMap;
                const secondaryInputs = document.querySelectorAll(`input[name="{{ $secondaryName }}"]`);
                
                secondaryInputs.forEach(input => {
                    const wrapper = input.closest('[x-data]');
                    if (!wrapper) return;
                    
                    const buttons = wrapper.querySelectorAll('button[data-val]');
                    let validIds = primaryId ? (map[primaryId] || []) : null;

                    buttons.forEach(btn => {
                        const val = btn.getAttribute('data-val');
                        if (val === "") {
                            btn.style.display = 'block';
                            return;
                        }
                        
                        if (validIds === null) {
                            btn.style.display = 'block';
                        } else {
                            if (validIds.some(id => String(id) === String(val))) {
                                btn.style.display = 'block';
                            } else {
                                btn.style.display = 'none';
                            }
                        }
                    });

                    // Reset selected value if it is no longer valid
                    const currentValue = input.value;
                    if (currentValue && validIds !== null && !validIds.some(id => String(id) === String(currentValue))) {
                        input.value = '';
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            }, 50);
        };

        window['{{ $filterPrimaryFn }}'] = function(secondaryId) {
            setTimeout(() => {
                const map = window.cascadingMaps['{{ $primaryName }}_{{ $secondaryName }}'].secondaryMap;
                const primaryInputs = document.querySelectorAll(`input[name="{{ $primaryName }}"]`);
                
                primaryInputs.forEach(input => {
                    const wrapper = input.closest('[x-data]');
                    if (!wrapper) return;
                    
                    const buttons = wrapper.querySelectorAll('button[data-val]');
                    let validIds = secondaryId ? (map[secondaryId] || []) : null;

                    buttons.forEach(btn => {
                        const val = btn.getAttribute('data-val');
                        if (val === "") {
                            btn.style.display = 'block';
                            return;
                        }
                        
                        if (validIds === null) {
                            btn.style.display = 'block';
                        } else {
                            if (validIds.some(id => String(id) === String(val))) {
                                btn.style.display = 'block';
                            } else {
                                btn.style.display = 'none';
                            }
                        }
                    });

                    // Reset selected value if it is no longer valid
                    const currentValue = input.value;
                    if (currentValue && validIds !== null && !validIds.some(id => String(id) === String(currentValue))) {
                        input.value = '';
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            }, 50);
        };

        // Initialize state on load
        setTimeout(() => {
            const initialPrimary = document.querySelector(`input[name="{{ $primaryName }}"]`);
            const initialSecondary = document.querySelector(`input[name="{{ $secondaryName }}"]`);
            if (initialPrimary && initialPrimary.value) window['{{ $filterSecondaryFn }}'](initialPrimary.value);
            if (initialSecondary && initialSecondary.value) window['{{ $filterPrimaryFn }}'](initialSecondary.value);
        }, 100);
    });
</script>
@endpush
