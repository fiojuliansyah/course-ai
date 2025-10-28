<?php

use Illuminate\Support\Facades\Route;
// Import Admin Controllers
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\CourseModuleController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\ModuleMaterialController;
use App\Http\Controllers\StudentCheckoutController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\Student\LearningProgressController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::prefix('student')->name('student.')->group(function () {

        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/my-courses', [StudentDashboardController::class, 'myCourse'])->name('courses.enrolled.index');
        Route::get('/my-courses/{course}', [StudentDashboardController::class, 'myCourseShow'])->name('courses.enrolled.show');
        Route::post('/material/{material}/complete', [LearningProgressController::class, 'completeMaterial'])->name('student.material.complete'); 

        Route::get('/courses/{course}/quiz', [StudentQuizController::class, 'showQuiz'])->name('courses.quiz.show');
        Route::post('/courses/{course}/quiz/submit', [StudentQuizController::class, 'submitQuiz'])->name('courses.quiz.submit');
        Route::get('/courses/{course}/quiz/results', [StudentQuizController::class, 'showResults'])->name('courses.quiz.results');

        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');

        Route::post('/enroll/{course}', [StudentCheckoutController::class, 'enroll'])->name('enroll.start');
        Route::get('/checkout/{enrollment}', [StudentCheckoutController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/{enrollment}/confirm', [StudentCheckoutController::class, 'confirmPayment'])->name('checkout.confirm');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index'); 

        Route::resource('courses', CourseController::class);
        
        Route::get('/courses/{course}/material', [MaterialController::class, 'index'])->name('courses.material.index');
        Route::post('/courses/{course}/material', [MaterialController::class, 'store'])->name('courses.material.store');
        Route::post('/courses/{course}/material/generate', [MaterialController::class, 'generateModules'])->name('courses.material.generate');

        Route::get('/courses/{course}/quizzes/create', [QuizController::class, 'create'])->name('courses.quizzes.create');
        Route::post('/courses/{course}/quizzes', [QuizController::class, 'store'])->name('courses.quizzes.store');
        Route::post('/courses/{course}/quizzes/generate', [QuizController::class, 'generateQuestions'])->name('courses.quizzes.generate');
        Route::put('/courses/{course}/quizzes/{question}', [QuizController::class, 'update'])->name('courses.quizzes.update');
        Route::delete('/courses/{course}/quizzes/{question}', [QuizController::class, 'destroy'])->name('courses.quizzes.destroy');

        Route::post('/modules', [CourseModuleController::class, 'store'])->name('modules.store'); 
        Route::put('/modules/{module}', [CourseModuleController::class, 'update'])->name('modules.update');
        Route::get('/modules/{module}/edit', [CourseModuleController::class, 'edit'])->name('modules.edit');
        Route::delete('/modules/{module}', [CourseModuleController::class, 'destroy'])->name('modules.destroy');

        Route::post('/materials', [ModuleMaterialController::class, 'store'])->name('materials.store');
        Route::put('/materials/{material}', [ModuleMaterialController::class, 'update'])->name('materials.update');
        Route::get('/materials/{material}/edit', [ModuleMaterialController::class, 'edit'])->name('materials.edit');
        Route::delete('/materials/{material}', [ModuleMaterialController::class, 'destroy'])->name('materials.destroy');

        Route::resource('categories', CategoryController::class); 

        Route::resource('users', UserController::class); 

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';