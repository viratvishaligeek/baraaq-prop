<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Menu;
class AppearanceController extends Controller
{
    public function index()
    {
        return view('admin.appearance.index', [
            'menus' => Menu::withCount('allItems')->orderBy('name')->get()
        ]);
    }
}
