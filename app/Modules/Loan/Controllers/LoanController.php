<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Loan\DataProvider\LoanDataProvider;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Requests\Loan\ApproveRequest;
use App\Modules\Loan\Requests\Loan\IndexRequest;
use App\Modules\Loan\Requests\Loan\RejectRequest;
use App\Modules\Loan\Requests\Loan\RequestLoanRequest;
use App\Modules\Loan\Requests\Loan\ShowRequest;
use App\Modules\Loan\Transformers\LoanShowTransformer;
use Illuminate\Http\JsonResponse;

class LoanController extends ApiController
{
    /**
     * List all loans (for both admin and customer)
     * Admin: list all loans
     * Customer: list my loans
     *
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $loans = Loan::query()->paginate($request->get('per_page'));

        return $this->respondSuccess(fractal($loans, new LoanShowTransformer())->toArray(), 'retrieved loans success');
    }

    /**
     * Request loan
     *
     * @param RequestLoanRequest $request
     * @param LoanDataProvider $provider
     * @return JsonResponse
     */
    public function requestLoan(RequestLoanRequest $request, LoanDataProvider $provider)
    {
        $createdLoan = $provider->requestLoan($request->get('amount'), $request->get('term'));

        return $this->respondSuccess(fractal($createdLoan, new LoanShowTransformer())->toArray(), 'requested loan success');
    }

    /**
     * View Loan information
     *
     * @param ShowRequest $request
     * @param Loan $loan
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Loan $loan)
    {
        // no need use policy check, because applied global scope in Loan model
        return $this->respondSuccess(
            fractal($loan, new LoanShowTransformer())->toArray(),
            'retrieved loan data success');
    }

    /**
     * Approve loan request
     *
     * @param ApproveRequest $request
     * @param LoanDataProvider $provider
     * @param Loan $loan
     * @return JsonResponse
     */
    public function approveLoanRequest(ApproveRequest $request, LoanDataProvider $provider, Loan $loan)
    {
        $provider->approveLoan($loan, $request->get('notes'));

        return $this->respondSuccess(
            fractal($loan->refresh(), new LoanShowTransformer())->toArray(),
            'approve loan success');
    }

    /**
     * Reject loan request
     *
     * @param RejectRequest $request
     * @param LoanDataProvider $provider
     * @param Loan $loan
     * @return JsonResponse
     */
    public function rejectLoanRequest(RejectRequest $request, LoanDataProvider $provider, Loan $loan)
    {
        $provider->rejectLoan($loan, $request->get('notes'));

        return $this->respondSuccess(
            fractal($loan->refresh(), new LoanShowTransformer())->toArray(),
            'reject loan success');
    }
}