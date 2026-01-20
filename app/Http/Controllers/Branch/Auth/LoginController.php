<?php

namespace App\Http\Controllers\Branch\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:branch', ['except' => ['logout']]);
    }

    public function captcha($tmp)
    {
        $phrase = new PhraseBuilder;
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 100, $height = 40);
        Session::put('default_captcha_code_branch', $builder->getPhrase());

        $headers = [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
        ];

        ob_start(); // Capture the binary output
        $builder->output();
        $imageData = ob_get_clean();

        return Response::make($imageData, 200, $headers);
    }


    /**
     * @return Application|Factory|View
     */
    public function login(): View|Factory|Application
    {
        $logo = Helpers::get_business_settings('logo');
        $logo = Helpers::onErrorImage(
            $logo,
            asset('storage/app/public/ecommerce') . '/' . $logo,
            asset('public/assets/admin/img/160x160/img2.jpg'),
            'ecommerce/');
        return view('branch-views.auth.login', compact('logo'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1 && !$request?->set_default_captcha) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                        $response = $value;

                        $gResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                            'secret' => $secret_key,
                            'response' => $value,
                            'remoteip' => \request()->ip(),
                        ]);

                        if (!$gResponse->successful()) {
                            $fail(translate('ReCaptcha Failed'));
                        }
                    },
                ],
            ]);
        } else {
            if (strtolower($request->default_captcha_value) != strtolower(Session('default_captcha_code_branch'))) {
                return back()->withErrors(\App\CentralLogics\translate('Captcha Failed'));
            }
        }

        if(Session::has('default_captcha_code_branch')) {
            Session::forget('default_captcha_code_branch');
        }

        if (auth('branch')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            return redirect()->route('branch.dashboard');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['Credentials does not match.']);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        auth()->guard('branch')->logout();
        return redirect()->route('branch.auth.login');
    }
}
