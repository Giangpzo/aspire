<?php

namespace App\Modules\Loan\DataProvider;

use App\Modules\Common\DataProvider\DatabaseProvider;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\ScheduledRepayment;

class LoanDataProvider extends DatabaseProvider
{
    public $model = Loan::class;

    public function requestLoan($amount, $term)
    {
        return $this->newQuery()->create([
            'customer_id' => auth()->id(),
            'amount' => $amount,
            'term' => $term,
            'status' => Loan::STATUS_PENDING
        ]);
    }

    /**
     * Approve loan
     *
     * @param Loan $loan
     * @param $note
     */
    public function approveLoan(Loan $loan, $notes)
    {
        $loan->status = Loan::STATUS_APPROVED;
        $loan->approver_id = auth()->id();
        $loan->approver_notes = $notes;

        return $loan->save();
    }

    /**
     * Reject loan
     *
     * @param Loan $loan
     * @param $note
     */
    public function rejectLoan(Loan $loan, $notes)
    {
        $loan->status = Loan::STATUS_REJECTED;
        $loan->approver_id = auth()->id();
        $loan->approver_notes = $notes;

        return $loan->save();
    }

    /**
     * If all repayments was paid, change status of loan to paid, too
     *
     * @param Loan $loan
     */
    public function shouldUpdateStatusToPaid(Loan $loan)
    {
        $repaymentsAllPaid = $loan->repayments->every(function ($repayment) {
            return $repayment->status == ScheduledRepayment::STATUS_PAID;
        });

        if ($repaymentsAllPaid) {
            $this->update($loan, [
                'status' => Loan::STATUS_PAID
            ]);
        }
    }
}