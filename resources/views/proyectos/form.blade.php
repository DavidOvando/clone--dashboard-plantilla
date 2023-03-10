@extends('layouts.app')

@section('title', 'Proyectos - Form')

@section('head')
@endsection

@section('page-title', 'Proyectos')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 text-primary">Proyectos</h6>
                </div>
                <div class="card-body" id="app">
                    <proyectos-form
                    @isset($proyecto)
                        :proyecto_props="{{$proyecto}}"
                    @endisset
                    @isset($cotizacion)
                        :cotizacion_props="{{$cotizacion}}"
                    @endisset
                    ></proyectos-form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection
