<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        $tasks = Task::with('project')
            ->when(request('project_id'), function($query) {
                $query->where('project_id', request('project_id'));
            })
            ->orderBy('priority')
            ->get();
            // dd($projects);

        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('tasks.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
        ]);

        $task = Task::create([
            'name' => $request->name,
            'project_id' => $request->project_id,
            'priority' => Task::where('project_id', $request->project_id)->count() + 1,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $projects = Project::all();
        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
        ]);

        $task->update($request->only('name', 'project_id'));

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->taskOrder as $index => $taskId) {
            Task::find($taskId)->update(['priority' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }
}
