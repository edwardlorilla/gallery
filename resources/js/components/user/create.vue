<template>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">

                <div class="card-header">
                    <h5 class="m-0">{{$route.meta.type}} {{$route.meta.title}}</h5>
                </div>
                <div class="card-body">
                    <el-form ref="form" @submit.native.prevent="onSubmit" label-position="labelPosition" size="small"
                             label-width="100px" :model="form">
                        <el-form-item :class="errors.file ? 'is-error is-required' : ''" label="Photo">
                            <upload removeUrl="/api/users" :image="form.image ? form.image.url : ''"
                                    @file="form.file=$event"/>
                            <div v-if="errors.file" v-for="error in errors.file" class="el-form-item__error">
                                {{error}}
                            </div>
                        </el-form-item>
                        <el-form-item :class="errors.name ? 'is-error is-required' : ''" label="Name">
                            <el-input type="text" required v-model="form.name"></el-input>
                            <div v-if="errors.name" v-for="error in errors.name" class="el-form-item__error">
                                {{error}}
                            </div>
                        </el-form-item>
                        <el-form-item :class="errors.email ? 'is-error is-required' : ''" label="Email">
                            <el-input type="email" required v-model="form.email"></el-input>
                            <div v-if="errors.email" v-for="error in errors.email" class="el-form-item__error">
                                {{error}}
                            </div>
                        </el-form-item>
                        <el-form-item :class="errors.password ? 'is-error is-required' : ''" label="Password">
                            <el-input type="password" required v-model="form.password"></el-input>
                            <div v-if="errors.password" v-for="error in errors.password" class="el-form-item__error">
                                {{error}}
                            </div>
                        </el-form-item>
                        <el-form-item :class="errors.confirm_password ? 'is-error is-required' : ''"
                                      label="Confirm Password">
                            <el-input type="password" required v-model="form.confirm_password"></el-input>
                            <div v-if="errors.confirm_password" v-for="error in errors.confirm_password"
                                 class="el-form-item__error">
                                {{error}}
                            </div>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="onSubmit" :loading="isDisabled">{{$route.params.id ? 'Update' : 'Create'}}
                            </el-button>
                            <el-button @click="$router.push('/' + $route.meta.url)">Cancel</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card" :class="errors.roles ? 'card-danger' : ''">

                <div class="card-header">
                    <h5 class="m-0">Roles</h5>
                </div>
                <div class="card-body">
                    <el-checkbox :indeterminate="isIndeterminate" v-model="checkAllRoles"
                                 @change="handleCheckAllRolesChange">Check all
                    </el-checkbox>
                    <div style="margin: 15px 0;"></div>
                    <el-checkbox-group v-model="form.roles" @change="handleCheckedRolesChange">
                        <el-checkbox v-for="(role, index) in roles" :label="role.id" :key="role.id">
                            {{role.display_name}}
                        </el-checkbox>
                    </el-checkbox-group>
                </div>

            </div>
        </div>
    </div>

</template>
<script>
    import upload from '../upload/index'
    import objectToFormData from '../mixins/objectToFormData'
    import catchError from '../mixins/catchError'

    export default {
        components: {
            upload
        },
        data() {
            return {
                isDisabled: false,
                labelPosition: 'left',
                errors: {
                    division: {
                        name: '',
                        station: ''
                    }
                },
                form: {
                    name: '',
                    email: '',
                    password: '',
                    confirm_password: '',
                    roles: [],

                },
                division: {name: '', station: ''},
                checkAllRoles: false,
                isIndeterminate: true,
                roles: [],

                isUser: false

            }
        },

        beforeRouteEnter(to, from, next) {
            if (to.params.id) {
                axios.get(`/api/users/${to.params.id}`).then(function (response) {
                    next(vm => vm.setData(response.data))
                }).catch(function (error) {
                    if (error.response.statusText) {
                        next(vm => catchError(vm, error))
                    }
                })
            } else {
                axios.get(`/api/users/create`).then(function (response) {
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
                axios.get(`/api/users/${to.params.id}`).then(function (response) {
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
        methods: {
            contains(a, obj) {
                var i = a.length;
                while (i--) {
                    if (a[i] === obj) {
                        return true;
                    }
                }
                return false;
            },
            handleCheckAllRolesChange(val) {
                var vm = this
                vm.form.roles = val ? _.map(vm.roles, 'id') : [];
                vm.isIndeterminate = false;
            },
            handleCheckedRolesChange(value) {

                let vm = this, checkedCount = value.length;
                vm.contains(value, 3) ? vm.isUser = true : vm.isUser = false
                vm.checkAllRole = checkedCount === vm.roles.length;
                vm.isIndeterminate = checkedCount > 0 && checkedCount < vm.roles.length;
            },

            onSubmit() {
                var vm = this
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        vm.isDisabled = true
                        vm.errors = {}
                        let id = vm.$route.params.id;
                        var formData = objectToFormData(vm.form)
                        if (id) {
                            formData.append('id', id)
                        }
                        formData.append('url', vm.$route.fullPath)
                        axios.post(`/api/users`, formData).then(function (response) {

                            vm.isDisabled = false
                            vm.$message({
                                type: 'success',
                                message: `User has been ${id ? 'updated' : 'created'}`
                            })
                        }).catch(function (error) {
                            vm.isDisabled = false
                            if (error.response && error.response.data.errors && error.response.data.message) {
                                vm.errors = error.response.data.errors;
                                vm.$message({message: error.response.data.message, type: 'error'})
                            }
                        })
                    }
                })
            },

            setData(row) {
                var vm = this
                vm.roles = row.roles
                if (row.user) {

                    vm.form = row.user
                    vm.form.roles = _.map(vm.form.roles, 'id');
                    vm.contains(vm.form.roles, 3) ? vm.isUser = true : vm.isUser = false
                } else {
                    vm.isUser = true
                    vm.form.roles = [3]
                }


                //vm.permissions = row.permissions

            },

        },
    }
</script>