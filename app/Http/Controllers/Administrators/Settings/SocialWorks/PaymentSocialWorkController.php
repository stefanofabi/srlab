<?php

namespace App\Http\Controllers\Administrators\Settings\SocialWorks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Contracts\Repository\SocialWorkRepositoryInterface;
use App\Contracts\Repository\PaymentSocialWorkRepositoryInterface;

use Lang;

class PaymentSocialWorkController extends Controller
{
    private const ATTRIBUTES = [
        'payment_date',
        'social_work_id',
        'billing_period_id',
        'amount',
    ];

    /** @var \App\Contracts\Repository\SocialWorkRepositoryInterface */
    private $socialWorkRepository;
    
    public function __construct(
        SocialWorkRepositoryInterface $socialWorkRepository,
        PaymentSocialWorkRepositoryInterface $paymentSocialWorkRepository,
    ) {
        $this->socialWorkRepository = $socialWorkRepository;
        $this->paymentSocialWorkRepository = $paymentSocialWorkRepository;
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
    public function create($social_work_id)
    {
        //

        $social_work = $this->socialWorkRepository->findOrFail($social_work_id);

        return view('administrators/settings/social_works/payments/create')->with('social_work', $social_work);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric',
            'billing_period_id' => 'required|numeric|min:1',
        ]);

        if (! $this->paymentSocialWorkRepository->create($request->only(self::ATTRIBUTES))) {
            return back()->withInput($request->all())->withErrors(Lang::get('forms.failed_transaction'));
        }
   
        return redirect()->action([SocialWorkController::class, 'edit'], ['id' => $request->social_work_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //

        $payment = $this->paymentSocialWorkRepository->findOrFail($id);

        return view('administrators/settings/social_works/payments/edit')->with('payment', $payment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric',
            'billing_period_id' => 'required|numeric|min:1',
        ]);

        if (! $this->paymentSocialWorkRepository->update($request->only(self::ATTRIBUTES), $id)) {
            return back()->withInput($request->all())->withErrors(Lang::get('forms.failed_transaction'));
        }

        $social_work_id = $this->paymentSocialWorkRepository->findOrFail($id)->social_work_id;

        return redirect()->action([SocialWorkController::class, 'edit'], ['id' => $social_work_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        
        $social_work_id = $this->paymentSocialWorkRepository->findOrFail($id)->social_work_id;  

        if (! $this->paymentSocialWorkRepository->delete($id)) {
            return back()->withErrors(Lang::get('forms.failed_transaction'));
        }

        return redirect()->action([SocialWorkController::class, 'edit'], ['id' => $social_work_id]);
    }
}
