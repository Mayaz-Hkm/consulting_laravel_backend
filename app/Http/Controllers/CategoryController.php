<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller{





    public function getCategoriesWithSections()
    {
        // جلب التصنيفات مع الأقسام الفرعية
        $categories = Category::with('sections')->get();

        // التحقق إذا كانت البيانات موجودة
        if ($categories->isEmpty()) {
            return response()->json([
                'status' => 0,
                'message' => 'No categories found',
                'data' => []
            ]);
        }

        // إذا كانت البيانات موجودة
        return response()->json([
            'status' => 1,
            'message' => 'Categories with sections retrieved successfully',
            'data' => $categories
        ]);
    }


    public function showCategory($id)
    {
        $category = Category::with('sections')->find($id);

        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid Category ID' ], 404);
        }

        return response()->json([
            'status' => 1,
            'message' => 'All Sections in Category (' . $category->categoryName . ')',
            'data' => $category->sections
        ]);
    }

}
git init
git remote add origin https://github.com/Mayaz-Hkm/consulting-laravel-backend.git
git branch -M main
git add .
git commit -m "Initial commit for backend"
git push -u origin main
