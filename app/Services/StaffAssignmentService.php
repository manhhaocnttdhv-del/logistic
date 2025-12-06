<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;
use App\Models\ImportOrder;
use App\Models\ExportOrder;
use Illuminate\Support\Facades\DB;

class StaffAssignmentService
{
    /**
     * Danh sách positions và tên hiển thị
     */
    public static function getPositions(): array
    {
        return [
            'import_staff' => 'Nhân viên nhập kho',
            'export_staff' => 'Nhân viên xuất kho',
            'inventory_staff' => 'Nhân viên kiểm kê',
            'warehouse_staff' => 'Nhân viên kho',
            'general_staff' => 'Nhân viên tổng hợp',
        ];
    }

    /**
     * Map task type to preferred positions
     */
    public static function getPositionsForTaskType(string $taskType): array
    {
        $positionMap = [
            'import' => ['import_staff', 'warehouse_staff', 'general_staff'],
            'export' => ['export_staff', 'warehouse_staff', 'general_staff'],
            'inventory_check' => ['inventory_staff', 'warehouse_staff', 'general_staff'],
            'picking' => ['export_staff', 'warehouse_staff', 'general_staff'],
            'stock_report' => ['inventory_staff', 'warehouse_staff', 'general_staff'],
        ];

        return $positionMap[$taskType] ?? ['warehouse_staff', 'general_staff'];
    }

    /**
     * Tính workload của nhân viên dựa trên loại công việc
     */
    private function calculateWorkload(User $user, string $workloadType = 'task'): int
    {
        return match($workloadType) {
            'task' => Task::where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
            'import' => ImportOrder::where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'processing'])
                ->count(),
            'export' => ExportOrder::where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'processing'])
                ->count(),
            default => 0,
        };
    }

    /**
     * Tìm nhân viên phù hợp với workload thấp nhất
     * 
     * @param array $preferredPositions Danh sách positions ưu tiên
     * @param string $workloadType Loại workload: 'task', 'import', 'export'
     * @param int $maxWorkloadThreshold Ngưỡng workload tối đa để dừng tìm kiếm
     * @return User|null
     */
    public function assignStaffByPositions(
        array $preferredPositions,
        string $workloadType = 'task',
        int $maxWorkloadThreshold = 3
    ): ?User {
        return DB::transaction(function() use ($preferredPositions, $workloadType, $maxWorkloadThreshold) {
            $assignedUser = null;
            $minWorkload = PHP_INT_MAX;

            foreach ($preferredPositions as $position) {
                // Lock users để tránh concurrent assignment
                $candidates = User::where('role', 'staff')
                    ->where('is_active', true)
                    ->where('position', $position)
                    ->lockForUpdate()
                    ->get();

                foreach ($candidates as $user) {
                    // Tính workload với lock
                    $workload = $this->calculateWorkloadWithLock($user, $workloadType);

                    if ($workload < $minWorkload) {
                        $minWorkload = $workload;
                        $assignedUser = $user;
                    }
                }

                // Nếu đã tìm được nhân viên với workload thấp, dừng lại
                if ($assignedUser && $minWorkload <= $maxWorkloadThreshold) {
                    break;
                }
            }

            // Nếu không tìm được từ preferred positions, tìm trong tất cả nhân viên
            if (!$assignedUser) {
                $allStaff = User::where('role', 'staff')
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->get();

                foreach ($allStaff as $user) {
                    $workload = $this->calculateWorkloadWithLock($user, $workloadType);

                    if ($workload < $minWorkload) {
                        $minWorkload = $workload;
                        $assignedUser = $user;
                    }
                }
            }

            return $assignedUser;
        });
    }

    /**
     * Tính workload với lock để tránh race condition
     */
    private function calculateWorkloadWithLock(User $user, string $workloadType): int
    {
        return match($workloadType) {
            'task' => Task::where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->lockForUpdate()
                ->count(),
            'import' => ImportOrder::where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'processing'])
                ->lockForUpdate()
                ->count(),
            'export' => ExportOrder::where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'processing'])
                ->lockForUpdate()
                ->count(),
            default => 0,
        };
    }

    /**
     * Phân công nhân viên cho task
     */
    public function assignStaffForTask(string $taskType): ?User
    {
        $preferredPositions = self::getPositionsForTaskType($taskType);
        return $this->assignStaffByPositions($preferredPositions, 'task', 3);
    }

    /**
     * Phân công nhân viên cho import order
     */
    public function assignStaffForImport(): ?User
    {
        $preferredPositions = ['import_staff', 'warehouse_staff', 'general_staff'];
        return $this->assignStaffByPositions($preferredPositions, 'import', 2);
    }

    /**
     * Phân công nhân viên cho export order
     */
    public function assignStaffForExport(): ?User
    {
        $preferredPositions = ['export_staff', 'warehouse_staff', 'general_staff'];
        return $this->assignStaffByPositions($preferredPositions, 'export', 2);
    }
}

