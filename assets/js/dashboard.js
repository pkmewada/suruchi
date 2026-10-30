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
let activePage = 'home';

function cleanupKey(page, section) {
  return page + ':' + section;
}

function labelFor(key) {
  return String(key).replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
}

function singularLabel(key) {
  return labelFor(key).replace(/ies$/, 'y').replace(/s$/, '');
}

const IMAGE_KEY = /^(image|image\d+|bg_image|hover_image|photo)$/i;
const HIDDEN_KEYS = ['bg_image'];
const UPLOAD_ICON = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 16V4m0 0-4.5 4.5M12 4l4.5 4.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>';

function previewSrc(path) {
  if (!path) return '';
  return /^(https?:)?\/\//i.test(path) || path.startsWith('/') || path.startsWith('data:') ? path : '../' + path;
}

function blankCopy(value) {
  if (Array.isArray(value)) return value.length ? [blankCopy(value[0])] : [];
  if (value !== null && typeof value === 'object') {
    return Object.fromEntries(Object.entries(value).map(([childKey, childValue]) => [childKey, blankCopy(childValue)]));
  }
  return '';
}

function renderImageField(key, value, update) {
  const fieldPage = current.page;
  const fieldSection = current.section;
  let path = String(value ?? '');

  const field = make('div', {className: 'field image-field'});
  field.append(make('label', {textContent: labelFor(key)}));
  const picker = make('div', {className: 'image-picker'});
  const thumb = make('button', {type: 'button', className: 'image-thumb', title: 'Click to upload a new photo'});
  const img = make('img', {alt: ''});
  const empty = make('span', {className: 'image-empty', textContent: 'No photo'});
  const badge = make('span', {className: 'image-badge', innerHTML: UPLOAD_ICON});
  thumb.append(img, empty, badge);

  const meta = make('div', {className: 'image-meta'});
  const uploadButton = make('button', {type: 'button', className: 'upload-btn', innerHTML: UPLOAD_ICON + '<span>Upload photo</span>'});
  const status = make('span', {className: 'image-status'});
  meta.append(uploadButton, status);

  const fileInput = make('input', {type: 'file', accept: 'image/jpeg,image/png,image/webp,image/gif', hidden: true});
  picker.append(thumb, meta, fileInput);
  field.append(picker);

  const paint = () => {
    img.hidden = !path;
    empty.hidden = Boolean(path);
    if (path) img.src = previewSrc(path);
    status.className = 'image-status';
    status.textContent = path ? path.split('/').pop() : 'Click the icon to add a photo';
  };
  img.addEventListener('error', () => { img.hidden = true; empty.hidden = false; });

  const openPicker = () => fileInput.click();
  thumb.addEventListener('click', openPicker);
  uploadButton.addEventListener('click', openPicker);

  fileInput.addEventListener('change', async () => {
    const file = fileInput.files && fileInput.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
      status.className = 'image-status err';
      status.textContent = 'Photo is too big (max 5 MB).';
      fileInput.value = '';
      return;
    }
    picker.classList.add('busy');
    status.className = 'image-status';
    status.textContent = 'Uploading...';
    try {
      const formData = new FormData();
      formData.append('image', file);
      const response = await fetch('../api/home.php?action=upload', {
        method: 'POST',
        headers: {'X-CSRF-Token': CSRF},
        body: formData
      });
      const result = await response.json();
      if (!response.ok || !result.ok) throw new Error(result.error || 'Upload failed');
      if (/^assets\/images\/uploads\/[A-Za-z0-9][A-Za-z0-9._-]*$/.test(path)) {
        const sectionKey = cleanupKey(fieldPage, fieldSection);
        const paths = pendingUploadCleanup.get(sectionKey) || [];
        paths.push(path);
        pendingUploadCleanup.set(sectionKey, paths);
      }
      path = result.path;
      update(path);
      paint();
      status.className = 'image-status ok';
      status.textContent = '✓ Photo added. Now press "Save changes".';
    } catch (error) {
      status.className = 'image-status err';
      status.textContent = 'Error: ' + error.message;
    } finally {
      picker.classList.remove('busy');
      fileInput.value = '';
    }
  });

  paint();
  return field;
}

