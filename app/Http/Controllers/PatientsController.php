<?php

namespace App\Http\Controllers;

use App\Services\PatientService;
use App\Validators\PatientValidator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class PatientsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PatientsController extends Controller
{
    /**
     * @var PatientService
     */
    protected $service;

    /**
     * @var PatientValidator
     */
    protected $validator;

    /**
     * PatientsController constructor.
     *
     * @param PatientService $service
     * @param PatientValidator $validator
     */
    public function __construct(PatientService $service, PatientValidator $validator)
    {
        $this->validator  = $validator;
        $this->service    = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyPatientPhone(Request $request):\Illuminate\Http\JsonResponse
    {
        return response()->json($this->service->verifyPatientPhone($request->all()));

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request): JsonResponse
    {
        $limit     = $request->query->get('limit', 15);
        $cacheName = str_replace($request->url(), '', $request->fullUrl());
        $objects   = Cache::store('redis')->tags('patients')->remember($cacheName, 12000, function () use ($limit) {
            return $this->service->all($limit);
        });
        return response()->json($objects, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getImageProfile(Request $request): JsonResponse
    {
        $phone = $request->get('phone');
        return $this->getImagebyPhone($phone);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getImageProfileById(Request $request): JsonResponse
    {
        $id      = $request->get('id');
        $patient = $this->service->find($id, true);
        return $this->getImagebyPhone($patient->chat_id);
    }

    /**
     * @param $phone
     * @return JsonResponse
     */
    private function getImagebyPhone($phone): JsonResponse
    {
        return response()->json($this->service->getlinkImageByPhone($phone), 200);
    }

    /**
     * @param int $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function patientShow(int $id): Factory|View|\Illuminate\Foundation\Application|Application
    {
        $patient = $this->service->find($id, true);
        $photo   = $this->service->getlinkImageByPhone($patient->chat_id);
        return view('patients.show', [
            'title'    => 'Paciente',
            'subtitle' => 'Detalhe do(a) Paciente',
            'patient'  => $patient,
            'photo'    => $photo->success?? 'https://ui-avatars.com/api/?name='.urlencode($patient->name),
        ]);
    }

    public function patientChat(int $id): Factory|View|\Illuminate\Foundation\Application|Application
    {
        $patient = $this->service->find($id, true);
        $photo   = $this->service->getImageToContactWhatsApp($patient->chat_id);
        return view('patients.chat', [
            'title'    => 'Paciente',
            'subtitle' => 'Chat com Paciente',
            'patient'  => $patient,
            'photo'    => $photo->success?? 'https://ui-avatars.com/api/?name='.urlencode($patient->name),
        ]);
    }


}
