<?php

namespace App\Http\Controllers\Administrators\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Contracts\Repository\ProtocolRepositoryInterface;
use App\Contracts\Repository\BillingPeriodRepositoryInterface;

use Illuminate\Database\QueryException;

use Lang;
use PDF;

class SettingController extends Controller
{
    /** @var \App\Contracts\Repository\ProtocolRepositoryInterface */
    private $protocolRepository;

    /** @var \App\Contracts\Repository\BillingPeriodRepositoryInterface */
    private $billingPeriodRepository;

    public function __construct (
        ProtocolRepositoryInterface $protocolRepository,
        BillingPeriodRepositoryInterface $billingPeriodRepository
    ) {
        $this->protocolRepository = $protocolRepository;
        $this->billingPeriodRepository = $billingPeriodRepository;
    }

    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return view('administrators/settings/index');
    }

    /**
     * Display a generate reports view
     *
     * @return \Illuminate\Http\Response
     */
    public function generate_reports()
    {
        //

        return view('administrators/settings/generate_reports/generate_reports');
    }

    /**
     * Generate a report on the created protocols
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function protocols_report(Request $request)
    {
        //

        $request->validate([
            'initial_date' => 'required|date',
            'ended_date' => 'required|date',
        ]);

        $protocols = $this->protocolRepository->getProtocolsInDatesRange($request->initial_date, $request->ended_date);

        $pdf = PDF::loadView('pdf/generate_reports/protocols_report', [
            'protocols' => $protocols,
            'initial_date' => $request->initial_date,
            'ended_date' => $request->ended_date, 
        ]);

        return $pdf->stream('protocols_report');
    }

    /**
     * Generate a report on the patients flow
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function patients_flow(Request $request)
    {
        //

        $request->validate([
            'initial_date' => 'required|date',
            'ended_date' => 'required|date',
        ]);
        
        try {
            $protocols = $this->protocolRepository->getProtocolsInDatesRange($request->initial_date, $request->ended_date);
        } catch (QueryException $exception) {
            return response(Lang::get('errors.generate_pdf'), 500);
        }
        
        $pdf = PDF::loadView('pdf/generate_reports/patients_flow', [
            'protocols' => $protocols,
            'initial_date' => $request->initial_date,
            'ended_date' => $request->ended_date,
        ]);

        return $pdf->stream('patient_flow');
    }

    /**
     * Generate a report on the debt of social works
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function debt_social_works(Request $request)
    {
        //

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        try {
            $billing_periods = $this->billingPeriodRepository->getAmountBilledByPeriod($request->start_date, $request->end_date);
        } catch (QueryException $exception) {
            return response(Lang::get('errors.generate_pdf'), 500);
        }

        $pdf = PDF::loadView('pdf/generate_reports/debt_social_works', [
            'billing_periods' => $billing_periods,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return $pdf->stream('debt_social_works');
    }
}
