<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Region;
use App\Commune;
use App\Http\Requests\CreateCommuneRequest;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\CreateRegionRequest;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Esta funcion devuelve todos los registros de los clientes de la base de datos y hace busqueda por DNI o Email
     * @param Request Objeto sobre el que podemos consultar información sobre el cliente que realiza la solicitud
     * @return Custumer Retorna la información del cliente
     */
    public function index(Request $request)
    {
        if($request->has(key:'search'))
        {

            // Esta variable almacena la información del cliente consultado por el DNI o Email 
            $search = Customer::where('dni', 'like', '%'.$request->search .'%')
            ->orWhere('email', $request->search)
            ->first();

            // Validación que la consulta no sea nula y el clinete no tenga status trash o I
            if($search == null || $search->status == "trash" || $search->status == "I"){
                return response()->json([
                    'res'=> 'error',
                    'message'=> 'Error al buscar el cliente'
                ], status:200);
            } else {
                return response()->json([
                    'res'=> 'success',
                    'customer'=> $search
                ], status:200);
            }
        } else {
            return Customer::all();
        }
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
     * Esta funcion registra valida toda la información de Region, commune, customer. Registra y asociarregion, commune, custumer
     * @param Request Objeto sobre el que podemos consultar información sobre el cliente que realiza la solicitud
     * @return Custumer Retorna success y mensaje de cliente registrado correctamente
     */
    public function store(Request $request)
    {
        // Obtenemos la información enviada desde Post como un archivo Json y Hacemos la validaciones correspondientes a cada campo
        $validate = Validator::make($request->all(), [
            'descriptionRegions' => 'required',
            'statusRegions' => 'required',
            'descriptionCommunes' => 'required',
            'statusCommunes' => 'required',
            'dni' => 'required|unique:customers,dni',
            'id_reg' => 'required',
            'id_com' => 'required',
            'email' => 'required|unique:customers,email',
            'name' => 'required',
            'last_name' => 'required',
            'status' => 'required',
        ]);
        
        // Validamos que no ocurra ningun fallo con las validaciones de los campos 
        if ($validate->fails()) {
            return response()->json([
                'res'=> 'error',
                'message'=> 'Error al registrar el cliente',
                'errors' => $validate->errors()
            ], status:500);
        } else {

            // Creamos una instancia de Region Para poder guardar en la base de datos con los campos ya validados
            $region = new Region();
            $region->description = $request->descriptionRegions;
            $region->status = $request->statusRegions;
            $region->save();

            // Consultamos el ultimo Id registrado en region para poder asignarlo en la tabla commune
            $id_region = Region::latest('id_reg')->first();

            // Creamos una instancia de Commune para poder registrar en la base de datos con los campos ya validados y el id de la region
            $commune = new Commune();
            $commune->id_reg = $id_region->id_reg;
            $commune->description = $request->descriptionCommunes;
            $commune->status = $request->statusCommunes;
            $commune->save();
            
            // Consultamos el ultimo Id registrado de commune para asignarlo en la table customer
            $id_commune = Commune::latest('id_com')->first();

            // Creamos una nueva instancia de DateTime para obtener la Fecha del sistema
            $dt = new DateTime();    
            // Creamos una instancia de Customer para poder registrarla con los datos validados y el id de la region y de commune 
            $customer = new Customer();
            $customer->dni = $request->dni;
            $customer->id_reg = $id_region->id_reg;
            $customer->id_com = $id_commune->id_com;
            $customer->email = $request->email;
            $customer->name = $request->name;
            $customer->last_name = $request->last_name;
            $customer->address = $request->address;
            $customer->date_reg = $dt->format('Y-m-d H:i:s');; // Asignamos la fecha con formato (Y-m-d H:i:s)
            $customer->status = $request->status;
            $customer->save();

            return response()->json([
                'res'=> 'success',
                'message'=> 'cliente registrado correctamente'
            ], status:200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Esta funcion Elimina el cliente de la base de datos cuando el esatdo es diferente a trash
     * @param Request Objeto sobre el que podemos consultar información sobre el cliente que realiza la solicitud
     * @return Custumer Retorna mensaje de eliminación del cliente
     */
    public function destroy(Request $request)
    {
        // Consulta la información del cliente de acuerdo al DNI
        $customer = Customer::where('dni', $request->dni)->first();

        // Valida que el status no sea igual a trash
        if($customer->status == "trash"){
            return response()->json([
                'res'=> 'error',
                'message'=> 'Registro no existe'
            ], status:200);
        } else {
            // Elimina el cliente encontrado con el DNI, si el status es diferente a trash
            Customer::where('dni', $request->dni)->delete();
            return response()->json([
                'res'=> 'success',
                'message'=> 'Cliente eliminado correctamente'
            ], status:200);
        }

    }
}
