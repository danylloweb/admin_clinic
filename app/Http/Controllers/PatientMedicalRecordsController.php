<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\PatientMedicalRecordCreateRequest;
use App\Http\Requests\PatientMedicalRecordUpdateRequest;
use App\Repositories\PatientMedicalRecordRepository;
use App\Validators\PatientMedicalRecordValidator;

/**
 * Class PatientMedicalRecordsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PatientMedicalRecordsController extends Controller
{
    /**
     * @var PatientMedicalRecordRepository
     */
    protected $repository;

    /**
     * @var PatientMedicalRecordValidator
     */
    protected $validator;

    /**
     * PatientMedicalRecordsController constructor.
     *
     * @param PatientMedicalRecordRepository $repository
     * @param PatientMedicalRecordValidator $validator
     */
    public function __construct(PatientMedicalRecordRepository $repository, PatientMedicalRecordValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $patientMedicalRecords = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $patientMedicalRecords,
            ]);
        }

        return view('patientMedicalRecords.index', compact('patientMedicalRecords'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PatientMedicalRecordCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(PatientMedicalRecordCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $patientMedicalRecord = $this->repository->create($request->all());

            $response = [
                'message' => 'PatientMedicalRecord created.',
                'data'    => $patientMedicalRecord->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patientMedicalRecord = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $patientMedicalRecord,
            ]);
        }

        return view('patientMedicalRecords.show', compact('patientMedicalRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $patientMedicalRecord = $this->repository->find($id);

        return view('patientMedicalRecords.edit', compact('patientMedicalRecord'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PatientMedicalRecordUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(PatientMedicalRecordUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $patientMedicalRecord = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'PatientMedicalRecord updated.',
                'data'    => $patientMedicalRecord->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'PatientMedicalRecord deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'PatientMedicalRecord deleted.');
    }
}
