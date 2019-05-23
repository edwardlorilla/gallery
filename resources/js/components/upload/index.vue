<template>

        <el-upload
                class="avatar-uploader"
                action="/api/user/"
                :auto-upload="false"
                :show-file-list="false"
                :on-change="handleChange"
                name="file"
                :before-upload="beforeAvatarUpload">
            <img v-if="!imageUrl && image" :src="`/upload/100_${image}`" alt="avatar" class="avatar">
            <img v-else-if="imageUrl || image" :src="imageUrl" alt="avatar" class="avatar">
            <div v-else>
                <i class="el-icon-plus avatar-uploader-icon"></i>
                <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
            </div>
            <el-button v-if="imageUrl || image" @click="imageUrl=''">Clear Image</el-button>
        </el-upload>
</template>

<style scoped>
    .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .avatar-uploader .el-upload:hover {
        border-color: #409EFF;
    }

    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }

    .avatar {
        width: 178px;
        height: 178px;
        display: block;
    }
</style>

<script>
    export default {
        props: ['image'],
        data() {
            return {
                imageUrl: this.image ? `/upload/${this.image}` : '',
                raw: ''
            }
        },
        methods: {
            handleChange(file, fileList){
                var vm = this
                vm.$emit('file', file.raw)
                vm.imageUrl = URL.createObjectURL(file.raw)
            },
            beforeAvatarUpload(file) {
                const isJPG = file.type === 'image/jpeg';
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isJPG) {
                    this.$message.error('Avatar picture must be JPG format!');
                }
                if (!isLt2M) {
                    this.$message.error('Avatar picture size can not exceed 2MB!');
                }
                return isJPG && isLt2M;
            },
        }
    }
</script>
