<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FormController extends Controller
{
    
    public function getCategories() { 
        $categories = $this->loadCategories();
        return response()->json(['data' => [
            'name' => User::first()->name,
            'categories' => $categories,
            'category_dom' => collect(Category::get())->map(function ($item) {
                return trim($item->option);
            })->toArray()
        ]]);
    }

    public function loadCategories() {
        $user = User::first();
        if (!$user) $user = $this->createUser();
        $user_categories = explode(",", trim($user->categories));
        // $categories = collect(Category::get())->map(function ($item) use ($user_categories) {
        //     $option = $this->checkSelection($user_categories, $item->option);
        //     return trim($option);
        // })->toArray();
        return $user_categories;
    }

    public function createUser() {
        $user = new User();
        $user->name = "";
        $user->categories = "";
        $user->email = 'user@gmail.com';
        $user->password = Hash::make('password');
        $user->save();
        return $user;
    }


    public function saveData(Request $request)
    {
        $user = User::first();
        $user->name = $request->name ?? '';
        $user->categories = $request->categories ?? '';
        $user->accepted_terms = true;
        $user->save();

        return response()->json(['data' => [
            'message' => "saved",
            'name' => $user->name,
            'categories' => $this->loadCategories(),
        ]]);
    }
}
