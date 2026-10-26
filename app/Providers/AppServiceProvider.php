<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use App\Models\Admin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (!is_dir($target)) {
            mkdir($target, 0777, true);
        }

        if (!is_link($link) && !file_exists($link)) {
            @symlink($target, $link);
        }

        $writeAudit = function ($model, string $action, ?string $description = null, $old = null, $new = null) {
            $actorId = null;
            $actorType = 'system';
            $actorName = 'System';

            $actorRole = null;
            $actorContact = null;

            if (Auth::check()) {
                $user = Auth::user();
                $actorId = $user->id;
                $actorType = 'user';
                $actorName = $user->name ?? ($user->username ?? ($user->email ?? 'User'));
                $actorRole = $user->position ?? 'User';
                $actorContact = $user->phone ?? $user->email ?? null;
            } elseif (session()->has('admin_id')) {
                $admin = Admin::find(session('admin_id'));
                if ($admin) {
                    $actorId = $admin->id;
                    $actorType = 'admin';
                    $actorName = $admin->name ?: $admin->username;
                    $actorRole = 'Admin';
                    $actorContact = $admin->email ?? null;
                }
            }

            $ip = null;
            $agent = null;
            if (!app()->runningInConsole() && request()) {
                $ip = request()->ip();
                $agent = request()->userAgent();
            }

            AuditLog::create([
                'actor_id' => $actorId,
                'actor_type' => $actorType,
                'actor_name' => $actorName,
                'actor_role' => $actorRole,
                'actor_contact' => $actorContact,
                'action' => $action,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'auditable_name' => method_exists($model, 'name') ? $model->name : null,
                'description' => $description,
                'old_values' => $old ? json_encode($old, JSON_UNESCAPED_UNICODE) : null,
                'new_values' => $new ? json_encode($new, JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => $ip,
                'user_agent' => $agent,
                'metadata' => [
                    'model' => class_basename($model),
                ],
            ]);
        };

        foreach ([Payment::class, Shop::class, User::class] as $modelClass) {
            $modelClass::created(function ($model) use ($writeAudit) {
                $description = class_basename($model) . ' created ' . json_encode($model->getAttributes(), JSON_UNESCAPED_UNICODE);
                $writeAudit($model, 'created', $description, null, $model->getAttributes());
            });

            $modelClass::updated(function ($model) use ($writeAudit) {
                $description = class_basename($model) . ' updated ' . json_encode($model->getChanges(), JSON_UNESCAPED_UNICODE);
                $writeAudit($model, 'updated', $description, $model->getOriginal(), $model->getChanges());
            });

            $modelClass::deleted(function ($model) use ($writeAudit) {
                $description = class_basename($model) . ' deleted ' . json_encode($model->getAttributes(), JSON_UNESCAPED_UNICODE);
                $writeAudit($model, 'deleted', $description, $model->getAttributes(), null);
            });
        }
    }
}
