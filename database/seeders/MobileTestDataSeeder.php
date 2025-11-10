<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;
use App\Models\Division;
use App\Models\Client;
use App\Models\ClientData;
use App\Models\Task;
use App\Models\EmployeeTask;
use App\Models\Attendance;
use App\Models\AttendanceTask;
use App\Models\Meeting;
use App\Models\MeetingUser;
use App\Models\MeetingClient;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceTypeField;
use App\Models\ServiceTypeData;
use App\Models\WorkhourPlan;

class MobileTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks based on database driver
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        // Clear existing data (only if tables exist)
        $tables = [
            'service_type_data',
            'service_type_fields',
            'services',
            'service_types',
            'meeting_clients',
            'meeting_users',
            'meetings',
            'attendance_tasks',
            'attendances',
            'employee_tasks',
            'workhour_plans',
            'tasks',
            'client_data',
            'clients',
            'employees',
            'users',
            'divisions'
        ];

        foreach ($tables as $table) {
            try {
                DB::table($table)->delete();
            } catch (\Exception $e) {
                $this->command->warn("Table {$table} does not exist, skipping...");
            }
        }

        // Re-enable foreign key checks
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        // Seed Divisions
        $divisions = [
            [
                'id' => 1,
                'division_name' => 'Developer',
                'required_workhours' => 40,
                'is_deleted' => false,
                'created_at' => '2024-05-01 09:00:00',
                'updated_at' => '2024-05-01 09:00:00',
            ],
            [
                'id' => 2,
                'division_name' => 'Project Manager',
                'required_workhours' => 40,
                'is_deleted' => false,
                'created_at' => '2024-05-01 09:00:00',
                'updated_at' => '2024-05-01 09:00:00',
            ],
            [
                'id' => 3,
                'division_name' => 'Designer',
                'required_workhours' => 40,
                'is_deleted' => false,
                'created_at' => '2024-05-01 09:00:00',
                'updated_at' => '2024-05-01 09:00:00',
            ],
            [
                'id' => 4,
                'division_name' => 'QA Tester',
                'required_workhours' => 35,
                'is_deleted' => false,
                'created_at' => '2024-05-01 09:00:00',
                'updated_at' => '2024-05-01 09:00:00',
            ],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }

        // Seed Users (with passwords)
        $users = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@company.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_deleted' => false,
                'created_at' => '2024-05-15 09:00:00',
                'updated_at' => '2024-06-01 12:00:00',
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane.smith@company.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_deleted' => false,
                'created_at' => '2024-05-20 10:00:00',
                'updated_at' => '2024-06-05 14:00:00',
            ],
            [
                'id' => 3,
                'name' => 'Admin User',
                'email' => 'admin@company.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_deleted' => false,
                'created_at' => '2024-05-01 08:00:00',
                'updated_at' => '2024-05-01 08:00:00',
            ],
            [
                'id' => 4,
                'name' => 'Bob Designer',
                'email' => 'bob@company.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_deleted' => false,
                'created_at' => '2024-05-25 10:00:00',
                'updated_at' => '2024-05-25 10:00:00',
            ],
            [
                'id' => 5,
                'name' => 'Alice Tester',
                'email' => 'alice@company.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_deleted' => false,
                'created_at' => '2024-05-28 10:00:00',
                'updated_at' => '2024-05-28 10:00:00',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // Seed Employees
        $employees = [
            [
                'id' => 1,
                'user_id' => 1,
                'division_id' => 2,
                'full_name' => 'John Doe',
                'phone_number' => '08123456789',
                'gender' => 'male',
                'birth_date' => '1995-03-15',
                'address' => '123 Main St, Jakarta',
                'image_path' => 'employees/john.jpg',
                'is_deleted' => false,
                'created_at' => '2024-05-15 09:30:00',
                'updated_at' => '2024-06-01 12:00:00',
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'division_id' => 1,
                'full_name' => 'Jane Smith',
                'phone_number' => '08198765432',
                'gender' => 'female',
                'birth_date' => '1998-07-07',
                'address' => '456 Developer Ave, Bandung',
                'image_path' => 'employees/jane.jpg',
                'is_deleted' => false,
                'created_at' => '2024-05-20 10:30:00',
                'updated_at' => '2024-06-05 14:00:00',
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'division_id' => 2,
                'full_name' => 'Admin User',
                'phone_number' => '08111111111',
                'gender' => 'male',
                'birth_date' => '1990-01-01',
                'address' => '789 Admin Road, Jakarta',
                'image_path' => 'employees/admin.jpg',
                'is_deleted' => false,
                'created_at' => '2024-05-01 08:30:00',
                'updated_at' => '2024-05-01 08:30:00',
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'division_id' => 3,
                'full_name' => 'Bob Designer',
                'phone_number' => '08133333333',
                'gender' => 'male',
                'birth_date' => '1996-05-20',
                'address' => '321 Design St, Surabaya',
                'image_path' => 'employees/bob.jpg',
                'is_deleted' => false,
                'created_at' => '2024-05-25 10:30:00',
                'updated_at' => '2024-05-25 10:30:00',
            ],
            [
                'id' => 5,
                'user_id' => 5,
                'division_id' => 4,
                'full_name' => 'Alice Tester',
                'phone_number' => '08144444444',
                'gender' => 'female',
                'birth_date' => '1997-09-12',
                'address' => '654 QA Lane, Yogyakarta',
                'image_path' => 'employees/alice.jpg',
                'is_deleted' => false,
                'created_at' => '2024-05-28 10:30:00',
                'updated_at' => '2024-05-28 10:30:00',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }

        // Seed Clients
        $clients = [
            [
                'id' => 1,
                'name' => 'Acme Corporation',
                'email' => 'contact@acme.com',
                'phone_number' => '+1-555-123-4567',
                'address' => '123 Business St, City, State 12345',
                'is_deleted' => false,
                'created_at' => '2024-06-01 09:00:00',
                'updated_at' => '2024-06-10 14:30:00',
            ],
            [
                'id' => 2,
                'name' => 'Globex Ltd',
                'email' => 'hello@globex.com',
                'phone_number' => '+44-20-555-7890',
                'address' => '456 Global Ave, London, UK',
                'is_deleted' => false,
                'created_at' => '2024-06-05 09:00:00',
                'updated_at' => '2024-06-11 11:15:00',
            ],
            [
                'id' => 3,
                'name' => 'Tech Innovations Inc',
                'email' => 'info@techinnovations.com',
                'phone_number' => '+1-555-999-8888',
                'address' => '789 Innovation Blvd, Silicon Valley, CA',
                'is_deleted' => false,
                'created_at' => '2024-06-03 10:00:00',
                'updated_at' => '2024-06-03 10:00:00',
            ],
            [
                'id' => 4,
                'name' => 'Global Solutions',
                'email' => 'contact@globalsolutions.com',
                'phone_number' => '+65-555-1234',
                'address' => '100 Marina Bay, Singapore',
                'is_deleted' => false,
                'created_at' => '2024-06-07 11:00:00',
                'updated_at' => '2024-06-07 11:00:00',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }

        // Seed Client Data (skip if table doesn't exist)
        if (DB::getSchemaBuilder()->hasTable('client_data')) {
        $clientData = [
            [
                'id' => 1,
                'client_id' => 1,
                'account_type' => 'Email',
                'account_credential' => 'admin@acme.com',
                'account_password' => 'encrypted_password_1',
                'is_deleted' => false,
                'created_at' => '2024-06-01 09:15:00',
                'updated_at' => '2024-06-01 09:15:00',
            ],
            [
                'id' => 2,
                'client_id' => 2,
                'account_type' => 'Slack',
                'account_credential' => 'globex.slack.com',
                'account_password' => 'encrypted_password_2',
                'is_deleted' => false,
                'created_at' => '2024-06-05 10:00:00',
                'updated_at' => '2024-06-05 10:00:00',
            ],
            [
                'id' => 3,
                'client_id' => 1,
                'account_type' => 'CRM',
                'account_credential' => 'acme_crm_user',
                'account_password' => 'encrypted_password_3',
                'is_deleted' => false,
                'created_at' => '2024-06-02 10:00:00',
                'updated_at' => '2024-06-02 10:00:00',
            ],
            [
                'id' => 4,
                'client_id' => 3,
                'account_type' => 'Email',
                'account_credential' => 'admin@techinnovations.com',
                'account_password' => 'encrypted_password_4',
                'is_deleted' => false,
                'created_at' => '2024-06-03 10:15:00',
                'updated_at' => '2024-06-03 10:15:00',
            ],
        ];

        foreach ($clientData as $data) {
            ClientData::create($data);
        }
        } else {
            $this->command->warn('Table client_data does not exist, skipping client data seeding...');
        }

        // Seed Tasks
        $tasks = [
            [
                'id' => 1,
                'task_name' => 'Implement user authentication',
                'task_description' => 'Create login and registration functionality with JWT tokens',
                'status' => 'in_progress',
                'deadline' => '2024-06-20',
                'parent_task_id' => null,
                'note' => 'High priority task',
                'is_deleted' => false,
                'created_at' => '2024-06-10 08:00:00',
                'updated_at' => '2024-06-12 10:30:00',
            ],
            [
                'id' => 2,
                'task_name' => 'Design database schema',
                'task_description' => 'Define tables and relationships for the HRMS system',
                'status' => 'completed',
                'deadline' => '2024-06-15',
                'parent_task_id' => null,
                'note' => null,
                'is_deleted' => false,
                'created_at' => '2024-06-08 10:00:00',
                'updated_at' => '2024-06-15 12:00:00',
            ],
            [
                'id' => 3,
                'task_name' => 'Create API endpoints',
                'task_description' => 'Build RESTful API for mobile app',
                'status' => 'pending',
                'deadline' => '2024-06-25',
                'parent_task_id' => 1,
                'note' => 'Subtask of authentication',
                'is_deleted' => false,
                'created_at' => '2024-06-12 09:00:00',
                'updated_at' => '2024-06-12 09:00:00',
            ],
            [
                'id' => 4,
                'task_name' => 'Design UI mockups',
                'task_description' => 'Create user interface designs for all screens',
                'status' => 'in_progress',
                'deadline' => '2024-06-18',
                'parent_task_id' => null,
                'note' => 'Assigned to design team',
                'is_deleted' => false,
                'created_at' => '2024-06-09 10:00:00',
                'updated_at' => '2024-06-14 11:00:00',
            ],
            [
                'id' => 5,
                'task_name' => 'Write unit tests',
                'task_description' => 'Create comprehensive test suite',
                'status' => 'pending',
                'deadline' => '2024-06-30',
                'parent_task_id' => null,
                'note' => null,
                'is_deleted' => false,
                'created_at' => '2024-06-11 08:00:00',
                'updated_at' => '2024-06-11 08:00:00',
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }

        // Seed Employee Tasks
        $employeeTasks = [
            [
                'employee_id' => 1,
                'task_id' => 1,
                'is_deleted' => false,
                'created_at' => '2024-06-10 08:15:00',
                'updated_at' => '2024-06-10 08:15:00',
            ],
            [
                'employee_id' => 2,
                'task_id' => 2,
                'is_deleted' => false,
                'created_at' => '2024-06-08 11:00:00',
                'updated_at' => '2024-06-08 11:00:00',
            ],
            [
                'employee_id' => 2,
                'task_id' => 3,
                'is_deleted' => false,
                'created_at' => '2024-06-12 09:15:00',
                'updated_at' => '2024-06-12 09:15:00',
            ],
            [
                'employee_id' => 4,
                'task_id' => 4,
                'is_deleted' => false,
                'created_at' => '2024-06-09 10:15:00',
                'updated_at' => '2024-06-09 10:15:00',
            ],
            [
                'employee_id' => 5,
                'task_id' => 5,
                'is_deleted' => false,
                'created_at' => '2024-06-11 08:15:00',
                'updated_at' => '2024-06-11 08:15:00',
            ],
            [
                'employee_id' => 1,
                'task_id' => 3,
                'is_deleted' => false,
                'created_at' => '2024-06-12 09:20:00',
                'updated_at' => '2024-06-12 09:20:00',
            ],
        ];

        foreach ($employeeTasks as $employeeTask) {
            EmployeeTask::create($employeeTask);
        }

        // Seed Attendances
        $attendances = [
            [
                'id' => 1,
                'employee_id' => 1,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'work_location' => 'office',
                'longitude' => 106.8456,
                'latitude' => -6.2088,
                'image_path' => 'attendances/att_1.jpg',
                'task_link' => '',
                'is_deleted' => false,
                'created_at' => '2024-06-12 08:00:00',
                'updated_at' => '2024-06-12 17:00:00',
            ],
            [
                'id' => 2,
                'employee_id' => 2,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'work_location' => 'anywhere',
                'longitude' => 107.6191,
                'latitude' => -6.9175,
                'image_path' => 'attendances/att_2.png',
                'task_link' => 'task/2',
                'is_deleted' => false,
                'created_at' => '2024-06-13 09:00:00',
                'updated_at' => '2024-06-13 18:00:00',
            ],
            [
                'id' => 3,
                'employee_id' => 1,
                'start_time' => '08:30:00',
                'end_time' => null,
                'work_location' => 'office',
                'longitude' => 106.8456,
                'latitude' => -6.2088,
                'image_path' => 'attendances/att_3.png',
                'task_link' => 'task/1',
                'is_deleted' => false,
                'created_at' => '2025-11-10 07:00:00',
                'updated_at' => '2025-11-10 12:00:00',
            ],
            [
                'id' => 4,
                'employee_id' => 4,
                'start_time' => '09:00:00',
                'end_time' => '17:30:00',
                'work_location' => 'office',
                'longitude' => 112.7521,
                'latitude' => -7.2575,
                'image_path' => 'attendances/att_4.jpg',
                'task_link' => '',
                'is_deleted' => false,
                'created_at' => '2024-06-14 09:00:00',
                'updated_at' => '2024-06-14 17:30:00',
            ],
            [
                'id' => 5,
                'employee_id' => 5,
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'work_location' => 'anywhere',
                'longitude' => 110.3695,
                'latitude' => -7.7956,
                'image_path' => '',
                'task_link' => '',
                'is_deleted' => false,
                'created_at' => '2024-06-14 08:00:00',
                'updated_at' => '2024-06-14 16:00:00',
            ],
        ];

        foreach ($attendances as $attendance) {
            Attendance::create($attendance);
        }

        // Seed Attendance Tasks
        $attendanceTasks = [
            [
                'attendance_id' => 1,
                'task_id' => 1,
                'created_at' => '2024-06-12 08:30:00',
                'updated_at' => '2024-06-12 08:30:00',
            ],
            [
                'attendance_id' => 2,
                'task_id' => 2,
                'created_at' => '2024-06-13 10:00:00',
                'updated_at' => '2024-06-13 10:00:00',
            ],
            [
                'attendance_id' => 3,
                'task_id' => 1,
                'created_at' => '2025-11-10 08:30:00',
                'updated_at' => '2025-11-10 08:30:00',
            ],
            [
                'attendance_id' => 4,
                'task_id' => 4,
                'created_at' => '2024-06-14 09:15:00',
                'updated_at' => '2024-06-14 09:15:00',
            ],
            [
                'attendance_id' => 5,
                'task_id' => 5,
                'created_at' => '2024-06-14 08:15:00',
                'updated_at' => '2024-06-14 08:15:00',
            ],
        ];

        foreach ($attendanceTasks as $attendanceTask) {
            AttendanceTask::create($attendanceTask);
        }

        // Seed Meetings
        $meetings = [
            [
                'id' => 1,
                'meeting_title' => 'Project Kickoff Meeting',
                'meeting_note' => 'Initial project discussion and planning',
                'date' => '2024-06-15',
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'is_deleted' => false,
                'created_at' => '2024-06-12 09:00:00',
                'updated_at' => '2024-06-12 09:00:00',
            ],
            [
                'id' => 2,
                'meeting_title' => 'Sprint Retrospective',
                'meeting_note' => 'Review completed sprint tasks',
                'date' => '2024-06-20',
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
                'is_deleted' => false,
                'created_at' => '2024-06-19 12:00:00',
                'updated_at' => '2024-06-19 12:00:00',
            ],
            [
                'id' => 3,
                'meeting_title' => 'Client Presentation',
                'meeting_note' => 'Demo new features to client',
                'date' => '2024-06-22',
                'start_time' => '15:00:00',
                'end_time' => '16:30:00',
                'is_deleted' => false,
                'created_at' => '2024-06-20 10:00:00',
                'updated_at' => '2024-06-20 10:00:00',
            ],
            [
                'id' => 4,
                'meeting_title' => 'Design Review',
                'meeting_note' => 'Review UI/UX mockups',
                'date' => '2024-06-17',
                'start_time' => '11:00:00',
                'end_time' => '12:00:00',
                'is_deleted' => false,
                'created_at' => '2024-06-16 14:00:00',
                'updated_at' => '2024-06-16 14:00:00',
            ],
        ];

        foreach ($meetings as $meeting) {
            Meeting::create($meeting);
        }

        // Seed Meeting Users
        $meetingUsers = [
            [
                'meeting_id' => 1,
                'user_id' => 1,
                'created_at' => '2024-06-12 09:05:00',
                'updated_at' => '2024-06-12 09:05:00',
            ],
            [
                'meeting_id' => 1,
                'user_id' => 2,
                'created_at' => '2024-06-12 09:05:00',
                'updated_at' => '2024-06-12 09:05:00',
            ],
            [
                'meeting_id' => 2,
                'user_id' => 2,
                'created_at' => '2024-06-19 12:10:00',
                'updated_at' => '2024-06-19 12:10:00',
            ],
            [
                'meeting_id' => 2,
                'user_id' => 3,
                'created_at' => '2024-06-19 12:10:00',
                'updated_at' => '2024-06-19 12:10:00',
            ],
            [
                'meeting_id' => 3,
                'user_id' => 1,
                'created_at' => '2024-06-20 10:05:00',
                'updated_at' => '2024-06-20 10:05:00',
            ],
            [
                'meeting_id' => 3,
                'user_id' => 3,
                'created_at' => '2024-06-20 10:05:00',
                'updated_at' => '2024-06-20 10:05:00',
            ],
            [
                'meeting_id' => 4,
                'user_id' => 4,
                'created_at' => '2024-06-16 14:05:00',
                'updated_at' => '2024-06-16 14:05:00',
            ],
            [
                'meeting_id' => 4,
                'user_id' => 1,
                'created_at' => '2024-06-16 14:05:00',
                'updated_at' => '2024-06-16 14:05:00',
            ],
        ];

        foreach ($meetingUsers as $meetingUser) {
            MeetingUser::create($meetingUser);
        }

        // Seed Meeting Clients
        $meetingClients = [
            [
                'meeting_id' => 1,
                'client_id' => 1,
                'created_at' => '2024-06-12 09:05:00',
                'updated_at' => '2024-06-12 09:05:00',
            ],
            [
                'meeting_id' => 2,
                'client_id' => 2,
                'created_at' => '2024-06-19 12:15:00',
                'updated_at' => '2024-06-19 12:15:00',
            ],
            [
                'meeting_id' => 3,
                'client_id' => 1,
                'created_at' => '2024-06-20 10:05:00',
                'updated_at' => '2024-06-20 10:05:00',
            ],
            [
                'meeting_id' => 3,
                'client_id' => 3,
                'created_at' => '2024-06-20 10:05:00',
                'updated_at' => '2024-06-20 10:05:00',
            ],
        ];

        foreach ($meetingClients as $meetingClient) {
            MeetingClient::create($meetingClient);
        }

        // Seed Service Types
        $serviceTypes = [
            [
                'id' => 1,
                'name' => 'Premium Support',
                'description' => '24/7 premium support package with dedicated team',
                'is_deleted' => false,
                'created_at' => '2024-06-01 09:30:00',
                'updated_at' => '2024-06-01 09:30:00',
            ],
            [
                'id' => 2,
                'name' => 'Basic Hosting',
                'description' => 'Shared hosting plan with standard features',
                'is_deleted' => false,
                'created_at' => '2024-06-02 09:30:00',
                'updated_at' => '2024-06-02 09:30:00',
            ],
            [
                'id' => 3,
                'name' => 'Cloud Infrastructure',
                'description' => 'Scalable cloud infrastructure solution',
                'is_deleted' => false,
                'created_at' => '2024-06-02 10:00:00',
                'updated_at' => '2024-06-02 10:00:00',
            ],
            [
                'id' => 4,
                'name' => 'Custom Development',
                'description' => 'Tailored software development services',
                'is_deleted' => false,
                'created_at' => '2024-06-03 09:30:00',
                'updated_at' => '2024-06-03 09:30:00',
            ],
        ];

        foreach ($serviceTypes as $serviceType) {
            ServiceType::create($serviceType);
        }

        // Seed Service Type Fields
        $serviceTypeFields = [
            [
                'id' => 1,
                'service_type_id' => 1,
                'field_name' => 'Response Time',
                'is_deleted' => false,
                'created_at' => '2024-06-01 09:35:00',
                'updated_at' => '2024-06-01 09:35:00',
            ],
            [
                'id' => 2,
                'service_type_id' => 2,
                'field_name' => 'Storage',
                'is_deleted' => false,
                'created_at' => '2024-06-02 09:35:00',
                'updated_at' => '2024-06-02 09:35:00',
            ],
            [
                'id' => 3,
                'service_type_id' => 1,
                'field_name' => 'Support Hours',
                'is_deleted' => false,
                'created_at' => '2024-06-01 09:36:00',
                'updated_at' => '2024-06-01 09:36:00',
            ],
            [
                'id' => 4,
                'service_type_id' => 2,
                'field_name' => 'Bandwidth',
                'is_deleted' => false,
                'created_at' => '2024-06-02 09:36:00',
                'updated_at' => '2024-06-02 09:36:00',
            ],
            [
                'id' => 5,
                'service_type_id' => 3,
                'field_name' => 'CPU Cores',
                'is_deleted' => false,
                'created_at' => '2024-06-02 10:05:00',
                'updated_at' => '2024-06-02 10:05:00',
            ],
            [
                'id' => 6,
                'service_type_id' => 3,
                'field_name' => 'RAM',
                'is_deleted' => false,
                'created_at' => '2024-06-02 10:06:00',
                'updated_at' => '2024-06-02 10:06:00',
            ],
        ];

        foreach ($serviceTypeFields as $field) {
            ServiceTypeField::create($field);
        }

        // Seed Services
        $services = [
            [
                'id' => 1,
                'client_id' => 1,
                'service_type_id' => 1,
                'status' => 'ongoing',
                'price' => 10000,
                'start_time' => '2024-06-04 10:00:00',
                'expired_time' => '2025-06-04 10:00:00',
                'is_deleted' => false,
                'created_at' => '2024-06-01 10:00:00',
                'updated_at' => '2024-06-01 10:00:00',
            ],
            [
                'id' => 2,
                'client_id' => 2,
                'service_type_id' => 2,
                'status' => 'ongoing',
                'price' => 5000,
                'start_time' => '2024-06-06 09:00:00',
                'expired_time' => '2025-06-06 09:00:00',
                'is_deleted' => false,
                'created_at' => '2024-06-05 09:30:00',
                'updated_at' => '2024-06-05 09:30:00',
            ],
            [
                'id' => 3,
                'client_id' => 3,
                'service_type_id' => 3,
                'status' => 'ongoing',
                'price' => 15000,
                'start_time' => '2024-06-05 10:00:00',
                'expired_time' => '2025-06-05 10:00:00',
                'is_deleted' => false,
                'created_at' => '2024-06-03 11:00:00',
                'updated_at' => '2024-06-03 11:00:00',
            ],
            [
                'id' => 4,
                'client_id' => 4,
                'service_type_id' => 4,
                'status' => 'pending',
                'price' => 50000,
                'start_time' => '2024-06-10 09:00:00',
                'expired_time' => '2024-12-10 09:00:00',
                'is_deleted' => false,
                'created_at' => '2024-06-07 11:30:00',
                'updated_at' => '2024-06-07 11:30:00',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Seed Service Type Data
        $serviceTypeData = [
            [
                'field_id' => 1,
                'service_id' => 1,
                'value' => '2 hours',
                'is_deleted' => false,
                'created_at' => '2024-06-01 09:40:00',
                'updated_at' => '2024-06-01 09:40:00',
            ],
            [
                'field_id' => 2,
                'service_id' => 2,
                'value' => '50 GB',
                'is_deleted' => false,
                'created_at' => '2024-06-06 09:40:00',
                'updated_at' => '2024-06-06 09:40:00',
            ],
            [
                'field_id' => 3,
                'service_id' => 1,
                'value' => '24/7',
                'is_deleted' => false,
                'created_at' => '2024-06-01 09:41:00',
                'updated_at' => '2024-06-01 09:41:00',
            ],
            [
                'field_id' => 4,
                'service_id' => 2,
                'value' => '100 GB/month',
                'is_deleted' => false,
                'created_at' => '2024-06-06 09:41:00',
                'updated_at' => '2024-06-06 09:41:00',
            ],
            [
                'field_id' => 5,
                'service_id' => 3,
                'value' => '8',
                'is_deleted' => false,
                'created_at' => '2024-06-03 11:10:00',
                'updated_at' => '2024-06-03 11:10:00',
            ],
            [
                'field_id' => 6,
                'service_id' => 3,
                'value' => '16 GB',
                'is_deleted' => false,
                'created_at' => '2024-06-03 11:11:00',
                'updated_at' => '2024-06-03 11:11:00',
            ],
        ];

        foreach ($serviceTypeData as $data) {
            ServiceTypeData::create($data);
        }

        // Seed Workhour Plans
        $workhourPlans = [
            [
                'id' => 1,
                'employee_id' => 1,
                'plan_date' => '2024-06-08',
                'planned_starttime' => '10:00:00',
                'planned_endtime' => '18:00:00',
                'work_location' => 'office',
                'is_deleted' => false,
                'created_at' => '2024-06-08 10:00:00',
                'updated_at' => '2024-06-08 10:00:00',
            ],
            [
                'id' => 2,
                'employee_id' => 2,
                'plan_date' => '2024-06-09',
                'planned_starttime' => '09:00:00',
                'planned_endtime' => '17:00:00',
                'work_location' => 'anywhere',
                'is_deleted' => false,
                'created_at' => '2024-06-09 09:00:00',
                'updated_at' => '2024-06-09 09:00:00',
            ],
            [
                'id' => 3,
                'employee_id' => 1,
                'plan_date' => '2024-06-16',
                'planned_starttime' => '08:00:00',
                'planned_endtime' => '16:00:00',
                'work_location' => 'office',
                'is_deleted' => false,
                'created_at' => '2024-06-15 10:00:00',
                'updated_at' => '2024-06-15 10:00:00',
            ],
            [
                'id' => 4,
                'employee_id' => 4,
                'plan_date' => '2024-06-17',
                'planned_starttime' => '09:00:00',
                'planned_endtime' => '17:00:00',
                'work_location' => 'office',
                'is_deleted' => false,
                'created_at' => '2024-06-16 09:00:00',
                'updated_at' => '2024-06-16 09:00:00',
            ],
            [
                'id' => 5,
                'employee_id' => 5,
                'plan_date' => '2024-06-18',
                'planned_starttime' => '08:00:00',
                'planned_endtime' => '16:00:00',
                'work_location' => 'anywhere',
                'is_deleted' => false,
                'created_at' => '2024-06-17 08:00:00',
                'updated_at' => '2024-06-17 08:00:00',
            ],
        ];

        foreach ($workhourPlans as $plan) {
            WorkhourPlan::create($plan);
        }

        $this->command->info('Mobile test data seeded successfully!');
        $this->command->info('Test users created:');
        $this->command->info('- john.doe@company.com (password: password123) - Project Manager');
        $this->command->info('- jane.smith@company.com (password: password123) - Developer');
        $this->command->info('- admin@company.com (password: admin123) - Admin');
        $this->command->info('- bob@company.com (password: password123) - Designer');
        $this->command->info('- alice@company.com (password: password123) - QA Tester');
    }
}
