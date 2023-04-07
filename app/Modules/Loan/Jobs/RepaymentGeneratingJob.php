<?php

namespace App\Modules\Loan\Jobs;

use App\Modules\Loan\DataProvider\ScheduledRepaymentProvider;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\ScheduledRepayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RepaymentGeneratingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $loan;

    /**
     * Create a new job instance.
     * @param Loan $loan
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // todo now() or request date?
        $today = now();
        $term = $this->loan->term;

        /** @var ScheduledRepaymentProvider $provider */
        $provider = app(ScheduledRepaymentProvider::class);

        for ($i = 1; $i <= $term; $i++) {
            $repaymentDate = date_add($today, date_interval_create_from_date_string('7 days'));
            $provider->create([
                'loan_id' => $this->loan->id,
                'repayment_date' => $repaymentDate,
                'amount' => $this->loan->amount / $term,
                'status' => ScheduledRepayment::STATUS_UNPAID
            ]);
        }
    }
}
