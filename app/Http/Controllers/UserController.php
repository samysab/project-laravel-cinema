<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //Display users
    function showUsers(){
        $users = User::where('isAuthor', 0)
            ->get();

        return view('displayUsers', ['users' => $users]);
    }

    function showAuthors(){
        $users = User::where('isAuthor', 1)
            ->get();

        return view('displayUsers', ['users' => $users]);
    }

    //Form to create users
    function formUser(){
        $users = User::where('isAuthor', 0);

        return view('formUser', ['users' => $users]);
    }

    function formAuthor(){
        $authors = User::where('isAuthor', 1);

        return view('formAuthor', ['authors' => $authors]);
    }

    function createUser(Request $request){

        $validatedData = $request->validate([
            'name' => ['required', 'max:255', 'min: 2', 'unique:users'],
            'password' => 'required|confirmed|min:6',
            'email' => ['required', 'unique:users'],
//            'isAuthor' => ['required', Rule::in([0])]

        ],
            [
                'name.required' => 'Le champs :attribute est requis.',
                'max' => 'Le champs :attribute ne doit pas comporter plus de :max caracteres',
                'min' => 'Le champs :attribute ne doit pas comporter moins de :min caracteres',

                'password.required' => 'Entrez un mot de passe',
                'confirmed' => 'Le mot de passe de confirmation ne correspond pas',
                'password.min' => 'Votre mot de passe doit faire au minimum 6 caractères',

                'email.required' => 'Entrez votre email',
                'email.unique' => 'Adresse mail déjà existante'
            ]
        );

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->isAuthor = 0;

        $user->save();
        return redirect('users');
    }



    function createAuthor(Request $request){

        $user = new User();

        $validatedData = $request->validate([
            'name' => ['required', 'max:255', 'min: 2',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('isAuthor', 1);
                })],
        ],
            [
                'name.required' => 'Le champs :attribute est requis.',
                'max' => 'Le champs :attribute ne doit pas comporter plus de :max caracteres',
                'min' => 'Le champs :attribute ne doit pas comporter moins de :min caracteres',

            ]
        );

        $user = new User();
        $user->name = $request->name;
        $user->email = "n/a";
        $user->password = "n/a";
        $user->isAuthor = 1;

        $user->save();
        return redirect('authors');
    }


    function deleteUser(Request $request){

        $user = User::find($request->user_id);
        $user->delete();

        return redirect('users')->with('status', 'User Deleted !');
    }

    function deleteAuthor(Request $request){

        $user = User::find($request->author_id);
        $user->delete();

        return redirect('authors')->with('status', 'Author Deleted !');
    }

    function updateUser(Request $request){
        $user = User::find($request->user_id);

        $data = User::where('id', $user->id)
            ->update([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password)
            ]);

        return redirect('users')->with('status', 'User updated !');
    }

    function updateAuthor(Request $request){
        $user = User::find($request->author_id);

        $data = User::where('id', $user->id)
            ->update([
                "name" => $request->name,
            ]);

        return redirect('authors')->with('status', 'User updated !');
    }

    /*form to update user*/
    function formUserUpdate(Request $request){
        $user = User::find($request->user_id);
        $data = User::where('id', $user->id)
            ->get();

        return view('displayUserUpdate', ['user' => $user]);
    }

    function formAuthorUpdate(Request $request){
        $author = User::find($request->author_id);
        $data = User::where('id', $author->id)
            ->get();

        return view('displayAuthorUpdate', ['author' => $author]);
    }

}
