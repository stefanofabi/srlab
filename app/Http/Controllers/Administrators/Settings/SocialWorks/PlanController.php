<?php

namespace App\Http\Controllers\Administrators\Settings\SocialWorks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Contracts\Repository\SocialWorkRepositoryInterface;
use App\Contracts\Repository\NomenclatorRepositoryInterface;
use App\Contracts\Repository\PlanRepositoryInterface;

use Lang;

class PlanController extends Controller
{
    private const ATTRIBUTES = [
        'name',
        'nbu_price',
        'social_work_id',
        'nomenclator_id',
    ];

    /** @var \App\Contracts\Repository\SocialWorkRepositoryInterface */
    private $socialWorkRepository;

    /** @var \App\Contracts\Repository\NomenclatorRepositoryInterface */
    private $nomenclatorRepository;

    /** @var \App\Contracts\Repository\PlanRepositoryInterface */
    private $planRepository;

    public function __construct(
        SocialWorkRepositoryInterface $socialWorkRepository, 
        NomenclatorRepositoryInterface $nomenclatorRepository,
        PlanRepositoryInterface $planRepository
    ) {
        $this->socialWorkRepository = $socialWorkRepository;
        $this->nomenclatorRepository = $nomenclatorRepository;
        $this->planRepository = $planRepository;
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

        $social_work =  $this->socialWorkRepository->findOrFail($social_work_id);
        
        $nomenclators = $this->nomenclatorRepository->all();

        return view('administrators/settings/social_works/plans/create')
            ->with('social_work', $social_work)
            ->with('nomenclators', $nomenclators);
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
            'name' => 'required|string',
            'nbu_price' => 'required|numeric',
        ]);

        if (! $plan = $this->planRepository->create($request->only(self::ATTRIBUTES))) {
            return back()->withInput($request->all())->withErrors(Lang::get('forms.failed_transaction'));
        }

        return redirect()->action([SocialWorkController::class, 'edit'], ['id' => $plan->social_work_id]);
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
        
        $plan = $this->planRepository->findOrFail($id);
        
        $nomenclators = $this->nomenclatorRepository->all();

        return view('administrators/settings/social_works/plans/edit')
            ->with('plan', $plan)
            ->with('nomenclators', $nomenclators);
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
            'name' => 'required|string',
            'nbu_price' => 'required|numeric',
        ]);
    
        if (! $this->planRepository->update($request->only(self::ATTRIBUTES), $id)) {
            return back()->withInput($request->all())->withErrors(Lang::get('forms.failed_transaction'));
        }

        $social_work_id = $this->planRepository->findOrFail($id)->social_work_id;
        
        return redirect()->action([SocialWorkController::class, 'edit'], ['id' => $social_work_id]);
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

        $social_work_id = $this->planRepository->findOrFail($id)->social_work_id;

        if (! $this->planRepository->delete($id)) {
            return back()->withErrors(Lang::get('forms.failed_transaction'));
        }

        return redirect()->action([SocialWorkController::class, 'edit'], ['id' => $social_work_id]);
    }
}
