<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); // معرف فريد للسلة
            
            // user_id: للربط مع المستخدم المسجل
            // nullable: لأن السلة قد تكون لزائر غير مسجل
            // onDelete('cascade'): إذا تم حذف المستخدم، يتم حذف سلته
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // session_id: لتخزين سلة الزائر غير المسجل
            // nullable: لأن السلة قد تكون لمستخدم مسجل
            $table->string('session_id')->nullable();
            
            // total: إجمالي قيمة السلة
            // default(0): القيمة الافتراضية صفر
            $table->decimal('total', 10, 2)->default(0);
            
            // timestamps: لتخزين وقت الإنشاء والتحديث
            $table->timestamps();

            // إضافة فهرس للبحث السريع
            // يساعد في تحسين أداء البحث عن السلة
            $table->index(['user_id', 'session_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
}; 