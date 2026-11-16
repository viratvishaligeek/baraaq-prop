<?php
namespace Database\Seeders;
use App\Models\Role; use App\Models\User; use Illuminate\Database\Seeder; use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder { public function run():void{$this->call(RolePermissionSeeder::class);$email=env('CMS_ADMIN_EMAIL');$password=env('CMS_ADMIN_PASSWORD');if($email&&$password){$user=User::updateOrCreate(['email'=>$email],['name'=>env('CMS_ADMIN_NAME','Site Administrator'),'password'=>Hash::make($password),'is_active'=>true]);$user->roles()->syncWithoutDetaching([Role::where('slug','administrator')->value('id')]);}} }