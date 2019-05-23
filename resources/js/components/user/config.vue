<template>
    <el-container :style="{padding: '0 50px'}">
        <el-main :style="{padding: '24px 0', minHeight: '280px', background: '#fff'}">
            <el-container>
                <el-aside :style="{padding: '24px 0', width: 'auto', background: '#fff'}">
                    <el-menu :unique-opened="true"
                             :default-active="$root.currentRoute"
                             :router="true">
                        <component v-for="(item, index) in nav" :key="index" :is="item.type"
                                   :index="item.data.route"
                                   :data="item.data"></component>
                    </el-menu>
                </el-aside>
                <el-main>
                    <router-view ></router-view>
                </el-main>
            </el-container>
        </el-main>

    </el-container>
</template>
<script>
    import MenuItem from './../Navigation/menu-item.vue'
    export default{
        components: {
            MenuItem
        },
        data(){
            return {
                nav: [
                    {
                        type: 'menu-item',
                        data: {
                            route: 'edit-user',
                            text: 'Profile'
                        }
                    },
                    {
                        type: 'menu-item',
                        data: {
                            route: 'view-user-gallery',
                            text: 'Gallery'
                        }
                    },

                ],
            }
        },
    }
</script>