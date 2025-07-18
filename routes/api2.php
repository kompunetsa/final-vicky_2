<?
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Hanya admin bisa GET semua task
    Route::get('/tasks', [TaskController::class, 'index'])->middleware('role:admin');

    // Hanya user login yang bisa create
    Route::post('/tasks', [TaskController::class, 'store']);

    // Lihat task milik sendiri (opsional)
    Route::get('/tasks/{task}', [TaskController::class, 'show']);

    // Update hanya jika owner (opsional)
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->middleware('task.owner');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->middleware('task.owner');

    // Delete hanya jika owner
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->middleware('task.owner');
});
