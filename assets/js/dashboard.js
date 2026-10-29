const config = JSON.parse(document.getElementById('dashboard-data').textContent);
const CSRF = config.csrf;
const DATA = config.data;
const PAGE_LABELS = config.pageLabels;
const PAGE_API = {
  home: '../api/home.php',
  about: '../api/about.php',
  '4fs': '../api/4fs.php',
  'design-research': '../api/design-research.php',
  product: '../api/product.php',
  contact: '../api/contact.php'
};
const $ = selector => document.querySelector(selector);
const make = (tag, properties = {}) => Object.assign(document.createElement(tag), properties);
let current = {page: 'home', section: Object.keys(DATA.home)[0]};
const pendingUploadCleanup = new Map();

function cleanupKey(page, section) {
  return page + ':' + section;
}

function labelFor(key) {
  return String(key).replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
}

function renderValue(key, value, update, allowListChanges) {
  if (Array.isArray(value)) {
    const box = make('div', {className: 'field'});
    box.append(make('label', {textContent: labelFor(key)}));
    const holder = make('div');
    const itemPrototype = value.length ? JSON.parse(JSON.stringify(value[0])) : null;
    const draw = () => {
      holder.replaceChildren();
      value.forEach((item, index) => {
        const card = make('div', {className: 'list-item'});
        const heading = make('div', {className: 'item-head'});
        heading.append(make('span', {textContent: labelFor(key) + ' ' + (index + 1)}));
        const actions = make('div', {className: 'item-actions'});
        const action = (text, className, handler) => {
          const button = make('button', {type: 'button', textContent: text, className: 'btn sm ' + className});
          button.addEventListener('click', handler);
          actions.append(button);
        };
        if (allowListChanges && index > 0) action('↑', 'light', () => { [value[index - 1], value[index]] = [value[index], value[index - 1]]; draw(); });
        if (allowListChanges && index < value.length - 1) action('↓', 'light', () => { [value[index + 1], value[index]] = [value[index], value[index + 1]]; draw(); });
        if (allowListChanges) action('Remove', 'danger', () => { value.splice(index, 1); draw(); });
        heading.append(actions);
        card.append(heading);
        if (item !== null && typeof item === 'object') {
          Object.entries(item).forEach(([childKey, childValue]) => {
            card.append(renderValue(childKey, childValue, next => { item[childKey] = next; }, allowListChanges));
          });
        } else {
          const input = make('input', {type: 'text', value: item ?? ''});
          input.addEventListener('input', () => { value[index] = input.value; update(value); });
          card.append(input);
        }
        holder.append(card);
      });
    };
    box.append(holder);
    if (allowListChanges) {
      const add = make('button', {type: 'button', textContent: 'Add ' + labelFor(key).replace(/s$/, ''), className: 'btn light'});
      add.addEventListener('click', () => {
        value.push(itemPrototype !== null && typeof itemPrototype === 'object' ? JSON.parse(JSON.stringify(itemPrototype)) : '');
        draw();
      });
      box.append(add);
    }
    draw();
    return box;
  }

  if (value !== null && typeof value === 'object') {
    const group = make('fieldset', {className: 'group'});
    group.append(make('legend', {textContent: labelFor(key)}));
    Object.entries(value).forEach(([childKey, childValue]) => {
      group.append(renderValue(childKey, childValue, next => { value[childKey] = next; update(value); }, allowListChanges));
    });
    return group;
  }

  const field = make('div', {className: 'field'});
  field.append(make('label', {textContent: labelFor(key)}));
  const text = String(value ?? '');
  const input = text.length > 120 || text.includes('\n')
    ? make('textarea', {value: text})
    : make('input', {type: 'text', value: text});
  input.addEventListener('input', () => update(input.value));
  field.append(input);
  if (/^(image|image\d+|bg_image|hover_image|photo)$/i.test(key)) {
    const fieldPage = current.page;
    const fieldSection = current.section;
    const upload = make('input', {type: 'file', accept: 'image/jpeg,image/png,image/webp,image/gif'});
    const uploadButton = make('button', {type: 'button', textContent: 'Upload image', className: 'btn light sm'});
    const uploadStatus = make('span', {className: 'hint'});
    uploadButton.addEventListener('click', async () => {
      if (!upload.files || !upload.files[0]) {
        uploadStatus.textContent = 'Choose an image first.';
        return;
      }
      uploadStatus.textContent = 'Uploading...';
      try {
        const formData = new FormData();
        formData.append('image', upload.files[0]);
        const response = await fetch('../api/home.php?action=upload', {
          method: 'POST',
          headers: {'X-CSRF-Token': CSRF},
          body: formData
        });
        const result = await response.json();
        if (!response.ok || !result.ok) throw new Error(result.error || 'Upload failed');
        const replacedPath = input.value;
        if (/^assets\/images\/uploads\/[A-Za-z0-9][A-Za-z0-9._-]*$/.test(replacedPath)) {
          const sectionKey = cleanupKey(fieldPage, fieldSection);
          const paths = pendingUploadCleanup.get(sectionKey) || [];
          paths.push(replacedPath);
          pendingUploadCleanup.set(sectionKey, paths);
        }
        input.value = result.path;
        update(result.path);
        uploadStatus.textContent = 'Uploaded. Save changes to keep it and remove the previous upload.';
      } catch (error) {
        uploadStatus.textContent = 'Error: ' + error.message;
      }
    });
    field.append(upload, uploadButton, uploadStatus);
  }
  return field;
}

function show(page, section) {
  current = {page, section};
  $('#title').textContent = PAGE_LABELS[page] + ' / ' + labelFor(section);
  $('#msg').textContent = '';
  $('#msg').className = '';
  const form = $('#form');
  form.replaceChildren();
  const card = make('div', {className: 'card'});
  card.append(renderValue(section, DATA[page][section], updated => { DATA[page][section] = updated; }, page === 'home'));
  form.append(card);
  document.querySelectorAll('.nav-section').forEach(button => {
    button.classList.toggle('active', button.dataset.page === page && button.dataset.section === section);
  });
}

Object.entries(PAGE_LABELS).forEach(([page, label]) => {
  const group = make('div', {className: 'page-nav'});
  group.append(make('div', {className: 'page-label', textContent: label}));
  Object.keys(DATA[page]).forEach(section => {
    const button = make('button', {type: 'button', textContent: labelFor(section), className: 'nav-section'});
    button.dataset.page = page;
    button.dataset.section = section;
    button.addEventListener('click', () => show(page, section));
    group.append(button);
  });
  $('#nav').append(group);
});

$('#saveBtn').addEventListener('click', async () => {
  const message = $('#msg');
  message.className = '';
  message.textContent = 'Saving...';
  try {
    const endpoint = PAGE_API[current.page] + (current.page === 'home' ? '?action=save' : '');
    const key = cleanupKey(current.page, current.section);
    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-Token': CSRF},
      body: JSON.stringify({
        section: current.section,
        data: DATA[current.page][current.section],
        cleanup: pendingUploadCleanup.get(key) || []
      })
    });
    const result = await response.json();
    if (!response.ok || !result.ok) throw new Error(result.error || 'Unable to save changes');
    pendingUploadCleanup.delete(key);
    message.className = 'ok';
    message.textContent = result.cleanupWarnings && result.cleanupWarnings.length
      ? 'Saved, but an old uploaded image could not be removed.'
      : 'Saved. Website par update ho gaya.';
  } catch (error) {
    message.className = 'err';
    message.textContent = 'Error: ' + error.message;
  }
});

show(current.page, current.section);