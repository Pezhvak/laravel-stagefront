<?php

namespace CodeZero\StageFront\Controllers;

use CodeZero\StageFront\Rules\LoginAndPasswordMatch;
use Illuminate\Routing\Controller;

class StageFrontController extends Controller
{
    /**
     * Show the StageFront login screen.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        if (session('stagefront.unlocked') === true) {
            return redirect('/');
        }

        $liveSite = config('stagefront.live_site');

        if ($liveSite) {
            $liveSite = [
                'url' => $liveSite,
                'host' => parse_url($liveSite, PHP_URL_HOST),
            ];
        }

        session()->flash('url.intended', session()->previousUrl());

        return view('stagefront::login', compact('liveSite'));
    }

    /**
     * Attempt to log a user in to StageFront.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        request()->validate([
            'login' => ['required'],
            'password' => ['required', new LoginAndPasswordMatch()],
        ], [
            'login.required' => trans('stagefront::errors.login.required'),
            'password.required' => trans('stagefront::errors.password.required'),
        ]);

        session()->put('stagefront.unlocked', true);

        return redirect()->intended('/');
    }
}
