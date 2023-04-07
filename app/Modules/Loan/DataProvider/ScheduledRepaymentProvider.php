<?php

namespace App\Modules\Loan\DataProvider;

use App\Modules\Common\DataProvider\DatabaseProvider;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\ScheduledRepayment;
use Illuminate\Support\HigherOrderTapProxy;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ScheduledRepaymentProvider extends DatabaseProvider
{
    public $model = ScheduledRepayment::class;

    /**
     * Get repayments
     *
     * @param Loan $loan
     * @param bool $paginate
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getRepayments(Loan $loan, $paginate = true)
    {
        $query = $this->newQuery()
            ->where('loan_id', $loan->id)
            ->orderBy('repayment_date', 'ASC');

        return $this->paginateResult($query, $paginate);
    }

    /**
     * Repay
     *
     * @param ScheduledRepayment $repayment
     * @param $amount
     * @return HigherOrderTapProxy|mixed
     */
    public function repay(ScheduledRepayment $repayment, $amount)
    {
       return $this->update($repayment, [
            'actual_amount' => $amount,
            'actual_repayment_date' => today(),
            'status' => ScheduledRepayment::STATUS_PAID
        ]);
    }
}