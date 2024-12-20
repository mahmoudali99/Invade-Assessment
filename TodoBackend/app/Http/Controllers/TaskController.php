<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function getAllTasks(Request $request)
{
    // Get the authenticated user
    $user = $request->user();
    
    // Retrieve request parameters
    $search = $request->input('search');
    $sort_by = $request->input('sort_by', 'created_at'); // Default to 'created_at'
    $sort_order = $request->input('sort_order', 'desc'); // Default to 'desc'
    $categories = $request->input('categories', []);
    $statuses = $request->input('statuses', []);
    
    // Build the query
    $query = Task::where('userId', $user->id)
                 ->where('status', '!=', 'deleted');

    // Apply search filter
    if (!empty($search)) {
        $query->where('title', 'like', "%{$search}%") // Adjust 'title' to your column name
              ->orWhere('description', 'like', "%{$search}%");
    }

    // Apply categories filter
    if (!empty($categories)) {
        $query->whereIn('categoryId', $categories); // Adjust 'category_id' to your column name
    }

    // Apply statuses filter
    if (!empty($statuses)) {
        $query->whereIn('status', $statuses);
    }

    // Apply sorting
    $query->orderBy($sort_by, $sort_order);

    // Paginate results
    $tasks = $query->paginate(10);


    // Return response
    return response()->json($tasks);
}


    public function addTask(Request $request)
    {
        $request->validate([
            'categoryId' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dueDate' => [
            'required',
            'date',
            function ($attribute, $value, $fail) {
                if (Carbon::parse($value)->isPast()) {
                    $fail('The due date must be a future date.');
                }
            },
        ],
        ]);

        $task = Task::create([
            'userId' => $request->user()->id,
            'categoryId' => $request->categoryId,
            'title' => $request->title,
            'description' => $request->description,
            'dueDate' => $request->dueDate,
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

        $task->update($request->all());
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
        $tasks = Task::where('status', 'deleted')->where('userId', $request->user()->id)->paginate(10);
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
    public function getCategories(Request $request) {
        $categories = Category::all();
        return response()->json(['categories' => $categories]);
    }
}
