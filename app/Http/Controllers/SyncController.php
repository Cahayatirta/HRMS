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

    public function mobileSync(Request $request)
    {
        // First, handle incoming data from mobile (push local changes)
        $payload = $request->all();
        
        // Process each entity type - upsert based on updated_at comparison
        if (!empty($payload['attendances'])) {
            foreach ($payload['attendances'] as $data) {
                if (isset($data['id']) && $data['id'] > 0) {
                    $existing = Attendance::find($data['id']);
                    if (!$existing || (isset($data['updated_at']) && $existing->updated_at <= $data['updated_at'])) {
                        Attendance::updateOrCreate(['id' => $data['id']], $data);
                    }
                } else {
                    unset($data['id']);
                    Attendance::create($data);
                }
            }
        }

        if (!empty($payload['attendance_tasks'])) {
            foreach ($payload['attendance_tasks'] as $data) {
                AttendanceTask::updateOrCreate(
                    ['attendance_id' => $data['attendance_id'], 'task_id' => $data['task_id']], 
                    $data
                );
            }
        }

        if (!empty($payload['clients'])) {
            foreach ($payload['clients'] as $data) {
                if (isset($data['id']) && $data['id'] > 0) {
                    $existing = Client::find($data['id']);
                    if (!$existing || (isset($data['updated_at']) && $existing->updated_at <= $data['updated_at'])) {
                        Client::updateOrCreate(['id' => $data['id']], $data);
                    }
                } else {
                    unset($data['id']);
                    Client::create($data);
                }
            }
        }

        if (!empty($payload['client_data'])) {
            foreach ($payload['client_data'] as $data) {
                if (isset($data['id']) && $data['id'] > 0) {
                    $existing = ClientData::find($data['id']);
                    if (!$existing || (isset($data['updated_at']) && $existing->updated_at <= $data['updated_at'])) {
                        ClientData::updateOrCreate(['id' => $data['id']], $data);
                    }
                } else {
                    unset($data['id']);
                    ClientData::create($data);
                }
            }
        }

        if (!empty($payload['meetings'])) {
            foreach ($payload['meetings'] as $data) {
                if (isset($data['id']) && $data['id'] > 0) {
                    $existing = Meeting::find($data['id']);
                    if (!$existing || (isset($data['updated_at']) && $existing->updated_at <= $data['updated_at'])) {
                        Meeting::updateOrCreate(['id' => $data['id']], $data);
                    }
                } else {
                    unset($data['id']);
                    Meeting::create($data);
                }
            }
        }

        if (!empty($payload['meeting_users'])) {
            foreach ($payload['meeting_users'] as $data) {
                MeetingUser::updateOrCreate(
                    ['meeting_id' => $data['meeting_id'], 'user_id' => $data['user_id']], 
                    $data
                );
            }
        }

        if (!empty($payload['meeting_clients'])) {
            foreach ($payload['meeting_clients'] as $data) {
                MeetingClient::updateOrCreate(
                    ['meeting_id' => $data['meeting_id'], 'client_id' => $data['client_id']], 
                    $data
                );
            }
        }

        if (!empty($payload['services'])) {
            foreach ($payload['services'] as $data) {
                if (isset($data['id']) && $data['id'] > 0) {
                    $existing = Service::find($data['id']);
                    if (!$existing || (isset($data['updated_at']) && $existing->updated_at <= $data['updated_at'])) {
                        Service::updateOrCreate(['id' => $data['id']], $data);
                    }
                } else {
                    unset($data['id']);
                    Service::create($data);
                }
            }
        }

        if (!empty($payload['tasks'])) {
            foreach ($payload['tasks'] as $data) {
                if (isset($data['id']) && $data['id'] > 0) {
                    $existing = Task::find($data['id']);
                    if (!$existing || (isset($data['updated_at']) && $existing->updated_at <= $data['updated_at'])) {
                        Task::updateOrCreate(['id' => $data['id']], $data);
                    }
                } else {
                    unset($data['id']);
                    Task::create($data);
                }
            }
        }

        if (!empty($payload['employee_tasks'])) {
            foreach ($payload['employee_tasks'] as $data) {
                EmployeeTask::updateOrCreate(
                    ['employee_id' => $data['employee_id'], 'task_id' => $data['task_id']], 
                    $data
                );
            }
        }

        if (!empty($payload['workhour_plans'])) {
            foreach ($payload['workhour_plans'] as $data) {
                if (isset($data['id']) && $data['id'] > 0) {
                    $existing = WorkhourPlan::find($data['id']);
                    if (!$existing || (isset($data['updated_at']) && $existing->updated_at <= $data['updated_at'])) {
                        WorkhourPlan::updateOrCreate(['id' => $data['id']], $data);
                    }
                } else {
                    unset($data['id']);
                    WorkhourPlan::create($data);
                }
            }
        }

        // Now pull all data from server (optionally filtered by 'since')
        $since = $request->input('since');
        $since = $since ? Carbon::parse($since) : null;

        $baseQuery = fn($model) => $model::when($since, fn($q) => 
            $q->where('updated_at', '>', $since)
        );

        // Fetch all data
        $attendances = $baseQuery(Attendance::class)->get()->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'employee_id' => $attendance->employee_id,
                'start_time' => $attendance->start_time,
                'end_time' => $attendance->end_time,
                'work_location' => $attendance->work_location,
                'longitude' => $attendance->longitude ? (string) $attendance->longitude : null,
                'latitude' => $attendance->latitude ? (string) $attendance->latitude : null,
                'image_path' => $attendance->image_path ?? '',
                'task_link' => $attendance->task_link ?? '',
                'is_deleted' => (bool) $attendance->is_deleted,
                'created_at' => $attendance->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $attendance->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $attendanceTasks = $baseQuery(AttendanceTask::class)->get()->map(function ($task) {
            return [
                'attendance_id' => $task->attendance_id,
                'task_id' => $task->task_id,
                'is_deleted' => (bool) ($task->is_deleted ?? false),
                'created_at' => $task->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $task->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $clients = $baseQuery(Client::class)->get()->map(function ($client) {
            return [
                'id' => $client->id,
                'full_name' => $client->name,
                'email' => $client->email,
                'phone_number' => $client->phone_number,
                'address' => $client->address,
                'is_deleted' => (bool) $client->is_deleted,
                'created_at' => $client->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $client->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $clientData = $baseQuery(ClientData::class)->get()->map(function ($data) {
            return [
                'id' => $data->id,
                'client_id' => $data->client_id,
                'account_type' => $data->account_type,
                'account_credentials' => $data->account_credential,
                'account_password' => $data->account_password,
                'is_deleted' => (bool) ($data->is_deleted ?? false),
                'created_at' => $data->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $data->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $meetings = $baseQuery(Meeting::class)->get()->map(function ($meeting) {
            return [
                'id' => $meeting->id,
                'title' => $meeting->meeting_title,
                'note' => $meeting->meeting_note ?? '',
                'start_time' => $meeting->date . ' ' . $meeting->start_time,
                'end_time' => $meeting->date . ' ' . $meeting->end_time,
                'is_deleted' => (bool) $meeting->is_deleted,
                'created_at' => $meeting->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $meeting->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $meetingUsers = $baseQuery(MeetingUser::class)->get()->map(function ($mu) {
            return [
                'meeting_id' => $mu->meeting_id,
                'user_id' => $mu->user_id,
                'is_deleted' => (bool) ($mu->is_deleted ?? false),
                'created_at' => $mu->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $mu->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $meetingClients = $baseQuery(MeetingClient::class)->get()->map(function ($mc) {
            return [
                'meeting_id' => $mc->meeting_id,
                'client_id' => $mc->client_id,
                'is_deleted' => (bool) ($mc->is_deleted ?? false),
                'created_at' => $mc->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $mc->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $services = $baseQuery(Service::class)->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'client_id' => $service->client_id,
                'service_type_id' => $service->service_type_id,
                'status' => $service->status,
                'price' => $service->price,
                'start_time' => $service->start_time,
                'expired_time' => $service->expired_time,
                'is_deleted' => (bool) $service->is_deleted,
                'created_at' => $service->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $service->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $serviceTypes = $baseQuery(ServiceType::class)->get()->map(function ($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'description' => $type->description ?? '',
                'is_deleted' => (bool) $type->is_deleted,
                'created_at' => $type->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $type->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $serviceTypeFields = $baseQuery(ServiceTypeField::class)->get()->map(function ($field) {
            return [
                'id' => $field->id,
                'service_type_id' => $field->service_type_id,
                'field_name' => $field->field_name,
                'is_deleted' => (bool) ($field->is_deleted ?? false),
                'created_at' => $field->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $field->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $serviceTypeData = $baseQuery(ServiceTypeData::class)->get()->map(function ($data) {
            return [
                'field_id' => $data->field_id,
                'service_id' => $data->service_id,
                'value' => $data->value,
                'is_deleted' => (bool) ($data->is_deleted ?? false),
                'created_at' => $data->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $data->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $tasks = $baseQuery(Task::class)->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->task_name,
                'description' => $task->task_description ?? '',
                'status' => $task->status,
                'deadline' => $task->deadline,
                'is_deleted' => (bool) $task->is_deleted,
                'created_at' => $task->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $task->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $employeeTasks = $baseQuery(EmployeeTask::class)->get()->map(function ($et) {
            return [
                'employee_id' => $et->employee_id,
                'task_id' => $et->task_id,
                'is_deleted' => (bool) ($et->is_deleted ?? false),
                'created_at' => $et->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $et->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $users = $baseQuery(User::class)->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
                'is_deleted' => (bool) $user->is_deleted,
                'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $employees = $baseQuery(Employee::class)->get()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'user_id' => $employee->user_id,
                'division_id' => $employee->division_id,
                'full_name' => $employee->full_name,
                'phone_number' => $employee->phone_number,
                'gender' => $employee->gender,
                'birth_date' => $employee->birth_date,
                'address' => $employee->address,
                'image_path' => $employee->image_path ?? '',
                'is_deleted' => (bool) $employee->is_deleted,
                'created_at' => $employee->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $employee->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $divisions = $baseQuery(Division::class)->get()->map(function ($division) {
            return [
                'id' => $division->id,
                'name' => $division->division_name,
                'required_workhours' => $division->required_workhours,
                'is_deleted' => (bool) $division->is_deleted,
                'created_at' => $division->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $division->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        $workhourPlans = $baseQuery(WorkhourPlan::class)->get()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'employee_id' => $plan->employee_id,
                'plan_date' => $plan->plan_date,
                'planned_start_time' => $plan->planned_starttime,
                'planned_end_time' => $plan->planned_endtime,
                'work_location' => $plan->work_location,
                'is_deleted' => (bool) $plan->is_deleted,
                'created_at' => $plan->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $plan->updated_at?->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'attendances' => $attendances,
            'attendance_tasks' => $attendanceTasks,
            'clients' => $clients,
            'client_data' => $clientData,
            'meetings' => $meetings,
            'meeting_users' => $meetingUsers,
            'meeting_clients' => $meetingClients,
            'services' => $services,
            'service_types' => $serviceTypes,
            'service_type_fields' => $serviceTypeFields,
            'service_type_data' => $serviceTypeData,
            'tasks' => $tasks,
            'employee_tasks' => $employeeTasks,
            'users' => $users,
            'employees' => $employees,
            'divisions' => $divisions,
            'workhour_plans' => $workhourPlans,
        ]);
    }
}
