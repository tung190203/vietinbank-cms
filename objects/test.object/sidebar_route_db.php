Route::prefix('m__1')->group(function () {
    route_creator(App\Http\Controllers\Admin\M__0Controller::class,'m__1');
});

======================================================

{% if  (item.field_name) == 'list_m__1s' %}
    <li class="nav-item">
        <a href="{{ route('admin_m__1_all',{'p_id' : object.product.p_id}) }}" class="nav-link {{ selectedMainMenu == 'm__1' ? 'active' : '' }}">
            <i class="nav-icon  fa fa-minus"></i>
            <p>Quản lý object___title</p>
        </a>
    </li>
{% endif %}

======================================================

php artisan make:migration create_m__1s_table

$table -> string('name',100) -> unique();
$table -> string('slug',50)-> unique()->nullable() ;

//   ------------------------------------------
$table->string('order_no',11) ->default( 999999 ) ;
$table->string('lang_code',10) ->default( "en" ) ;
$table->unsignedInteger('state')->default( 1 ) ;









