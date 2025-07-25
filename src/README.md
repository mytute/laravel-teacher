
install '"laravel/ui": "^4.6"' package.  
```bash 
 root@22753bc6b0e4:/var/www/html# composer require laravel/ui
```

to set ui for auth laravel run following command   
```bash 
root@22753bc6b0e4:/var/www/html# php artisan ui bootstrap --auth
```

### above command will change  

* Installs the basic **Bootstrap layout** for your application.
* Updates `resources/sass/app.scss` to include Bootstrap styles.
* Adds required Bootstrap JavaScript in `resources/js/bootstrap.js`.
* Updates `webpack.mix.js` to compile Bootstrap assets.

Generates basic authentication functionality, including:

* 📁 **Views**:

  * `resources/views/auth/login.blade.php`
  * `resources/views/auth/register.blade.php`
  * `resources/views/auth/passwords/*` (reset links)
  * `resources/views/layouts/app.blade.php`
  * `resources/views/home.blade.php`

* 📄 **Routes**:

  * Adds `Auth::routes();` to your `routes/web.php` for:

    * `/login`
    * `/register`
    * `/logout`
    * `/password/reset`
    * `/home` (dashboard)

* 📁 **Controllers**:

  * Installs Laravel’s default auth controllers inside:

    * `app/Http/Controllers/Auth/`

Users will be able to:

* Register
* Login
* Reset password
* Logout
* Be redirected to a `/home` page upon login



then run following commands   

```bash
root@22753bc6b0e4:/var/www/html# npm install #Install NPM packages
root@22753bc6b0e4:/var/www/html# npm run dev #Compile frontend assets
root@22753bc6b0e4:/var/www/html# php artisan migrate #Run migrations
```

Installs the Laravel Sanctum package into your project  
```bash 
root@22753bc6b0e4:/var/www/html# composer require laravel/sanctum 
```

Publishes Sanctum's configuration and migration files to your project.  
```bash 
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
php artisan migrate
```
above command will create 'config/sanctum.php' file and 'personal_access_tokens' table.  


to add new two fields(login_id and role) to 'users' table  
```bash 
php artisan make:migration add_login_id_and_role_to_users_table --table=users

```

update above created migration file with following code.   
```bash 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('login_id')->after('id');
            $table->string('role')->default('user')->after('login_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['login_id', 'role']);
        });
    }
};
```
apply above change run mirate   
```bash 
 php artisan migrate
```

update 'User' model  
> app/Models/User.php  
```php 
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'login_id', 'password', 'role',
    ];

    protected $hidden = [
        'remember_token', 'created_at', 'updated_at', 'email_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getUserRoleName() {
        $user = auth()->user();
        return $user->role;
    }

    public static function getUserName() {
        $user = auth()->user();
        return $user->name;
    }
}
```

now need to create following tables and update models.   

| Table              | Purpose                                                             |
| ------------------ | ------------------------------------------------------------------- |
| `roles`            | List of all role names (e.g., admin, user)                          |
| `modules`          | Logical grouping of permissions (e.g., Users, Posts)                |
| `permissions`      | List of actions (e.g., view\_user, edit\_post)                      |
| `role_permissions` | Pivot table for many-to-many relation between roles and permissions |


create roles table  
```bash 
php artisan make:migration create_roles_table
```

```bash 
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->timestamps();
});
```

> app/Models/Role.php  
```bash 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}
```

create modules table  
```bash 
php artisan make:migration create_modules_table
```

```bash 
Schema::create('modules', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->timestamps();
});
```
>app/Models/Module.php 
```bash 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
```

create permissions table  
```bash 
php artisan make:migration create_permissions_table
```

```bash 
Schema::create('permissions', function (Blueprint $table) {
    $table->id();
    $table->string('action');
    $table->foreignId('module_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

> app/Models/Permission.php  
```bash 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['action', 'module_id'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
```

create role_permissions pivot table  
```bash 
php artisan make:migration create_role_permissions_table
```

```bash 
Schema::create('role_permissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('role_id')->constrained()->onDelete('cascade');
    $table->foreignId('permission_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

update user table with roles   
```bash 
php artisan make:migration add_role_id_to_users_table --table=users
```
```bash 
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
});
```
user model file need to update  
```bash
public function role()
{
    return $this->belongsTo(Role::class);
}
```


apply changes by run 'migrate' command  
```bash
php artisan migrate
```

### Create the Seeder File  
```bash 
php artisan make:seeder RolePermissionSeeder  
```

> database/seeders/RolePermissionSeeder.php  
```bash 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Step 1: Clean old data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('modules')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Step 2: Create Roles
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole  = Role::create(['name' => 'User']);

        // Step 3: Create Modules
        $userModule         = Module::create(['name' => 'User Management']);
        $roleModule         = Module::create(['name' => 'Role']);
        $fileTransferModule = Module::create(['name' => 'File Transfer Management']);

        // Step 4: Define permissions
        $actions = ['view','add','edit','delete'];
        $permissions = [];

        foreach ([$userModule,$roleModule,$fileTransferModule] as $module) {
            foreach ($actions as $action) {
                $permissions[] = Permission::create([
                    'module_id' => $module->id,
                    'action'    => $action,
                ]);
            }
        }

        // Step 5: Assign all permissions to Admin
        $adminRole->permissions()->sync(array_column($permissions, 'id'));

        // Step 6: Assign limited permissions to User
        $userPermissions = Permission::where(function ($query) use ($fileTransferModule, $userModule) {
            $query->where('module_id', $fileTransferModule->id)
                  ->orWhere(function ($subQuery) use ($userModule) {
                      $subQuery->where('module_id', $userModule->id)
                               ->where('action', 'view');
                  });
        })->pluck('id')->toArray();

        $userRole->permissions()->sync($userPermissions);
    }
}
```

Register the Seeder in DatabaseSeeder.php   
```bash
public function run()
{
    // Optional: Users table
    // $this->call(UsersTableSeeder::class);

    $this->call(RolePermissionSeeder::class);
}
```

run seeder using following command  
```bash 
php artisan db:seed
```

### Create custom middleware   

```bash 
php artisan make:middleware RoleMiddleware
```

> app/Http/Middleware/RoleMiddleware.php   
```php 
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect('/login'); // Redirect if not authenticated
        }

        $user = Auth::user();

        // Check if the user's role matches one of the allowed roles
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized action.'); // Show 403 error
        }

        return $next($request);
    }
}
```

register middleware in Kernal (add the middleware in the $routeMiddleware array)    
> app/Http/Kernel.php  
```php 
protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    // ...
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

example that how to use above routes middleware.   
```php 
// for single role  
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'role:Admin']);

// for Multiple roles  
Route::get('/file-transfer', function () {
    return view('file.transfer');
})->middleware(['auth', 'role:Admin,Manager']);
```


