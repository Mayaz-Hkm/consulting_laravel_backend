<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expert;
use App\Models\Section;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // جلب التصنيفات مع الأقسام الفرعية
    public function getCategoriesWithSections()
    {
        $categories = Category::with('sections')->paginate(10);  // التصفح

        if ($categories->isEmpty()) {
            return response()->json([
                'status' => 0,
                'message' => 'No categories found',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ]);
    }

    // جلب قسم معين بناءً على المعرف
    public function showCategory($id)
    {
        $category = Category::with('sections')->find($id);

        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid Category ID' // النص بدون ترجمة
            ], 404);
        }

        return response()->json([
            'status' => 1,
            'message' => 'All Sections in Category (' . $category->categoryName . ')',
            'data' => $category->sections
        ]);
    }

    // البحث عن الخبراء بناءً على التقييم
    public function searchExpertsByRating($categoryId, Request $request)
    {
        // التحقق من صحة المدخلات
        $validated = $request->validate([
            'rating' => 'nullable|numeric|min:0|max:5', // التحقق من قيمة التقييم
        ]);

        $rating = $request->input('rating', 0);

        // جلب التصنيف مع الأقسام والخبراء
        $category = Category::with(['sections.experts' => function ($query) use ($rating) {
            $query->where('rate', '>=', $rating);
        }])->find($categoryId);

        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid Category ID'
            ], 404);
        }

        // تجميع الخبراء وترتيبهم حسب التقييم
        $experts = $category->sections
            ->flatMap(fn($section) => $section->experts)
            ->sortByDesc('rate')
            ->values(); // إعادة تعيين المفاتيح

        return response()->json([
            'status' => 1,
            'message' => 'Experts filtered by rating',
            'data' => $experts
        ]);
    }

    // Fetch categories
    public function getCategories()
    {
        $categories = Category::all(['id', 'CategoryName']);
        return response()->json($categories);
    }

    // Fetch Sections


     public function getSections($category_id)
     {
         // التأكد من أن الـ category_id موجود
         if (!$category_id) {
             return response()->json(['error' => 'Category ID is required'], 400);
         }

         // جلب الأقسام المرتبطة بالـ category_id
         $sections = Section::where('category_id', $category_id)
             ->get(['id', 'sectionName']);

         // التحقق إذا لم يتم العثور على بيانات
         if ($sections->isEmpty()) {
             return response()->json(['message' => 'No sections found for this category.'], 404);
         }

         // إرجاع البيانات كـ JSON
         return response()->json($sections);
     }

}
