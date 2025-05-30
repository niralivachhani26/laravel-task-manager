<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = Project::withCount('tasks')
            ->with('tasks:id,project_id,status')
            ->get();

        foreach ($projects as $project) {
            $completedTasks = $project->tasks->where('status', 'completed')->count();
            $totalTasks = $project->tasks->count();
            $project->total_tasks = $totalTasks;
            $project->completed_percentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        }

        if ($request->ajax()) {
            return response()->json(['projects' => $projects]);
        }
        
        return view('Projects.index', compact('projects'));
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
                'name' => 'required',
                'description' => 'nullable'
            ]);
           
            $project = Project::create($validated);
            logActivity('Created Project', $project, 'Project titled "' . $project->name . '" was created.');


            return response()->json([
                'success' => true,
                'message' => 'Project created successfully!',
                'projects' => $project
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
                'message' => 'An error occurred while creating the project.',
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
            $project = Project::findOrFail($id);
            return response()->json([
                'success' => true,
                'project' => $project
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.'
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
                'name' => 'required',
                'description' => 'nullable'
            ]);
            $project = Project::findOrFail($id);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found.'
                ], 404);
            }

            $project->update($validated);
            logActivity('Updated Project', $project, 'Project titled "' . $project->name . '" was updated.');

            return response()->json([
                'success' => true,
                'message' => 'Project Updated successfully!',
                'projects' => $project
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
                'message' => 'An error occurred while creating the project.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        if (!$project) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Project not found!'], 404);
            }
            return redirect()->route('projects.index')->with('error', 'Project not found!');
        }
        $project->delete();
        logActivity('Deleted Project', $project, 'Project titled "' . $project->name . '" was deleted.');
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Project deleted!']);
        }
        return redirect()->route('projects.index')->with('success', 'Project deleted!');
    }
}