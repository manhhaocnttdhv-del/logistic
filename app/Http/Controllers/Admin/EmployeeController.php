<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StaffAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'staff');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $employees = $query->latest()->paginate(15);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $positions = StaffAssignmentService::getPositions();
        return view('admin.employees.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|in:import_staff,export_staff,warehouse_staff,inventory_staff,general_staff',
            'is_active' => 'boolean',
        ]);
        
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        return redirect()->route('admin.employees.index')
            ->with('success', 'Thêm nhân viên thành công.');
    }

    public function show(User $employee)
    {
        if ($employee->role !== 'staff') {
            return redirect()->route('admin.employees.index')
                ->with('error', 'Không tìm thấy nhân viên.');
        }
        
        $employee->load('importOrdersAssigned', 'exportOrdersAssigned', 'tasksAssigned');
        
        // Statistics
        $stats = [
            'import_orders' => $employee->importOrdersAssigned()->where('status', 'completed')->count(),
            'export_orders' => $employee->exportOrdersAssigned()->where('status', 'completed')->count(),
            'tasks_completed' => $employee->tasksAssigned()->where('status', 'completed')->count(),
            'tasks_pending' => $employee->tasksAssigned()->whereIn('status', ['pending', 'in_progress'])->count(),
            'tasks_overdue' => $employee->tasksAssigned()
                ->whereIn('status', ['pending', 'in_progress'])
                ->where('due_date', '<', now()->toDateString())
                ->count(),
        ];
        
        // Recent tasks
        $recentTasks = $employee->tasksAssigned()
            ->with('assignedBy')
            ->latest()
            ->limit(10)
            ->get();
        
        // Recent import orders
        $recentImports = $employee->importOrdersAssigned()
            ->with('supplier', 'creator')
            ->latest()
            ->limit(10)
            ->get();
        
        // Recent export orders
        $recentExports = $employee->exportOrdersAssigned()
            ->with('creator')
            ->latest()
            ->limit(10)
            ->get();
        
        return view('admin.employees.show', compact('employee', 'stats', 'recentTasks', 'recentImports', 'recentExports'));
    }

    public function edit(User $employee)
    {
        if ($employee->role !== 'staff') {
            return redirect()->route('admin.employees.index')
                ->with('error', 'Không tìm thấy nhân viên.');
        }
        
        $positions = StaffAssignmentService::getPositions();
        return view('admin.employees.edit', compact('employee', 'positions'));
    }

    public function update(Request $request, User $employee)
    {
        if ($employee->role !== 'staff') {
            return redirect()->route('admin.employees.index')
                ->with('error', 'Không tìm thấy nhân viên.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|in:import_staff,export_staff,warehouse_staff,inventory_staff,general_staff',
            'is_active' => 'boolean',
        ]);
        
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'position' => $validated['position'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ];
        
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
        
        $employee->update($data);
        
        return redirect()->route('admin.employees.index')
            ->with('success', 'Cập nhật nhân viên thành công.');
    }

    public function destroy(User $employee)
    {
        if ($employee->role !== 'staff') {
            return redirect()->route('admin.employees.index')
                ->with('error', 'Không tìm thấy nhân viên.');
        }
        
        if ($employee->importOrdersAssigned()->exists() || $employee->exportOrdersAssigned()->exists()) {
            return redirect()->route('admin.employees.index')
                ->with('error', 'Không thể xóa nhân viên đã có dữ liệu liên quan.');
        }
        
        $employee->delete();
        
        return redirect()->route('admin.employees.index')
            ->with('success', 'Xóa nhân viên thành công.');
    }

    public function toggleStatus(User $employee)
    {
        if ($employee->role !== 'staff') {
            return back()->with('error', 'Không tìm thấy nhân viên.');
        }
        
        $employee->update([
            'is_active' => !$employee->is_active,
        ]);
        
        $status = $employee->is_active ? 'kích hoạt' : 'khóa';
        
        return back()->with('success', "Đã {$status} tài khoản nhân viên.");
    }
}
