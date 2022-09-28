<?php

namespace App\Http\Controllers;

use App\Models\promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class API_promotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return promotion::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $promotion = promotion::create($request->all());
        return  response()->json($promotion, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return promotion::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $promotion = promotion::findOrFail($id);
        $promotion->fill($request->all())->save();

        return response()->json($promotion, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $promotion = promotion::findOrFail($id);
        File::delete("images/" . $promotion->image);

        $promotion->delete();

        return response()->json(null, 204);
    }
}
