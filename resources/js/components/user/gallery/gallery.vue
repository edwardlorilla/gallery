<template>
    <div>
        <div id="gallery">
            <div id="header">
                <el-row>
                    <el-col :span="6">
                        <el-button type="primary" @click="$router.push({ name: 'create-user-gallery'})">Create
                        </el-button>
                    </el-col>
                </el-row>
            </div>
            <div style="margin-top: 3px;" id="content">
                <div class="card-columns">
                    <card v-for="(img, index) in imgsArr" :click="clickFn" :key="index" :data="img"/>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    import catchError from '../../mixins/catchError'
    import card from '../gallery/card'
    import clickFn from '../../mixins/clickFn'
    export default {
        mixins:[clickFn],
        components:{
            card
        },
        data() {
            return {
                meta: {},
                links: {},
                imgsArr: [],
                group: -1 // request param
            }
        },
        beforeRouteEnter(to, from, next) {
            if (to.params.id) {
                axios.get(`/api/gallery/user/${to.params.id}`).then(function (response) {
                    next(vm => vm.setData(response.data))
                }).catch(function (error) {
                    if (error.response.statusText) {
                        next(vm => catchError(vm, error))
                    }
                })
            }
        },
        beforeRouteUpdate(to, from, next) {
            var vm = this
            if (to.params.id) {
                axios.get(`/api/gallery/user/${to.params.id}`).then(function (response) {
                    vm.setData(response.data)
                    next()
                }).catch(function (error) {
                    if (error.response.statusText) {
                        next(vm => catchError(vm, error))
                    }
                })
            } else {
                next()
            }
        },
        computed: {
            nextPage() {
                if (!this.meta || this.meta.current_page === this.meta.last_page) {
                    return;
                }
                return this.meta.current_page + 1;
            },
        },
        methods: {
            setData(response) {
                var vm = this
                this.links = {
                    first: response.first_page_url,
                    last: response.last_page_url,
                    next: response.next_page_url,
                    prev: response.prev_page_url
                };
                this.meta = {
                    current_page: response.current_page,
                    from: response.from,
                    to: response.to,
                    last_page: response.last_page_url,
                    per_page: response.per_page,
                    total: response.total
                }
                vm.imgsArr = vm.imgsArr.concat(response.data);
                this.group++;
            },
            goToNext() {
                this.$router.push({
                    query: {
                        page: this.nextPage,
                    },
                });
            },

        }
    }
</script>