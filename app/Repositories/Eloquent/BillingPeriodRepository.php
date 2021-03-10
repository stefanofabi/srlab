<?php

namespace App\Repositories\Eloquent;

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
    public function getBillingPeriods($current_date) {
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
}