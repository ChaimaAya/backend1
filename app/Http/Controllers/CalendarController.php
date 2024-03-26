<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
class CalendarController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }





    public function getTasks()
    {
        if (Auth::check()) {
            $userId = Auth()->user()->id;
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
                if ($userId) {
            $tasksCreatedByUser = Task::where('created_by', $userId)
                ->with('createdBy', 'assignedTo')
                ->get();

            $tasksAssignedToUser = Task::where('assigned_to', $userId)
                ->with('createdBy', 'assignedTo')
                ->get();

            $tasks = $tasksCreatedByUser->merge($tasksAssignedToUser);

            $tasksData = [];

            foreach ($tasks as $task) {
                $taskData = [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start_time' => $task->start_time,
                    'end_time' => $task->end_time,
                    'description' => $task->description,
                    'created_by' => $task->created_by,
                    'assigned_to' => $task->assigned_to,
                ];

                $tasksData[] = $taskData;
            }

            return response()->json($tasksData, 200);
        } else {
            return response()->json(['error' => 'User ID not provided'], 401);
        }
    }








    public function show(Task $task)
    {
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($task, 200);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

    public function getMessage($id)
    {
        $user_auth_id = 2;

        // Recherche de l'utilisateur correspondant à l'ID donné
        $user = User::find($id);

        // Vérifier si l'utilisateur existe
        if ($user) {
            // Vérifier si l'utilisateur authentifié correspond à l'utilisateur donné
            if ($user_auth_id === $id) {
                return response()->json(['Message' => 'Rendez-vous avec ' . $user->name]);
            } else {
                return response()->json(['Message' =>$user_auth_id ], 403);
            }
        } else {
            return response()->json(['Message' => 'User not found'], 404);
        }

    }







    public function store(Request $request)
    {
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $title = $request->input('title');
        $description = $request->input('description');
        $created_by = Auth()->user()->id;
        $assigned_to = $request->input('assigned_to');
        if (empty($title)) {
            return response()->json(['error' => 'All required fields must be filled.'], 400);
        }

        $color = $request->input('color', '#3788d8');

        $task = Task::create([

            'title' => $title,
            'description' => $description,
            'created_by' => $created_by,
            'assigned_to' => $assigned_to,
            'color' => $color,
            "start_time" => $start_time,
            "end_time" => $end_time,
        ]);

        if ($task) {
            return response()->json($task, 201);
        } else {
            return response()->json(['error' => 'An error occurred while adding the task.'], 400);
        }
    }



}
















