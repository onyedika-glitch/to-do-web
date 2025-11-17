import './bootstrap';

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
    const data = await res.json();
    renderTodos(data);
  }catch(err){ toastError('Failed to load todos'); }
}

function createTodoNode(item){
  const div = document.createElement('div');
  div.className = 'p-4 bg-white border rounded flex items-start justify-between';
  div.dataset.id = item.id;
  div.innerHTML = `
    <div>
      <label class="flex items-center gap-3">
        <input type="checkbox" class="toggle" ${item.completed ? 'checked':''}/>
        <div>
          <div class="font-medium ${item.completed ? 'line-through text-gray-400':''}">${escapeHtml(item.title)}</div>
          <div class="text-sm text-gray-500">${escapeHtml(item.description || '')}</div>
          <div class="text-xs text-gray-400 mt-1">${new Date(item.created_at).toLocaleString()}</div>
        </div>
      </label>
    </div>
    <div class="flex gap-2 items-center ml-4">
      <button class="edit btn-secondary">Edit</button>
      <button class="delete btn-danger">Delete</button>
    </div>
  `;
  # handlers
  div.querySelector('.delete').addEventListener('click', ()=> deleteTodo(item.id));
  div.querySelector('.edit').addEventListener('click', ()=> openEditModal(item));
  div.querySelector('.toggle').addEventListener('change', (e)=> toggleComplete(item.id, e.target.checked));
  return div;
}

function renderTodos(items){
  const container = qs('#todos');
  container.innerHTML = '';
  if(items.length === 0){
    container.innerHTML = '<div class="p-4 text-gray-500">No tasks yet. Add one!</div>';
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
  # create
  qs('#create-form').addEventListener('submit', (e)=>{
    e.preventDefault();
    const title = qs('#title').value.trim();
    const description = qs('#description').value.trim();
    if(!title) return toastError('Title required');
    createTodo(title, description);
    qs('#create-form').reset();
  });
  // edit cancel
  qs('#cancel-edit').addEventListener('click', (e)=>{ e.preventDefault(); closeEditModal(); });
  // edit submit
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
