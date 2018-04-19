<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Log;

class Payments extends Model
{

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'payments';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loan_id',
        'payment',
        'amount',
        'date',
    ];



    public static $newPayment = [
        'amount'  => 'required|min:0',
        'date'    => 'required|date_format:Y-m-d H:i\Z',
        'payment' => 'required|in:made,missed',
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
        $this->attributes['date'] = date_create_from_format('Y-m-d H:i\Z', $date);   
    }

     /**
      * Date mutator
      */
    public function getDateAttribute()
    {  
        $date = strtotime($this->attributes['date']);  
        return date('Y-m-d H:i\Z', $date);
    }

      
    /**
     * Relation with Loans
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loan()
    {
        return $this->belongsTo(\App\Models\Loans::class, 'loan_id', 'id');
    }

}