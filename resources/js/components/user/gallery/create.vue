<template>

    <el-form ref="form" @submit.native.prevent="onSubmit" label-width="120px" class="demo-ruleForm">
        <el-form-item :class="errors.file ? 'is-error is-required' : ''" label="Photo">
            <upload @file="form.file=$event"/>
            <div v-if="errors.file" v-for="error in errors.file" class="el-form-item__error">
                {{error}}
            </div>
        </el-form-item>
        <el-form-item :class="errors.title ? 'is-error is-required' : ''" label="Name">
            <el-input type="text" required v-model="form.title"></el-input>
            <div v-if="errors.title" v-for="error in errors.title" class="el-form-item__error">
                {{error}}
            </div>
        </el-form-item>
        <el-form-item :class="errors.info ? 'is-error is-required' : ''" label="Info:">
            <el-input type="text" required v-model="form.info"></el-input>
            <div v-if="errors.info" v-for="error in errors.info" class="el-form-item__error">
                {{error}}
            </div>
        </el-form-item>

        <el-form-item>
            <el-button type="primary" :loading="isDisabled" @click="onSubmit">Submit</el-button>
            <el-button @click="$router.go(-1)">Back</el-button>
        </el-form-item>
    </el-form>
</template>

<script>
    import upload from '../../upload/index'
    import objectToFormData from '../../mixins/objectToFormData'
    export default {
        name: "create-gallery",
        components:{
            upload
        },
        data(){
            return{
                errors: [],
                isDisabled: false,
                form: {
                    file: '',
                    title: '',
                    info: ''
                },
            }
        },
        methods:{
            onSubmit() {
                var vm = this
                vm.isDisabled = true
                vm.errors = {}
                let id = vm.$route.params.id;
                var formData = objectToFormData(vm.form)
                if (id) {
                    formData.append('id', id)
                }
                axios.post(`/api/gallery/user`, formData).then(function (response) {
                    vm.isDisabled = false
                    vm.$message({
                        type: 'success',
                        message: 'Item has been created'
                    })
                }).catch(function (error) {
                    vm.isDisabled = false
                    if (error.response && error.response.data.errors && error.response.data.message) {
                        vm.errors = error.response.data.errors;
                        vm.$message({message: error.response.data.message, type: 'error'})
                    }
                })
            }
        }
    }
</script>

<style scoped>

</style>