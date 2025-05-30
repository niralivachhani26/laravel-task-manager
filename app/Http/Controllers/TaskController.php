<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::with('project');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->get();

        if ($request->ajax()) {
            return response()->json(['tasks' => $tasks]);
        }


        $projects = Project::latest()->get();

        return view('tasks.index', compact('projects','tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date',
                'project_id' => 'required|exists:projects,id',
                'status' => 'nullable|in:pending,completed',
            ]);

            if (empty($validated['status'])) {
                $validated['status'] = 'pending';
            }

            $task = Task::create($validated);

            logActivity('Created Task', $task, 'Task titled "' . $task->title . '" was created.');

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the task.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            return response()->json([
                'success' => true,
                'task' => $task
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date',
                'project_id' => 'required|exists:projects,id',
            ]);
            $task = Task::findOrFail($id);
            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.'
                ], 404);
            }

            $task->update($validated);
            logActivity('Updated Task', $task, 'Task titled "' . $task->title . '" was updated.');

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!',
                'task' => $task
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the task.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id); // automatically throws 404 if not found

        $task->delete();
        logActivity('Deleted Task', $task, 'Task titled "' . $task->title . '" was deleted.');

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Task deleted!']);
        }

        return redirect()->route('tasks.index')->with('success', 'Task deleted!');
    }
    public function toggleStatus(Task $task)
    {
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        $task->status = $task->status === 'pending' ? 'completed' : 'pending';
        $task->save();
        
        logActivity('Toggled Task Status', $task, 'Task titled "' . $task->title . '" status toggled to ' . $task->status . '.');

        return response()->json(['status' => $task->status]);
    }
    public function filter(Request $request)
    {
        $status = $request->status;
        $tasks = $status === 'all' ? Task::all() : Task::where('status', $status)->get();
        return response()->json($tasks);
    }

    public function search(Request $request)
    {
        $tasks = Task::where('title', 'like', '%' . $request->q . '%')->get();
        return response()->json($tasks);
    }
}
