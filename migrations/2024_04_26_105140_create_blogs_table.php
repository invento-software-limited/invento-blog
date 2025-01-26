<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug');
            $table->boolean('status')->default(true);
            $table->integer('display_order')->nullable();
            $table->string('meta_title')->nullable();
            $table->mediumText('meta_description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->mediumText('short_description')->nullable();
            $table->text('content')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('display_order')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->unsignedBigInteger('blog_category_id');
            $table->string('category_name')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('slug')->nullable();
            $table->string('banner')->nullable();
            $table->string('meta_title')->nullable();
            $table->mediumText('meta_description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Insert permissions for the blog module
        $permissions_list = [
            'manage blog' => ['view blogs','add and update blog','delete blog','view blog categories','add and update blog category','delete blog category']
        ];

        foreach ($permissions_list as $key => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create([
                    'name' => $permission,
                    'prefix' => $key,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_categories');

        $permissions =  DB::table('permissions')->where('prefix', 'manage blogs')->pluck('id')->toArray();
        DB::table('role_has_permissions')->whereIn('permission_id', $permissions)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permissions)->delete();
        DB::table('permissions')->whereIn('id', $permissions)->delete();
    }
};
