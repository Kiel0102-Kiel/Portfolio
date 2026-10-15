document.querySelectorAll('[data-repeater]').forEach((repeater) => {
    const list = repeater.querySelector('[data-repeat-list]');
    const template = repeater.querySelector('[data-repeat-template]');
    const addButton = repeater.querySelector('[data-add-row]');

    if (!list || !template || !addButton) return;

    const nextIndex = () => {
        const indexes = [...list.querySelectorAll('[name]')]
            .map((input) => input.name.match(/\[(\d+)\]/)?.[1])
            .filter(Boolean)
            .map(Number);

        return indexes.length ? Math.max(...indexes) + 1 : 0;
    };

    addButton.addEventListener('click', () => {
        const row = template.content.firstElementChild.cloneNode(true);
        row.innerHTML = row.innerHTML.replaceAll('__INDEX__', String(nextIndex()));
        list.append(row);
        row.querySelector('input, textarea')?.focus();
    });

    list.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-remove-row]');
        if (removeButton) removeButton.closest('[data-repeat-row]')?.remove();
    });
});

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
});

const templateName = document.querySelector('#selected-template-name');
document.querySelectorAll('input[name="template"]').forEach((input) => {
    input.addEventListener('change', () => {
        document.querySelectorAll('.template-choice').forEach((choice) => choice.classList.remove('is-selected'));
        input.closest('.template-choice')?.classList.add('is-selected');
        if (templateName) templateName.textContent = input.value.charAt(0).toUpperCase() + input.value.slice(1);
    });

    if (input.checked) input.closest('.template-choice')?.classList.add('is-selected');
});