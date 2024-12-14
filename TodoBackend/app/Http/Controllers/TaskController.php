<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function getAllTasks(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Retrieve tasks for the authenticated user, excluding deleted ones
        $tasks = Task::where('user_id', $user->id) // assuming 'user_id' column is used for the relationship
                     ->where('status', '!=', 'deleted')
                     ->paginate(10);

        return response()->json($tasks);
    }

    public function addTask(Request $request)
    {
        $request->validate([
            'categoryId' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = Task::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->categoryId,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Task added successfully', 'task' => $task]);
    }

    public function editTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,completed',
        ]);

        $task->update($request->only(['title', 'description', 'status']));
        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }

    public function softDeleteTask($id)
    {
        $task = Task::findOrFail($id);
        $task->update(['status' => 'deleted']);
        return response()->json(['message' => 'Task soft deleted']);
    }

    public function toggleTaskStatus($id)
    {
        $task = Task::findOrFail($id);
        $task->status = $task->status === 'pending' ? 'completed' : 'pending';
        $task->save();

        return response()->json(['message' => 'Task status toggled', 'task' => $task]);
    }

    public function restoreTask($id)
    {
        $task = Task::where('id', $id)->where('status', 'deleted')->firstOrFail();
        $task->update(['status' => 'completed']);

        return response()->json(['message' => 'Task restored successfully', 'task' => $task]);
    }

    public function getDeletedTasks(Request $request)
    {
        $tasks = Task::where('status', 'deleted')->paginate(10);
        return response()->json($tasks);
    }

    public function addCategory(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $category = Category::create($request->all());
        return response()->json(['message' => 'Category added successfully', 'category' => $category]);
    }
}
