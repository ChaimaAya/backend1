<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $authenticatedUser = Auth::user();
    
            $publications = Publication::where('user_id', '!=', $authenticatedUser->id)
                                        ->with('user')
                                        ->get();
            foreach ($publications as $publication) {
                $likes = Like::where('post_id', $publication->id)
                                ->where('like', 1)
                                ->with('user')
                                ->get();
                                
                    $likesCount = $likes->count();
                    $publication->countLikes = $likesCount;
                    $publication->likes = $likes; 
            }
    
            $data = [
                'status' => 200,
                'publications' => $publications
            ];
            return response()->json($data, 200);
        } catch (\Exception $e) {
            $errorData = [
                'status' => 500,
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ];
             return response()->json($errorData, 500);
        }
    }
    public function userProfilePublications(){
        try {
            $authenticatedUser = Auth::user();

            $publications = Publication::where('user_id', $authenticatedUser->id)
                                        ->with('user')
                                        ->get();

            $data = [
                'status' => 200,
                'publications' => $publications
            ];
            return response()->json($data, 200);
        } catch (\Exception $e) {
            $errorData = [
                'status' => 500,
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ];
             return response()->json($errorData, 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    if (Auth::check()) {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:200',
            'file' => 'required|mimes:jpg,png,jpeg,mp4,mov,avi|max:20480',
        ]);

        if ($validator->fails()) {
            $data = [
                'status' => 422,
                'message' => $validator->messages()
            ];
            return response()->json($data, 422);
        } else {
            $authenticatedUser = Auth::user();

            if ($request->has('file')) {
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $path = 'uploads/';
                $file->move($path, $filename);
            }

            $publication = new Publication();
            $publication->description = $request->description;
            $publication->file = $filename;
            $publication->user_id = $authenticatedUser->id;
            $publication->save();

            $data = [
                'status' => 200,
                'message' => 'Données créées avec succès'
            ];
            return response()->json($data, 200);
        }
    } else {
        $data = [
            'status' => 401,
            'message' => 'Utilisateur non authentifié'
        ];
        return response()->json($data, 401);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $publication = Publication::find($id);
            $data = [
                'status' => 200,
                'publications' => $publication
            ];
            return response()->json($data, 200);
        } catch (\Exception $e) {
            $errorData = [
                'status' => 500,
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ];
            return response()->json($errorData, 500);
        }
    }



    public function edit(string $id)
    {
        try {
            $publication = Publication::find($id);
            $data = [
                'status' => 200,
                'publications' => $publication
            ];
            return response()->json($data, 200);
        } catch (\Exception $e) {
            $errorData = [
                'status' => 500,
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ];
            return response()->json($errorData, 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $publication=Publication::find($id);

        $filename = null;

        if ($request->has('fiel')) {
            $file = $request->file('file');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $path='uploads/';
            $file->move($path,$filename);
            }
            $publication->description = $request->description;
            $publication->file=$filename;
            $publication->user_id = $request->user_id;
            $publication->save();


            $data=[
                'status'=>200,
                'message'=>'data update with success'
            ];
            return response()->json($data,200);





    }


    public function destroy(string $id)
    {
        $publication=Publication::find($id);
        $publication->delete();
        $data = [
            'status' => 200,
            'publications' => 'publication deleted with success'
        ];
        return response()->json($data,200);
    }
    public function like($id){
        $post_id = $id;
        $user_id = Auth::user()->id;
        
        $existingLike = Like::where('post_id', $post_id)
                            ->where('user_id', $user_id)
                            ->first();
    
        if ($existingLike) {
            if ($existingLike->like === 1) {
                return response()->json('You have already liked this post.');
            } else {
                $existingLike->like = 1;
                $existingLike->save();
                return response()->json('You disliked this post previously but now you liked it.');
            }
        } else {
            $like = new Like();
            $like->post_id = $post_id;
            $like->user_id = $user_id;
            $like->like = 1;
            $like->save();
            
            
            return response()->json('You liked this post.');
        }
    }
    public function dislike($id){
        $post_id = $id;
        $user_id = Auth::user()->id;
    
        $like = Like::where('post_id', $post_id)
                    ->where('user_id', $user_id)
                    ->first();
    
        if($like) {
            $like->update(['like' => 0]);
            return response()->json('you have disliked this post');
        } else {
            return response()->json('you have not liked this post before');
        }
    }

}
