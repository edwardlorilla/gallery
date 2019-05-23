<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-columns">
                    <card v-for="(img, index) in imgsArr" :key="index" :data="img" :click="clickFn"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import catchError from '../components/mixins/catchError'
    import card from '../components/user/gallery/card'
    import clickFn from '../components/mixins/clickFn'
    export default {
        mixins:[clickFn],
        data(){
            return{
                link:{
                    first: '',
                    last: '',
                    next: '',
                    prev: ''
                },
                meta:{
                    current_page: '',
                    from: '',
                    to: '',
                    last_page: '',
                    per_page: '',
                    total: '',
                },
                imgsArr: []
            }
        },
        mounted(){
            var vm =  this
            axios.get(`/api/galleries`).then(function (response) {
                vm.setData(response.data)
            }).catch(function (error) {
                if (error.response.statusText) {
                   catchError(vm, error)
                }
            })
        },
        components:{
            card
        },
        computed: {
            nextPage() {
                if (!this.meta || this.meta.current_page === this.meta.last_page) {
                    return;
                }
                return this.meta.current_page + 1;
            },
        },
        methods:{
            goToNext() {
                this.$router.push({
                    query: {
                        page: this.nextPage,
                    },
                });
            },
            setData(response){
                var vm = this
                vm.links = {
                    first: response.first_page_url,
                    last: response.last_page_url,
                    next: response.next_page_url,
                    prev: response.prev_page_url
                };
                vm.meta = {
                    current_page: response.current_page,
                    from: response.from,
                    to: response.to,
                    last_page: response.last_page_url,
                    per_page: response.per_page,
                    total: response.total
                }
                vm.imgsArr = vm.imgsArr.concat(response.data);
            }
        },

    }
</script>
