@extends('layouts.app')

@section('content')
<div class="bg-white shadow rounded p-6">
  <h2 class="text-2xl font-semibold mb-4">Your Tasks</h2>

  <form id="create-form" class="flex gap-3 items-start mb-6">
    <div class="flex-1">
      <label class="block text-sm text-gray-600">Title</label>
      <input id="title" name="title" type="text" placeholder="e.g. Finish report" required
             class="mt-1 block w-full border rounded p-2 focus:outline-none focus:ring"/>
    </div>
    <div class="w-1/3">
      <label class="block text-sm text-gray-600">Description</label>
      <input id="description" name="description" type="text" placeholder="Optional"
             class="mt-1 block w-full border rounded p-2 focus:outline-none focus:ring"/>
    </div>
    <div class="pt-6">
      <button class="btn-primary" type="submit">Add</button>
    </div>
  </form>

  <div id="todos" class="space-y-3">
    <!-- JS will render todos here -->
  </div>
</div>

<!-- Edit modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
  <div class="bg-white rounded p-6 w-full max-w-lg">
    <h3 class="text-lg font-semibold mb-3">Edit Task</h3>
    <form id="edit-form" class="space-y-3">
      <input id="edit-id" type="hidden" />
      <div>
        <label class="block text-sm text-gray-600">Title</label>
        <input id="edit-title" required class="mt-1 block w-full border rounded p-2"/>
      </div>
      <div>
        <label class="block text-sm text-gray-600">Description</label>
        <input id="edit-description" class="mt-1 block w-full border rounded p-2"/>
      </div>
      <div class="flex items-center gap-3">
        <label class="flex items-center gap-2"><input id="edit-completed" type="checkbox"/> Completed</label>
        <div class="ml-auto">
          <button type="button" id="cancel-edit" class="btn-secondary">Cancel</button>
          <button type="submit" class="btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection
