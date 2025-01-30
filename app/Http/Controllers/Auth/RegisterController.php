<?php

namespace App\Http\Controllers\Auth;

use Throwable;
use Inertia\Inertia;
use App\Rules\TaxNumber;
use Illuminate\Http\Request;
use App\Validation\Validation;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\BTNInputException;
use App\Exceptions\BTNModalException;
use App\Services\Countries\Countries;
use App\Services\Entities\Models\UBO;
use Illuminate\Support\Facades\Crypt;
use App\Services\Entities\Models\User;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use App\Services\Bisnode\BisnodeReport;
use App\Services\Entities\EntityService;
use App\Services\Entities\Models\Entity;
use App\Services\Entities\Models\Person;
use Illuminate\Support\Facades\Validator;
use App\Services\Entities\Events\RegistrationEvent;
use App\Services\Entities\Registration\Registration;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex() 
    {
        session()->forget('registration');

        return Inertia::render('Auth/Register', [
            'complianceOptions' => array_combine(Entity::COMPLIANCES, Entity::COMPLIANCES)
        ]);
    }

    /**
     * Prvi post ob registraciji (pravna ali fizična oseba, profesionalni vlagatelj ali presoja)
     *
     * @param Request $request
     * @return void
     */
    public function postIndex(Request $request) 
    {
        $request->validate([
            'type' => ['required'],
            'compliance' => ['required']
        ]);

        session()->put('registration', [
            'type' => $request->type,
            'compliance' => $request->compliance
        ]);

        return redirect()->route("register.$request->type");
    }

    /**
     * Post za entity registracijo / final step.
     * 
     * @param Request $request
     * @param Validation $validation
     * @param Registration $registration
     * @return RedirectResponse
     */
    public function postEntity(Request $request, Validation $validation, Registration $registration) {

        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }


        // manjka session data iz registration route
        /*if (session()->missing('registration')) {
            return redirect()->route('register');
        }*/

        $request->validate(array_merge(
            $validation->entityData('entityData')
        ));

        $input = $request->input();

        $entity = $registration->registerEntity($input);

        RegistrationEvent::dispatch($entity);

        //dd(session()->all());

        session()->forget('registration');

        return redirect()->route('entities')->with([
            'modal' => [
                'title' => __('modals.register.title'),
                'content' => __('modals.register.success'),
                'status' => 'success',
                'actions' => [[
                    'action' => [
                        // 'type' => 'redirect',
                        'type' => 'close',
                        //'url' => route('login')
                    ],
                    'text' => __('modals.common.confirm')
                ]]
            ]
        ]);
        
    }

    /**
     * Post za enterprise registration / Data step validacija.
     */
    public function postEntityData(Request $request, Validation $validation) 
    {
        $request->validate($validation->entityData());
        return redirect()->back();
    }

}
