<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
   public function showUser($id=null){
       if($id == ''){
           $users=User::get();
           return response()->json(['users'=>$users],200);
       }else{
           $users=User::find();
           return response()->json(['users'=>$users],200);
       }
   }

   //   post api for single user
   public function addUser(Request $request){
       if ($request->ismethod('post')){
           $data=$request->all();
          // return $data;
           $rules=[
               'name'=>'required',
               'email'=>'required|email',
               'password'=>'required',
           ];
           $customMessage=[
               'name.required'=>'Name is Required',
               'email.required'=>'Email is Required',
               'password.required'=>'Password is Required',
           ];
           $validator=Validator::make( $data,$rules,$customMessage);
           if($validator->fails()){
               return response()->json($validator->errors(),422);
           }
           $user=new User();
           $user->name=$data['name'];
           $user->email=$data['email'];
           $user->password=bcrypt($data['password']);
           $user->save();
           $message='User successfully added';
           return response()->json(['message'=>$message],201);
       }
   }

//   post api for multiple user
   public function addMultipleUser(Request $request){
        if ($request->ismethod('post')){
            $data=$request->all();
            // return $data;
            $rules=[
                'users.*.name'=>'required',
                'users.*.email'=>'required|email',
                'users.*.password'=>'required',
            ];
            $customMessage=[
                'users.*.name.required'=>'Name is Required',
                'users.*.email.required'=>'Email is Required',
                'users.*.password.required'=>'Password is Required',
            ];
            $validator=Validator::make( $data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            foreach ($data['users'] as $adduser){
                $user=new User();
                $user->name=$adduser['name'];
                $user->email=$adduser['email'];
                $user->password=bcrypt($adduser['password']);
                $user->save();
                $message='User successfully added';

            }
            return response()->json(['message'=>$message],201);

        }
    }

//   put api for update user
   public function updateUserDetails(Request $request,$id){
       if ($request->ismethod('put')){
           $data=$request->all();
           // return $data;
           $rules=[
               'name'=>'required',
               'password'=>'required',
           ];
           $customMessage=[
               'name.required'=>'Name is Required',
               'password.required'=>'Password is Required',
           ];
           $validator=Validator::make( $data,$rules,$customMessage);
           if($validator->fails()){
               return response()->json($validator->errors(),422);
           }
           $user=User::findorFail($id);
           $user->name=$data['name'];
           $user->password=bcrypt($data['password']);
           $user->save();
           $message='User successfully Updated';
           return response()->json(['message'=>$message],202);
       }
   }

//   patch api for single record
    public function updateSingleRecord(Request $request,$id){
        if ($request->ismethod('patch')){
            $data=$request->all();
            // return $data;
            $rules=[
                'name'=>'required',
            ];
            $customMessage=[
                'name.required'=>'Name is Required',
            ];
            $validator=Validator::make( $data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            $user=User::findorFail($id);
            $user->name=$data['name'];
            $user->save();
            $message='User successfully Updated';
            return response()->json(['message'=>$message],202);
        }
    }

//   delete api for single user
    public function deleteUser($id=null){
        User::findorFail($id)->delete();
        $message='User successfully Deleted';
        return response()->json(['message'=>$message],200);
    }

//   delete api for single user with json
    public function deleteUserJson(Request $request){
        if($request->isMethod('delete')){
            $data=$request->all();
            User::where('id',$data['id'])->delete();

            $message='User successfully Deleted';
            return response()->json(['message'=>$message],200);
        }

    }

//   delete api for multiple user
    public function deleteMultipleUser($ids){
        $ids=explode(',',$ids);
        User::whereIn('id',$ids)->delete();
        $message='User successfully Deleted';
        return response()->json(['message'=>$message],200);

    }

//delete api for multiple user with json
public function deleteMultipleUserJson(Request $request){
       $header=$request->header("Authorization");
       if ($header==""){
           $message='Authorization is required';
           return response()->json(['message'=>$message],422);
       }else{
           if($header=="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IlByb25heSBEYXMiLCJpYXQiOjE1MTYyMzkwMjJ9.40KJh1oZbTN61A1iz4mqtoJhAZ55vDpsNYhWrnnF3Rw"){
               if ($request->ismethod('delete')){
                   $data=$request->all();
                   User::whereIn('id',$data['ids'])->delete();

                   $message='User successfully Deleted';
                   return response()->json(['message'=>$message],200);
               }
           }else{
               $message='Authorization does not match';
               return response()->json(['message'=>$message],422);
           }
       }

}

//Register APi using Passport
public function registerUserUsingPassport(Request $request){
    if ($request->ismethod('post')){
        $data=$request->all();
        // return $data;
        $rules=[
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required',
        ];
        $customMessage=[
            'name.required'=>'Name is Required',
            'email.required'=>'Email is Required',
            'password.required'=>'Password is Required',
        ];
        $validator=Validator::make( $data,$rules,$customMessage);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $user=new User();
        $user->name=$data['name'];
        $user->email=$data['email'];
        $user->password=bcrypt($data['password']);
        $user->save();
        if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
            $user=User::where('email',$data['email'])->first();
            $access_token=$user->createToken($data['email'])->access_token;
            User::where('email',$data['email'])->update(['access_token'=>$access_token]);
            $message='User successfully registered';
            return response()->json(['message'=>$message,'access_token'=>$access_token],201);
        }else{
            $message='Opps! Somethings went Wrong';
            return response()->json(['message'=>$message],422);
        }

    }
}

}
