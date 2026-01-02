<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        return response()->json(['categories' => $categories], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $data = $request->only('name', 'description');
        $data['slug'] = Str::slug($request->name);

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time(). '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/categories'), $filename);
            $data['image'] = 'uploads/categories/'.$filename;
        }
        $category = Category::create($data);
        return response()->json(['message' => 'Category created successfully', 'category' => $category]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        if(!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json(['category' => $category], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $category = Category::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255|unique:categories,name,' . $id,
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
    ]);

    $category->name = $request->name;
    $category->slug = Str::slug($request->name);
    $category->description = $request->description;

    if ($request->hasFile('image')) {
        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/categories'), $filename);
        $category->image = 'uploads/categories/' . $filename;
    }

    $category->save();

    return response()->json([
        'message' => 'Category updated successfully',
        'category' => $category
    ], 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        if(!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        if($category->image && file_exists(public_path($category->image))){
            unlink($category->image);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
