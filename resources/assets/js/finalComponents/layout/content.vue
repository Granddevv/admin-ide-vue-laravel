<template>

	<!-- Content Wrapper. Contains page content -->
	<div v-else class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				{{title}}
				<small>{{description}}</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li v-show="listPath.length > 0" v-for="item in listPath" :class="{'active': listPath.length-1 == $index}" track-by="$index">{{item}}</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">

			<!--<router-view class="animated" transition="fade" transition-mode="out-in" keep-alive></router-view>-->
			<router-view></router-view>

		</section>
		<!-- /.content -->
	</div>
	<!-- /.content-wrapper -->
</template>

<script>

	module.exports = {
		name : 'Content',
		
		props: {
			title: String,
			description:{
				type: String,
				default: ''
			},
			listPath: {
				type: Array,
				default: function(){
					return [];
				}
			}
		},
		
	}
</script>
