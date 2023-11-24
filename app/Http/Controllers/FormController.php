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
            'categories' => $categories
        ]]);
    }

    public function loadCategories() {
        $user = User::first();
        if (!$user) $user = $this->createUser();
        $user_categories = explode(",", trim($user->categories));
        $categories = collect(Category::get())->map(function ($item) use ($user_categories) {
            $option = $this->checkSelection($user_categories, $item->option);
            return trim($option);
        })->toArray();
        return $categories;
    }

    public function checkSelection($user_categories, $option) {
        $value = $option;
        foreach($user_categories as $user_cat) {
            if (str_contains($option, 'value="'.$user_cat.'"')) $value = substr_replace(trim($option), "selected ", 8, 0);
        }
        return $value;
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
            'categories' => $this->loadCategories()
        ]]);
    }
}
