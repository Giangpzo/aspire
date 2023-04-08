<?php

namespace App\Modules\Loan\Models;

use Database\Factories\Loan\RepaymentFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScheduledRepayment extends Model
{
    use HasFactory;

    protected $table = 'scheduled_repayments';

    protected $guarded = [];

    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;

    /**
     * Loan
     *
     * @return BelongsTo
     */
    protected function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class,'loan_id','id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return RepaymentFactory::new();
    }
}