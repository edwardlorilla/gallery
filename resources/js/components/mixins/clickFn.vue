<script>
    import catchError from './catchError'

    export default {
        data() {
            return {
                msg: ''
            }
        },
        methods: {
            clickFn(data) {
                var vm = this
                vm.msg = ''
                const h = this.$createElement;
                this.$msgbox({
                    title: data.data.title,
                    message: h("form", {
                        "class": "form-group"
                    }, [h("div", [data.data.info]), h("img", {
                        style: "width:60%;",
                        attrs: {
                            src: `/upload/${data.url}`,
                            alt: data.data.title
                        }
                    }), h("div", ['Message']), h('input', {
                        'class': 'form-control',
                        'style': 'margin-top: 3px;',
                        domProps: {
                            value: vm.msg
                        },
                        on: {
                            input: $event => {
                                if ($event.target.composing) return;
                                vm.msg = $event.target.value;
                            }
                        },
                        directives: [{
                            name: "model",
                            value: vm.msg
                        }]
                    })]),

                    showCancelButton: true,
                    confirmButtonText: 'Download',
                    cancelButtonText: 'Cancel',
                    beforeClose: (action, instance, done) => {

                        if (action === 'confirm') {
                            instance.confirmButtonLoading = true;
                            instance.confirmButtonText = 'Loading...';
                            axios.post('/api/download', {data: data, message: vm.msg}, {responseType: 'blob'})
                                .then(response => {
                                    var headers = response.headers;
                                    console.log(headers)
                                    var blob = new Blob([response.data], {type: headers['content-type']});
                                    var link = document.createElement('a');
                                    link.href = window.URL.createObjectURL(blob);
                                    link.download = data.title;
                                    link.click();
                                    link.remove()
                                    vm.isDisabled = false
                                    vm.$message({
                                        type: 'success',
                                        message: 'Image Downloading...'
                                    })
                                    done();
                                    instance.confirmButtonLoading = false;
                                })
                                .catch(error => {
                                    if (error.response.statusText) {
                                        catchError(vm, error)
                                    }
                                    done();
                                    instance.confirmButtonLoading = false;
                                })
                        } else {
                            done();
                        }
                    }
                }).catch(action => {

                    this.$message({
                        type: 'info',
                        message: 'action: ' + this.msg
                    });
                });
            }
        }
    }
</script>