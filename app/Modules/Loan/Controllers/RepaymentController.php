<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Loan\DataProvider\LoanDataProvider;
use App\Modules\Loan\DataProvider\ScheduledRepaymentProvider;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\ScheduledRepayment;
use App\Modules\Loan\Requests\Repayment\IndexRequest;
use App\Modules\Loan\Requests\Repayment\RepayRequest;
use App\Modules\Loan\Transformers\RepaymentTransformer;
use Illuminate\Http\JsonResponse;

class RepaymentController extends ApiController
{
    private ScheduledRepaymentProvider $provider;

    public function __construct(ScheduledRepaymentProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get repayments of loan
     *
     * @param IndexRequest $request
     * @param Loan $loan
     * @return JsonResponse
     */
    public function index(IndexRequest $request, Loan $loan)
    {
        $repayments = $this->provider->getRepayments($loan);

        return $this->respondSuccess(
            fractal($repayments, new RepaymentTransformer())->toArray(),
            'retrieve repayments success'
        );
    }

    /**
     * Repay the repayment
     *
     * @param RepayRequest $request
     * @param Loan $loan
     * @param ScheduledRepayment $repayment
     * @return JsonResponse
     */
    public function repay(RepayRequest $request, Loan $loan, ScheduledRepayment $repayment)
    {
        $amount = $request->get('amount');
        $this->provider->repay($repayment, $amount);

        /** @var LoanDataProvider $loanProvider */
        $loanProvider = app(LoanDataProvider::class);
        $loanProvider->shouldUpdateStatusToPaid($loan);

        return $this->respondSuccess(
            fractal($repayment->refresh(), new RepaymentTransformer())->toArray(),
            'repay success'
        );
    }
}