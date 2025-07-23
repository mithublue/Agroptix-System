<?php

namespace App\Http\Middleware;

use App\Models\Batch;
use App\Services\StateTransitionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceStateTransitions
{
    /**
     * The state transition service instance.
     *
     * @var StateTransitionService
     */
    protected $stateTransitionService;

    /**
     * Create a new middleware instance.
     *
     * @param StateTransitionService $stateTransitionService
     * @return void
     */
    public function __construct(StateTransitionService $stateTransitionService)
    {
        $this->stateTransitionService = $stateTransitionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $model = 'batch')
    {
        // Only process for PATCH, PUT, and POST requests where status might change
        if (!in_array($request->method(), ['PATCH', 'PUT', 'POST'])) {
            return $next($request);
        }

        // Get the model instance from the route
        $modelInstance = $request->route($model);

        // If no model instance or not a Batch, continue
        if (!$modelInstance || !$modelInstance instanceof Batch) {
            return $next($request);
        }

        // Get the new status from the request
        $newStatus = $request->input('status');

        // If status isn't being changed, continue
        if (!$newStatus || $newStatus === $modelInstance->status) {
            return $next($request);
        }

        // Validate the state transition
        $validation = $this->stateTransitionService->validateTransition($modelInstance, $newStatus);

        // If the transition is not valid, return an error response
        if (!$validation['is_valid']) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['error'],
                    'errors' => [
                        'status' => [$validation['message']],
                    ],
                    'validation' => $validation,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $validation['error'],
                ])
                ->with('validation', $validation);
        }

        // If we get here, the transition is valid, so continue with the request
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        // This method can be used for any cleanup or post-request processing
        // For example, logging the state transition after the response is sent
    }
}
