<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shunt;
use App\Patient;
use App\Http\Traits\Pagination;

class PatientsController extends Controller
{

    use Pagination;
    private const PER_PAGE = 15;
    private const ADJACENTS = 4;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $shunts = Shunt::all();

        return view('patients/patients')->with('shunts', $shunts);
    }

    public function new_patient_human() {
        $shunts = Shunt::all();

        return view('patients/new_patient_human')->with('shunts', $shunts);        
    }


    /**
	* Load patients from a shunt
    */
    public function load(Request $request) {
        $shunts = Shunt::all();
        $valid_types = array("animals", "humans", "industrials");

        // Request
        $patient_type = $request['type'];
        $shunt = $request['shunt'];
        $filter = $request['filter'];

        if (in_array($patient_type, $valid_types)) {
            // Array query
            $page = $request['page'];
            $offset = ($page - 1) * self::PER_PAGE;
            $query_patients = Patient::show_patients($patient_type, $shunt, $filter, $offset, self::PER_PAGE);

            // Pagination
            $count_rows = Patient::count_patients($patient_type, $shunt, $filter);
            $total_pages = ceil($count_rows / self::PER_PAGE);
            
            $paginate = $this->paginate($page, $total_pages, self::ADJACENTS);

            switch($patient_type) {
                case $valid_types[0]: {
                    break;
                }
                case $valid_types[1]: {
                    $view = view('patients/patients_humans')
                    ->with('shunts', $shunts)
                    ->with('request', $request->all())
                    ->with('data', $query_patients)
                    ->with('paginate', $paginate);

                    break;
                }
                case $valid_types[2]: {
                    break;
                }
                default: break;
            }


        } else {
            $view = view('patients/patients')->with('shunts', $shunts);
        }

        return $view;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }
}
