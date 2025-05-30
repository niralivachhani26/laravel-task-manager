@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-4">Projects</h2>

    <button id="addProjectBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded mb-6 transition">Add Project</button>

@if(session('success'))
    <div class="bg-green-200 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
@endif


    <table class="min-w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-300">
                <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Description</th>
                <th class="border border-gray-300 px-4 py-2 text-center">Total Tasks</th>
                <th class="border border-gray-300 px-4 py-2 text-center">% Completed</th>
                <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="projectTableBody">
            @foreach($projects as $index => $project)
                <tr style="background-color: {{ $index % 2 === 0 ? '#c2d9ff' : '#c2ffef' }}">
                    <td class="border border-gray-300 px-4 py-2">{{ $project->name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $project->description }}</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $project->total_tasks }}</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $project->completed_percentage }} %</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        <button id="editprojectModal" class="editProjectBtn bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded mb-6 transition" data-id="${project.id}">
                            Edit
                        </button>
                        <button class="deleteProjectBtn bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded mb-6 transition" data-id="${project.id}">Delete</button>
                    </td>
                </tr>
            @endforeach
            @if($projects->isEmpty())
                <tr>
                    <td colspan="3" class="text-center p-4 text-gray-600">No projects found.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div id="projectModal" style="display:none;" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-xl p-6 rounded-lg shadow-lg relative">
        <button id="closeModalBtn" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
            ✕
        </button>
        <h2 class="text-2xl font-semibold text-gray-700 mb-6" id="projectlabel">Create New Project</h2>

        @if ($errors->any())
            <div class="mb-4">
                <ul class="bg-red-100 text-red-700 p-3 rounded">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="#" id="ProjectForm" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="project_id" id="project_id">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Project Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" id="projectbtn"
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
$(document).ready(function() {
    // Load project initially
    loadProjects();

    // Add project button click
    $(document).on('click', '#addProjectBtn', function() {
        $('#projectModal').show();
    });

    // Close modal
    $(document).on('click', '#closeModalBtn', function() {
        $('#projectModal').hide();
    });

    

    // Submit Add/Edit project form

    $(document).on('click', '.deleteProjectBtn', function() {
        if (!confirm('Delete this project?')) {
            return;
        }

        const projectId = $(this).data('id');

        $.ajax({
            url: `/projects/${projectId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message || 'Project Deleted successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });    
                loadProjects(); // reload the project list
            },
            error: function(xhr) {
                let msg = 'Error saving project';

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

    $('#ProjectForm').submit(function(e) {
        e.preventDefault();

        let projectId = $('#project_id').val();
        let url = projectId ? `/projects/${projectId}` : '/projects';
        let method = projectId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Project saved successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                $('#projectModal').hide();
                loadProjects();
                $('#ProjectForm')[0].reset(); // Optional: clear the form
                $('#projectModal').val('');
            },
            error: function(xhr) {
                let msg = 'Error saving Project';

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

    // Edit Project
    $(document).on('click', '#editprojectModal', function() {
        let projectId = $(this).data('id');

        $.get(`/projects/${projectId}`, function(data) {
            $('#projectlabel').text('Edit Project');
            $('#projectbtn').text('Update');
            $('#project_id').val(data.project.id);
            $('#name').val(data.project.name);
            $('#description').val(data.project.description);
            $('#projectModal').show();
        });
    });


    // Load projects
    function loadProjects() {

        $.ajax({
            url: '/projects',
            type: 'GET',
            data: 'json',
            success: function(response) {
                let rows = '';

                if (response.projects.length === 0) {
                    rows = `
                        <tr>
                            <td colspan="3" class="text-center py-4">No records found!</td>
                        </tr>
                    `;
                } else {
                    $.each(response.projects, function(index, project) {
                        let bgColor = (index % 2 === 0) ? '#c2d9ff' : '#c2ffef';

                        rows += `
                            <tr style="background-color: ${bgColor}">
                                <td class="border border-gray-300 px-4 py-2">${project.name}</td>
                                <td class="border border-gray-300 px-4 py-2">${project.description}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${project.total_tasks}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">${project.completed_percentage} %</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <div class="mt-2">
                                        <button id="editprojectModal" class="editProjectBtn bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded mb-6 transition" data-id="${project.id}">
                                            Edit
                                        </button>
                                        <button class="deleteProjectBtn bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded mb-6 transition" data-id="${project.id}">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#projectTableBody').html(rows);
            }
        });
    }
});
</script>
@endsection