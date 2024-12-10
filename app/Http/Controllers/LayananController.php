<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Exception;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $data = Layanan::all();
            return response()->json([
                "status" => true,
                "message" => "Get succesful",
                "data" => $data
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                "status" => true,
                "message" => "Something went wrong",
                "data" => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $data = Layanan::create($request->all());
            return response()->json([
                "status" => true,
                "message" => "Create succesful",
                "data" => $data
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                "status" => true,
                "message" => "Something went wrong",
                "data" => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{
            $data = Layanan::find($id);
            return response()->json([
                "status" => true,
                "message" => "Get succesful",
                "data" => $data
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                "status" => true,
                "message" => "Something went wrong",
                "data" => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            $data = Layanan::find($id);
            $data->update($request->all());
            return response()->json([
                "status" => true,
                "message" => "Update succesful",
                "data" => $data
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                "status" => true,
                "message" => "Something went wrong",
                "data" => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $data = Layanan::find($id);
            $data->delete();
            return response()->json([
                "status" => true,
                "message" => "Delete succesful",
                "data" => $data
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                "status" => true,
                "message" => "Something went wrong",
                "data" => $e->getMessage()
            ], 400);
        }
    }
}
