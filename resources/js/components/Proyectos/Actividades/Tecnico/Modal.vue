<template>
    <div>
         <!-- Modal Reportes -->
        <div class="modal fade" id="modalActividad" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content" v-if="load">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Actividad</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center col-12">
                            <h4>Datos de la actividad</h4>
                        </div>
                        <div class="row p-4">
                            <div class="col-12 col-lg-6" style="border-left: 0.01cm solid;">
                                <h5 class="font-size-tittle color-gray">Fecha Inicial</h5>
                                <p class="text-muted mt-1">{{(actividad.reagendamientos.length > 0)? actividad.reagendamientos[0].fecha_inicio:actividad.fecha_inicio}}</p>
                            </div>
                            <div class="col-12 col-lg-6" style="border-left: 0.01cm solid;">
                                <h5 class="font-size-tittle color-gray">Fecha Final</h5>
                                <p class="text-muted mt-1">{{(actividad.reagendamientos.length > 0)? actividad.reagendamientos[0].fecha_final:actividad.fecha_final}}</p>
                            </div>
                            <div class="col-12 col-lg-6" style="border-left: 0.01cm solid;">
                                <h5 class="font-size-tittle color-gray">Proyecto</h5>
                                <p class="text-muted mt-1">{{(actividad.proyecto_id == null) ? 'Sin asignar' :actividad.proyecto.nombre}}</p>

                            </div>
                            <div class="col-12 col-lg-6" style="border-left: 0.01cm solid;">
                                <h5 class="font-size-tittle color-gray">Estado</h5>
                                <b-alert class="text-muted mt-1" show :variant="getColorAlert(actividad.estado)">{{actividad.estado}}</b-alert>
                            </div>
                            <div class="col-12" style="border-left: 0.01cm solid;">
                                <h5 class="font-size-tittle color-gray">Descripcion</h5>
                                <p class="text-muted mt-1">{{actividad.descripcion}}</p>
                            </div>
                        </div>
                        <b-tabs content-class="mt-3">
                            <b-tab title="Files" active>
                                <div class="row row-cols-1 row-cols-md-2 g-4 mt-4">
                                    <div class="col" v-for="(file, index) in actividad.files" :key="index">
                                        <div class="card">
                                            <video class="card-img-top" height="300px" controls style="height:300px;" v-if="file.tipo == 'video/mp4'">
                                                <source :src="`https://res.cloudinary.com/dcj3tck83/video/upload/v1650564336/${file.file}`" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                            <img v-else :src="`https://res.cloudinary.com/dcj3tck83/image/upload/v1650566179/${file.file}`" alt="Image 1" style="height:300px;">
                                        </div>
                                    </div>
                                </div>
                            </b-tab>
                            <b-tab title="Inventario">
                                <inventario-table :inventario="actividad.inventario"></inventario-table>
                            </b-tab>
                            <b-tab title="Reportes">
                                <reportes-table :reportes="actividad.reportes"></reportes-table>
                            </b-tab>
                            <b-tab title="Solicitudes">
                                <solicitudes-table :solicitudes="actividad.solicitudes"></solicitudes-table>
                            </b-tab>
                            <b-tab title="Asistencias">
                                <asistencia-table :asistencias="actividad.asistencias"></asistencia-table>
                            </b-tab>
                            <b-tab title="Pruebas">
                                <pruebas-table :pruebas="actividad.pruebas"></pruebas-table>
                            </b-tab>
                            <b-tab title="Acciones">
                                <div class="row container text-center">
                                    <div class="col-12 col-sm-4 p-2">
                                        <b-button block variant="primary" @click="setForm('reportes')">Enviar Reporte</b-button>
                                    </div>
                                    <div class="col-12 col-sm-4 p-2">
                                        <b-button block variant="primary" @click="setForm('solicitud_productos')">Solicitar Producto</b-button>
                                    </div>
                                    <div class="col-12 col-sm-4 p-2">
                                        <b-button block variant="primary" @click="setForm('finalizar')">Subir Pruebas</b-button>
                                    </div>
                                    <div class="col-12 col-sm-4 p-2" v-if="actividad.estado != 'completado'">
                                        <b-button block variant="primary" :disabled="loading" @click="ubicacion()">
                                            <b-spinner small type="grow" v-if="loading"></b-spinner>
                                            Asistencia
                                        </b-button>
                                    </div>
                                    <div class="col-12 col-sm-4 p-2">
                                        <b-button block variant="warning" @click="finalizarActividad()">Finalizar actividad</b-button>
                                    </div>
                                </div>
                                <div>
                                    <form-reportes :actividad_id="id" :estado="actividad.estado" v-if="load_form_reportes"></form-reportes>
                                    <form-solicitud-productos :actividad_id="id" :estado="actividad.estado" v-if="load_form_solicitud_productos"></form-solicitud-productos>
                                    <form-finalizar :actividad_id="id" :estado="actividad.estado" v-if="load_form_finalizar"></form-finalizar>
                                </div>
                            </b-tab>
                        </b-tabs>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ReportesTable from './Tables/ReportesTable.vue'
    import AsistenciaTable from './Tables/AsistenciaTable.vue'
    import InventarioTable from './Tables/InventarioTable.vue'
    import SolicitudesTable from './Tables/SolicitudesTable.vue'
    import FormSolicitudProductos from './Forms/SolicitudProductos.vue'
    import FormReportes from './Forms/Reportes.vue'
    import FormFinalizar from './Forms/Finalizar.vue'
    import PruebasTable from './Tables/PruebasTable.vue'

    export default {
        props: ['id'],
        components: {
            FormReportes,
            FormSolicitudProductos,
            FormFinalizar,
            ReportesTable,
            SolicitudesTable,
            InventarioTable,
            AsistenciaTable,
            PruebasTable
        },
        data(){
            return{
                actividad:{},
                table:[],
                load: false,
                load_form_reportes: false,
                load_form_solicitud_productos: false,
                load_form_finalizar: false,
                asistencia: {
                    latitude: '',
                    longitude: '',
                    url: ''
                },
                loading: false
            }
        },
        created(){
            this.getActividad()
        },
        methods:{
            getActividad(){
                this.load = false
                axios.get(`/Proyectos/Actividades/get/${this.id}`).then(res=>{
                    this.actividad = res.data.actividad
                    this.load = true
                }).catch(function (error) {
                    console.log(error.response)
                })
            },
            getColorAlert(estado){
                if(estado == 'pendiente'){
                    return 'primary'
                }
                if(estado == 'completado'){
                    return 'success'
                }
                if(estado == 'incompleto'){
                    return 'danger'
                }
            },
            setForm(tipo){
                this.load_form_reportes = (tipo == 'reportes')? true : false
                this.load_form_solicitud_productos = (tipo == 'solicitud_productos')? true : false
                this.load_form_finalizar = (tipo == 'finalizar')? true : false
            },
            ubicacion(){

                if (!"geolocation" in navigator) {
                    this.alert('Error', 'Tu navegador no soporta, Geolocation', 'error')
                }

                const onUbicacionConcedida = ubicacion => {
                    const coordenadas = ubicacion.coords;
                    this.asistencia = {
                        latitude: coordenadas.latitude,
                        longitude: coordenadas.longitude,
                        url: `https://www.google.com/maps/@${coordenadas.latitude},${coordenadas.longitude},20z`
                    }
                    this.setAsistencia()
                }

                const onErrorDeUbicacion = err => {
                    console.log("Error obteniendo ubicaci??n: ", err);
                    this.alert('Error', 'Error obteniendo ubicaci??n', 'error')

                }

                const opcionesDeSolicitud = {
                    enableHighAccuracy: true, // Alta precisi??n
                    maximumAge: 0, // No queremos cach??
                    timeout: 5000 // Esperar solo 5 segundos
                };
                // Solicitar
                navigator.geolocation.getCurrentPosition(onUbicacionConcedida, onErrorDeUbicacion, opcionesDeSolicitud);
            },
            alert(titulo, text, tipo){
                this.$fire({
                    title: titulo,
                    text: text,
                    type: tipo,
                    timer: 3000
                })
            },
            finalizarActividad(){
                this.$fire({
                    title: 'Actividad',
                    text: 'Una vez finalizada la actividad no podras modificar los datos.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#1cc88a',
                }).then((result) => {
                    if(result.value){
                        this.loading = true
                        axios.get(`/Proyectos/Actividades/Finalizar/${this.actividad.id}`).then(res=>{
                            if(res.data.status){
                                this.alert('Asistencia', res.data.msg, 'success')
                            }else{
                                this.alert('Asistencia', res.data.msg, 'error')
                            }
                            this.loading = false
                            setTimeout(()=>{
                                location.reload()
                            },2000)
                        }).catch(function (error) {
                            this.alert('Asistencia', 'Error en el servidor contactese con el programador', 'error')
                            this.loading = false
                        });
                    }
                });
            },
            setAsistencia(){
                this.$fire({
                    title: 'Asistencia',
                    text: 'Solo puedes realizar el registro de la asistencia 2 veces al dia. Una vez registrada no se podra modificar.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#1cc88a',
                }).then((result) => {
                    if(result.value){
                        this.loading = true
                        axios.post(`/Proyectos/Actividades/Asistencias/store/${this.actividad.id}`, this.asistencia).then(res=>{
                            console.log(res.data)
                            if(res.data.status){
                                this.alert('Asistencia', res.data.msg, 'success')
                            }else{
                                this.alert('Asistencia', res.data.msg, 'error')
                            }
                            this.loading = false
                        }).catch(function (error) {
                            this.alert('Asistencia', 'Error en el servidor contactese con el programador', 'error')
                            this.loading = false
                        });
                    }
                });
            }
        }
    }
</script>
