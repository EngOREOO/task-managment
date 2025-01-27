@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Task Management</h1>
    <div class="mb-3">
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">Create Task</a>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Manage Projects</a>
    </div>

    <form action="{{ route('tasks.index') }}" method="GET" class="mb-3">
        <select name="project_id" onchange="this.form.submit()" class="form-select">
            <option value="">All Projects</option>
            @php
               $projects = \App\Models\Project::all();
            @endphp
            @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
    </form>

    <ul id="taskList" class="list-group">
        @php
            $tasks = \App\Models\Task::when(request('project_id'), function($query) {
                $query->where('project_id', request('project_id'));
            })
            ->orderBy('priority')
            ->get();
        @endphp
        @foreach($tasks as $task)
            <li data-task-id="{{ $task->id }}" class="list-group-item d-flex justify-content-between align-items-center">
                {{ $task->name }} (Priority: {{ $task->priority }})
                <div>
                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const taskList = document.getElementById('taskList');
    new Sortable(taskList, {
        animation: 150,
        onEnd: function(evt) {
            const taskOrder = Array.from(taskList.children).map(task => task.dataset.taskId);

            fetch("{{ route('tasks.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ taskOrder })
            });
        }
    });
</script>
@endsection
