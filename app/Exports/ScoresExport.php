<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ScoresExport implements FromCollection, WithHeadings
{
    protected $scores;

    public function __construct(array $scores)
    {
        $this->scores = $scores;
    }

    public function collection()
    {
        // Transformamos los scores a una colección de Laravel
        $scoresCollection = collect($this->scores);

        // Mapeamos los elementos para que solo queden la descripción y el valor de similitud
        $scoresCollection = $scoresCollection->map(function($score) {
            return [
                'descripcion' => $score['description'],
                'similarity_value' => $score['similarity_value']
            ];
        });
        
        return $scoresCollection;
    }
    
    public function headings(): array
    {
        return [
            'Descripción',
            'Similitud'
        ];
    }
}

?>
