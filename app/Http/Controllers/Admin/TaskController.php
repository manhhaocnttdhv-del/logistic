<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Services\StaffAssignmentService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Task::with('assignedTo', 'assignedBy');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Sort by priority and due date
        $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
              ->orderBy('due_date', 'asc')
              ->latest();
        
        $tasks = $query->paginate(15);
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        
        // Workload statistics
        $workloadStats = [];
        foreach ($staff as $s) {
            $workloadStats[$s->id] = [
                'pending' => Task::where('assigned_to', $s->id)->where('status', 'pending')->count(),
                'in_progress' => Task::where('assigned_to', $s->id)->where('status', 'in_progress')->count(),
                'urgent' => Task::where('assigned_to', $s->id)->where('priority', 'urgent')->whereIn('status', ['pending', 'in_progress'])->count(),
                'overdue' => Task::where('assigned_to', $s->id)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->where('due_date', '<', now()->toDateString())
                    ->count(),
            ];
        }
        
        return view('admin.tasks.index', compact('tasks', 'staff', 'workloadStats'));
    }

    public function create()
    {
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        $positions = StaffAssignmentService::getPositions();
        return view('admin.tasks.create', compact('staff', 'positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'position' => 'nullable|in:import_staff,export_staff,warehouse_staff,inventory_staff,general_staff',
            'assigned_to' => 'nullable|exists:users,id',
            'type' => 'required|in:import,export,inventory_check,picking,stock_report',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'related_order_id' => 'nullable|integer',
            'related_order_type' => 'nullable|in:import_order,export_order',
            'due_date' => 'nullable|date',
        ]);
        
        // Tự động phân công nếu không chỉ định nhân viên cụ thể
        $assignedTo = $validated['assigned_to'];
        if (!$assignedTo) {
            $assignmentService = new StaffAssignmentService();
            $assignedUser = $assignmentService->assignStaffForTask($validated['type']);
            if (!$assignedUser) {
                return back()->withErrors(['position' => 'Không tìm thấy nhân viên phù hợp để phân công.'])->withInput();
            }
            $assignedTo = $assignedUser->id;
        }
        
        // Auto-generate description if not provided
        $description = $validated['description'] ?? $this->getDefaultDescription($validated['type'], $validated['title']);
        
        Task::create([
            'assigned_to' => $assignedTo,
            'assigned_by' => auth()->id(),
            'type' => $validated['type'],
            'priority' => $validated['priority'] ?? 'normal',
            'title' => $validated['title'],
            'description' => $description,
            'related_order_id' => $validated['related_order_id'] ?? null,
            'related_order_type' => $validated['related_order_type'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => 'pending',
        ]);
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Phân công công việc thành công.');
    }

    public function show(Task $task)
    {
        $task->load('assignedTo', 'assignedBy');
        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        if ($task->status !== 'pending') {
            return redirect()->route('admin.tasks.show', $task)
                ->with('error', 'Chỉ có thể chỉnh sửa công việc ở trạng thái chờ xử lý.');
        }
        
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        return view('admin.tasks.edit', compact('task', 'staff'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->status !== 'pending') {
            return redirect()->route('admin.tasks.show', $task)
                ->with('error', 'Chỉ có thể chỉnh sửa công việc ở trạng thái chờ xử lý.');
        }
        
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'type' => 'required|in:import,export,inventory_check,picking,stock_report',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'related_order_id' => 'nullable|integer',
            'related_order_type' => 'nullable|in:import_order,export_order',
            'due_date' => 'nullable|date',
        ]);
        
        $task->update($validated);
        
        return redirect()->route('admin.tasks.show', $task)
            ->with('success', 'Cập nhật công việc thành công.');
    }

    public function destroy(Task $task)
    {
        if ($task->status !== 'pending') {
            return redirect()->route('admin.tasks.index')
                ->with('error', 'Chỉ có thể xóa công việc ở trạng thái chờ xử lý.');
        }
        
        $task->delete();
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Xóa công việc thành công.');
    }
    
    /**
     * Get default description based on task type
     */
    private function getDefaultDescription($type, $title = '')
    {
        $descriptions = [
            'import' => 'Xử lý phiếu nhập hàng: Kiểm tra số lượng, chất lượng hàng hóa, nhập vào kho và cập nhật tồn kho.',
            'export' => 'Xử lý phiếu xuất hàng: Soạn hàng theo phiếu, kiểm tra số lượng, xuất kho và cập nhật tồn kho.',
            'inventory_check' => 'Kiểm kê kho: Đối chiếu số lượng thực tế với sổ sách, phát hiện chênh lệch và báo cáo.',
            'picking' => 'Soạn hàng: Lấy hàng theo đơn hàng, đóng gói và chuẩn bị xuất kho.',
            'stock_report' => 'Báo cáo tồn kho: Tổng hợp số liệu tồn kho, báo cáo hàng tồn kho thấp và đề xuất nhập hàng.',
        ];
        
        return $descriptions[$type] ?? "Thực hiện công việc: {$title}";
    }
}
