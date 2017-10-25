<?php

namespace Eightfold\Registered\Controllers;

use Eightfold\Registered\Controllers\BaseController;

use Auth;
use Validator;
use Illuminate\Http\Request;

use Eightfold\Registered\Models\UserEmailAddress;

class EmailsController extends BaseController
{
    public function addEmailAddress($username, Request $request)
    {
        $this->validatorEmailAddress($request->all())->validate();
        Auth::user()->registration->addEmail($request->email);
        // Redirect, if necessary
        $message = [
            'type' => 'success',
            'title' => 'Successfully added email address',
            'body' => '<p>Your email address was added.</p>'
        ];
        return back()
            ->with('message', $message);
    }

    private function validatorEmailAddress(array $data)
    {
        return Validator::make($data, [
            'email' => UserEmailAddress::validation()
        ]);
    }

    public function makePrimary($username, Request $request)
    {
        $user = Auth::user();
        $registration = $user->registration;
        $registration->defaultEmail = $request->address;
        $registration->save();

        return back()
            ->with('message', [
                'type' => 'success',
                'title' => 'Default address changed',
                'body' => '<p>The default email address for your account was updated.</p>'
            ]);
    }

    public function delete(Request $request)
    {
        Auth::user()->registration->deleteEmail($request->address);
        Auth::user()->save();
        $message = [
            'type' => 'success',
            'title' => 'Email address successfully deleted'
        ];
        return back()
            ->with('message', $message);
    }
}
