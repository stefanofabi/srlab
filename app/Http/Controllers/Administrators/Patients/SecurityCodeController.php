<?php

namespace App\Http\Controllers\Administrators\Patients;

use App\Http\Controllers\Controller;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Http\Traits\PrintSecurityCode;

use App\Models\Patient;
use App\Models\SecurityCode;

use Lang;

class SecurityCodeController extends Controller
{
    use PrintSecurityCode;

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
    public function create()
    {
        //
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
        $patient_id = $request->patient_id;
        $patient = Patient::findOrFail($patient_id);

        $new_security_code = Str::random(10);

        $date_today = date("Y-m-d");
        $expiration_date = date("Y-m-d", strtotime($date_today."+ 1 week"));

        try {
            DB::beginTransaction();
            SecurityCode::updateOrCreate([
                'patient_id' => $patient_id,
            ], [
                'security_code' => Hash::make($new_security_code),
                'expiration_date' => $expiration_date,
            ]);

            $this->print_security_code($patient->id, $patient->full_name, $new_security_code, $expiration_date);

            DB::commit();
        } catch (QueryException $exception) {
            DB::rollBack();

            exit(Lang::get('errors.error_processing_transaction'));
        }
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
}