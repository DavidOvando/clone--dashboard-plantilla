<template>
    <div class="mt-2">
        <form method="post" @submit.prevent="store()" enctype="multipart/form-data">
            <h5>Subir pruebas</h5>
            <div class="form-group row">
                <div class="col-12">
                    <label>Descripcion de lo realizado</label>
                    <b-form-textarea
                        id="textarea"
                        placeholder="..."
                        v-model="reporte.descripcion"
                        :state="reporteValidacion.descripcion"
                        rows="3"
                        max-rows="6"
                        :disabled="(show == true)? false : true"
                    ></b-form-textarea>
                    <b-form-invalid-feedback :state="reporteValidacion.descripcion">
                        Este espacio no puede quedar vacio
                    </b-form-invalid-feedback>
                    <b-form-valid-feedback :state="reporteValidacion.descripcion">
                        Esta bien!
                    </b-form-valid-feedback>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-12">
                    <label for="fecha_inicial">Fecha</label>
                    <b-form-datepicker id="fecha_inicial" :state="reporteValidacion.fecha" v-model="reporte.fecha" placeholder="Selecciona una fecha"></b-form-datepicker>
                    <b-form-invalid-feedback :state="reporteValidacion.fecha">
                        El campo no puede estar vacio
                    </b-form-invalid-feedback>
                    <b-form-valid-feedback :state="reporteValidacion.fecha">
                        OK!.
                    </b-form-valid-feedback>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-12">
                    <label>Pruebas visuales</label>
                    <b-form-file placeholder="Elije las imagenes a subir" accept="image/*" multiple v-on:change="onImageChange" ref="file-input" v-if="show"></b-form-file>
                </div>
                <div class="row row-cols-1 row-cols-md-2 g-4 mt-4" v-if="loadImage">
                    <div class="col mt-2" v-for="(src, index) in urls" :key="index">
                        <div class="card">
                            <b-img :src="(show == true)? src : url+src" fluid alt="Responsive image"></b-img>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center" v-if="show">
                <button type="submit" class="btn btn-success" v-if="!loading" >Subir pruebas</button>
                <spinner-view :loading="loading"></spinner-view>
            </div>
        </form>
        <div v-if="!show" class="mt-4">
            <b-alert show variant="success">Actualmente la actividad se encuentra completada, No se puede enviar pruebas</b-alert>
        </div>
    </div>
</template>

<script>
    import url from '../../../../../mixins/cloudinary'
    import SpinnerView from '../../../../Spinner.vue'

    export default {
        components:{
            SpinnerView
        },
        props:['actividad_id', 'estado'],
        mixins: [url],
        data(){
            return{
                reporte:{
                    'descripcion': '',
                    'fecha' : ''
                },
                reporteValidacion:{
                    'descripcion': null,
                    'fecha':null
                },
                files: [],
                urls: [],
                loadImage: false,
                loading: false,
                show: (this.estado == 'completado')? false : true
            }
        },
        created(){
            // this.validarActividad()
        },
        methods:{
            resetData(){
                this.reporte = {
                    'descripcion' : '',
                    'fecha' : ''
                }
                this.reporteValidacion = {
                    'descripcion' : null,
                    'fecha' : null
                }
                this.files = []
                this.urls = []
                this.loadImage = false
            },
            onImageChange(e){
                let files = e.target.files
                // Image Preview
                for (let i = 0; i < files.length; i++) {
                    this.urls[i] = URL.createObjectURL(files[i]);
                }
                //
                //Set variables
                for (let i = 0; i < files.length; i++) {
                    this.files.push(files[i])
                }
                //
                this.loadImage = true
            },
            // validarActividad(){
            //     if(!this.show){
            //         axios.get(`/Proyectos/Actividades/get/${this.actividad_id}`).then(res=>{
            //             console.log(res.data)
            //             if(res.data.actividad.pruebas.files.length > 0){
            //                 this.urls = res.data.actividad.pruebas.files
            //                 this.reporte.descripcion = res.data.actividad.pruebas.descripcion
            //                 this.loadImage = true
            //             }
            //         })
            //     }
            // },
            store(){
                if(this.reporte.descripcion != '' && this.reporte.fecha != ''){
                    this.$fire({
                        title: 'Actividad',
                        text: 'Una vez enviada las pruebas no podras modificarlas',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Confirmar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#1cc88a',
                    }).then((result) => {
                        if(result.value){
                            this.loading = true
                            let data = new FormData();
                            data.append("descripcion", this.reporte.descripcion)
                            data.append("fecha", this.reporte.fecha)
                            for (let i = 0; i < this.files.length; i++) {
                                data.append('files[]', this.files[i], this.files[i].name)
                            }
                            const config = { 'header' : { 'Content-Type': 'multipart/form-data' }}
                            axios.post(`/Proyectos/Actividades/store/pruebas/${this.actividad_id}`, data, config).then(res=>{
                                if(res.data.status){
                                    this.alert('Actividad', res.data.msg, 'success')
                                }else{
                                    this.alert('Actividad', res.data.msg, 'error')
                                }
                                this.$refs['file-input'].reset()
                                this.resetData()
                                this.loading = false
                            }).catch(error=>{
                                console.log(error.response)
                                this.alert('Actividad', 'Error del servidor', 'error')
                                this.loading = false
                            })
                        }
                    });
                }else{
                    this.validar()
                }
            },

            validar(){
                this.reporteValidacion.descripcion = (this.reporte.descripcion == '')? false : true
                this.reporteValidacion.fecha = (this.reporte.fecha == '')? false : true
            },

            alert(titulo, text, tipo){
                this.$fire({
                    title: titulo,
                    text: text,
                    type: tipo,
                    timer: 3000
                })
            }
        }
    }
</script>
