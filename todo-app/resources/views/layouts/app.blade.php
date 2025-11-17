<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>To-Do — Website</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-color: #18181b;
      color: #f3f4f6;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      line-height: 1.5;
    }

    nav {
      background-color: #27272a;
      border-bottom: 1px solid #2563eb;
      padding: 1rem 0;
      box-shadow: 0 4px 6px -4px rgba(0,0,0,0.3);
    }

    nav .max-w-4xl {
      max-width: 56rem;
      margin: 0 auto;
      padding: 0 1.5rem;
    }

    h1 {
      color: #FFD600;
      font-size: 1.875rem;
      font-weight: 700;
      letter-spacing: -0.025em;
    }

    main {
      max-width: 56rem;
      margin: 0 auto;
      padding: 2rem 1.5rem;
    }

    .card {
      background-color: #171717;
      border: 1px solid #2563eb;
      border-radius: 0.75rem;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }

    .card h2 {
      color: #FFD600;
      font-size: 1.25rem;
      margin-bottom: 1rem;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    textarea {
      background-color: #27272a;
      color: #f3f4f6;
      border: 1px solid #3f3f46;
      border-radius: 0.5rem;
      padding: 0.625rem 0.75rem;
      font-size: 1rem;
      width: 100%;
      margin-bottom: 0.75rem;
      transition: border-color 0.2s;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    textarea:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    button, .btn {
      font-weight: 600;
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
      border: none;
      cursor: pointer;
      transition: all 0.2s;
      font-size: 1rem;
    }

    .btn-primary {
      background-color: #2563eb;
      color: #ffffff;
    }

    .btn-primary:hover {
      background-color: #f59e42;
    }

    .btn-secondary {
      background-color: #374151;
      color: #f3f4f6;
    }

    .btn-secondary:hover {
      background-color: #4b5563;
    }

    .btn-danger {
      background-color: #dc2626;
      color: #ffffff;
      padding: 0.375rem 0.75rem;
      font-size: 0.875rem;
    }

    .btn-danger:hover {
      background-color: #991b1b;
    }

    input[type="checkbox"] {
      accent-color: #2563eb;
      cursor: pointer;
      width: 1.25rem;
      height: 1.25rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: #f3f4f6;
    }

    .toast {
      padding: 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      font-weight: 500;
    }

    .toast-success {
      background-color: #059669;
      color: #ffffff;
      border-left: 4px solid #10b981;
    }

    .toast-error {
      background-color: #dc2626;
      color: #ffffff;
      border-left: 4px solid #ef4444;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }

    th {
      background-color: #27272a;
      color: #FFD600;
      padding: 0.75rem;
      text-align: left;
      border-bottom: 2px solid #2563eb;
    }

    td {
      padding: 0.75rem;
      border-bottom: 1px solid #3f3f46;
    }

    tr:hover {
      background-color: #27272a;
    }
  </style>
</head>
<body>
  <nav>
    <div class="max-w-4xl">
      <h1>To-Do — Website</h1>
    </div>
  </nav>

  <main>
    @yield('content')
  </main>

  <!-- SweetAlert2 CDN (used for confirmations/toasts) -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Minimal helper UI utilities
    function qs(sel){ return document.querySelector(sel); }
    function qsa(sel){ return Array.from(document.querySelectorAll(sel)); }

    const api = '/api/todos';
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function toastSuccess(msg){
      Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, icon: 'success', title: msg });
    }
    function toastError(msg){
      Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, icon: 'error', title: msg });
    }

    async function fetchTodos(){
      try{
        const res = await fetch(api);
        if(!res.ok) throw new Error(`API error: ${res.status}`);
        const data = await res.json();
        renderTodos(data);
      }catch(err){
        console.error('Fetch error:', err);
        toastError('Failed to load todos');
      }
    }

    function createTodoNode(item){
      const div = document.createElement('div');
      div.className = 'card';
      div.style.display = 'flex';
      div.style.justifyContent = 'space-between';
      div.style.alignItems = 'flex-start';
      div.dataset.id = item.id;
      div.innerHTML = `
        <div style="flex: 1;">
          <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
            <input type="checkbox" class="toggle" ${item.completed ? 'checked':''}/>
            <div>
              <div style="font-weight: 600; ${item.completed ? 'text-decoration: line-through; color: #9ca3af;':''}">${escapeHtml(item.title)}</div>
              <div style="font-size: 0.875rem; color: #9ca3af;">${escapeHtml(item.description || '')}</div>
              <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">${new Date(item.created_at).toLocaleString()}</div>
            </div>
          </label>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center; margin-left: 1rem;">
          <button class="edit btn-secondary" style="padding: 0.375rem 0.75rem; font-size: 0.875rem;">Edit</button>
          <button class="delete btn-danger">Delete</button>
        </div>
      `;
      div.querySelector('.delete').addEventListener('click', ()=> deleteTodo(item.id));
      div.querySelector('.edit').addEventListener('click', ()=> openEditModal(item));
      div.querySelector('.toggle').addEventListener('change', (e)=> toggleComplete(item.id, e.target.checked));
      return div;
    }

    function renderTodos(items){
      const container = qs('#todos');
      container.innerHTML = '';
      if(items.length === 0){
        container.innerHTML = '<div style="padding: 1rem; color: #9ca3af; text-align: center;">No tasks yet. Add one!</div>';
        return;
      }
      items.forEach(it => container.appendChild(createTodoNode(it)));
    }

    function escapeHtml(str){
      return String(str).replace(/[&<>\"']/g, function(m){ return ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;' })[m]; });
    }

    async function createTodo(title, description){
      try{
        const res = await fetch(api, {
          method: 'POST',
          headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf},
          body: JSON.stringify({title,description})
        });
        if(!res.ok) throw new Error('Create failed');
        toastSuccess('Task added');
        fetchTodos();
      }catch(err){ toastError('Failed to add task'); }
    }

    async function updateTodo(id, payload){
      try{
        const res = await fetch(`${api}/${id}`, {
          method: 'PATCH',
          headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf},
          body: JSON.stringify(payload)
        });
        if(!res.ok) throw new Error('Update failed');
        toastSuccess('Task updated');
        fetchTodos();
      }catch(err){ toastError('Failed to update'); }
    }

    async function deleteTodo(id){
      const {isConfirmed} = await Swal.fire({
        title: 'Delete task?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete'
      });
      if(!isConfirmed) return;
      try{
        const res = await fetch(`${api}/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN':csrf} });
        if(!res.ok) throw new Error('Delete failed');
        toastSuccess('Task deleted');
        fetchTodos();
      }catch(err){ toastError('Failed to delete'); }
    }

    async function toggleComplete(id, completed){
      await updateTodo(id, { completed });
    }

    function openEditModal(item){
      qs('#modal').classList.remove('hidden');
      qs('#modal').classList.add('flex');
      qs('#edit-id').value = item.id;
      qs('#edit-title').value = item.title;
      qs('#edit-description').value = item.description || '';
      qs('#edit-completed').checked = !!item.completed;
    }

    function closeEditModal(){
      qs('#modal').classList.add('hidden');
      qs('#modal').classList.remove('flex');
    }

    // init handlers
    document.addEventListener('DOMContentLoaded', ()=>{
      fetchTodos();
      qs('#create-form').addEventListener('submit', (e)=>{
        e.preventDefault();
        const title = qs('#title').value.trim();
        const description = qs('#description').value.trim();
        if(!title) return toastError('Title required');
        createTodo(title, description);
        qs('#create-form').reset();
      });
      qs('#cancel-edit').addEventListener('click', (e)=>{ e.preventDefault(); closeEditModal(); });
      qs('#edit-form').addEventListener('submit', (e)=>{
        e.preventDefault();
        const id = qs('#edit-id').value;
        const title = qs('#edit-title').value.trim();
        const description = qs('#edit-description').value.trim();
        const completed = qs('#edit-completed').checked;
        if(!title) return toastError('Title required');
        updateTodo(id, { title, description, completed });
        closeEditModal();
      });
    });
  </script>
</body>
</html>
