<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained()->restrictOnDelete();
        });

        Schema::create('client_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('feature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_app_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default('feature');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('budget')->nullable();
            $table->string('status', 20)->default('submitted')->index();
            $table->unsignedBigInteger('agreed_price')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('feature_request_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        // Roles live here (not only in the seeder) so existing admins keep panel access right after deploy.
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $admin = Role::findOrCreate('admin', 'web');
        Role::findOrCreate('client', 'web');

        DB::table('model_has_roles')->insertOrIgnore(
            DB::table('users')->pluck('id')->map(fn ($id) => [
                'role_id' => $admin->id,
                'model_type' => 'App\Models\User',
                'model_id' => $id,
            ])->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_request_notes');
        Schema::dropIfExists('feature_requests');
        Schema::dropIfExists('client_apps');
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('client_id'));
        Role::whereIn('name', ['admin', 'client'])->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
