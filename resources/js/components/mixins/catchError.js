export default (vm, error) => {
    vm.$message({
        type: 'error',
        message: error.response.statusText
    })
    if(error.response.status === 404){
        vm.$router.push({name: 'not_found'})
    }
}