<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\LeaveRequestController as UserLeaveController;
use App\Http\Controllers\User\MarriageRequestController as UserMarriageController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLeaveController;
use App\Http\Controllers\Admin\AdminMarriageController;
use App\Http\Controllers\Admin\AdminPersonelController;
use App\Http\Controllers\Admin\AdminLeaveTypeController;
use App\Http\Controllers\Admin\AdminHolidayController;
use App\Http\Controllers\Admin\AdminLetterController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\User\MarriageApplicationController;
use App\Http\Controllers\Admin\AdminMarriageApplicationController;

// Landing Page / Login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/check-nrp', [AuthController::class, 'checkNrp'])->name('register.check_nrp');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');

// User / Anggota Routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Cuti
    Route::get('/leave', [UserLeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [UserLeaveController::class, 'create'])->name('leave.create');
    Route::get('/leave/template', [UserLeaveController::class, 'downloadPermohonanTemplate'])->name('leave.template');
    Route::get('/leave/template/permohonan', [UserLeaveController::class, 'downloadPermohonanTemplate'])->name('leave.template_permohonan');
    Route::get('/leave/template/izin', [UserLeaveController::class, 'downloadIzinTemplate'])->name('leave.template_izin');
    Route::post('/leave/calculate', [UserLeaveController::class, 'calculateDays'])->name('leave.calculate');
    Route::post('/leave', [UserLeaveController::class, 'store'])->name('leave.store');
    Route::get('/leave/{id}', [UserLeaveController::class, 'show'])->name('leave.show');
    Route::get('/leave/{id}/surat', [UserLeaveController::class, 'downloadSuratCuti'])->name('leave.download_surat');
    Route::post('/leave/{id}/cancel', [UserLeaveController::class, 'cancel'])->name('leave.cancel');

    // Nikah
    Route::get('/marriage', [UserMarriageController::class, 'index'])->name('marriage.index');
    Route::get('/marriage/create', [UserMarriageController::class, 'create'])->name('marriage.create');
    Route::post('/marriage', [UserMarriageController::class, 'store'])->name('marriage.store');
    Route::get('/marriage/{id}', [UserMarriageController::class, 'show'])->name('marriage.show');

    // Pengajuan Nikah (New Module)
    Route::get('/pengajuan-nikah', [MarriageApplicationController::class, 'index'])->name('pengajuan_nikah.index');
    Route::get('/pengajuan-nikah/create', [MarriageApplicationController::class, 'create'])->name('pengajuan_nikah.create');
    Route::post('/pengajuan-nikah', [MarriageApplicationController::class, 'store'])->name('pengajuan_nikah.store');
    Route::post('/pengajuan-nikah/draft', [MarriageApplicationController::class, 'saveDraft'])->name('pengajuan_nikah.save_draft');
    Route::get('/pengajuan-nikah/{id}', [MarriageApplicationController::class, 'show'])->name('pengajuan_nikah.show');
    Route::post('/pengajuan-nikah/{id}/documents', [MarriageApplicationController::class, 'uploadDocument'])->name('pengajuan_nikah.upload_document');
    Route::get('/pengajuan-nikah/{id}/documents/{docId}/download', [MarriageApplicationController::class, 'downloadDocument'])->name('pengajuan_nikah.download_document');
    Route::post('/pengajuan-nikah/{id}/submit', [MarriageApplicationController::class, 'submit'])->name('pengajuan_nikah.submit');
    Route::post('/pengajuan-nikah/{id}/generate', [MarriageApplicationController::class, 'generateLetter'])->name('pengajuan_nikah.generate_letter');
    Route::get('/pengajuan-nikah/{id}/letters/{letterId}/download', [MarriageApplicationController::class, 'downloadLetter'])->name('pengajuan_nikah.download_letter');

    // Profil
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [UserProfileController::class, 'update'])->name('profile.update');
});

