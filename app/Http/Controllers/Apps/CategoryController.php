<?php

namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get Category
        $categories = Category::when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        // Render with inertia
        return Inertia::render('Apps/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Apps/Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * Validate request
         */
        $this->validate($request, [
            'image'     => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'name'    => 'required|unique:categories',
            'description' => 'required'
        ]);

        // Upload Image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        /**
         * Create Category
         */
        Category::create([
            'image'     => $image->hashName(),
            'name'    => $request->name,
            'description' => $request->description,
        ]);

        //redirect
        return redirect()->route('apps.categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        // Rander with inertia
        return Inertia::render('Apps/Category/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        // Validate reqeust
        $this->validate($request, [
            'name'      => 'required|unique:categories,name,' . $category->id,
            'description' => 'required',
        ]);

        // Check Image Update
        if ($request->file('image')) {

            // remove old image
            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            // Upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName(true));

            // Update Category with new Image
            $category->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'description' => $request->description,
            ]);
        }

        // Update Category without image
        $category->update([
            'name' => $request->name,
            'description' => $request->description
        ]);


        // redirect
        return redirect()->route('apps.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find By Id Category
        $category = Category::findOrFail($id);

        // Remove image
        Storage::disk('local')->delete('public/categories/' . basename($category->image));

        // Delete user
        $category->delete();

        // redirect
        return redirect()->route('apps.categories.index');
    }
}
