<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\Attendance;
use App\Models\AttendanceTask;
use App\Models\Client;
use App\Models\ClientData;
use App\Models\Division;
use App\Models\DivisionAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\Meeting;
use App\Models\MeetingClient;
use App\Models\MeetingUser;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceTypeData;
use App\Models\ServiceTypeField;
use App\Models\Task;
use App\Models\WorkhourPlan;

class SyncController extends Controller
{
    public function push(Request $request)
    {
        $payload = $request->all();

        if (!empty($payload['attendances'])) {
            foreach ($payload['attendances'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']); // Remove temp ID
                    Attendance::create($data);
                } else {
                    $existing = Attendance::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        Attendance::updateOrCreate(['id' => $data['id']], $data);
                    }
                    // If server data is newer, skip update (server wins)
                }
            }
        }

        if (!empty($payload['attendance_tasks'])) {
            foreach ($payload['attendance_tasks'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    AttendanceTask::create($data);
                } else {
                    $existing = AttendanceTask::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        AttendanceTask::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['clients'])) {
            foreach ($payload['clients'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    Client::create($data);
                } else {
                    $existing = Client::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        Client::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['client_data'])) {
            foreach ($payload['client_data'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    ClientData::create($data);
                } else {
                    $existing = ClientData::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        ClientData::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['meetings'])) {
            foreach ($payload['meetings'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    Meeting::create($data);
                } else {
                    $existing = Meeting::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        Meeting::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['meeting_users'])) {
            foreach ($payload['meeting_users'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    MeetingUser::create($data);
                } else {
                    $existing = MeetingUser::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        MeetingUser::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['meeting_clients'])) {
            foreach ($payload['meeting_clients'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    MeetingClient::create($data);
                } else {
                    $existing = MeetingClient::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        MeetingClient::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['services'])) {
            foreach ($payload['services'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    Service::create($data);
                } else {
                    $existing = Service::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        Service::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['service_types'])) {
            foreach ($payload['service_types'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    ServiceType::create($data);
                } else {
                    $existing = ServiceType::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        ServiceType::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['service_type_fields'])) {
            foreach ($payload['service_type_fields'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    ServiceTypeField::create($data);
                } else {
                    $existing = ServiceTypeField::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        ServiceTypeField::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['service_type_data'])) {
            foreach ($payload['service_type_data'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    ServiceTypeData::create($data);
                } else {
                    $existing = ServiceTypeData::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        ServiceTypeData::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['tasks'])) {
            foreach ($payload['tasks'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    Task::create($data);
                } else {
                    $existing = Task::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        Task::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['employee_tasks'])) {
            foreach ($payload['employee_tasks'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    EmployeeTask::create($data);
                } else {
                    $existing = EmployeeTask::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        EmployeeTask::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['users'])) {
            foreach ($payload['users'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    User::create($data);
                } else {
                    $existing = User::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        User::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['employees'])) {
            foreach ($payload['employees'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    Employee::create($data);
                } else {
                    $existing = Employee::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        Employee::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['divisions'])) {
            foreach ($payload['divisions'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    Division::create($data);
                } else {
                    $existing = Division::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        Division::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['accesses'])) {
            foreach ($payload['accesses'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    Access::create($data);
                } else {
                    $existing = Access::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        Access::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['division_accesses'])) {
            foreach ($payload['division_accesses'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    DivisionAccess::create($data);
                } else {
                    $existing = DivisionAccess::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        DivisionAccess::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        if (!empty($payload['workhour_plans'])) {
            foreach ($payload['workhour_plans'] as $data) {
                if ($data['id'] < 0) {
                    unset($data['id']);
                    WorkhourPlan::create($data);
                } else {
                    $existing = WorkhourPlan::find($data['id']);
                    if (!$existing || $existing->updated_at <= $data['updated_at']) {
                        WorkhourPlan::updateOrCreate(['id' => $data['id']], $data);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function pull(Request $request)
    {
        $since = $request->query('since');
        $since = $since ? Carbon::parse($since) : null;

        $baseQuery = fn($model) => $model::when($since, fn($q) => 
            $q->where('updated_at', '>', $since)
        );

        return response()->json([
            'users' => $baseQuery(User::class)->get(),
            'employees' => $baseQuery(Employee::class)->get(),
            'tasks' => $baseQuery(Task::class)->get(),
            'attendances' => $baseQuery(Attendance::class)->get(),
            'attendance_tasks' => $baseQuery(AttendanceTask::class)->get(),
            'employee_tasks' => $baseQuery(EmployeeTask::class)->get(),
            'clients' => $baseQuery(Client::class)->get(),
            'client_data' => $baseQuery(ClientData::class)->get(),
            'meetings' => $baseQuery(Meeting::class)->get(),
            'meeting_users' => $baseQuery(MeetingUser::class)->get(),
            'meeting_clients' => $baseQuery(MeetingClient::class)->get(),
            'services' => $baseQuery(Service::class)->get(),
            'service_types' => $baseQuery(ServiceType::class)->get(),
            'service_type_fields' => $baseQuery(ServiceTypeField::class)->get(),
            'service_type_data' => $baseQuery(ServiceTypeData::class)->get(),
            'divisions' => $baseQuery(Division::class)->get(),
            'accesses' => $baseQuery(Access::class)->get(),
            'division_accesses' => $baseQuery(DivisionAccess::class)->get(),
            'workhour_plans' => $baseQuery(WorkhourPlan::class)->get(),
        ]);
    }
}
