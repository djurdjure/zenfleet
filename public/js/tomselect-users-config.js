// Configuration TomSelect pour la sélection d'utilisateurs
window.initUserTomSelect = function(element, preselectedIds = []) {
    if (!element) return;
    
    return new TomSelect(element, {
        plugins: ['checkbox_options', 'remove_button'],
        create: false,
        placeholder: 'Rechercher et sélectionner des utilisateurs...',
        searchField: ['text'],
        valueField: 'value',
        labelField: 'text',
        maxItems: null,
        items: preselectedIds.map(String),
        render: {
            option: function(data, escape) {
                const option = data.$option || data;
                const name = option.dataset?.name || data.text.split(' (')[0];
                const email = option.dataset?.email || data.text.match(/\(([^)]+)\)/)?.[1] || '';
                
                return `<div class="flex items-center space-x-3 p-2">
                    <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" ${data.selected ? 'checked' : ''}>
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-900">${escape(name)}</span>
                        <span class="text-sm text-gray-500">${escape(email)}</span>
                    </div>
                </div>`;
            },
            item: function(data, escape) {
                const option = data.$option || data;
                const name = option.dataset?.name || data.text.split(' (')[0];
                
                return `<div class="flex items-center space-x-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                        ${escape(name)}
                    </span>
                </div>`;
            }
        }
    });
};

