<?php

namespace App\Http\Controllers\Administrators\Protocols;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Laboratory\Repositories\Patients\PatientRepositoryInterface;
use App\Laboratory\Repositories\Protocols\ProtocolRepositoryInterface;
use App\Laboratory\Repositories\Prescribers\PrescriberRepositoryInterface;
use App\Laboratory\Repositories\BillingPeriods\BillingPeriodRepositoryInterface;

use App\Laboratory\Prints\Worksheets\PrintWorksheetContext;
use App\Laboratory\Prints\Protocols\Our\PrintOurProtocolContext;

use Lang;

class OurProtocolController extends Controller
{

    /** @var \App\Laboratory\Repositories\Protocols\ProtocolRepositoryInterface */
    private $protocolRepository;

    /** @var \App\Laboratory\Repositories\Patients\PatientRepositoryInterface */
    private $patientRepository;

    /** @var \App\Laboratory\Repositories\Prescribers\PrescriberRepositoryInterface */
    private $prescriberRepository;

    /** @var \App\Laboratory\Repositories\BillingPeriods\BillingPeriodRepositoryInterface */
    private $billingPeriodRepository;

    /** @var \App\Laboratory\Prints\Worksheets\PrintWorksheetContext */
    private $printWorksheetContext;

    /** @var \App\Laboratory\Prints\Protocols\Our\PrintOurProtocolContext */
    private $printOurProtocolContext;

    public function __construct (
        ProtocolRepositoryInterface $protocolRepository, 
        PatientRepositoryInterface $patientRepository, 
        PrescriberRepositoryInterface $prescriberRepository,
        BillingPeriodRepositoryInterface $billingPeriodRepository,
        PrintWorksheetContext $printWorksheetContext,
        PrintOurProtocolContext $printOurProtocolContext
    ) {
        $this->protocolRepository = $protocolRepository;
        $this->patientRepository = $patientRepository;
        $this->prescriberRepository = $prescriberRepository;
        $this->billingPeriodRepository = $billingPeriodRepository;
        $this->printWorksheetContext = $printWorksheetContext;
        $this->printOurProtocolContext = $printOurProtocolContext;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $patient_id = $request->patient_id;
        

        if ($patient = $this->patientRepository->find($patient_id)) {
            $affiliates = $patient->affiliates;
        } else {
            $affiliates = [];
        }

        $current_date = date('Y-m-d');

        return view('administrators/protocols/our/create')
            ->with('patient', $patient)
            ->with('billing_periods', $this->billingPeriodRepository->getBillingPeriods($current_date))
            ->with('current_billing_period', $this->billingPeriodRepository->getCurrentBillingPeriod());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $request->validate([
            'completion_date' => 'required|date',
            'patient_id' => 'required|numeric|min:1',
            'plan_id' => 'required|numeric|min:1',
            'prescriber_id' => 'required|numeric|min:1',
            'billing_period_id' => 'required|numeric|min:1',
            'quantity_orders' => 'required|numeric|min:0',
        ]);

        try {
            if (! $our_protocol = $this->protocolRepository->create($request->all())) {
                return back()->withInput($request->all())->withErrors(Lang::get('forms.failed_transaction'));
            }
        } catch (QueryException $exception) {
            return back()->withInput($request->all())->withErrors(Lang::get('errors.error_processing_transaction'));
        }
        
        return redirect()->action([OurProtocolController::class, 'show'], ['id' => $our_protocol->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        $protocol = $this->protocolRepository->findOrFail($id);

        return view('administrators/protocols/our/show')->with('protocol', $protocol);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //

        $protocol = $this->protocolRepository->findOrFail($id);


        return view('administrators/protocols/our/edit')->with('protocol', $protocol);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //  
        
        $request->validate([
            'completion_date' => 'required|date',
            'plan_id' => 'required|numeric|min:1',
            'prescriber_id' => 'required|numeric|min:1',
            'quantity_orders' => 'required|numeric|min:0',
        ]);
        
        try {
            // If you wish, you can make $request->except('patient_id')
            if (! $this->protocolRepository->update($request->all(), $id)) {
                return back()->withInput($request->all())->withErrors(Lang::get('forms.failed_transaction'));
            }
        } catch (QueryException $exception) {
            return back()->withInput($request->all())->withErrors(Lang::get('errors.error_processing_transaction'));
        }
        
        return redirect()->action([OurProtocolController::class, 'show'], ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Returns a list of filtered patients
     *
     * @return \Illuminate\Http\Response
     */
    public function load_patients(Request $request)
    {
        
        return $this->patientRepository->loadPatients($request->filter);
    }

    /**
     * Returns a list of filtered prescribers
     *
     * @return \Illuminate\Http\Response
     */
    public function load_prescribers(Request $request)
    {
        
        return $this->prescriberRepository->loadPrescribers($request->filter);
    }

    /**
     * Returns a view for add practices
     *
     * @return \Illuminate\Http\Response
     */
    public function add_practices($protocol_id)
    {
        $our_protocol = $this->protocolRepository->findOrFail($protocol_id);

        return view('administrators/protocols/our/add_practices')->with('protocol', $our_protocol);
    }

    /**
     * Returns a list of practices for a protocol
     *
     * @return \Illuminate\Http\Response
     */
    public static function get_practices($protocol_id)
    {
        return $this->protocolRepository->getPractices($protocol_id);
    }

    /**
     * Returns a protocol in pdf
     *
     * @return \Illuminate\Http\Response
     */
    public function print_protocol($protocol_id, $filter_practices = [])
    {
        $strategy = 'modern_style';
        $strategyClass = PrintOurProtocolContext::STRATEGIES[$strategy];

        $this->printOurProtocolContext->setStrategy(new $strategyClass);

        return $this->printOurProtocolContext->print($protocol_id, $filter_practices);
    }

    /**
     * Returns a worksheet of protocol in pdf
     *
     * @return \Illuminate\Http\Response
     */
    public function print_worksheet($protocol_id, $filter_practices = [])
    {
        $strategy = 'simple_style';
        $strategyClass = PrintWorksheetContext::STRATEGIES[$strategy];

        $this->printWorksheetContext->setStrategy(new $strategyClass);
        
        return $this->printWorksheetContext->print($protocol_id, $filter_practices);
    }

}
