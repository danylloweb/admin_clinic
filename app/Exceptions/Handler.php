<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Prettus\Validator\Exceptions\ValidatorException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    /**
     * @param Request $request
     * @param Throwable $exception
     * @return JsonResponse|Response|ResponseAlias
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return $this->renderValidationException($exception);
        }

        if ($exception instanceof ValidatorException) {
            return $this->renderValidatorException($exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * @param $exception
     * @return JsonResponse
     */
    private function renderValidationException($exception): JsonResponse
    {
        $bag = $exception->validator->getMessageBag();
        Log::error('Validation error', [
            'message' => implode(', ', $this->parseMessages($bag)),
            'errors' => $this->parseMessages($bag)
        ]);
        return response()->json([
            'error' => true,
            'message' => implode(', ', $this->parseMessages($bag)),
            'errors' => $this->parseMessages($bag)
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param $exception
     * @return JsonResponse
     */
    private function renderValidatorException($exception): JsonResponse
    {
        $bag = $exception->getMessageBag();

        return response()->json([
            'error' => true,
            'message' => implode(', ', $this->parseMessages($bag)),
            'errors' => $this->parseMessages($bag)
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param $bag
     * @return array
     */
    private function parseMessages($bag): array
    {
        $messages = [];

        if (is_object($bag)) {
            $bag = json_decode(json_encode($bag), true);
            foreach ($bag as $field) {
                foreach ($field as $m) {
                    $messages[] = $m;
                }
            }
        }

        return $messages;
    }

    /**
     * @param $request
     * @param AuthenticationException $exception
     * @return JsonResponse|ResponseAlias
     */
    public function unauthenticated($request, AuthenticationException $exception): JsonResponse|ResponseAlias
    {
        return response()->json(['error' => true,'message' => 'unauthenticated'],401);
    }
}
