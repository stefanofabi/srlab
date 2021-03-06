<?php

namespace App\Laboratory\Prints\Protocols\Our;

use App\Laboratory\Prints\Protocols\PrintProtocolStrategyInterface;
use App\Repositories\Eloquent\ProtocolRepository;

use PDF;
use Lang;

class ModernStyleProtocolStrategy implements PrintProtocolStrategyInterface
{

    /** @var \App\Laboratory\Repositories\Protocols\ProtocolRepository */
    private $protocolRepository;    
    
    public function __construct () 
    {
        $this->protocolRepository = resolve(ProtocolRepository::class);
    }   

    /**
     * Returns a report in pdf
     *
     * @return \Illuminate\Http\Response
     */
    public function printProtocol($protocol_id, $filter_practices = [])
    {
        $protocol = $this->protocolRepository->findOrFail($protocol_id);

        if (empty($filter_practices)) {
            $practices = $protocol->practices;
        } else {
            $practices = $practices->whereIn('id', $filter_practices);
        }
        
        if (!$this->haveResults($practices)) {
            return Lang::get('protocols.empty_protocol');
        }

        $pdf = PDF::loadView('pdf/protocols/modern_style', [
            'protocol' => $protocol,
            'practices' => $practices,
        ]);

        $protocol_name = "protocol_$protocol->id";

        if ($protocol->practices->count() != $practices->count()) {
            $protocol_name = 'partial_'.$protocol_name;
        }

        return $pdf->stream($protocol_name);
    }

    public function haveResults($practices)
    {
        /* Returns true if there is at least one reported practice, false otherwise */
        $have_results = false;

        foreach ($practices as $practice) {
            if ($practice->results->isNotEmpty()) {
                $have_results = true;
            }
        }

        return $have_results;
    }
}
