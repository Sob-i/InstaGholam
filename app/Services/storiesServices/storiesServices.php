<?php

namespace App\Services\storiesServices;


use App\Models\story\storyModel;

class storiesServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }

    public function NewStory($request,$user)
    {
        $requestFile = $request->file('media');
        $type = strstr($requestFile->getClientMimeType(), '/', true);
        $username = strstr($user->email, '@', true);
        $file = $this->fileName($requestFile, $username ,$type );

        $story = storyModel::create([
            'user_id' => $user->id ,
            'media' => $file,
            'media_type' => $type,
            'expires_at' => now()->addDay(),
            'audience' => $request->input('privacy'),
        ]);

        if ($story) {
            return response()->json([
                'success' => true,
                'message' => 'story added successfully',
                'type' => 'success',
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'story not added',
                'type' => 'error'
            ]);
        }
    }
    private function fileName($file , $name , $type)
    {
        $fileName = $name . time(). 'story' . '.' . $file->getClientOriginalExtension();
        $this->moveFile($file, $fileName,$name, $type);
        return $fileName;
    }
    private function moveFile($file , $fileName, $name , $type)
    {
        $file->move(public_path('users/stories/' . $type . '/' . $name), $fileName);
    }
}