function renderValue(key, value, update, allowListChanges, parentKey = '') {
  if (Array.isArray(value)) {
    const itemName = singularLabel(key === 'items' && parentKey ? parentKey : key);
    const box = make('div', {className: 'field'});
    box.append(make('label', {textContent: labelFor(key === 'items' && parentKey ? parentKey : key)}));
    const holder = make('div', {className: 'list-grid'});
    const itemPrototype = value.length ? blankCopy(value[0]) : '';
    const draw = focusIndex => {
      holder.replaceChildren();
      value.forEach((item, index) => {
        const singleField = item === null || typeof item !== 'object' || Object.keys(item).length === 1;
        const card = make('div', {className: 'list-item' + (singleField ? ' single-field' : '')});
        const heading = make('div', {className: 'item-head'});
        heading.append(make('span', {className: 'item-number', textContent: itemName + ' ' + (index + 1)}));
        const actions = make('div', {className: 'item-actions'});
        const action = (text, className, title, handler) => {
          const button = make('button', {type: 'button', textContent: text, title, className: 'btn sm ' + className});
          button.setAttribute('aria-label', title);
          button.addEventListener('click', handler);
          actions.append(button);
        };
        if (allowListChanges && index > 0) action('↑', 'light', 'Move up', () => { [value[index - 1], value[index]] = [value[index], value[index - 1]]; draw(); });
        if (allowListChanges && index < value.length - 1) action('↓', 'light', 'Move down', () => { [value[index + 1], value[index]] = [value[index], value[index + 1]]; draw(); });
        if (allowListChanges && value.length > 1) action('✕ Remove', 'danger', 'Remove ' + itemName.toLowerCase(), () => {
          if (!confirm('Remove ' + itemName + ' ' + (index + 1) + '?')) return;
          value.splice(index, 1);
          draw();
        });
        heading.append(actions);
        card.append(heading);
        if (item !== null && typeof item === 'object') {
          Object.entries(item).forEach(([childKey, childValue]) => {
            if (HIDDEN_KEYS.includes(childKey)) return;
            card.append(renderValue(childKey, childValue, next => { item[childKey] = next; }, allowListChanges, key));
          });
        } else {
          const input = make('input', {type: 'text', value: item ?? ''});
          input.addEventListener('input', () => { value[index] = input.value; update(value); });
          card.append(input);
        }
        holder.append(card);
        if (index === focusIndex) {
          requestAnimationFrame(() => {
            card.scrollIntoView({behavior: 'smooth', block: 'center'});
            const first = card.querySelector('input[type=text], textarea');
            if (first) first.focus({preventScroll: true});
          });
        }
      });
      if (allowListChanges) {
        const add = make('button', {type: 'button', className: 'add-item', innerHTML: '<span aria-hidden="true">+</span>'});
        add.append(make('strong', {textContent: 'Add new ' + itemName.toLowerCase()}));
        add.addEventListener('click', () => {
          value.push(JSON.parse(JSON.stringify(itemPrototype)));
          update(value);
          draw(value.length - 1);
        });
        holder.append(add);
      }
    };
    box.append(holder);
    draw();
    return box;
  }

  if (value !== null && typeof value === 'object') {
    const group = make('fieldset', {className: 'group'});
    group.append(make('legend', {textContent: labelFor(key)}));
    const fields = make('div', {className: 'group-fields'});
    Object.entries(value).forEach(([childKey, childValue]) => {
      if (HIDDEN_KEYS.includes(childKey)) return;
      fields.append(renderValue(childKey, childValue, next => { value[childKey] = next; update(value); }, allowListChanges, key));
    });
    group.append(fields);
    return group;
  }

  if (IMAGE_KEY.test(key)) {
    return renderImageField(key, value, update);
  }

  const field = make('div', {className: 'field'});
  field.append(make('label', {textContent: labelFor(key)}));
  const text = String(value ?? '');
  const input = text.length > 120 || text.includes('\n')
    ? make('textarea', {value: text})
    : make('input', {type: 'text', value: text});
  input.addEventListener('input', () => update(input.value));
  field.append(input);
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
  const allowListChanges = page === 'home' || page === 'product';
  card.append(renderValue(section, DATA[page][section], updated => { DATA[page][section] = updated; }, allowListChanges));
  form.append(card);
  document.querySelectorAll('.section-link').forEach(button => {
    button.classList.toggle('active', button.dataset.page === page && button.dataset.section === section);
    button.setAttribute('aria-current', button.dataset.page === page && button.dataset.section === section ? 'page' : 'false');
  });
}

function renderSections(page, selectedSection) {
  activePage = page;
  const nav = $('#section-nav');
  const filter = $('#section-filter');
  const query = filter.value.trim().toLowerCase();
  const sections = Object.keys(DATA[page]);
  nav.replaceChildren();

  sections.forEach(section => {
    const label = labelFor(section);
    if (query && !label.toLowerCase().includes(query)) return;
    const button = make('button', {type: 'button', textContent: label, className: 'section-link'});
    button.dataset.page = page;
    button.dataset.section = section;
    button.addEventListener('click', () => show(page, section));
    button.classList.toggle('active', section === selectedSection);
    button.setAttribute('aria-current', section === selectedSection ? 'page' : 'false');
    nav.append(button);
  });

  $('#empty-sections').hidden = nav.childElementCount > 0;
  if (!nav.childElementCount && query) {
    $('#form').replaceChildren();
    $('#title').textContent = PAGE_LABELS[page];
  } else if (selectedSection && !query) {
    show(page, selectedSection);
  }
}

Object.entries(PAGE_LABELS).forEach(([page, label]) => {
  const button = make('button', {type: 'button', className: 'page-link'});
  const icons = {home: '⌂', about: '✳', '4fs': '◇', 'design-research': '⌕', product: '▧', contact: '✉'};
  button.append(make('span', {className: 'page-icon', textContent: icons[page] || '•', 'aria-hidden': 'true'}));
  button.append(make('span', {textContent: label}));
  button.dataset.page = page;
  button.addEventListener('click', () => {
    $('#section-filter').value = '';
    const section = Object.keys(DATA[page])[0];
    renderSections(page, section);
    document.querySelectorAll('.page-link').forEach(item => {
      item.classList.toggle('active', item.dataset.page === page);
      item.setAttribute('aria-current', item.dataset.page === page ? 'page' : 'false');
    });
    show(page, section);
  });
  $('#page-nav').append(button);
});

$('#section-filter').addEventListener('input', () => renderSections(activePage, current.section));

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

renderSections(current.page, current.section);
document.querySelectorAll('.page-link').forEach(button => {
  button.classList.toggle('active', button.dataset.page === current.page);
  button.setAttribute('aria-current', button.dataset.page === current.page ? 'page' : 'false');
});
show(current.page, current.section);