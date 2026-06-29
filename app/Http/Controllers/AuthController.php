<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Redirector; 
use Session;
use App\Models\Professionnel;
use App\Models\Etablissement;
use App\Models\Station;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected SmsService $smsService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showlogin()
    {
        return view('auth.login');
    }

    /**
     * connexion des utilisateurs
     * @param Request $request
     */
    public function login(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'mobile' => 'required|digits_between:8,20',
            'password' => 'required|string|min:8',
        ]);

        $credentials = [
            'mobile' => $request->mobile,
            'password' => $request->password,
        ];

        $remember = $request->has('remember'); // Option "souviens-toi de moi"

        // Tentative d'authentification
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user(); // Récupère l'utilisateur authentifié

            // Vérification du rôle de l'utilisateur
            if ($user->role === '01') {
                // Vérifie si l'utilisateur a un établissement enregistré
                $hasEtablissement = Etablissement::where('professionnel_id', $user->id)->exists();

                if ($hasEtablissement) {
                    return redirect()->route('dashboard'); // Redirection vers le tableau de bord
                } else {
                    // Redirection vers la page d'enregistrement de l'établissement
                    session()->flash('type', 'alert-success');
                    session()->flash('message', "Félicitations ! Votre compte a été créé avec succès. Maintenant, il est temps de configurer votre établissement. Veuillez enregistrer les informations relatives à votre établissement pour commencer à utiliser toutes les fonctionnalités de notre plateforme.");
                    return redirect()->route('etablissement.create');
                }
            } else {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Votre rôle n'est pas autorisé à accéder à cette application.");
                Auth::logout();
                return redirect('/login');
            }
        } else {
            // En cas d'informations d'identification incorrectes
            session()->flash('type', 'alert-danger');
            session()->flash('message', 'Informations de connexion incorrectes.');
            return back();
        }
    }

    

    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showregister()
    {
        $data['title'] ='Inscriptions';
        $data['menu'] ='register';
        
        return view('auth.register',$data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ecole  $ecole
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validation des champs
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:professionnels',
            'password' => 'required|string|min:8|confirmed',
            'cgu' => 'accepted', // Vérifie que les conditions générales sont acceptées
        ]);

        // Création du professionnel utilisateur
        $professionnel = Professionnel::create([
            'nom' => html_entity_decode($request->nom),
            'prenoms' => html_entity_decode($request->prenoms),
            'role' => '01', // Rôle par défaut
            'mobile' => html_entity_decode($request->mobile),
            'password' => Hash::make($request->password), // Hash du mot de passe
        ]);

        // Vérification si l'utilisateur a bien été créé
        if (!empty($professionnel)) {
            session()->flash('type', 'alert-success');
            session()->flash('message', 'Votre inscription a été effectuée avec succès');
            
            return redirect('/login');
        } else {
            session()->flash('type', 'alert-danger');
            session()->flash('message', 'Une erreur est survenue');
            
            return back();
        }
    }
	
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showpasswordforget()
    {
        return view('auth.password_forget');
    }

    /**
     * Envoie un SMS via l'API MTarget.
     *
     * @param string $to Numéro du destinataire (avec indicatif)
     * @param string $message Message à envoyer
     * @param string $senderId Nom ou numéro de l'expéditeur
     * @return array Réponse de l'API
     */
    private function sendSms($to, $message, $senderId = 'TOO AUTO')
    {
        $response = $this->smsService->sendSmsMtarget($message, $to, $senderId);

        return [
            'status' => 200,
            'body' => $response,
            'json' => json_decode($response, true),
        ];
    }

    /**
     * Traite la demande de mot de passe oublié et envoie un OTP par SMS
     */
    public function postPasswordForget(Request $request)
    {
        $request->validate([
            'indicatif' => 'required|string|max:10',
            'phone' => 'required',
        ]);

        $indicatif = ltrim(trim($request->indicatif), '+');
        $phone = trim($request->phone);
        $fullPhone = '+' . $indicatif . $phone;
        $otp = rand(100000, 999999);
        session([
            'otp' => $otp,
            'otp_phone' => $fullPhone,
            'otp_indicatif' => $indicatif,
            'phone' => $phone,
        ]);
        $message = "Votre code de réinitialisation est : $otp";
        $smsResult = $this->sendSms($fullPhone, $message);

        if ($smsResult['status'] == 200) {
            return redirect()->route('auth.otp')->with('success', 'Le code OTP a été envoyé par SMS.');
        } else {
            return back()->with('error', "Erreur lors de l'envoi du SMS. Veuillez réessayer.");
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $otpSaisi = $request->otp;
        $otpSession = session('otp');
        $phoneSession = session('otp_phone');
        $phone = session('phone');
        if ($otpSaisi == $otpSession) {
            session(['otp_verified' => true]);
            return redirect()->route('password.reset.form')->with('success', 'Code OTP vérifié. Veuillez choisir un nouveau mot de passe.');
        } else {
            return back()->with('error', 'Code OTP incorrect. Veuillez réessayer.');
        }
    }

    public function resetPassword(Request $request)
    {
        // Vérifier que l'utilisateur a bien validé l'OTP
        if (!session('otp_verified') || !session('otp_phone')) {
            return redirect()->route('auth.otp')->with('error', 'Veuillez valider le code OTP.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $phone = session('phone');
        $user = \App\Models\Professionnel::where('mobile', $phone)->first();
        if (!$user) {
            return back()->with('error', 'Utilisateur introuvable.');
        }
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        // Nettoyer la session OTP
        session()->forget(['otp', 'otp_phone', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.');
    }

}