// Admin / Staf Personalia Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Verification Cuti
    Route::get('/leave', [AdminLeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/{id}', [AdminLeaveController::class, 'show'])->name('leave.show');
    Route::get('/leave/{id}/surat', [AdminLeaveController::class, 'downloadSuratCuti'])->name('leave.download_surat');
    Route::post('/leave/{id}/status', [AdminLeaveController::class, 'updateStatus'])->name('leave.update_status');
    Route::post('/leave/{id}/issue-letter', [AdminLeaveController::class, 'issueSuratCuti'])->name('leave.issue_letter');

    // Verification Nikah (Old)
    Route::get('/marriage', [AdminMarriageController::class, 'index'])->name('marriage.index');
    Route::get('/marriage/{id}', [AdminMarriageController::class, 'show'])->name('marriage.show');
    Route::post('/marriage/{id}/status', [AdminMarriageController::class, 'updateStatus'])->name('marriage.update_status');

    // Pengajuan Nikah (New Module)
    Route::get('/pengajuan-nikah', [AdminMarriageApplicationController::class, 'index'])->name('admin.pengajuan_nikah.index');
    Route::get('/pengajuan-nikah/{id}', [AdminMarriageApplicationController::class, 'show'])->name('admin.pengajuan_nikah.show');
    Route::post('/pengajuan-nikah/{id}/verify', [AdminMarriageApplicationController::class, 'verifyDocument'])->name('admin.pengajuan_nikah.verify_document');
    Route::post('/pengajuan-nikah/{id}/status', [AdminMarriageApplicationController::class, 'updateStatus'])->name('admin.pengajuan_nikah.update_status');
    Route::post('/pengajuan-nikah/{id}/generate', [AdminMarriageApplicationController::class, 'generateLetter'])->name('admin.pengajuan_nikah.generate_letter');
    Route::get('/pengajuan-nikah/{id}/documents/{docId}/download', [AdminMarriageApplicationController::class, 'downloadDocument'])->name('admin.pengajuan_nikah.download_document');
    Route::get('/pengajuan-nikah/{id}/letters/{letterId}/download', [AdminMarriageApplicationController::class, 'downloadLetter'])->name('admin.pengajuan_nikah.download_letter');

    // Nominatif Personel
    Route::get('/personel',                   [AdminPersonelController::class, 'index'])->name('personel.index');
    Route::get('/personel/create',            [AdminPersonelController::class, 'create'])->name('personel.create');
    Route::get('/personel/import',            [AdminPersonelController::class, 'importForm'])->name('personel.import_form');
    Route::post('/personel/import/preview',   [AdminPersonelController::class, 'importPreview'])->name('personel.import_preview');
    Route::post('/personel/import/confirm',   [AdminPersonelController::class, 'importConfirm'])->name('personel.import_confirm');
    Route::get('/personel/export',            [AdminPersonelController::class, 'export'])->name('personel.export');
    Route::post('/personel',                  [AdminPersonelController::class, 'store'])->name('personel.store');
    Route::get('/personel/{id}',              [AdminPersonelController::class, 'show'])->name('personel.show');
    Route::get('/personel/{id}/edit',         [AdminPersonelController::class, 'edit'])->name('personel.edit');
    Route::put('/personel/{id}',              [AdminPersonelController::class, 'update'])->name('personel.update');
    Route::delete('/personel/{id}',           [AdminPersonelController::class, 'destroy'])->name('personel.destroy');

    // Pengguna Web (Akun Anggota)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/link-personel', [AdminUserController::class, 'linkPersonel'])->name('users.link_personel');

    // Log Aktivitas
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');

    // Jenis Cuti & Syarat
    Route::get('/leave-types', [AdminLeaveTypeController::class, 'index'])->name('leave_types.index');
    Route::post('/leave-types', [AdminLeaveTypeController::class, 'store'])->name('leave_types.store');
    Route::put('/leave-types/{id}', [AdminLeaveTypeController::class, 'update'])->name('leave_types.update');

    // Kalender / Hari Libur
    Route::get('/holidays', [AdminHolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays', [AdminHolidayController::class, 'store'])->name('holidays.store');
    Route::delete('/holidays/{id}', [AdminHolidayController::class, 'destroy'])->name('holidays.destroy');

    // Arsip Surat Intern
    Route::get('/letters', [AdminLetterController::class, 'index'])->name('letters.index');
    Route::post('/letters', [AdminLetterController::class, 'store'])->name('letters.store');
    Route::get('/letters/{id}/download', [AdminLetterController::class, 'download'])->name('letters.download');
    Route::delete('/letters/{id}', [AdminLetterController::class, 'destroy'])->name('letters.destroy');

    // Pengaturan Template Persuratan
    Route::get('/templates', [\App\Http\Controllers\Admin\AdminTemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates/upload', [\App\Http\Controllers\Admin\AdminTemplateController::class, 'upload'])->name('templates.upload');
    Route::get('/templates/download/{type}', [\App\Http\Controllers\Admin\AdminTemplateController::class, 'download'])->name('templates.download');
    


    // Pengaturan Struktur & Pejabat
    Route::get('/organization', [\App\Http\Controllers\Admin\OrganizationStructureController::class, 'index'])->name('organization.index');
    Route::post('/organization/unit', [\App\Http\Controllers\Admin\OrganizationStructureController::class, 'storeUnit'])->name('organization.unit.store');
    Route::put('/organization/unit/{unit}', [\App\Http\Controllers\Admin\OrganizationStructureController::class, 'updateUnit'])->name('organization.unit.update');
    Route::post('/organization/assign', [\App\Http\Controllers\Admin\OrganizationStructureController::class, 'assignOfficial'])->name('organization.assign');
    Route::put('/organization/assignment/{assignment}/deactivate', [\App\Http\Controllers\Admin\OrganizationStructureController::class, 'deactivateAssignment'])->name('organization.assignment.deactivate');
});
