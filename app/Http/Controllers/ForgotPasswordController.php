<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\PasswordReset;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Artisan;

class ForgotPasswordController extends Controller
{
    /**
     * @var PasswordResetRepository
     */
    protected $repository;
    protected $userRepository;
    protected $passwordReset;

    /**
     * @param PasswordResetRepository $repository
     * @param UserRepository $userRepository
     * @param PasswordReset $passwordReset
     */
    public function __construct(PasswordResetRepository $repository,
                                UserRepository          $userRepository,
                                PasswordReset           $passwordReset)
    {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
        $this->passwordReset  = $passwordReset;
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $data_create = [
            'email' =>  $request->get('email'),
            'token' => str_replace('/','',bcrypt($request->get('email')))
        ];
        $pass_reset = $this->repository->skipPresenter()->create($data_create);
        $this->passwordReset->create($data_create);

        $findWhere = ['email' => $request->get('email')];
        $user      = $this->userRepository->skipPresenter()->findWhere($findWhere)->first();

        $url  = "https://suportetamas.impactadigital.net/new/password/$pass_reset->token";
        $to   = ['name' => $user->name, 'email'=> $user->email];
        $html = View::make('forgotPassword',['name' => $user->name, 'link' => $url])->render();

        $data = [
            'from'    => ['name' => 'Suporte Tamas', 'email' => 'contato@epione.com.br'],
            'to'      => $to,
            'subject' => 'Recuperação de Senha',
            'html'    => $html,
        ];
        $this->sendMail($data);
        return response()->json(['error'=> false, 'message' => 'Reset de senha enviado!']);
    }

    /**
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $pass_reset = $this->repository->skipPresenter()->findWhere(['token' => $request->get('token')])->first();
        $passReset  = $this->passwordReset->find($request->get('_id'));
        $user       = $this->userRepository->skipPresenter()->findWhere(['email'=> $pass_reset->email])->first();
        $this->userRepository->update(['password' => bcrypt($request->get('password'))],$user->id);
        $passReset->delete();
        $this->repository->delete($pass_reset->id);
        return response()->json(['data' => ['message' => 'Senha atualizada com sucesso!']]);
    }

    /**
     * @param $data
     * @throws GuzzleException
     */
    private function sendMail($data):void
    {

        $endpoint = "http://notification_tamas/sendMail";
        $options  = [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($data)
        ];
        $this->getHttpClient()->request('POST', $endpoint, $options)->getBody();

    }

    /**
     * @return string[]
     */
    public function startSchedule(): array
    {
        Artisan::call('schedule:run');
        return ['message'=>'ok'];
    }

    public function startGetLastCommand(): array
    {
        Artisan::call('conversaions:run');
        return ['message'=>'ok'];
    }
}
