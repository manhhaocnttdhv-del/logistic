<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where('assigned_to', auth()->id())
            ->with('assignedBy');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $tasks = $query->latest()->paginate(15);
        
        return view('staff.tasks.index', compact('tasks'));
    }

    public function show($id)
    {
        $task = Task::where('assigned_to', auth()->id())
            ->with('assignedBy')
            ->findOrFail($id);
        
        return view('staff.tasks.show', compact('task'));
    }

    public function start($id)
    {
        $task = Task::where('assigned_to', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $task->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
        
        return redirect()->route('staff.tasks.show', $task)
            ->with('success', 'Đã bắt đầu thực hiện công việc.');
    }

    public function complete(Request $request, $id)
    {
        $task = Task::where('assigned_to', auth()->id())
            ->where('status', 'in_progress')
            ->findOrFail($id);
        
        $validated = $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);
        
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $validated['completion_notes'] ?? null,
        ]);
        
        return redirect()->route('staff.tasks.show', $task)
            ->with('success', 'Đã hoàn thành công việc.');
    }
}
