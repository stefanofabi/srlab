<?php

namespace App\Http\Controllers\Administrators\Determinations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\Pagination;

use App\Laboratory\Repositories\Determinations\DeterminationRepositoryInterface;
use App\Laboratory\Repositories\Nomenclators\NomenclatorRepositoryInterface;

use Lang;

class DeterminationController extends Controller
{
    use Pagination;

    private const PER_PAGE = 15;

    private const ADJACENTS = 4;

    /** @var \App\Laboratory\Repositories\Determinations\DeterminationRepositoryInterface */
    private $determinationRepository;

    /** @var \App\Laboratory\Repositories\Nomenclators\NomenclatorRepositoryInterface */
    private $nomenclatorRepository;

    public function __construct(DeterminationRepositoryInterface $determinationRepository, NomenclatorRepositoryInterface $nomenclatorRepository)
    {
        $this->determinationRepository = $determinationRepository;
        $this->nomenclatorRepository = $nomenclatorRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $nomenclators = $this->nomenclatorRepository->all();

        return view('administrators/determinations/determinations')->with('nomenclators', $nomenclators);
    }

    /**
     * Load determinations
     *
     * @param \Illuminate\Http\Request $request
     * @return View $view
     */
    public function load(Request $request)
    {

        $offset = ($request->page - 1) * self::PER_PAGE;

        $determinations = $this->determinationRepository->index($request->nomenclator, $request->filter, $offset, self::PER_PAGE);

        // Pagination
        $count_rows = $determinations->count();
        $total_pages = ceil($count_rows / self::PER_PAGE);
        $paginate = $this->paginate($request->page, $total_pages, self::ADJACENTS);

        $nomenclators = $this->nomenclatorRepository->all();

        return view('administrators/determinations/index')
            ->with('request', $request)
            ->with('determinations', $determinations)
            ->with('paginate', $paginate)
            ->with('nomenclators', $nomenclators);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $nomenclators = $this->nomenclatorRepository->all();

        return view('administrators/determinations/create')->with('nomenclators', $nomenclators);
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
            'code' => 'required|numeric|min:0',
            'name' => 'required|string',
            'position' => 'required|numeric|min:1',
            'biochemical_unit' => 'required|numeric|min:0',
        ]);

        if (! $determination = $this->determinationRepository->create($request->all())) {
            return back()->withInput($request->all())->withErrors(Lang::get('forms.failed_transaction'));
        }
    
        return redirect()->action([DeterminationController::class, 'show'], ['id' => $determination->id]);
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

        $determination = $this->determinationRepository->findOrFail($id);

        return view('administrators/determinations/show')->with('determination', $determination);
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
        $determination = $this->determinationRepository->findOrFail($id);

        return view('administrators/determinations/edit')->with('determination', $determination);
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
            'code' => 'required|numeric|min:0',
            'name' => 'required|string',
            'position' => 'required|numeric|min:1',
            'biochemical_unit' => 'required|numeric|min:0',
        ]);

        if (!$this->determinationRepository->update($request->except(['_token', '_method']), $id)) {
            return back()->withInput($request->all())->withErrors(Lang::get('forms.failed_transaction'));
        }

        return redirect()->action([DeterminationController::class, 'show'], ['id' => $id]);
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

        if (!$this->determinationRepository->delete($id)) {
            return back()->withErrors(Lang::get('forms.failed_transaction'));
        }
        
        $nomenclators = $this->nomenclatorRepository->all();

        return view('administrators/determinations/determinations')
            ->with('success', [Lang::get('determinations.success_destroy_message')])
            ->with('nomenclators', $nomenclators);
    }
}
