<template>
	<li @click="toggleMenu" class="pageLink" v-if="!isParent">
		<a v-link="{path:item.link}"><i :class="item.iconClass"></i> <span>{{item.name}}</span></a>
	</li>
	<li v-else class="treeview">
		<a href="#"><i :class='item.iconClass'></i> <span>{{item.name}}</span> <i class="fa fa-angle-left pull-right"></i></a>
		<ul class="treeview-menu">
			<crea-menu v-for="itemenu in item.children" :item="itemenu"  :is-parent="hasChildren(itemenu.children)" ></crea-menu>
		</ul>
	</li>
</template>

<script>

	import MenuItem from './menuItem.vue';

	module.exports = {
		name: 'menu-item',
		components: {
			creaMenu: MenuItem
		},
		props:{
			item: {
				type: Object,
				required: true
			},
			isParent: {
				type: Boolean,
				required: true
			}
		},
		methods: {
			toggleMenu: function (event) {
				$('li.pageLink').removeClass('active');
				var itemElement = event.toElement.parentElement;
				if(itemElement.className.indexOf('pageLink')>=0){
					itemElement.className = 'pageLink active';	
				}
				else {
					itemElement.parentElement.className = 'pageLink active';
				}
			},
			hasChildren: function(nodo){
				if(typeof nodo === 'undefined')
					return false;
				return nodo.length>0;
			}
		}
	}

</script>