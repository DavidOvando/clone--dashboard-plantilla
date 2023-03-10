<?php

namespace App\Http\Controllers\Proyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyectos\ProyectoActividad;
use App\Models\Proyectos\ProyectoActividadProductoSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyectoActividadProductoSolicitudController extends Controller
{
    public function index(){
        return view('proyectos.actividades.solicitudes.index');
    }

    public function store(ProyectoActividad $actividad, Request $request){
        $solicitud = new ProyectoActividadProductoSolicitud($request->all());
        $solicitud->actividad_id = $actividad->id;
        $solicitud->save();
        return response()->json(['status' => true, 'msg' => 'Su solicitud a sido enviada, estara a la espera de una aprobacion']);
    }

    public function get(){
        return response()->json(['solicitudes' => ProyectoActividadProductoSolicitud::with('actividades', 'producto')->get()]);
    }

    public function updateEstado(ProyectoActividadProductoSolicitud $solicitud, $estado){
        if(Auth::user()->role_user[0] == 'admin' || Auth::user()->role_user[0] == 'comercial'){
            $estado = ($estado == 'aceptado')? 'aprobado' : 'rechazado';
        }
        if(Auth::user()->role_user[0] == 'lider.ingenieria'){
            $estado = ($estado == 'aceptado')? 'aceptado' : 'rechazado';
        }
        if(Auth::user()->role_user[0] == 'administracion.compras'){
            if($solicitud->estado == 'aprobado'){
                $estado = ($estado == 'aceptado')? 'pedido' : 'rechazado';
            }
            if($solicitud->estado == 'pedido'){
                $estado = ($estado == 'aceptado')? 'completada' : 'rechazado';
            }
        }
        $solicitud->estado = $estado;
        $solicitud->update();
        $solicitud->save();
        return response()->json(['status' => true, 'msg' => ($estado == 'aceptado')? 'La solicitud a sido aceptada': 'La solicitud a sido rechazada']);
    }

    public function show(){
        return view('proyectos.actividades.solicitudes.pdf');
    }
}
