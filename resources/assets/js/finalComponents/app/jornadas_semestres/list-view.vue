<template>
	
	<div v-if="loading">
		<app-loading></app-loading>
	</div>

	<div v-else>

		<template v-if="ciclo">

		<div class="col-xs-12 show-div">
			<a v-link="{path: '/jornadasemestres/create'}" class="btn btn-primary btn-flat btn-spacing"> <i class="fa fa-plus"></i> AGREGAR JORNADA</a>
		</div>
		<cool-table 
			:option-toolbar="toolbar"
			:url="url" 
			:data.sync="datos" 
			:columns="columnas" 
			filter-key-word="search">
		</cool-table>

		<!-- Modal logic -->
		</template>
		<template v-else>
			<div class="alert alert-danger">
				<p>No hay ciclo activo</p>
			</div>
		</template>

	</div>

</template>

<style>
	.show-div{
		z-index: 999;
	}
	.btn-spacing{
		margin: .5em;
	}
</style>


<script>

	import Loading from '../../reusable/loading.vue';

	import coolTable from '../../reusable/cool-table.vue';

	import myMixins from './mixins';

	import {urlJornadaSemestre} from '../config';

	export default {
		name: 'jornada-semestre-ciclo',
		mixins: [myMixins],
		route: {
			data: function(transition){
				this.load();
				transition.next();
			}
		},
		data(){
			return {
				ciclo: null,
				showModal: false,
				url: urlJornadaSemestre,
				toolbar: null,
				currentModel: {},
				materiasSeleccionadas: [],
				datos: [],
				columnas: [
				{
					title: 'Ciclo',
					field: 'ciclo',
					hidden: false,
					sortable: true,
					template: '${col.descripcion_ciclo.anio} - ${col.descripcion_ciclo.anio+1} (${col.descripcion_ciclo.ciclo})'
				},
				{
					title: 'Jornada',
					field: 'jornada',
					hidden: false,
					sortable: false,
					template: '<span class="color-palette label ${col.catalogo_jornada=="MAT"?"bg-primary":col.catalogo_jornada=="VES"?"bg-orange":col.catalogo_jornada=="NOC"?"bg-navy":"bg-purple"}"> <i class="fa fa-clock-o"></i> ${col.jornada.descripcion} </span>'

				},
				{
					title: 'Semestre',
					field: 'semestre',
					hidden: false,
					sortable: false,
					template: '${col.semestre.descripcion}'
				},
				{
					title: 'Paralelo',
					field: 'paralelo',
					hidden: false,
					sortable: false,
					template: '${col.paralelo.descripcion}'
				},
				{
					title: 'Curso / Aula / Paralelo',
					field: 'aula',
					hidden: false,
					sortable: false,
					template: '${col.aula.codigo} - ${col.aula.descripcion}'
				},
				{
					title: 'Horas Asignadas',
					field: 'horario',
					hidden: false,
					sortable: false,
					template: '<span class="text-green"><i class="fa fa-clock-o"></i> ${col.horario.length > 0 ? _.sumBy(col.horario, function(o) { return parseFloat(o.num_horas); }) : 0}H</span>'
				},
				{
					title: 'Opciones',
					titleClass: 'text-center',
					hidden: false,
					fieldClass: 'text-center',
					itemActions: [
							/*
						{
							nameEmit: 'jornadasemestre-update-event',
							btnClass: 'btn btn-primary btn-xs',
							iconClass: 'fa fa-pencil',
							label: 'Modificar'
						},
						*/
						{
							nameEmit: 'jornadasemestre-calendario-edit-event',
							btnClass: 'btn btn-success btn-xs',
							iconClass: 'fa fa-calendar',
							label: 'Asignar Horario'
						},
						{
							nameEmit: 'jornadasemestre-delete-event',
							btnClass: 'btn btn-danger btn-xs',
							iconClass: 'fa fa-close',
							label: 'Eliminar'
						}

					]
				}
				],
				loading: false
			}
		},
		components: {
			'cool-table' : coolTable,
			'app-loading' : Loading
		},
		events: {
			'jornadasemestre-update-event' : function(model){
				this.$router.go('/jornadasemestres/edit/' + model.id);
			},
			'jornadasemestre-delete-event' : function(model){
				this.destroy(model);
			},
			'jornadasemestre-calendario-edit-event' : function(model){
				this.$router.go('jornadasemestres/horario/' + model.id);
			}
		}
	}

</script>
