<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Log;

class Loans extends Model
{

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'loans';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'term',
        'rate',
        'date',
    ];



    public static $createLoans = [
        'amount' => 'required|min:0',
        'term'   => 'required|integer',
        'rate'   => 'required|numeric',
        'date'   => 'required|date_format:Y-m-d H:i\Z',
    ];

    public static $getLoansByDate = [
        'from'   => 'required|date_format:Y-m-d H:i\Z',
        'to'   => 'required|date_format:Y-m-d H:i\Z',
    ];

 
    /**
     * Relation with user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('GoShow\Models\User');
    }

    /**
     * Date mutator 
     *
     * @param type $date
     */
     public function setDateAttribute($date)
     {
        $this->attributes['date'] = date_create_from_format('Y-m-d H:i\Z',$date);   
     }

     /**
     * Date mutator
     *
     */
     public function getDateAttribute()
     {  
        $date = strtotime($this->attributes['date']);  
        return date('Y-m-d H:i\Z',$date);
     }

     public function getRawDate()
     {
        $date = date_create_from_format('Y-m-d H:i:s',$this->attributes['date']);  
        return $date;
     }

     /**
     * Relation with payments
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
     public function payments()
     {
         return $this->hasMany(\App\Models\Payments::class, 'loan_id');
     }

     /**
     * Function to calculate installment
     */
     public function installment(){
        $r = $this->rate/12;
        $term = $this->term;
        $amount = $this->amount;
        return round(($r + $r / ( pow(1+$r, $term)- 1))*$amount,2);
     }


     /**
     * Function to get the balance
     */
     public function getCurrentBalance()
     {
        $allPayments = $this->payments();
        $total = $this->installment()*$this->term;
        foreach($allPayments as $payment)
        {
            if($payment->payment == 'made'){
                $total = $total - $payment->amount; 
            }
        }

        return $total;
     }

     public function getBalanceUntilDate($date)
     {
        $allPayments = $this->payments()->where('date', '<=', $date);
        $total = $this->installment()*$this->term;
        foreach($allPayments as $payment)
        {
            if($payment->payment == 'made'){
                $total = $total - $payment->amount; 
            }
        }

        return $total;
     }


     /**
     * Function to check if there is some payment registered for a
     * particular date.
     */
     public function thereIsPaymentForDate($date){
        $paymentsRegisters = $this->payments()
                                  ->whereMonth('date', '=', $date->format('m'))
                                  ->whereYear('date', '=', $date->format('Y'))
                                  ->count();
        
        if($paymentsRegisters > 0){
            return true;
        }

        return false;
     }


}