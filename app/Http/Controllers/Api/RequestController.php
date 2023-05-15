<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RequestStore;
use App\Models\Request;
use App\Models\RequestHistory;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Throwable;

class RequestController extends AbstractApiController
{
    /**
     * @param RequestStore $requestStore
     * @return JsonResponse
     */
    public function store(RequestStore $requestStore): JsonResponse
    {
        try {
            $data = $requestStore->validated();
            $data['user_id'] = Auth::user()->getAuthIdentifier();

            $request = Request::query()
                ->create($data);

            return $this->response($request->toArray());

        } catch (Throwable $throwable) {
            return $this->response(
                [],
                500,
                500,
                errors: ['message' => $throwable->getMessage()]
            );
        }

    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $requests = Request::query()->get()->toArray();

        return $this->response($requests);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string|int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(\Illuminate\Http\Request $request, string|int $id): JsonResponse
    {
        /**
         * @var $requestTemplate Request
         */
        $requestTemplate = Request::query()
            ->findOrFail($id);

        if ($requestTemplate->user_id !== Auth::user()->getAuthIdentifier()) {
            throw new Exception('This task does not belong to you', 400);
        }

        $requestTemplate
            ->update($request->all());

        return $this->response($requestTemplate->refresh()->toArray());
    }

    /**
     * @param string|int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(string|int $id): JsonResponse
    {
        /**
         * @var $requestTemplate Request
         */
        $requestTemplate = Request::query()
            ->findOrFail($id);

        if ($requestTemplate->user_id !== Auth::user()->getAuthIdentifier()) {
            throw new Exception('This task does not belong to you', 400);
        }

        return $this->response(['success' => $requestTemplate->delete()]);
    }

    /**
     * @param string|int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function detail(string|int $id): JsonResponse
    {
        /**
         * @var $requestTemplate Request
         */
        $requestTemplate = Request::query()
            ->findOrFail($id);

        if ($requestTemplate->user_id !== Auth::user()->getAuthIdentifier()) {
            throw new Exception('This task does not belong to you', 400);
        }

        return $this->response($requestTemplate->toArray());
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function run(\Illuminate\Http\Request $request, int|string $id): JsonResponse
    {
        try {
            /**
             * @var $requestTemplate Request
             */
            $requestTemplate = Request::query()
                ->findOrFail($id);

            if ($requestTemplate->user_id !== Auth::user()->getAuthIdentifier()) {
                throw new Exception('This task does not belong to you', 400);
            }

            $headers = $request->get('headers', []);
            $response = Http::withHeaders($headers);

            foreach ($request->all()['params'] as $key => $item) {
                if (File::isFile($item)) {
                    /**
                     * @var $item UploadedFile
                     */
                    $response->attach($key, file_get_contents($item), $item->getClientOriginalName());
                }
            }

            $response = $response->{$requestTemplate->method}($requestTemplate->url, $request->get('params', []));

            $requestData = [
                'user_id' => Auth::user()->getAuthIdentifier(),
                'request_id' => $id,
                'request_data' => $request->all(),
                'response_data' => $response->json(),
                'response_code' => $response->status(),
            ];
            $this->setHistoryRequest($requestData);

            return $this->response($response->json());
        } catch (Throwable $throwable) {
            return $this->response(
                [],
                500,
                500,
                errors: ['message' => $throwable->getMessage()]
            );
        }
    }

    /**
     * @param array $data
     * @return void
     */
    private function setHistoryRequest(array $data): void
    {
        RequestHistory::query()
            ->create($data)->save();
    }

    /**
     * @param string|int $id
     * @return JsonResponse
     */
    public function getHistory(string|int $id): JsonResponse
    {
        try {
            /**
             * @var $history RequestHistory
             */
            $history = RequestHistory::query()
                ->where('request_id', $id)
                ->with('request')
                ->get()
                ->first();

            if (!$history) {
                throw (new ModelNotFoundException)->setModel(
                    get_class(new RequestHistory()), [$id]
                );
            }

            if ($history->user_id !== Auth::user()->getAuthIdentifier()) {
                throw new Exception('This task does not belong to you', 400);
            }

           return $this->response($history->toArray());

        } catch (Throwable $throwable) {
            return $this->response(
                [],
                500,
                500,
                errors: ['message' => $throwable->getMessage()]
            );
        }
    }
}
