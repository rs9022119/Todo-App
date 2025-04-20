<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $data = Task::where('complete_status', 0)->where('delete_status', 0)->get();
        return view('task', compact('data'));
    }

    public function addTask(Request $request)
    {
        $taskName = $request->input('task');

        //Check if task is already added
        $task = Task::where('task_name', $taskName)->where('delete_status', '!=', '1')->first();

        if ($task) {
            return response()->json(['status' => 'error', 'message' => 'Task already added, Please add another task!']);
        }

        $data = Task::create([
            'task_name' => $taskName
        ]);

        return response()->json(['status' => 'success', 'message' => 'Task added successfully!', 'data' => $data]);
    }

    public function completeTask(Request $request)
    {
        $taskId = $request->input('task_id');
        $completeStatus = $request->input('complete_status');

        $data = Task::where('id', $taskId)->update([
            'complete_status' => $completeStatus
        ]);

        return response()->json(['status' => 'success', 'message' => 'Task completed successfully!', 'data' => $data]);
    }

    public function deleteTask(Request $request)
    {
        $taskId = $request->input('task_id');

        $data = Task::where('id', $taskId)->update([
            'delete_status' => 1
        ]);

        return response()->json(['status' => 'success', 'message' => 'Task deleted successfully!', 'data' => $data]);
    }

    public function showAllTask()
    {
        $data = Task::where('delete_status', 0)->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
