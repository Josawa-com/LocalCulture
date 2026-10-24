// Dropdown provinsi dinamis di halaman register
const islandSelect = document.getElementById('island_id');
const provinceSelect = document.getElementById('province_id');

if (islandSelect && provinceSelect) {
    const allOptions = [...provinceSelect.options];

    const filterProvinces = () => {
        const islandId = islandSelect.value;

        allOptions.forEach((option) => {
            if (!option.value) {
                return;
            }
            option.hidden = option.dataset.parent !== islandId;
        });

        provinceSelect.value = '';
    };

    islandSelect.addEventListener('change', filterProvinces);

    // Restore pilihan setelah validation error
    const oldIsland = islandSelect.getAttribute('data-old');
    if (oldIsland) {
        islandSelect.value = oldIsland;
        filterProvinces();
        const oldProvince = provinceSelect.getAttribute('data-old');
        if (oldProvince) {
            provinceSelect.value = oldProvince;
        }
    }
}

