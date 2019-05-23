<template>
    <div class="card" @click="click(data )" >
        <img class="card-img-top" :src="`/upload/100_${data.url}`"
             :alt="data.data.title">
        <div class="card-body">
            <h5 class="card-title">{{data.data.title}}</h5>
            <p class="card-text">{{data.data.info}}</p>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                type: Object,
            },
            click:{
                type: Function
            },
        }
    }
</script>