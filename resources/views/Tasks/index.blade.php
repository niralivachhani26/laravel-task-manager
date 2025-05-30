@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-4">Tasks</h2>

    <button id="addTaskBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded mb-6 transition">Add Task</button>

    <select id="filterStatus">
        <option value="">All</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
    </select>

    <input type="text" id="searchTitle" placeholder="Search by title" />

@if(session('success'))
    <div class="bg-green-200 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
@endif
    <table class="min-w-full border-collapse border border-gray-300" id="tasksTable">
        <thead class="bg-gray-300">
            <tr>
                <th class="border border-gray-300 px-4 py-2 text-left">Project Name</td>
                <th class="border border-gray-300 px-4 py-2 text-left">Title</th>
                <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Due Date</th>
                <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
            </tr>
        </thead>
        <tbody id="tasksTableBody">
            @foreach($tasks as $index => $task)
            <tr style="background-color: {{ $index % 2 === 0 ? '#c2d9ff' : '#c2ffef' }}">
                <td class="border border-gray-300 px-4 py-2">{{ $task->project ? $task->project->name : 'N/A' }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $task->title }}</td>
                <td class="border border-gray-300 px-4 py-2 text-center">
                    <button class="toggle-status" data-id="{{ $task->id }}">
                        {{ ucfirst($task->status) }}
                    </button>
                </td>
                <td class="border border-gray-300 px-4 py-2">{{ $task->due_date }}</td>
                <td class="border border-gray-300 px-4 py-2 text-center">
                    <div class="mt-2">
                        <button id="edittaskModal" class="editTaskBtn bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded mb-6 transition" data-id="${task.id}">Edit</button>                        
                        <button class="deleteTaskBtn bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded mb-6 transition" data-id="${task.id}">Delete</button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
</table>
<!-- Modal for Add/Edit Task -->
<div id="taskModal" style="display:none;" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-xl p-6 rounded-lg shadow-lg relative">
        <button id="closeModalBtn" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
            ✕
        </button>
        <h2 class="text-2xl font-semibold text-gray-700 mb-6" id="tasklabel">Create New Task</h2>

        @if ($errors->any())
            <div class="mb-4">
                <ul class="bg-red-100 text-red-700 p-3 rounded">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="#" id="taskForm" class="space-y-6">
        @csrf
            <input type="hidden" name="task_id" id="task_id">
            <div>
                <label for="project_id" class="block text-sm font-medium text-gray-700">Project:</label>
                <select name="project_id" id="project_id" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
                <input type="text" name="title" id="title" value="" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date:</label>
                <input type="date" name="due_date" id="due_date" value="" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex justify-end">
                <button type="submit" id="taskbtn"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
const minDate = tomorrow.toISOString().split('T')[0];

document.getElementById('due_date').setAttribute('min', minDate);
$(document).ready(function() {

    // Load tasks initially
    loadTasks();

    // Filter and search events
    $('#filterStatus, #searchTitle').on('change keyup', function() {
        loadTasks();
    });

    // Add Task button click
    $(document).on('click', '#addTaskBtn', function() {
        $('#taskModal').show();
    });

    // Close modal
    $(document).on('click', '#closeModalBtn', function() {
        $('#taskModal').hide();
    });

    

    // Submit Add/Edit Task form

    $(document).on('click', '.deleteTaskBtn', function() {
        if (!confirm('Delete this task?')) {
            return;
        }

        const taskId = $(this).data('id');

        $.ajax({
            url: `/tasks/${taskId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message || 'Task Deleted successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });    
                loadTasks(); // reload the task list
            },
            error: function(xhr) {
                let msg = 'Error saving task';

                if (xhr.status === 422) {
                    // Validation error, show all errors
                    const errors = xhr.responseJSON.errors;
                    msg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: msg,
                });
            }
        });
    });

    $('#taskForm').submit(function(e) {
        e.preventDefault();

        let taskId = $('#task_id').val();
        let url = taskId ? `/tasks/${taskId}` : '/tasks';
        let method = taskId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Task saved successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                $('#taskModal').hide();
                loadTasks();
                $('#taskForm')[0].reset(); // Optional: clear the form
                $('#task_id').val('');
            },
            error: function(xhr) {
                let msg = 'Error saving task';

                if (xhr.status === 422) {
                    // Validation error, show all errors
                    const errors = xhr.responseJSON.errors;
                    msg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: msg,
                });
            }
        });
    });

    // Edit Task
    $(document).on('click', '#edittaskModal', function() {
        let taskId = $(this).data('id');

        $.get(`/tasks/${taskId}`, function(data) {
            $('#tasklabel').text('Edit Task');
            $('#taskbtn').text('Update');
            $('#task_id').val(data.task.id);
            $('#project_id').val(data.task.project_id);
            $('#title').val(data.task.title);
            $('#description').val(data.task.description);
            $('#due_date').val(data.task.due_date);
            $('#taskModal').show();
        });
    });

    // Toggle task status
    $(document).on('click', '.toggle-status', function () {
        const id = $(this).data('id');

        $.ajax({
            url: '/tasks/toggle-status/' + id,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message || 'Status updated successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
                $('#taskModal').hide();
                loadTasks();
            },
            error: function(xhr) {
                let msg = 'Error saving task';

                if (xhr.status === 422) {
                    // Validation error, show all errors
                    const errors = xhr.responseJSON.errors;
                    msg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: msg,
                });
            }
        });
    });

    // Load tasks
    function loadTasks() {
        let status = $('#filterStatus').val();
        let search = $('#searchTitle').val();

        $.ajax({
            url: '/tasks',
            type: 'GET',
            data: { status: status, search: search },
            success: function(response) {
                let rows = '';

                if (response.tasks.length === 0) {
                    rows = `
                        <tr>
                            <td colspan="4" class="text-center py-4">No records found!</td>
                        </tr>
                    `;
                } else {
                    $.each(response.tasks, function(index, task) {
                        let bgColor = (index % 2 === 0) ? '#c2d9ff' : '#c2ffef';

                        rows += `
                            <tr style="background-color: ${bgColor}">
                                <td class="border border-gray-300 px-4 py-2">${task.project ? task.project.name : 'N/A'}</td>
                                <td class="border border-gray-300 px-4 py-2">${task.title}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <button class="toggle-status" data-id="${task.id}">
                                        ${task.status.charAt(0).toUpperCase() + task.status.slice(1)}
                                    </button>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">${task.due_date}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <div class="mt-2">
                                        <button id="edittaskModal" class="editTaskBtn bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded mb-6 transition" data-id="${task.id}">
                                            Edit
                                        </button>
                                        <button class="deleteTaskBtn bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded mb-6 transition" data-id="${task.id}">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#tasksTableBody').html(rows);
            }
        });
    }
});
</script>
@endsection