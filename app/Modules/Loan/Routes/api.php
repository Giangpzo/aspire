<?php

use App\Modules\Loan\Controllers\LoanController;
use App\Modules\Loan\Controllers\RepaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/',[LoanController::class, 'index'])->name('listAllLoans');

Route::post('/request-loan',[LoanController::class, 'requestLoan'])->name('requestLoan');

Route::prefix('{loan}')->group(function (){
    Route::get('/',[LoanController::class, 'show'])->name('show');
    Route::post('/approve',[LoanController::class,'approveLoanRequest'])->name('approveLoanRequest');
    Route::post('/reject',[LoanController::class,'rejectLoanRequest'])->name('rejectLoanRequest');

    Route::prefix('scheduled-repayment')->name('scheduled-repayment.')->group(function (){
        Route::get('/',[RepaymentController::class, 'index'])->name('index');
        Route::post('/{repayment}/repay',[RepaymentController::class,'repay'])->name('repay');
    });
});
