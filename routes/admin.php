<?php
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
Route::middleware(['auth'])->prefix('dashboard')->group(function () {

    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('home_admin');

    // ======================================================== START

    function route_creator($class_controller,$object_name){
       
        Route::get('/', [$class_controller, 'index'])->name('admin_'.$object_name.'');
       
        Route::post('/', [$class_controller, 'saveDataIndex'])->name('admin_'.$object_name.'_save_data_index');
        Route::get('create', [$class_controller, 'edit'])->name('admin_'.$object_name.'_create');
        Route::get('edit/{app_obj}', [$class_controller, 'edit'])->name('admin_'.$object_name.'_edit');
        Route::post('save/{app_obj?}', [$class_controller, 'save'])->name('admin_'.$object_name.'_save');
        Route::get('delete/{id}', [$class_controller, 'delete'])->name('admin_'.$object_name.'_delete');
        Route::get('clone/{app_obj}', [$class_controller, 'clone'])->name('admin_'.$object_name.'_clone');
        Route::post('delete-check-box', [$class_controller, 'deleteCheckBox'])->name('admin_'.$object_name.'_delete_checkbox');

        // Route::get('p/{p_id?}', [$class_controller, 'index'])->name('admin_'.$object_name.'_all')->defaults('p_id', '1');
        // Route::get('create/p/{p_id?}', [$class_controller, 'edit1'])->name('admin_'.$object_name.'_create_all');
    }   

    // -------------------------------------------------------------

    Route::prefix('product')->group(function () {
        route_creator(App\Http\Controllers\Admin\ProductController::class,'product');
    });
    Route::prefix('product_category')->group(function () {
        route_creator(App\Http\Controllers\Admin\ProductcategoryController::class,'product_category');
    });

    Route::prefix('game')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\GameController::class, 'index'])->name('admin_game');
        Route::get('reward/{gift_id?}', [App\Http\Controllers\Admin\GameController::class, 'show_reward'])->name('admin_game_reward');
    });

    Route::prefix('vr_area')->group(function () {
        route_creator(App\Http\Controllers\Admin\VrareaController::class,'vr_area');
    });
    Route::prefix('vr_popup')->group(function () {
        route_creator(App\Http\Controllers\Admin\VrpopupController::class,'vr_popup');
        // Route::get('group/{groupslug}/area/{areaslug}', function($groupslug,$areaslug){
        //     return [$groupslug,$areaslug];
        // })->name('admin_hhhh');
        Route::get('group/{groupslug}/area/{areaslug?}', [App\Http\Controllers\Admin\VrpopupController::class, 'index_filter'])->name('admin_vrpopup_filter');
    });
    Route::prefix('vr_popup_group')->group(function () {
        route_creator(App\Http\Controllers\Admin\VrpopupgroupController::class,'vr_popup_group');
    });

    Route::prefix('guestbook')->group(function () {
        route_creator(App\Http\Controllers\Admin\GuestBookController::class,'guestbook');
    });

    Route::get('/videos', function () {
        $videos = Storage::disk('public')->files('videos');
        return view('admin.videos.index', compact('videos'));
    })->name('videos');

    Route::post('/videos/upload', function (Request $request) {
        if (!$request->hasFile('video')) {
            return response()->json(['error' => 'Không tìm thấy file video'], 400);
        }

        $file = $request->file('video');

        // 1. Lấy tên gốc của file (bao gồm cả đuôi extension, ví dụ: "video nha mau_sanh chinh.mp4")
        $originalNameWithExt = $file->getClientOriginalName();

        // 2. Tách tên và đuôi file để làm sạch (tránh lỗi font chữ tiếng Việt/khoảng trắng trên URL)
        $filenameOnly = pathinfo($originalNameWithExt, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        // Làm sạch tên: "video nha mau_sanh chinh" -> "video-nha-mau-sanh-chinh"
        $cleanFilename = Str::slug($filenameOnly);

        // Nối lại đuôi file thành: "video-nha-mau-sanh-chinh.mp4"
        $finalFilename = $cleanFilename . '.' . $extension;

        // Trường hợp trùng tên file đã tồn tại trong thư mục, tự động chèn thêm timestamp để tránh ghi đè
        if (Storage::disk('public')->exists('videos/' . $finalFilename)) {
            $finalFilename = $cleanFilename . '-' . time() . '.' . $extension;
        }

        // 3. Sử dụng storeAs để lưu chính xác với tên chúng ta cấu hình
        $path = $file->storeAs('videos', $finalFilename, 'public');
        $filename = basename($path);

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'url' => '/storage/videos/' . $filename
        ]);
    })->name('videos.upload');

    Route::delete('/videos/{filename}', function ($filename) {
        $disk = Storage::disk('public');
        $filePath = 'videos/' . $filename;
        clearstatcache(true, $disk->path($filePath));
        if (!$disk->exists($filePath)) {
            return redirect()->route('videos')->with('error', 'Video không tồn tại hoặc đã bị xóa trước đó!');
        }
        try {
            $fullPath = $disk->path($filePath);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
            return redirect()->route('videos')->with('success', 'Đã xóa video thành công!');
        } catch (\Exception $e) {
            return redirect()->route('videos')->with('error', 'Hệ thống đang xử lý luồng file video này. Vui lòng đợi 3-5 giây và thử lại!');
        }
    })->name('videos.delete');
    // ======================================================== END
    
    
    Route::prefix('user')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin_user');
        Route::post('save/{user?}', [App\Http\Controllers\Admin\UserController::class, 'save'])->name('admin_user_save');
        Route::get('create', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin_user_create');
        Route::get('edit/{user}', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin_user_edit');
        Route::get('delete/{user}', [App\Http\Controllers\Admin\UserController::class, 'delete'])->name('admin_user_delete');
    });
    
    Route::prefix('setting')->group(function () {
        Route::get('/general', [App\Http\Controllers\Admin\SettingController::class, 'general'])->name('admin_setting_general');
        Route::get('/product', [App\Http\Controllers\Admin\SettingController::class, 'product'])->name('admin_setting_product');
        Route::get('/payment', [App\Http\Controllers\Admin\SettingController::class, 'payment'])->name('admin_setting_payment');
        Route::get('/social', [App\Http\Controllers\Admin\SettingController::class, 'social'])->name('admin_setting_social');
        Route::get('/seo', [App\Http\Controllers\Admin\SettingController::class, 'seo'])->name('admin_setting_seo');
        Route::post('/save', [App\Http\Controllers\Admin\SettingController::class, 'save'])->name('admin_setting_save');
    });

    Route::get('filemanager', [App\Http\Controllers\Admin\FileManagerController::class, 'index'])->name('admin_file_manager');

    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        return redirect()->back()->with('success', 'Xóa cache thành công');
    })->name('admin_clear_cache');

});
