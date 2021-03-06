<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;

use App\Contracts\Repository\BillingPeriodRepositoryInterface;

use App\Models\BillingPeriod; 

use App\Exceptions\QueryValidateException;

use Lang;

final class BillingPeriodRepository implements BillingPeriodRepositoryInterface
{
    protected $model;

    /**
     * BillingPeriodRepository constructor.
     *
     * @param BillingPeriod $billingPeriod
     */
    public function __construct(BillingPeriod $billingPeriod)
    {
        $this->model = $billingPeriod;
    }

    public function all()
    {
        return $this->model->orderBy('start_date', 'DESC')->orderBy('end_date', 'DESC')->get();
    }

    public function create(array $data)
    {
        if ($data['start_date'] > $data['end_date']) {
            throw new QueryValidateException(Lang::get('billing_periods.start_date_after_end_date'));
        }

        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        if ($data['start_date'] > $data['end_date']) {
            throw new QueryValidateException(Lang::get('billing_periods.start_date_after_end_date'));
        }

        return $this->model->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Returns a list of the latest billing periods.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBillingPeriods() {
        $current_date = date('Y-m-d');
        
        $start_date = date("Y-m-d", strtotime($current_date."- 6 month"));
        $end_date = date("Y-m-d", strtotime($current_date."+ 6 month"));

        return $this->model
            ->where('start_date', '>=', $start_date)
            ->where('end_date', '<=', $end_date)
            ->orderBy('start_date', 'ASC')
            ->orderBy('end_date', 'ASC')
            ->get();
    }

    /**
     * Returns current billing period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCurrentBillingPeriod() 
    {
        $current_date = date('Y-m-d');

        return $this->model
            ->where('start_date', '<=', $current_date)
            ->where('end_date', '>=', $current_date)
            ->first();

    }

    /**
     * Returns a list billing periods filtered.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadBillingPeriods($filter) {
        return $this->model
            // label column is required
            ->select('id', 'name as label', 'start_date', 'end_date')
            ->where('name', 'like', "%$filter%")
            ->get()
            ->toJson();
    }

    public function getAmountBilledByPeriod($start_date, $end_date) 
    {   
        $practices = DB::table('practices')
        ->select('protocol_id', DB::raw('SUM(amount) as total_amount'))
        ->groupBy('protocol_id');

        return $this->model
            ->select('billing_periods.name', 'billing_periods.start_date', 'billing_periods.end_date', 'social_works.name as social_work', 'practices.total_amount', DB::raw('COALESCE(SUM(payment_social_works.amount), 0.0) as total_paid'))
            ->join('protocols', 'billing_periods.id', 'protocols.billing_period_id')
            ->join('plans', 'protocols.plan_id', '=', 'plans.id')
            ->join('social_works', 'plans.social_work_id', '=', 'social_works.id')
            ->leftJoinSub($practices, 'practices', function ($join) {
                $join->on('protocols.id', '=', 'practices.protocol_id');
            })
            ->leftJoin('payment_social_works', 'billing_periods.id', 'payment_social_works.billing_period_id')
            ->where('billing_periods.start_date', '>=', $start_date)
            ->where('billing_periods.end_date', '<=', $end_date)
            ->groupBy('billing_periods.id', 'billing_periods.name', 'billing_periods.start_date', 'billing_periods.end_date', 'social_works.name', 'practices.total_amount')
            ->orderBy('billing_periods.start_date', 'ASC')
            ->orderBy('billing_periods.end_date', 'ASC')
            ->orderBy('social_works.name', 'ASC')
            ->get();
    }
}