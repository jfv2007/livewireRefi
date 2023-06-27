<?php

namespace App\Http\Livewire\Admin\Tag18;

use App\Models\Centro;
use App\Models\Falla;
use App\Models\Planta;
use App\Models\Strabajo;
use App\Models\Tag18;
use App\Models\Trabajo;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListTrabajosTags18 extends Component
{
    use WithFileUploads;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $centros = [];
    public $statefalla = [];
    public $statelistfalla = [];


    public $tag18;
    public $plantas;
    public $centro;
    public $tag;
    public $foto_trabajo;

    public $trabajo;



    public $selectedCentroListFallas = NULL;
    public $selectedPlantaListFallas = NULL;
    public $selectedStatusModal=NULL;

    public $readyToLoad = false;


    public function editconsulta(Trabajo $trabajo)
    {

        /* dd($trabajo); */

        $this->consultaeditTrabajoModal = true;
        /*  dd('hola'); */
        $this->trabajo = $trabajo;
        $this->state = $trabajo->toArray();

          $this->id_falla = $this->state['id_falla'];
          $this->des_trabajo = $this->state['des_trabajo'];
          /* $this->selectedStatusModal =$this->state['id_strabajos']; */
          $this->id_tag18s =$this->id_falla;
         /*   dd($this->des_trabajo); */

         /* $falla = Falla::where('id', $this->id) */

           /* $falla1 = Falla::with('fallatrabajos')
           ->orWhereHas('fallatrabajos',function ($query){
              $query->where('id', $this->id_falla);
          })
          ->get(); */
           $falla1 = Falla::find($this->id_falla);
           $tagNombre = $falla1->id_tag18s;

           $tagsValores= Tag18::find($tagNombre);
           $tag= $tagsValores->tag;
             /* dd($tagsValores);  */

           $descripcionFalla=$falla1->descripcion_falla;

           $this->tagnombre = $tag;
           $this->descripcionfalla=$descripcionFalla;
           $this->descripciontrabajo1 = $this->des_trabajo;

          /*  dd($falla1); */

          /* $fallaTrabajo1 = Search::new()
          -> add(Falla::with('fallatrabajos'),'id')
          ->search($this->id_falla);
          dd($fallaTrabajo1); */

          /*$tags18 =$fallaTrabajo->fallatrabajos;

           dd($tags18); */

        /*   $fallaDescripcion=$falla->descripcion_falla; */

          /*  $trabajos = Falla::with(['tagfallas', 'fllastatus'])
           ->when('id', $this->id_fallas)
          ->get(); */

         /*  dd($this->fallaDescripcion); */

          $this->dispatchBrowserEvent('show-formtrabajoeditconsulta');

    }

    public function updatetrabajoconsulta()
    {
           /* dd($this->state); */

        $validateDate = Validator::make(
            $this->state,
            [
                'des_trabajo' => 'required',
                'id_strabajos' => 'required',
            ],
            [
                'des_trabajo.required' => 'La descripcion del trabajo es requerida.',
                'id_strabajos".required' => 'El Status es requerido.',
            ]
        )->validate();


        $validateDate['des_trabajo'] =  strtoupper($this->descripciontrabajo1);
        $validateDate['id_strabajos'] = $this->selectedStatusModal;
        /* dd($validateDate); */

        if ($this->foto_trabajo) {
            $validateDate['foto_trabajo'] = $this->foto_trabajo->store('/', 'planta');
        }

        $falla1 = Falla::find($this->id_falla);
         $tagNombre = $falla1->id_tag18s;

         $tag18= Tag18::find($tagNombre);
        /*  $tag= $tagsValores->tag; */

         if ($this->selectedStatusModal == 7) {
            $validateTags['id_status'] = 1;
            $tag18->update($validateTags);
        }


        $this->trabajo->update($validateDate);
        $this->dispatchBrowserEvent('hide-formtrabajoeditconsulta', ['message' => 'El trabajo  updated successfully!']);
    }


    public function mount(Tag18 $tag18)
    {
        /* se pasan los valores a los combos */

        $this->tag18 = $tag18;
        $this->statefalla = $tag18->toArray();
        /* dd($tag18); */

        $this->selectedCentroListFallas = $this->statefalla['id_cen'];
        $this->selectedPlantaListFallas = $this->statefalla['id_planta'];
        $this->tag = $this->statefalla['id'];  /* es el id del trag no de la falla */

        /* $this->centros = Centro::all(); */
        $this->centros = Centro::orderBy('centro_id', 'DESC')->get();
        $this->plantas = Planta::orderby('nombre_planta', 'DESC')->get();

        /* $this->status = Strabajo::all(); */

        /*  $fallas = Falla::with(['tagfallas', 'fllastatus'])
        ->where('id_tag18s','=', 1557); */

        /* $tagBusqueda= Tag18:: where('id','1557')->get(); */


        /* dd($fallas); */
        /* dd($tagBusqueda); */
    }



    public function render()
    {
        /* busqueda anterior  */
        /* $trabajos = Trabajo::join('fallas','fallas.id_tag18s','=','trabajos.id_tag18')
                    ->where('id_tag18','=',  $this->tag)
                    ->get();
 */



        $trabajos = Trabajo::join('fallas', 'fallas.id', '=', 'trabajos.id_falla')
            /* ->select('trabajos.*','fallas.*') */
            ->join('tag18s', 'tag18s.id', '=', 'fallas.id_tag18s')
            /* ->select('trabajos.*','tag18s.*','fallas.*') */
            ->whereRelation('fallatrabajos', 'id_cen', $this->selectedCentroListFallas)
            ->select('trabajos.*')

            ->whereRelation('fallatrabajos', 'id_planta', $this->selectedPlantaListFallas)
            ->select('trabajos.*')

            ->where('trabajos.id_tag18', '=', $this->tag)

            ->get();
        /* dd($trabajos); */



        /* dd($trabajos); */
        return view('livewire.admin.tag18.list-trabajos-tags18')
            ->with('trabajos', $trabajos);
    }
}
