<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Listing;

class ListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $listings = Listing::orderBy('created_at', 'desc')->get();
      
      if (count($listings) > 0) {
        return response()->json(["success" => true, "data" => $listings]);
      }
      else {
        return response()->json(["success" => false, "data" => []]);
      }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $data = json_decode($request->getContent(), true);    
      $data = filter_var_array($data, FILTER_SANITIZE_STRING);

      // validate inputs
      $validator = Validator::make($data, [
          'name'    => 'required',
          'email'   => 'email'
        ]
      );

      // if validation fails
      if($validator->fails()) {
        return response()->json(["success" => false, "errors" => $validator->errors(), "message" => "failed validation", "data" => $data]);
      }

      $listing = Listing::create($data);
      
      if(!is_null($listing)) {            
        return response()->json(["success" => true, "errors" => [], "message" => "listing created"]);
      }    
      else {
        return response()->json(["success" => false, "errors" => [], "message" => "listing not created"]);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $listing = Listing::find($id);
      
      if (!is_null($listing)) {
        return response()->json(["success" => true, "data" => $listing]);
      }
      else {
        return response()->json(["success" => false, "data" => []]);
      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
      $data = json_decode($request->getContent(), true);    
      $data = filter_var_array($data, FILTER_SANITIZE_STRING);

      // validate inputs
      $validator = Validator::make($data, [
          'name'    => 'required',
          'email'   => 'email'
        ]
      );

      // if validation fails
      if($validator->fails()) {
        return response()->json(["success" => false, "errors" => $validator->errors(), "message" => "failed validation", "data" => $data]);
      }

      $rows_updated = Listing::where("id", $id)->update($data);
    
      if ($rows_updated == 1) {            
        return response()->json(["success" => true, "errors" => null, "message" => "listing updated"]);
      }    
      else {
        return response()->json(["success" => false, "errors" => null, "message" => "listing not updated"]);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $listing = Listing::find($id);

      if (!is_null($listing)) {
        $listing->delete();
        return response()->json(["success" => true, "errors" => null, "message" => "listing removed"]);
      }
      else {
        return response()->json(["success" => true, "errors" => null, "message" => "listing not found"]);
      }
    }
}
